<?php
$allowedDirs = ['logs', 'error','log_cron','logs_allsend'];
$logDir = in_array($_GET['dir'] ?? '', $allowedDirs) ? $_GET['dir'] : 'logs';

$files = glob("$logDir/log_*.log");
rsort($files);

$selected = $_GET['date'] ?? date('Y-m-d');
$filterChatId = $_GET['chat_id'] ?? '';
$filterPhone = $_GET['phone'] ?? '';

$logFile = "$logDir/log_$selected.log";

if (isset($_POST['clear_log']) && file_exists($logFile)) {
    file_put_contents($logFile, '');
    header("Location: {$_SERVER['PHP_SELF']}?dir=$logDir&date=$selected");
    exit;
}

$logLines = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES) : [];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Лог переглядач</title>
    <style>
        body { font-family: Arial; background: #f8f8f8; padding: 20px; }
        h2 { margin-bottom: 10px; }
        select, button, input { padding: 5px; margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; font-size: 14px; }
        .INFO { background: #e0f7fa; }
        .WARNING { background: #fff3cd; }
        .ERROR { background: #f8d7da; }
        .DEBUG { background: #e2e3e5; }
        pre { margin: 0; white-space: pre-wrap; }
        form.inline { display: inline; margin-bottom: 15px; }
        .json-data { display: none; background: #f4f4f4; padding: 10px; border: 1px dashed #ccc; }
        .json-container { margin-top: 5px; }
        .toggle-json { background: #007bff; color: white; border: none; padding: 4px 8px; cursor: pointer; font-size: 13px; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-json').forEach(button => {
                button.addEventListener('click', function () {
                    const container = this.closest('.json-container');
                    const pre = container.querySelector('.json-data');
                    const isVisible = pre.style.display === 'block';

                    pre.style.display = isVisible ? 'none' : 'block';
                    this.textContent = isVisible ? '+ Показати' : '− Приховати';
                });
            });
        });
    </script>
</head>
<body>
<h2>Лог переглядач</h2>

<form method="get" class="inline">
    <label>Тип логу:
        <select name="dir" onchange="this.form.submit()">
            <?php foreach ($allowedDirs as $dir):
                $sel = ($dir === $logDir) ? 'selected' : '';
                echo "<option value=\"$dir\" $sel>" . ucfirst($dir) . "</option>";
            endforeach; ?>
        </select>
    </label>

    <label>Дата:
        <select name="date" onchange="this.form.submit()">
            <?php foreach ($files as $file):
                $date = basename($file, '.log');
                $date = str_replace('log_', '', $date);
                $sel = ($date === $selected) ? 'selected' : '';
                echo "<option value=\"$date\" $sel>$date</option>";
            endforeach; ?>
        </select>
    </label>

    <label>Chat ID: <input type="text" name="chat_id" value="<?= htmlspecialchars($filterChatId) ?>"></label>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($filterPhone) ?>"></label>
    <button type="submit">🔍 Фільтрувати</button>
</form>

<form method="post" class="inline" onsubmit="return confirm('Ви впевнені, що хочете очистити лог?');">
    <input type="hidden" name="clear_log" value="1">
    <button type="submit">🧹 Очистити лог</button>
</form>

<?php if (empty($logLines)): ?>
    <p>Лог-файл відсутній або порожній.</p>
<?php else: ?>
    <table>
        <thead>
        <tr><th>Час</th><th>Рівень</th><th>Файл:Рядок</th><th>Повідомлення</th></tr>
        </thead>
        <tbody>
        <?php foreach ($logLines as $line):
            if (preg_match('/^\[(.*?)\] \[(.*?)\] \((.*?)\) (.*)$/', $line, $matches)) {
                [$full, $time, $level, $location, $message] = $matches;
                $class = strtoupper($level);

                if ($filterChatId && strpos($message, "chat_id: $filterChatId") === false) continue;
                if ($filterPhone && strpos($message, "phone: $filterPhone") === false) continue;

                $isJson = false;
                if (strpos($message, '__JSON__:') === 0) {
                    $isJson = true;
                    $jsonData = substr($message, 9);
                    $jsonFormatted = json_encode(json_decode($jsonData, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    $message = "<div class='json-container'>
                        <button type='button' class='toggle-json'>+ Показати</button>
                        <pre class='json-data'>$jsonFormatted</pre>
                    </div>";
                } else {
                    $message = "<pre>$message</pre>";
                }

                echo "<tr class=\"$class\">
                        <td>$time</td>
                        <td>$level</td>
                        <td><small>$location</small></td>
                        <td>$message</td>
                    </tr>";
            } else {
                echo "<tr><td colspan='4'><pre>$line</pre></td></tr>";
            }
        endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
