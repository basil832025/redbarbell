<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class zapis_grp_tren extends ActionModule
{
    function __construct (){

    }
    function init (){
     //   s('zapis_grp_tren');
        actionmodule::DelLastOper('zapis_grp_tren');
        $inline_button1 = array("text"=>'Запис на фітнес тренування', "web_app"=> ["url"=> URL."webapps/web.php?action=zapis_to_fitness&tp=fitness&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club]);


        $inline_keyboard=array();
        $inline_keyboard[][] = $inline_button1;
      //  $inline_keyboard[][] = $inline_button2;

        $resultReturn =  actionmodule::sendMessText('Оберіть тип запису:',$inline_keyboard);
        // добавление лога в бд
        actionmodule::setLastOper('zapis_grp_tren','OK');
        actionmodule::setLastOperReturn($resultReturn['result']['message_id']);

    }

}