<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class NewReg extends ActionModule
{
    function __construct (){
         
    }
    function init (){

        $inline_button3 = array("text"=>'Розпочати реєстрацію', "web_app"=> ["url"=> URL."webapps/web.php?action=form_reg&phone=".SystemClass::$phone]

        );

        $inline_keyboard=array();
        $inline_keyboard[][] = $inline_button3;

        actionmodule::sendMessText('Натисніть кнопку "Розпочати реєстрацію!":',$inline_keyboard);

    }

}