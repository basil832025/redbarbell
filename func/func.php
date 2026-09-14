<?php
// отправляет с ключем авториазции пост запрсы
function send_data_b52( $post,$ip='') {

  //  header('Content-Type: application/json'); // Specify the type of data
    //  $ch = curl_init($url); // Initialise cURL
    $ch = curl_init(); // Initialise cUR
  //  s(($post));
    $ip = trim((string) $ip);
    if ($ip) {
        $ip = preg_match('~^https?://~i', $ip) ? rtrim($ip, '/') . '/' : 'http://' . $ip . '/';
    } elseif (defined('HOST_SOCKET') && HOST_SOCKET) {
        $ip = rtrim(HOST_SOCKET, '/') . '/';
    } else {
        curl_close($ch);
        return array('ERROR', 'NO_HOST_SOCKET', 'HOST_SOCKET is not configured');
    }
 //   s($ip);
  //  s(json_encode($post));
    $post = 'data='.json_encode($post); // Encode the data array into a JSON string
    $post = str_replace('&','@@==@@',$post); // замена для пост запроса спец сивола
    //  $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
    $authorization = "Api_Key:".SECRET_KEY; // Prepare the authorisation token
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_URL, $ip);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( $authorization )); // Inject the token into the header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
    curl_setopt($ch,CURLOPT_FAILONERROR,1);
    $result = curl_exec($ch); // Execute the cURL statement
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    if($curl_errno > 0){
        curl_close($ch);
        return array('ERROR','NO_INTERNET','Нет связи с сервером');
    }else
    {
        curl_close($ch); // Close the cURL connection

        $data_res = json_decode($result,1);
        $status = !empty($data_res['status']) ?  $data_res['status'] : 'NO_STATUS';

        if (!empty($data_res['dataCommand']) && $status=='ERROR' && !empty($data_res['dataCommand']['NO_RESULT']))
        {$type_result='NO_RESULT';$res='';}
        else {$type_result='1';$res = !empty($data_res['dataCommand']) ? $data_res['dataCommand'] : 'NODATA';}
   //     s('$status='.$status.' $type_result='.$type_result.' res=');
      //  s($res);
        return [$status,$type_result,$res]; // Return the received data
    }


}
// проверка это номер украинский или буржуйский
function validatePhone(string $phone): bool
{
    // Оставляем только цифры
    $digits = preg_replace('/\D+/', '', $phone);

    // Полный формат: 380XXXXXXXXX (12 цифр)
    // Сокращённый:    0XXXXXXXXX   (10 цифр)
    return (bool) preg_match('/^(?:380\d{9}|0\d{9})$/', $digits);
}
function wLog($message, $level = 'info', $logDir = 'logs') {
    // Преобразуем message в строку, если это массив или объект
    if (is_array($message) || is_object($message)) {
        $message = '__JSON__:' . json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    // Убираем лишние обратные слэши, если есть
    $message = stripslashes($message);

    $level = strtoupper($level);
    $timestamp = date('Y-m-d H:i:s');
    $date = date('Y-m-d');

    // Абсолютный путь к каталогу логов
    $logDir = __DIR__ . DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR. $logDir;
  //  echo $logDir;
    // Убедимся, что папка существует
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logFile = rtrim($logDir, '/\\') . "/log_$date.log";

    // Получаем имя файла и строку вызова
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file = $backtrace['file'] ?? 'unknown file';
    $line = $backtrace['line'] ?? 'unknown line';
    $chatId = class_exists('SystemClass') ? SystemClass::getChatId() : '-';
    $phone = class_exists('SystemClass') ? SystemClass::getPhone() : '-';
    $prefix = "[chat_id: $chatId][phone: $phone]";
    // Подключение ID и телефона из сессии, если доступны
 //   $prefix = "[chat_id: " . ($chatId ?? '-') . "][phone: " . ($phone ?? '-') . "]";

    // Собираем строку лога
    $logEntry = "[$timestamp] [$level] ($file:$line) $prefix $message" . PHP_EOL;

    // Запись
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}


//проверка формата телефОНА
function checkPhone($phone='')
{
    // удаляем все не цифровые символы
    $phone = preg_replace('/[^0-9]/', '', $phone);
     $phone .'  ='.strlen($phone);
    if (strlen($phone)!=10)  return array('ERROR','NO_10_CIFR','В данном номере не 10 цифр!');
    else
    {
        $Acode_operrators=array('050','063','066','067','068','073','089','091','092','093','094','095','096','097','098','099');
        $code_op=substr($phone,0,3);
        if (!in_array($code_op,$Acode_operrators)) return array('ERROR','ERROR_CODE_PHONE','Веденный код ('.$code_op.') не является кодом мобильного оператора Украины');
        else
            return  array('OK','GOOD_PHONE',$phone);

    }
}
function get($key){
    return isset($_GET[$key])?$_GET[$key]:false;
}
function post($key){
    if (isset($_POST[$key]) && is_array($_POST[$key])){
        foreach ($_POST[$key] as $k => $value) {
            $_POST[$key][$k] = escape($value);
        } //конец цикла fekv
    } // конец if
    else {
        $_POST[$key] = isset($_POST[$key])?escape($_POST[$key]):false;
    } // конец else
    return $_POST[$key];}
/*function post($key){
		return isset($_POST[$key])?$_POST[$key]:false;
}*/
function poste($key){
    if (isset($_POST[$key]) && is_array($_POST[$key])){
        foreach ($_POST[$key] as $k => $value) {
            if (is_array($value)){
                foreach ($value as $k2 => $v2) {
                    $_POST[$key][$k][$k2] = addslashes($v2);
                    //$_POST[$key][$k][$k2] = escape($v2);
                }
            }else{
                // $_POST[$key][$k] = escape($value);
                $_POST[$key][$k] = addslashes($value);
            }
        } //конец цикла fekv
    } // конец if
    else {
        $_POST[$key] = isset($_POST[$key])?addslashes($_POST[$key]):false;
        //$_POST[$key] = isset($_POST[$key])?escape($_POST[$key]):false;
    } // конец else
    return $_POST[$key];
}
function utf8_compliant($str) {
    if ( strlen($str) == 0 ) return true;
    return (preg_match('/^.{1}/us',$str) == 1);
}
function Ajax($masiv){

    // замечен глюк при перезагрузке выскакивало сообщения со сохранением файла
    if (empty($_POST['ajax_method'])){
        return false;
    }
    $_POST['ajax_method']='';
    if (!empty($_SESSION['error_predypreg'])){
        //$masiv= $masiv+ array('ERRN_AJAX' => $_SESSION['error_predypreg']);
        $_SESSION['error_predypreg']=false;
    }
    // замечен был глюк, что иногда проскакивала кодировка не utf-8, а старенькая WINDOWS-1251 потому проверям код на корректность utf8 если все впорядке то пропускаем, если проскочило то пітаемся преобразовать, конечно єто не ПАНАЦЕя от всех бед, но всеже единичній віход с положения, будем решать проблемі по мере поступления, продолбался с єтой проблемой 2 дня!!! с поиском подходящего решения
    if (function_exists('iconv') && !empty($masiv['content']) && !utf8_compliant($masiv['content'])) {
        $masiv['content']= iconv('WINDOWS-1251','UTF-8',$masiv['content']);
        //s('utt8');
    }

    //send_error(p($masiv,1));
    // $myJsonData=array2json($masiv);
    //s($masiv);
    //s('tyt1111');
    $myJsonData=json_encode($masiv);
    // s(json_last_error());
    //s($myJsonData);
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' .gmdate('D, d M Y H:i:s') .'GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header("Content-type: application/json; charset=utf-8");

    print $myJsonData;
    exit;
}

function date_for_sql_format($date) {
    //  s($date);
    //	return $date;
// вот  так выводит 2019-01-26
    return substr($date,6,4).'-' .substr($date,3,2).'-' .substr($date,0,2);
}
function date_for_firebird_format($date) {
    //  s($date);
    //	return $date;
// c такой 2019-01-26 вот  так выводит 26.01.2019
    return substr($date,8,2).'.' .substr($date,5,2).'.'.substr($date,0,4);
}
function writeToLog($text,$dir='logs')
{
    $text = 'Дата: ' .date("H:i:s d-m-Y ") ."=====================\n". $text ."\n-----------------------\n";

    $filename = ROOT_A . $dir.'/log_' .date('d-m-Y') .'.html';
    // Вначале давайте убедимся, что файл существует и доступен для записи.
    if (!chmod(ROOT_A .$dir, 0777)) {
        echo('Невозможно поменять права на временный файл. (' .ROOT_A .$dir .')  ERR_NUM=R4');
    }
    /*	if (!chmod(ROOT_A .'barcodes', 0777)) {
                                                    echo('Невозможно поменять права на временный файл. (' .ROOT_A .'logs' .')  ERR_NUM=R4');
                                            }
       */ // В нашем примере мы открываем $filename в режиме "записи в конец".
    // Таким образом, смещение установлено в конец файла и
    // наш $text допишется в конец при использовании fwrite().
    if (!$fp = fopen($filename, 'a')) {
        echo "Не могу открыть файл ($filename)";
        exit;
    }

    // Записываем $somecontent в наш открытый файл.
    if (fwrite($fp, $text) === FALSE) {
        echo "Не могу произвести запись в файл ($filename)";
        exit;
    }

    //echo "Ура! Записали ($text) в файл ($filename)";

    fclose($fp);


}
function sLog($data)
{
    wLog($data,'info','logs');
  //  writeToLog(p($data,1));
}
function sAllSend($data)
{
    wLog($data,'info','logs_allsend');
  //  writeToLog(p($data,1));
}
function sCron($data)
{
    wLog($data,'info','log_cron');
   // writeToLog(p($data,1),'log_cron');
}
function getDayofWeekUkr($num)
{
    $str = '';
    switch ($num)
    {
        case 1 : $str = 'Пн.'; break;
        case 2 : $str = 'Вт.'; break;
        case 3 : $str = 'Ср.'; break;
        case 4 : $str = 'Чт.'; break;
        case 5 : $str = 'Пт.'; break;
        case 6 : $str = 'Сб.'; break;
        case 0 : $str = 'Нд.'; break;

    }
    return $str ;
}
function getNumDayOfWeek  ($date) {
    // получим день недели
    $day = $date->format("w");
    // $day = 1;
    // вернем на 1 меньше [0 - вск]
    return ($day == 0) ? 6 : $day - 1;
};
/* для отправки текстовых сообщений */
function TG_sendMessage($getQuery) {
    // slog('TGGG   '.TG_TOKEN);
    // slog($getQuery);
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* для отправки изображений */
function TG_sendPhoto($arrayQuery) {
    $ch = curl_init('https://api.telegram.org/bot'. TG_TOKEN .'/sendPhoto');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

/* для получения данных о файле */
function TG_getFile($arrayQuery) {
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/getFile?" . http_build_query($arrayQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

function p($data,  $str = 0,$dop_param = 0) {
    if ($dop_param == 1){
        print '--------------------------------<br />Инфо о переменной: <pre>';
        print var_dump($data) .'</pre>' ;
    }
    if ($str){
        $str = '<br />-------------------------------<br />Вывод функции p(<br /><pre>'. "\r\n";
        $str .= print_r($data, true);
        $str .= '</pre>' .')==============================<br />'. "\r\n";
        return $str;
    }else{
        print '<br />-------------------------------<br />Вывод функции p(<br /><pre>';
        print print_r($data) .'</pre>' .')==============================<br />' ;
    }
}
function send_error($str) {
    if (!empty($str)){
        //  $_SESSION['error_predypreg'] = addslashes($str);
        user_error(addslashes($str), E_USER_ERROR);
    }
    return;
}
function s($message, $level = 'info', $logDir = 'error') {
    // Преобразуем message в строку, если это массив или объект
    if (is_array($message) || is_object($message)) {
        $message = print_r($message, true);
    }

    $level = strtoupper($level);
    $timestamp = date('Y-m-d H:i:s');
    $date = date('Y-m-d');

    // Убедимся, что папка существует
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logFile = rtrim($logDir, '/\\') . "/log_$date.log";

    // Определим, откуда вызвана функция
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file = $backtrace['file'] ?? 'unknown file';
    $line = $backtrace['line'] ?? 'unknown line';

    $logEntry = "[$timestamp] [$level] ($file:$line) $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
// Экранирует кавычки. data - mixed
function escape($data){
//	if (!get_magic_quotes_gpc()){
    $doc = $data;
    if (is_array($doc)){
        while( list($k,$v) = each ($doc))
            $doc[$k] = sql_valid(clean_word($v));
    }
    else $doc = sql_valid(clean_word($doc));
    return $doc;
    /*	}
        else{
            return $data;
        }*/
}
// замена или алтенатива mysql_real_escape_string
function sql_valid($data) {
    $data = str_replace("\\", "\\\\", $data);
    $data = str_replace("'", "\'", $data);
    $data = str_replace('"', '\"', $data);
    $data = str_replace("\x00", "\\x00", $data);
    $data = str_replace("\x1a", "\\x1a", $data);
    $data = str_replace("\r", "\\r", $data);
    $data = str_replace("\n", "\\n", $data);
    return($data);
}
function clean_word($data){
    $data=preg_replace('#<!--\[if gte mso 9\]>.*?<!\[endif\]-->#is','',$data);
    $data=preg_replace('#<!--\[if gte mso 10\]>.*?<!\[endif\]-->#is','',$data);
    return $data;
}
?>
