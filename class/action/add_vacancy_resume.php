<?php

class add_vacancy_resume extends ActionModule
{
    protected $userinfo = [];
    protected $arrDataAnswer = [];

    function __construct()
    {
        $this->userinfo = ActionModule::$UserInfo;
        $this->arrDataAnswer = SystemClass::getarrDataAnswer();
    }

    function init()
    {
        header('Content-Type: application/json; charset=utf-8');

        $form = $this->arrDataAnswer['form'] ?? [];
        $vacancyId = (int)($form['vacancy_id'] ?? 0);
        $fio = trim($form['fio'] ?? '');
        $agree = !empty($form['personal_data_agree']);
        $vacancy = $this->getVacancy($vacancyId);

        if (!$vacancy || $fio === '' || !$agree) {
            echo json_encode(['status' => 'error', 'message' => 'Заповніть обов’язкові поля']);
            exit;
        }

        $resume = $this->saveResume();
        if (!$resume['ok']) {
            echo json_encode(['status' => 'error', 'message' => $resume['message']]);
            exit;
        }

        $phone = $this->userinfo['phone'] ?? '';
        if (substr($phone, 0, 2) === '38') $phone = substr($phone, 2);
        $telegram = $this->userinfo['username'] ?? '';
        if ($telegram !== '' && substr($telegram, 0, 1) !== '@') $telegram = '@'.$telegram;

        $result = db()->insert('vacancy_resumes', [
            'chat_id' => $this->userinfo['chat_id'] ?? SystemClass::getChatId(),
            'user_id' => $this->userinfo['id'] ?? null,
            'phone' => $phone,
            'telegram' => $telegram,
            'fio' => $fio,
            'instagram' => trim($form['instagram'] ?? ''),
            'comment' => trim($form['comment'] ?? ''),
            'about' => trim($form['about'] ?? ''),
            'vacancy_id' => $vacancyId,
            'vacancy_title' => $vacancy['post_title'] ?? '',
            'vacancy_type' => $vacancy['vacancy_type'] ?? '',
            'location' => $vacancy['location'] ?? '',
            'resume_path' => $resume['path'],
            'resume_original_name' => $resume['original_name'],
            'created_at' => ['RAW' => 'NOW()'],
        ]);

        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => 'Помилка при збереженні заявки']);
            exit;
        }

        $resumeId = db()->lastInsertId();
        $downloadUrl = URL.'webapps/web.php?action=vacancy_resumes&download='.(int)$resumeId;
        $text = $this->buildManagerMessage($form, $vacancy, $phone, $telegram, $resume, $downloadUrl);
        $this->notifyManagers($text, ROOT.$resume['path']);

        echo json_encode(['status' => 'ok']);
        exit;
    }

    protected function buildManagerMessage($form, $vacancy, $phone, $telegram, $resume, $downloadUrl)
    {
        return '<b>Нове резюме на вакансію</b>'."\n\n".
            '<b>Вакансія:</b> '.$this->h($vacancy['post_title'] ?? '')."\n".
            '<b>Тип:</b> '.$this->h($vacancy['vacancy_type'] ?? '-')."\n".
            '<b>Локація:</b> '.$this->h($vacancy['location'] ?? '-')."\n\n".
            '<b>ПІБ:</b> '.$this->h($form['fio'] ?? '')."\n".
            '<b>Телефон:</b> '.$this->h($phone)."\n".
            '<b>Telegram:</b> '.$this->h($telegram ?: '-')."\n".
            '<b>Instagram:</b> '.$this->h($form['instagram'] ?? '-')."\n\n".
            '<b>Коментар / LinkedIn / портфоліо:</b>'."\n".$this->h($form['comment'] ?? '-')."\n\n".
            '<b>Короткі відомості про себе:</b>'."\n".$this->h($form['about'] ?? '-')."\n\n".
            '<b>Резюме:</b> '.$this->h($resume['original_name'])."\n".
            '<b>Скачати:</b> '.$this->h($downloadUrl);
    }

    protected function notifyManagers($text, $resumePath)
    {
        $chatIds = [IDCHAT_MENEGERS];
        if (!empty(SystemClass::$chat_admin) && !in_array(SystemClass::$chat_admin, $chatIds, true)) {
            $chatIds[] = SystemClass::$chat_admin;
        }

        foreach ($chatIds as $chatId) {
            $this->sendManagerText($chatId, $text);

            if (is_file($resumePath)) {
                $this->sendDocument($chatId, $resumePath);
            }
        }
    }

    protected function sendManagerText($chatId, $text)
    {
        $plain = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $text)), ENT_QUOTES, 'UTF-8');
        if (mb_strlen($text, 'UTF-8') <= 3900) {
            SystemClass::TG_sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'html',
                'disable_web_page_preview' => true,
            ]);
            return;
        }

        $part = 1;
        while (mb_strlen($plain, 'UTF-8') > 0) {
            $chunk = mb_substr($plain, 0, 3500, 'UTF-8');
            $plain = mb_substr($plain, 3500, null, 'UTF-8');
            SystemClass::TG_sendMessage([
                'chat_id' => $chatId,
                'text' => 'Нове резюме, частина '.$part.":\n\n".$chunk,
                'disable_web_page_preview' => true,
            ]);
            $part++;
        }
    }

    protected function sendDocument($chatId, $filePath)
    {
        $ch = curl_init('https://api.telegram.org/bot'.TG_TOKEN.'/sendDocument');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'chat_id' => $chatId,
            'document' => new CURLFile($filePath),
            'caption' => 'Резюме кандидата',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $rawRes = curl_exec($ch);
        if ($rawRes === false) {
            wLog('📎 Telegram document cURL error: '.curl_error($ch), 'error');
        } else {
            wLog('📎 Telegram document response: '.$rawRes, 'info');
        }
        curl_close($ch);
    }

    protected function saveResume()
    {
        if (empty($_FILES['resume']) || ($_FILES['resume']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => 'Прикріпіть резюме'];
        }

        $file = $_FILES['resume'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['txt', 'text', 'rtf', 'doc', 'docs', 'docx', 'odt'];
        if (!in_array($ext, $allowed, true)) {
            return ['ok' => false, 'message' => 'Дозволені тільки текстові файли: txt, rtf, doc, docs, docx, odt'];
        }

        if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
            return ['ok' => false, 'message' => 'Файл резюме завеликий'];
        }

        $dir = ROOT.'webapps/uploads/resumes/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $name = date('Ymd_His').'_'.SystemClass::getChatId().'_'.$safeName.'.'.$ext;
        $path = $dir.$name;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return ['ok' => false, 'message' => 'Не вдалося зберегти файл резюме'];
        }

        return [
            'ok' => true,
            'path' => 'webapps/uploads/resumes/'.$name,
            'original_name' => $file['name'],
        ];
    }

    protected function getVacancy($id)
    {
        $db = @new mysqli(DB_HOST_RED, DB_USER_RED, DB_PASS_RED, DB_NAME_RED);
        if ($db->connect_error) return null;
        $db->set_charset('utf8mb4');

        $sql = "
            SELECT
                p.ID,
                p.post_title,
                MAX(CASE WHEN tt.taxonomy = 'vacancy_type' THEN t.name END) AS vacancy_type,
                MAX(CASE WHEN tt.taxonomy = 'location' THEN t.name END) AS location
            FROM wp_posts p
            LEFT JOIN wp_term_relationships tr ON tr.object_id = p.ID
            LEFT JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            LEFT JOIN wp_terms t ON t.term_id = tt.term_id
            WHERE p.post_type = 'vacancy'
              AND p.post_status = 'publish'
              AND p.ID = ?
            GROUP BY p.ID, p.post_title
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    protected function h($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
