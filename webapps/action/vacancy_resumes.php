<?php

class vacancy_resumes
{
    function __construct()
    {
    }

    function init()
    {
        if (!empty($_GET['download'])) {
            $this->download((int)$_GET['download']);
            exit;
        }

        $this->view_html();
    }

    protected function h($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    protected function shortText($value, $length = 120)
    {
        $value = trim((string)$value);
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $length);
        }

        return substr($value, 0, $length);
    }

    protected function rows()
    {
        $sql = "SELECT *
            FROM vacancy_resumes
            ORDER BY created_at DESC, id DESC";

        return db()->query($sql) ? db()->fetchAll() : [];
    }

    protected function download($id)
    {
        $row = db()->selectOne('vacancy_resumes', 'id = :id', ['id' => $id]);
        if (!$row || empty($row['resume_path'])) {
            http_response_code(404);
            echo 'File not found';
            return;
        }

        $baseDir = realpath(ROOT.'webapps/uploads/resumes/');
        $filePath = realpath(ROOT.$row['resume_path']);
        if (!$baseDir || !$filePath || strpos($filePath, $baseDir) !== 0 || !is_file($filePath)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }

        $downloadName = $row['resume_original_name'] ?: basename($filePath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.str_replace('"', '', $downloadName).'"');
        header('Content-Length: '.filesize($filePath));
        readfile($filePath);
    }

    protected function body()
    {
        $rows = $this->rows();
        if (!$rows) {
            return '<div class="alert alert-light border mt-3">Нових резюме поки немає.</div>';
        }

        $html = '<div class="table-responsive mt-3">
<table class="table table-hover table-sm align-middle">
    <thead class="table-primary">
        <tr>
            <th>Дата</th>
            <th>Кандидат</th>
            <th>Вакансія</th>
            <th>Контакти</th>
            <th></th>
        </tr>
    </thead>
    <tbody>';

        foreach ($rows as $row) {
            $contactsParts = array_filter([
                $this->h($row['phone'] ?? ''),
                $this->h($row['telegram'] ?? ''),
                $this->h($row['instagram'] ?? ''),
            ]);
            $contacts = implode('<br>', $contactsParts);
            $vacancy = $this->h($row['vacancy_title'] ?? '');
            if (!empty($row['location'])) {
                $vacancy .= '<br><span class="text-muted">'.$this->h($row['location']).'</span>';
            }
            $downloadUrl = URL.'webapps/web.php?action=vacancy_resumes&download='.(int)$row['id'];

            $html .= '<tr>
                <td class="small text-nowrap">'.$this->h($row['created_at'] ?? '').'</td>
                <td class="small">
                    <strong>'.$this->h($row['fio'] ?? '').'</strong>
                    '.(!empty($row['about']) ? '<div class="text-muted mt-1">'.$this->h($this->shortText($row['about'])).'</div>' : '').'
                </td>
                <td class="small">'.$vacancy.'</td>
                <td class="small">'.$contacts.'</td>
                <td class="text-end">
                    <a class="btn btn-outline-danger btn-sm text-nowrap" href="'.$this->h($downloadUrl).'">Скачати</a>
                </td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    protected function header()
    {
        return '<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <title>Резюме</title>
    <style>
        body { background:#f6f7f9; }
        .page { max-width: 980px; margin:0 auto; padding:18px 12px 32px; }
        table { background:#fff; }
        th, td { vertical-align: middle; }
    </style>
</head>
<body>';
    }

    protected function footer()
    {
        return '<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
const tg = window.Telegram;
if (tg && tg.WebApp) {
    tg.WebApp.backgroundColor = "#f6f7f9";
    tg.WebApp.headerColor = "#212121";
    tg.WebApp.expand();
    const btn = tg.WebApp.MainButton;
    btn.show();
    btn.text = "Закрити";
    tg.WebApp.onEvent("mainButtonClicked", function() {
        tg.WebApp.close();
    });
}
</script>
</body>
</html>';
    }

    protected function view_html()
    {
        echo $this->header();
        echo '<main class="page"><h4 class="mb-2">Нові резюме</h4>';
        echo $this->body();
        echo '</main>';
        echo $this->footer();
    }
}
