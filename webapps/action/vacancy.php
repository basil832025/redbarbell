<?php

class vacancy
{
    protected $chatid = '';
    protected $vacancyId = 0;
    protected $user = null;

    function __construct()
    {
        $this->chatid = $_GET['chatid'] ?? '';
        $this->vacancyId = (int)($_GET['id'] ?? 0);
        if ($this->chatid !== '') {
            $this->user = db()->selectOne('spr_users', 'chat_id = :chat_id', ['chat_id' => $this->chatid]);
        }
    }

    function init()
    {
        $this->view_html();
    }

    protected function redDb()
    {
        $db = @new mysqli(DB_HOST_RED, DB_USER_RED, DB_PASS_RED, DB_NAME_RED);
        if ($db->connect_error) {
            return null;
        }
        $db->set_charset('utf8mb4');
        return $db;
    }

    protected function getVacancies()
    {
        $db = $this->redDb();
        if (!$db) return [];

        $sql = "
            SELECT
                p.ID,
                p.post_title,
                p.post_content,
                MAX(CASE WHEN tt.taxonomy = 'vacancy_type' THEN t.name END) AS vacancy_type,
                MAX(CASE WHEN tt.taxonomy = 'location' THEN t.name END) AS location
            FROM wp_posts p
            LEFT JOIN wp_term_relationships tr ON tr.object_id = p.ID
            LEFT JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            LEFT JOIN wp_terms t ON t.term_id = tt.term_id
            WHERE p.post_type = 'vacancy'
              AND p.post_status = 'publish'
            GROUP BY p.ID, p.post_title, p.post_content
            ORDER BY p.ID
        ";
        $res = $db->query($sql);
        if (!$res) return [];
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    protected function getVacancy($id)
    {
        foreach ($this->getVacancies() as $row) {
            if ((int)$row['ID'] === (int)$id) return $row;
        }
        return null;
    }

    protected function e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    protected function cleanContent($value)
    {
        $value = preg_replace('/<!--.*?-->/s', '', (string)$value);
        $value = strip_tags($value, '<p><br><ul><ol><li><strong><b><em><i><h2><h3><h4>');
        $value = preg_replace('/>\s+</', '><', $value);
        $value = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $value);

        if (preg_match('/<(p|ul|ol|li|h2|h3|h4)\b/i', $value)) {
            return $value;
        }

        return nl2br($value);
    }

    function view_html()
    {
        $phone = $this->user['phone'] ?? '';
        if (substr($phone, 0, 2) === '38') $phone = substr($phone, 2);
        $username = $this->user['username'] ?? '';
        if ($username !== '' && substr($username, 0, 1) !== '@') $username = '@'.$username;

        $html = '<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <title>Вакансії</title>
    <style>
        body { background:#f6f7f9; color:#1f2933; }
        .page { max-width: 760px; margin: 0 auto; padding: 22px 14px 36px; }
        .vacancy-card { display:block; padding:16px; margin-bottom:12px; border:1px solid #e3e6ea; border-radius:8px; background:#fff; color:#1f2933; text-decoration:none; }
        .vacancy-card:active, .vacancy-card:hover { color:#1f2933; border-color:#c6ccd4; }
        .meta { color:#667085; font-size:14px; margin-top:6px; }
        .content { background:#fff; border:1px solid #e3e6ea; border-radius:8px; padding:12px 14px; line-height:1.35; font-size:13px; }
        .content p { margin:0 0 8px; }
        .content ul, .content ol { margin:0 0 8px; padding-left:20px; }
        .content li { margin:0 0 4px; }
        .content h2, .content h3, .content h4 { margin:10px 0 6px; font-size:16px; line-height:1.25; }
        textarea { min-height: 220px; }
        .form-label { font-weight: 600; }
    </style>
</head>
<body>
<div class="page">';

        if ($this->vacancyId <= 0) {
            $html .= '<h3 class="mb-3">Активні вакансії</h3>';
            $items = $this->getVacancies();
            if (!$items) {
                $html .= '<div class="alert alert-light border">Зараз активних вакансій немає.</div>';
            }
            foreach ($items as $item) {
                $meta = trim(($item['vacancy_type'] ?? '').(($item['location'] ?? '') ? ' · '.$item['location'] : ''));
                $url = URL.'webapps/web.php?action=vacancy&chatid='.urlencode($this->chatid).'&id='.(int)$item['ID'];
                $html .= '<a class="vacancy-card" href="'.$this->e($url).'">
                    <h5 class="mb-1">'.$this->e($item['post_title']).'</h5>
                    <div class="meta">'.$this->e($meta).'</div>
                </a>';
            }
        } else {
            $item = $this->getVacancy($this->vacancyId);
            if (!$item) {
                $html .= '<div class="alert alert-warning">Вакансію не знайдено або вона вже не активна.</div>';
            } else {
                $html .= '<a href="'.URL.'webapps/web.php?action=vacancy&chatid='.$this->e($this->chatid).'" class="btn btn-outline-secondary btn-sm mb-3">Назад</a>
                <h3 class="mb-1">'.$this->e($item['post_title']).'</h3>
                <div class="meta mb-3">'.$this->e(trim(($item['vacancy_type'] ?? '').(($item['location'] ?? '') ? ' · '.$item['location'] : ''))).'</div>
                <div class="content mb-3">'.$this->cleanContent($item['post_content']).'</div>

                <form id="vacancyForm" enctype="multipart/form-data">
                    <input type="hidden" name="form[vacancy_id]" value="'.(int)$item['ID'].'">
                    <div class="mb-3">
                        <label class="form-label">Імя та прізвище *</label>
                        <input class="form-control" name="form[fio]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input class="form-control" value="'.$this->e($phone).'" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telegram</label>
                        <input class="form-control" value="'.$this->e($username).'" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instagram</label>
                        <input class="form-control" name="form[instagram]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Тут можна залишити коментар/посилання на LinkedIn/портфоліо</label>
                        <textarea class="form-control" name="form[comment]"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Короткі відомості про себе</label>
                        <textarea class="form-control" name="form[about]"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Резюме *</label>
                        <input class="form-control" type="file" name="resume" accept=".txt,.text,.rtf,.doc,.docs,.docx,.odt" required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" name="form[personal_data_agree]" id="agree" required>
                        <label class="form-check-label" for="agree">Я погоджуюсь на обробку моїх персональних даних *</label>
                    </div>
                    <button class="btn btn-danger w-100" type="submit">Відправити заявку</button>
                </form>';
            }
        }

        $html .= '</div>
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
const tg = window.Telegram;
if (tg && tg.WebApp) {
    tg.WebApp.backgroundColor = "#f6f7f9";
    tg.WebApp.headerColor = "#212121";
    tg.WebApp.expand();
}
const form = document.getElementById("vacancyForm");
if (form) {
    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const dataForm = new FormData(form);
        const safeOb = (tg && tg.WebApp && tg.WebApp.initDataUnsafe) ? tg.WebApp.initDataUnsafe : {};
        dataForm.append("action", "add_vacancy_resume");
        dataForm.append("user_id", safeOb.user && safeOb.user.id ? safeOb.user.id : "'.$this->e($this->chatid).'");
        const response = await fetch("'.URL.'/bot.php?", { method: "POST", body: dataForm });
        const data = await response.json().catch(() => ({status:"error"}));
        if (data.status === "ok") {
            alert("Заявку відправлено");
            if (tg && tg.WebApp) tg.WebApp.close();
        } else {
            alert(data.message || "Помилка при відправці заявки");
        }
    });
}
</script>
</body>
</html>';
        echo $html;
    }
}
