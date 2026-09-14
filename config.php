<?php
//s('config_tyt');
set_time_limit(0);
 // define('HOST_SOCKET','http://shelfie.ddns.net'); // бюти сервер
 // define('HOST_SOCKET','http://178.151.154.252'); // мой комп


//  define('HOST_SOCKET','http://178.151.119.180/'); //  red lesnoy


//  define('HOST_SOCKET','http://62.216.44.62'); // interfit
 //define('PORT_SOCKET','5556');
 define('PORT_SOCKET','80');
 //id чат группы куда будут отсылаться сообщния от клиентов
 //define('IDCHAT_3STUXII','-847849320');
// define('IDCHAT_3STUXII','-1001848437446');

 define('IDCHAT_MENEGERS','-1002615734939');

//ob_implicit_flush();
if (php_sapi_name() === 'cli') {
 $scriptPath = __FILE__; // путь к текущему файлу при запуске из cron
} else {
 $scriptPath = $_SERVER['SCRIPT_FILENAME']; // путь при запуске через веб
}

$path_parts = pathinfo($scriptPath);
$path_parts['dirname'] = $path_parts['dirname'] ?? __DIR__;
 //   $path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); // определяем директорию скрипта
$path_parts['dirname'] = strpos($path_parts['dirname'],'webapps') ? str_replace('/webapps','',$path_parts['dirname']) : $path_parts['dirname'];
$path_parts['dirname'] = str_replace(['\\webapps', '/webapps'], '', $path_parts['dirname']);

 $dirname=$path_parts['dirname'].'/';
  // путь к стартовой директории вычисляется автоматом  
//  define('ROOT', $_SERVER['DOCUMENT_ROOT'] .(substr($_SERVER['DOCUMENT_ROOT'],-1)=='/' ? '' : '/') .(PATH=='' || (substr(PATH, -1)=='/') ? PATH : PATH .'/'));
  define('ROOT', $dirname);
 define('LIB_DIR', __DIR__ . '/class/');
 define('TRAIT_DIR', __DIR__ . '/class/action/');
define('WEBAPPS_ACTION', __DIR__ . '/webapps/action/');
define('WEBAPPS_REPORTS', __DIR__ . '/webapps/reports/');
define('PATH', ''); // первый символ пути не слеш например: my_site/admin/

// путь к веб-сайту
if (!empty($_SERVER['SERVER_NAME'])){
 // define('URL', 'https://' .$_SERVER['SERVER_NAME'] .(substr(PATH,0,1)=='/' ? '' : '/') .(PATH=='' || (substr(PATH, -1)=='/') ? PATH : PATH .'/'));
  define('URL', 'https://braeden-inkiest-insistingly.ngrok-free.dev' .(substr(PATH,0,1)=='/' ? '' : '/') .(PATH=='' || (substr(PATH, -1)=='/') ? PATH : PATH .'/'));
}

define('URL2', 'https://braeden-inkiest-insistingly.ngrok-free.dev/'  .(substr(PATH,0,1)=='/' ? '' : '/') .(PATH=='' || (substr(PATH, -1)=='/') ? PATH : PATH .'/'));


  // путь к веб-сайту
 // define('URL', 'https://' .$_SERVER['SERVER_NAME'] .(substr(PATH,0,1)=='/' ? '' : '/') .(PATH=='' || (substr(PATH, -1)=='/') ? PATH : PATH .'/'));
define('SECRET_KEY', 'cgkD0xa9DL6BTjJpiwooyVwqxOKgNPptZ0pF5c4UFOEOkOVsQrN6afo8VJeRkXR9SzsYcI2QuUM5Hn3klPUWr3sZN20f89BhJNQ2OHSRhEgXuKkMj7NUfz1VSDQcEQKqFnebgkcC6pS9U1AyJvQY9Z0qiAVEJ6afxpB9Nj1z4UNSto94SzJ4rKFHhFvlkgmXcFUrGer8hO2hXUsAIWVMC6jk4USTSiOw4QQKxNm0NcgmTiPBuPIS9dy1pqfy4PsO');
if (!defined('HOST_SOCKET')) {
    $hostSocket = getenv('HOST_SOCKET') ?: '';
    define('HOST_SOCKET', $hostSocket);
}

define('ROOT_A', ROOT .'');
//  define('ROOT_A', ROOT .'telegram/');
// hрегиструем скрипт с котроым будет работать бот
// это локальный для теста бот на ноут через тунель
// https://api.telegram.org/bot8648117136:AAH6cR7GHwX-_sHCGIGqqPh4LbZayuGh-rk/setWebhook?url=https://braeden-inkiest-insistingly.ngrok-free.dev/bot.php
// https://api.telegram.org/bot7703736154:AAHAkJ3ImqN_gkIhAOHX4ewQrH4rnJ_Guu0/setWebhook?url=https://redbarbell.com.ua/telegram/bot.php
// узнаем chat_id ну и разную инфу  пользователе
// https://api.telegram.org/bot5632314594:AAGWi0KmQTWLalvt21eZquGmUhKWd3I6gScY/deleteWebhook
// https://api.telegram.org/bot5632314594:AAGWi0KmQTWLalvt21eZquGmUhKWd3IgScY/getUpdates
// https://api.telegram.org/bot8648117136:AAH6cR7GHwX-_sHCGIGqqPh4LbZayuGh-rk/getWebhookInfo
$token = '';
//define("TG_TOKEN", "7703736154:AAHAkJ3ImqN_gkIhAOHX4ewQrH4rnJ_Guu0"); // red
define("TG_TOKEN", "8648117136:AAH6cR7GHwX-_sHCGIGqqPh4LbZayuGh-rk"); // localtuneltest  @test_redbarbell_bot

 define('DISPLAY_ERRORS', 4);
  define('DB_HOST','localhost');
  define('DB_USER','root');
  define('DB_PASS','');
  define('DB_NAME','red_bot');

define('DB_HOST_RED','31.131.17.179');
define('DB_USER_RED','redbarbe_db');
define('DB_PASS_RED','QWErty123!!!');
define('DB_NAME_RED','redbarbe_db');


//    define('HOST_SOCKET','91.227.181.60');
    // Ошибки БАЗЫ данных MySql выводить или скрыть
  define('ERROR_DB', 1);
