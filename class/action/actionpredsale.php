<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class actionPredSale extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=MYPredSale');

        // читаем с Бд В52 инфу по номеру карты и по нескольким профилям например дети Мамы  
        $StatusReadAcc = ActionModule::readAccounts();

        if ($StatusReadAcc=='OK' || $StatusReadAcc=='OFFLINE')
        {
            // если несколько аккаунтов и не выбран главный то просим клиента выбрать
            if (SystemClass::$cntAccounts>1 && SystemClass::$activeAccount==0)
            {
                self::setActiveAcc(0);
            } else
            {

                $unixTime = time();
                $isNoWait = 1;
                // slog(getdate($unixTime));
                if (actionmodule::$LastTimeOper>$unixTime &&  actionmodule::$LastOper['status']=='NO_INTERNET') $isNoWait=0;
                $acc=actionmodule::$UserInfo['active_account'];
                if ($isNoWait) // если не прошло 3 минуты от отсутсиве интенета то пропускаем запрос на сервер ждем
                {
                 //   $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);

                    // $phone='380639136400';
                    // s('$type_result='.$type_result);
                    // list($status,$type_result,$msg_res)= $socket->GET_PRED_SALE($acc);
                 //   list($status,$type_result,$msg_res)= $socket->GET_PACK($acc)
                    $params=array('acc'=>$acc,'command'=>'get_pack');
                    list($status,$type_result,$msg_res)=send_data_b52($params,self::$ip_club);
                }
                // s('$type_resultPOSLE='.$type_result);
                // $status= 'NO';
                // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                if ($status=='OK')
                {
                    $textMessage='';
                    $i=1;
                    foreach ($msg_res as $abon)
                    { //s($abon);
                        if (!empty($abon['NAME']))
                        {
                            //Дата оплати: '.$abon['DATE_SALE'].'
                            $DATE_STOP = $abon['DATE_STOP']!='' ? '
                            до '.$abon['DATE_STOP1'] : '';
                            $cnt_tran = $abon['OST']>0 ? '
        Залишок тренувань: '. round($abon['OST']) : '';
                            $SOTR_NAME = $abon['SOTR_NAME']<>'' ? '
        Співробітник: '.$abon['SOTR_NAME'] : '';
                            $textMessage .='Абонемент/Послуга '.$i.':
        Назва: '.$abon['NAME'].'
        Сума: '.number_format($abon['SUMMA'],2, '.', '').' грн.
        Термін дії: з  '.$abon['DATE_START1'].
                                $DATE_STOP.
                                $cnt_tran.
                                $SOTR_NAME.'
 ---------------------
';
                            $i++;
                        }
                    }

                    //  s($textMessage);
                    // удаляем старый вывод карты, чтобы не мусорить
                    actionmodule::DelLastOper('MYPredSale');
                }else
                    if ($type_result=='NO_RESULT'){
                        actionmodule::DelLastOper('MYPredSale');
                        $textMessage = 'На даний час у Вас немає активних абонементів.';
                    }

                    else
                        $textMessage = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    actionmodule::setLastOper('MYPredSale',$type_result);

                }
                // slog($sql);

                $resultReturn =    actionmodule::sendMessText($textMessage);
              //  $res = json_decode($resultReturn,true);
                actionmodule::setLastOperReturn($resultReturn['result']['message_id']);

            }
        }
    }

}