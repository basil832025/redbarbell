<?php
//namespace action;

class historyshop extends ActionModule
{
    function __construct (){
        $this->textMessage =   SystemClass::getTextMessage();
    }
    function init (){

        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=HistoryShop');

        // читаем с Бд В52 инфу по номеру карты и по нескольким профилям например дети Мамы
        $StatusReadAcc = actionmodule::readAccounts();

        if ($StatusReadAcc=='OK' || $StatusReadAcc=='OFFLINE')
        {
            // если несколько аккаунтов и не выбран главный то просим клиента выбрать
            if (SystemClass::$cntAccounts>1 && SystemClass::$activeAccount==0)
            {
                actionmodule::setActiveAcc(0);
            } else
            {
                $textMessage='Виберіть варіанти платежів (покупок)';
                $inline_button1 = array("text"=>'Разові платежі',"callback_data"=>'/historytekshop_0');
                $inline_button2 = array("text"=>'Абонементи/пакети послуг',"callback_data"=>'/historypredshop_0');
                $this->inline_keyboard=array();
                $this->inline_keyboard[][] = $inline_button1;
                $this->inline_keyboard[][] = $inline_button2;
                actionmodule::sendMessText($textMessage,$this->inline_keyboard);

            }
        }

    }

}