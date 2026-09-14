<?php

date_default_timezone_set('Europe/Kiev');
//date_timezone_set('Europe/Kiev');


include_once 'config.php';
include_once 'func/func.php';

include_once 'func/mysql.php';
include_once 'func/mysql_class.php';
//slog('tyty2');

include_once 'func/error_func.php';

spl_autoload_register(function($class) {
    $ds = DIRECTORY_SEPARATOR;
    $a = explode('\\', $class);
    $last = array_pop($a);
    $fn =   $last . '.php';
    $fn = LIB_DIR . strtolower(str_replace('\\', $ds, $fn));
  //  s($fn);
  //  echo '<b>autoload: ' . $class . '</b> file: ' . $fn . '<br>';
    //spl_autoload($fn);
    if (file_exists($fn)) require $fn;
});
spl_autoload_register(function($class) {
    $ds = DIRECTORY_SEPARATOR;
    $a = explode('\\', $class);
    $last = array_pop($a);
    $fn =   $last . '.php';
    $fn = TRAIT_DIR . strtolower(str_replace('\\', $ds, $fn));
  //  s($fn);
   //   echo '<b>autoload: ' . $class . '</b> file: ' . $fn . '<br>';
    //spl_autoload($fn);
    if (file_exists($fn)) require $fn;
});
spl_autoload_register(function ($class) {
    $prefix  = 'Picqer\\Barcode\\';
    $baseDir = __DIR__ . '/vendor_picqer/src/'; // <-- твой путь

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}, true, true);

$data = file_get_contents('php://input');

$data = !empty($data) ? json_decode($data, true) : [];
//s('botphp=');
//s($data);
if (!empty($data)) {
    // Пример использования перед основной логикой
 //   if (!TelegramAntispamFilter::check($data)) exit;
}
if (!empty($_POST['action']) || !empty($data)){
    $objSYS  = new SystemClass();
    $data = !empty($_POST['action']) ? $_POST['action'] : $data;
    $objSYS ->init($data);

}




//echo $resultQuery;
?>