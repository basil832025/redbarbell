<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class actionPack extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=MyPack');

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
                $acc=actionmodule::$UserInfo['active_account'];
                if ($isNoWait) // если не прошло 3 минуты от отсутсиве интенета то пропускаем запрос на сервер ждем
                {
                    $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);

                    // $phone='380639136400';
                    // s('$type_result='.$type_result);
                    list($status,$type_result,$msg_res)= $socket->GET_PACK($acc);
                }
                // s('$type_resultPOSLE='.$type_result);
                // $status= 'NO';
                // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                if ($status=='OK')
                { $textMessage ='';
                    $cnt = count($msg_res)-1;
                    if ($cnt>1) {
                        $textMessage = 'На даний час в тебе '.$cnt .' діючий абонементи.';
                        $i=1;
                        foreach ($msg_res as $abon)
                        {
                            if (!empty($abon['NAME']))
                            {
                                $cn = $abon['TRANINGS_CNT']-$abon['CNT_VISIT'];
                                $cnt_tran = $abon['TRANINGS_CNT']>0 ? '
        Залишок тренувань: '. $cn : '';
                                $textMessage .=' 
        Абонемент '.$i.':
        Назва: '.$abon['NAME'].'
        Сума: '.number_format($abon['SUMMA'],2, '.', '').' грн.
        Дата оплати: '.$abon['DATE_SALE'].'; 
        Термін дії: з  '.$abon['DATE_START'].' 
                    до '.$abon['DATE_STOP'].
                                    $cnt_tran.'
        -------------------';
                                $i++;
                            }
                        }

                    } else
                    {
                        $abon = $msg_res[0];
                        //  slog($abon);
                        $cn = $abon['TRANINGS_CNT']-$abon['CNT_VISIT'];
                        $cnt_tran = $abon['TRANINGS_CNT']>0 ? '
        Залишок тренувань: '. $cn : '';

                        $textMessage .=' 
       Ваш діюячий абонемент:
        Назва: '.$abon['NAME'].'
        Сума: '.number_format($abon['SUMMA'],2, '.', '').' грн.
        Дата оплати: '.$abon['DATE_SALE'].'; 
        Термін дії: з  '.$abon['DATE_START'].' 
                           до '.$abon['DATE_STOP'].
                            $cnt_tran;
                    }

                }else
                    if ($msg_res=='NO_RESULT')
                        $textMessage = 'На даний час у Вас немає жодного діючого абонементу.';
                    else
                        $textMessage = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    $this->setLastOper('MyPack',$type_result);

                }
                // slog($sql);

                actionmodule::sendMessText($textMessage);
            }
        }


    }

}