<?php
 //$path_to_php=system("which php");
 //echo '$path_to_php='.$path_to_php;
//phpinfo();exit;

/*error_reporting(E_ALL); /usr/local/bin/php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
  // путь к стартовой директории вычисляется автоматом  
//  define('ROOTC', $_SERVER['DOCUMENT_ROOT'] .(substr($_SERVER['DOCUMENT_ROOT'],-1)=='/' ? '' : '/') );

   
 // define('ROOT_AC', ROOTC .'telegram/');
 
//chdir($path_parts['dirname']); // задаем директорию выполнение скрипта
//echo ROOT_AC.'config.php';
include_once __DIR__.'/config.php';
include_once __DIR__.'/func/func.php';

//slog('tyty1');
include_once __DIR__.'/func/mysql.php';
include_once __DIR__.'/func/mysql_class.php';
//slog('tyty2');
//echo 'ROOT='.ROOT;
//echo '$_SERVER[SCRIPT_FILENAME]='.$_SERVER['SCRIPT_FILENAME'];
include_once __DIR__.'/func/error_func.php';
spl_autoload_register(function($class) {
    $ds = DIRECTORY_SEPARATOR;
    $a = explode('\\', $class);
    $last = array_pop($a);
    $fn =   $last . '.php';
    $fn = LIB_DIR . strtolower(str_replace('\\', $ds, $fn));
   // s($fn);
    //  echo '<b>autoload: ' . $class . '</b> file: ' . $fn . '<br>';
    //spl_autoload($fn);
    if (file_exists($fn)) require $fn;
});
/*include_once 'class/class.socketKernel.php';
include_once 'class/class.socketB52.php';
include_once 'class/system.class.php';
include_once 'class/cron_action.php';*/
//slog('tytcron2');


$act = $argv[1];
list($f,$action)=explode('=',$act);
$action = !empty($action) ? $action : $_GET['action'];
//sCron('cron.php action='.$action);
$objSYS  = new SystemClass();
$objSYS ->init_cron($action);

/*
//за день до послуги

Чекаємо Вас [dat].Запис на [time_start] [podr_name].У разі відміни повідомте нас 0983888809. Гарного вечора.

select phone,date_start,r.dat,time_start,name,(select name from spr_podr where kod=r.podr) as podr_name 
from CB_RESERVATION r,cb_accounts c where r.ACCOUNT=c.KOD and r.dat="tomorrow"
--and  (date_start >= CURRENT_TIMESTAMP   and (date_start <= CURRENT_TIMESTAMP +(24 *3600 * 0.00001157409)))
--and exists(select * from spr_tov t,CB_RESERV_SERV rc where rc.RESERV_DOC=r.kod and t.kod=rc.serv )
 and r.podr <>'06'
and phone is not null and r.REJECT_TYPE is null
and r.place<>'1113'

and r.send is null

*/
/*
про закинчення абонементу за 1 день

[date_stop] дата поповнення Вашого абонементу [grp_name]. До сплати [price]грн. Гарного вечора.

select a.phone,u.date_stop,
(select  price from cb_pack_prices pp where pp.package=p.kod and pp.month_cnt=u.month_cnt ) as price,
g.name as grp_name,a.name as Klient,LAST_NAME,FIRST_NAME,
COALESCE(LAST_NAME,FIRST_NAME) as name from CB_USED_PACK u, CB_ACCOUNTS a,cb_packages p, cb_pack_groups g
where a.kod=u.ACCOUNT and u.DATE_STOP= cast('today' as date)+1 and u.send is null
and phone is not null  and p.kod=u.package and g.kod=p.grp
and ( p.grp='09' or p.grp='0905' or p.grp='06' or p.grp='0906' or p.grp='0908')
order by a.name
*/
/*
по группам басейну предварительние проодажи за день до окончания

[date_stop] дата поповнення Вашого абонементу. До сплати [price]грн. Гарного вечора.

select a.phone as phone,
t1.ALT_PRICE as price,
date_stop as date_stop,a.name
from cb_serv_sales c, CB_ACCOUNTS a,spr_tov t1 where t1.KOD=c.tov  and
a.kod=c.ACCOUNT  
--and c.account=5363 
and tov  in (select t.kod from spr_tov t where t.grp in (select grp from PRC_GET_CHILD_GRP(309)) ) 
and price_out>0
and date_stop='tomorrow'
and a.phone is not null and strlen(a.phone)=10
*/

//sCron($action);
?>