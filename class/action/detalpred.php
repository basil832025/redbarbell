<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class detalpred extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){

        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=DetalPred');

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

                $unixTime = time();
                $isNoWait = 1;
                // slog(getdate($unixTime));
                if (actionmodule::$LastTimeOper>$unixTime &&  actionmodule::$LastOper['status']=='NO_INTERNET') $isNoWait=0;
                if ($isNoWait) // если не прошло 3 минуты от отсутсиве интенета то пропускаем запрос на сервер ждем
                {
                    $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);

                    // $phone='380639136400';
                    // s('$type_result='.$type_result);
                    list($kod,$TypeUsl) = explode('-',actionmodule::$accId);
                    //   s($this->accId);
                    // s('$kod='.$kod.' $TypeUsl='.$TypeUsl);
                    list($status,$type_result,$msg_res)= $socket->GET_HISTORY_PRED_POZAN($kod,$TypeUsl);
                }
                // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                if ($status=='OK')
                {
                    $textMessage='Ваші відвідування по абонементу:';

                    $i=1;
                    $time_start='';
                    foreach ($msg_res as $abon)
                    {
                        if (!empty($abon['DAT']))
                        {     $time_open = substr($abon['TIME_OPEN'],0,5);
                            $textMessage.= '
Візит № '.$i.' Дата: '.$abon['DAT1'];//.' '.$time_open;
                            //.' '.round($abon['CNT']).' трен.';
                            $i++;
                        }
                    }
                    if (!empty($textMessage))
                    {

                        actionmodule::sendMessText($textMessage);
                        $textMessage='' ;
                    }
                    // $textMessage='';

                }else
                    if ($msg_res=='NO_RESULT')
                        $textMessage = 'Ви ще не відвідували тренування в рамках цього абонемента.';
                    else
                        $textMessage = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    $this->setLastOper('DetalPred',$type_result);

                }
                // slog($sql);
                if (!empty($textMessage))
                    actionmodule::sendMessText($textMessage);
            }
        }


    }

}