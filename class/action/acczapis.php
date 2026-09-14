<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class AccZapis extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=AccZapis');

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

                    list($status,$type_result,$msg_res)= $socket->GET_ZAPIS($acc,actionmodule::$accId);
                }
                     // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                if ($status=='OK')
                {
                    $textMessage ='';
                    $i=1;
                    //  slog($msg_res);
                    foreach ($msg_res as $abon)
                    {
                        if (!empty($abon['DAT']))
                        {

                            $SOTR_NAME = $abon['SOTR_NAME']<>'' ? '
        Спеціаліст: '.$abon['SOTR_NAME'] : '';
                            $textMessage .=' 
        Запис '.$i.':
        Дата запису: '.$abon['DAT'].' '.$abon['TIME_START'].'
        Підрозділ: '.$abon['PODR_NAME'].
                                $SOTR_NAME.'
        ---------------------';
                            $i++;
                        }
                    }



                }else
                    if ($msg_res=='NO_RESULT')
                        $textMessage = 'На даний час у Вас немає записів.';
                    else
                        $textMessage = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    $this->setLastOper('AccZapis',$type_result);

                }
                // slog($sql);

                actionmodule::sendMessText($textMessage);
            }
        }


    }

}