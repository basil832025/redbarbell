<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class actionTrenerZapis extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
        $inline_button3 = array("text"=>'за 3 дні',"callback_data"=>'/zapistrener_3');
        $inline_button7 = array("text"=>'за 7 днів',"callback_data"=>'/zapistrener_7');
        $inline_button14 = array("text"=>'за 14 днів',"callback_data"=>'/zapistrener_14');
        $this->inline_keyboard=array();
        $this->inline_keyboard[][] = $inline_button3;
        $this->inline_keyboard[][] = $inline_button7;
        $this->inline_keyboard[][] = $inline_button14;
        actionmodule::sendMessText('Виберіть за скількі днів відобразити ваші записи:',$this->inline_keyboard);

    

    }

}