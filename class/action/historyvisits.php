<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class historyvisits extends ActionModule
{
    function __construct (){

    }
    function init (){
     //   slog('historyvisits');
        actionmodule::DelLastOper('historyvisits');
        $inline_button1 = array("text"=>'Персональні тренування', "web_app"=> ["url"=> URL."webapps/web.php?action=hist_ind&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club]);
        $inline_button2 = array("text"=>'Групові заняття', "web_app"=> ["url"=> URL."webapps/web.php?action=hist_grp&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club]);
        $inline_button3 = array("text"=>'Залишки послуг', "web_app"=> ["url"=> URL."webapps/web.php?action=hist_ost_poslug&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club]);


        $inline_keyboard=array();
        $inline_keyboard[][] = $inline_button1;
        $inline_keyboard[][] = $inline_button2;
        $inline_keyboard[][] = $inline_button3;
        // добавление лога в бд
        actionmodule::setLastOper('historyvisits','OK');
        $resultReturn =  actionmodule::sendMessText('Вибиріть варіант історії візитів:":',$inline_keyboard);
        actionmodule::setLastOperReturn($resultReturn['result']['message_id']);

    }

}