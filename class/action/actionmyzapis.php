<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class ActionMyZapis extends ActionModule
{
    function __construct (){
    //        s('actionMYYY_CONSTR');
    }
    function init (){
      //  s('ActionMyZapis_TRAIT');
     //   s(URL."webapps/web.php?action=my_zapis&&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club.'&cnt_day=3');
        actionmodule::DelLastOper('MyZapis');
        $inline_button3 = array("text"=>'за 3 дні.', "web_app"=> ["url"=> URL."webapps/web.php?action=my_zapis&&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club.'&cnt_day=3']);
        $inline_button7 = array("text"=>'за 7 днів',"web_app"=> ["url"=> URL."webapps/web.php?action=my_zapis&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club.'&cnt_day=7']);
        // $inline_button14 = array("text"=>'за 14 днів',"callback_data"=>'/zapis_14');
        $inline_button14 = array("text"=>'за 14 днів',"web_app"=> ["url"=> URL."webapps/web.php?action=my_zapis&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club.'&cnt_day=14']);
        $inline_keyboard=array();
        //  $this->inline_keyboard[][] = $inline_button2;
        $inline_keyboard[][] = $inline_button3;
        $inline_keyboard[][] = $inline_button7;
        $inline_keyboard[][] = $inline_button14;
        $resultReturn =    $this->sendMessText('Оберіть за скільки днів відобразити ваші записи:',$inline_keyboard);
        // добавление лога в бд
        actionmodule::setLastOper('MyZapis','OK');
        actionmodule::setLastOperReturn($resultReturn['result']['message_id']);


    }

}