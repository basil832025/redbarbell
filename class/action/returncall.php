<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class returnCall extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
        // добавление лога в бд
        slog('command=returnCall');
        if (!empty(actionmodule::$UserInfo['active_account']))
        {
            $sql = 'select card,name,phone from spr_acc where acc="'.actionmodule::$UserInfo['active_account'].'" limit 1';
            $acc_info = db_row($sql);
            slog($sql);
            $phone = $acc_info['phone'];
            $textMessage = 'Клієнт '.$acc_info['name'].' ('.$acc_info['card'].') телефон: '.$acc_info['phone'].' запросив зворотній дзвінок!';
        } else
        {
            $textMessage = 'Клієнт '.actionmodule::$UserInfo['name'].'  телефон: '.actionmodule::$UserInfo['phone'].' запросив зворотній дзвінок!';
            $phone = actionmodule::$UserInfo['phone'];
        }
        slog(actionmodule::$UserInfo);
        $this->setLastOper('returnCall',$phone);
        SystemClass::$textMessage_bot = $textMessage;
        $arrayQuery = array(
            'chat_id' 		=> IDCHAT_INTERFIT,
            'text'			=> SystemClass::$textMessage_bot,
            'parse_mode'	=> "html",

        );
     //   s($arrayQuery);
        SystemClass::TG_sendMessage($arrayQuery);
        actionmodule::sendMessText('Вам найближчим часом зателефонує адміністратор!');



    }

}