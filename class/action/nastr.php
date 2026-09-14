<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class nastr extends ActionModule
{
    function __construct (){

    }
    function init (){
        slog('nastr');
        // удаляем предыдущее сообщения
        actionmodule::DelLastOper('nastr');
        $inline_button1 = array("text"=>'Співробітники', "web_app"=> ["url"=> URL."webapps/web.php?action=spr_sotr"]);
        $inline_button2 = array("text"=>'Клуби', "web_app"=> ["url"=> URL."webapps/web.php?action=spr_clubs"]);
        $inline_button3 = array("text"=>'Вакансії', "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy_resumes"]);
      //  $inline_button2 = array("text"=>'Залишити відгук', "web_app"=> ["url"=> URL."webapps/web.php?action=vidguk&club=".SystemClass::$club."&acc=".ActionModule::$UserInfo['active_account'].'&ip_club='.ActionModule::$ip_club]);


        $inline_keyboard=array();
        $inline_keyboard[][] = $inline_button1;
        $inline_keyboard[][] = $inline_button2;
        $inline_keyboard[][] = $inline_button3;

        $resultReturn= actionmodule::sendMessText('Оберіть меню налаштувань:',$inline_keyboard);
        // добавление лога в бд
        actionmodule::setLastOper('nastr','OK');
        // добавляем mess_id по последнему отправленному пользователем сообщению, чтобы потом удалить его вместе с командой
        actionmodule::setLastOperReturn($resultReturn['result']['message_id']);

    }

}
