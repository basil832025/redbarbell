<?php
//namespace action;

class reports extends ActionModule
{
    function __construct (){
      //  $this->textMessage =   SystemClass::getTextMessage();
    }
    function init (){

        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=reports');

        {
            $textMessage='Виберіть звіт';

            $inline_button1 = array("text"=>'Звіт по касі за період',"web_app"=> ["url"=> URL."/webappsweb.php?action=report"]);
            $inline_button2 = array("text"=>'Наявність товарів на складах',"web_app"=> ["url"=> URL."webapps/web.php?action=report_tov_sklad"]);
            //  $inline_button2 = array("text"=>'Абонементи/пакети послуг',"callback_data"=>'/historypredshop_0');
            $inline_keyboard=array();
            if (SystemClass::$admin==1)
                 $inline_keyboard[][] = $inline_button1;
                 $inline_keyboard[][] = $inline_button2;
            //  $this->inline_keyboard[][] = $inline_button2;
            actionmodule::sendMessText($textMessage,$inline_keyboard);

        }


    }

}