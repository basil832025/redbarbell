<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class ActionSpisBonus extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
      //  s('ActionSpisBonus');
        $inline_button1 = array("text"=>'Хочу списати/начислити бонуси', "web_app"=> ["url"=> "https://trystyhii.com.ua/telegram_tennessee/webapps/web.php?action=spis_bobus&type_sale=".actionmodule::$UserInfo['type_sale']."&phone=".actionmodule::$UserInfo['phone']]);
        $inline_button2 = array("text"=>'Застосувати знижку для клієнтів', "web_app"=> ["url"=> "https://trystyhii.com.ua/telegram_tennessee/webapps/web.php?action=work_sale&phone=".actionmodule::$UserInfo['phone']]);

        $inline_keyboard=array();
        $inline_keyboard[][] = $inline_button1;
        if (!empty(SystemClass::$admin)) {
            $inline_keyboard[][] = $inline_button2;
        }

        actionmodule::sendMessText('Щоб списати бонуси натисни кнопку:',$inline_keyboard);

    }

}