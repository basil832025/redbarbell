<?php

//phpinfo();exit;
date_default_timezone_set('Europe/Kiev');
//date_timezone_set('Europe/Kiev');
//phpinfo();
//ECHO date("m.d.y H:i");;
/*error_reporting(E_ALL);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
include_once '../config.php';

include_once ROOT.'func/func.php';
//echo ROOT;
//slog('tyty1');
include_once ROOT.'func/mysql.php';
include_once ROOT.'func/mysql_class.php';
//slog('tyty2');

include_once ROOT.'func/error_func.php';

include_once ROOT.'webapps/web_action.php';




//include_once ROOT.'class/socketkernel.php';
//include_once ROOT.'class/socketb52.php';



spl_autoload_register(function($class) {
    $ds = DIRECTORY_SEPARATOR;
    $a = explode('\\', $class);
    $last = array_pop($a);
    $fn_ =   $last . '.php';
    $fn = WEBAPPS_ACTION . strtolower(str_replace('\\', $ds, $fn_));
    $fnR = WEBAPPS_REPORTS . strtolower(str_replace('\\', $ds, $fn_));
    // s($fn);
    //  echo '<b>autoload: ' . $class . '</b> file: ' . $fn . '<br>';
    //spl_autoload($fn);s('$fn='.$fn);
    if (file_exists($fn)) {   require $fn; }
    if (file_exists($fnR)) {   require $fnR; }
});

$sql = 'select * from spr_acc where acc=35694 limit 1';
//$infoAcc = db_row($sql);
//p($infoAcc); exit;

$objSYS  = new WebActionModule();
$objSYS ->init();


//echo $resultQuery;
?>