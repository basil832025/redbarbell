<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class SotrZapis extends ActionModule
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
                $sotr=actionmodule::$UserInfo['sotr'];
                if ($isNoWait) // если не прошло 3 минуты от отсутсиве интенета то пропускаем запрос на сервер ждем
                {
                    $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);

                    // $phone='380639136400';
                    // s('$type_result='.$type_result);
                    list($status,$type_result,$msg_res)= $socket->GET_ZAPIS_TRENER($sotr,actionmodule::$accId);
                }
                // s('$type_resultPOSLE='.$type_result);
                // $status= 'NO';
                // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                $textMessage='';
                if ($status=='OK')
                {

                    $i=0;
                    //  slog($msg_res);
                    $dat='01.01.2000';

                    $cn_timstart=0;
                    $time_start='';
                    foreach ($msg_res as $abon)
                    {
                        if (!empty($abon['DAT']))
                        {
                            if ($abon['DAT']!=$dat)
                            {
                                if (!empty($textMessage))
                                {

                                    SystemClass::sendText($textMessage);
                                    $textMessage='' ;
                                }
                                $dat=$abon['DAT'];
                                $textMessage.= '
            ===============================
            Записи на дату: '.$dat.'
            ';
                            }
                            if ($abon['TIME_START']!=$time_start)
                            {
                                $textMessage.= '
        ---------------------';
                                $time_start=$abon['TIME_START'];
                                $textMessage.= '    на '.$time_start.':
            ';
                            }
                            $ACC_NAME = $abon['ACC_NAME'].'
           ' ;
                            $textMessage .=$ACC_NAME;
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
                        $textMessage = 'На даний час у Вас немає записів.';
                    else
                        $textMessage = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    actionmodule::setLastOper('AccZapis',$type_result);

                }
                // slog($sql);
                if (!empty($textMessage))
                    actionmodule::sendMessText($textMessage);
            }
        }



    }

}