<?php
//namespace action;

class history_pred_3mounth extends ActionModule
{
    function __construct (){
      //  s('actionMYYY_CONSTR');
    }
    function init (){
        $status='NO';$msg_res='WAIT';$type_result='NO';
        slog('command=HISTORY_PRED_3MOUNTH');

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
                    list($status,$type_result,$msg_res)= $socket->GET_HISTORY_PRED_3MOUNTH($acc);
                }
                // s('$type_resultPOSLE='.$type_result);
                // $status= 'NO';
                // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                if ($status=='OK')
                {

                    $i=1;
                    foreach ($msg_res as $abon)
                    {//s($abon);
                        $textMessage ='';
                        $vidp_cnt=0;$cn=0;
                        SystemClass::$buttonVidprac=0;
                        if (!empty($abon['NAME']))
                        {
                            $cn = $abon['OST_VIDPR'];
                        }
                        // перепроверить відпрацювання если есть то записать
                        if ($cn>0
                            &&  date("Y-m-d",strtotime($abon['DATE_STOP']))<date("Y-m-d")
                            && date("Y-m-d")<=date("Y-m-d", strtotime("+1 month",strtotime($abon['DATE_STOP']))))
                        {
                            $vidp_cnt =  $abon['OST_VIDPR'];
                            $dat_vidpr = date("Y-m-d", strtotime("+1 month",strtotime($abon['DATE_STOP'])));
                            $sql = 'update spr_acc set cnt_vidprac='.$vidp_cnt.', dat_to_vidpr="'.$dat_vidpr.'", 
                          dat_from_vidpr=CURRENT_DATE() where acc='.$acc;
                            db_query($sql);
                            SystemClass::$buttonVidprac=1;
                        }
                        if (!empty($abon['NAME']))
                        {
                            $cn = $abon['OST'];
                            $cnt_tran = $abon['OST']>0 ? '
        Залишок тренувань: '. round($cn) : '';
                            $DATE_STOP = $abon['DATE_STOP']!='' ? '
                            до '.$abon['DATE_STOP1'] : '';

                            $textMessage .=' 
        Послуга '.$i.':
        '.$abon['NAME'].'
        Сума: '.number_format($abon['SUMMA'],2, '.', '').' грн.
        Тренувань: '.round($abon['CNT']).'
        Дата оплати: '.$abon['DAT'].'; 
        Термін дії: з  '.$abon['DATE_START1'].
                                $DATE_STOP.

                                $cnt_tran.'';

                            $inline_button = array("text"=>'детально відвідування',"callback_data"=>'/detalpred_'.$abon['KOD'].'-'.$abon['TYPE_USL']);
                            $this->inline_keyboard=array();
                            $this->inline_keyboard[][] = $inline_button;
                            actionmodule::sendMessText($textMessage,$this->inline_keyboard);

                            $i++;
                        }
                    }



                }else
                    if ($msg_res=='NO_RESULT')
                        actionmodule::sendMessText('За остані 3 місяця у Вас не було платежів блоків/пакетів.');

                    else
                        actionmodule::sendMessText('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'); // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    $this->setLastOper('HISTORY_PRED_3MOUNTH',$type_result);

                }
                // slog($sql);


            }
        }

    }

}