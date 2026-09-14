<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++HISTORY_TEK_3MOUNTH');
class history_tek_3mounth extends ActionModule
{
    function __construct (){
     //   s('actionMYYY_CONSTR');
    }
    function init (){
        $status='NO';$msg_res='WAIT';$type_result='NO';
    //    slog('command=HISTORY_TEK_3MOUNTH');

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
                    list($status,$type_result,$msg_res)= $socket->GET_HISTORY_REALIZ_3MOUNTH($acc);
                }
                // s('$type_resultPOSLE='.$type_result);
                // $status= 'NO';
                // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
                if ($status=='OK')
                {

                    $i=1;

                    foreach ($msg_res as $abon)
                    {
                        $textMessage ='';
                        if (!empty($abon['NAME']))
                        {//s($abon);

                            $textMessage .=' 
        Послуга/товар '.$i.':
        '.$abon['NAME'].'
        Сума: '.number_format($abon['SUMMA'],2, '.', '').' грн.
        Кількість: '.round($abon['CNT']).'
        Дата оплати: '.$abon['DAT1'];
                            //
//s($textMessage);
                            actionmodule::sendMessText($textMessage);
                            $i++;
                        }
                    }
                    //  s($textMessage);



                }else
                    if ($msg_res=='NO_RESULT')
                        actionmodule::sendMessText('За остані 3 місяця у Вас не було разових платежів.');

                    else
                        actionmodule::sendMessText('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'); // если не ОК то какая то проблема и ошибка
                if ($isNoWait)
                {
                    // добавление лога в бд
                    $this->setLastOper('HISTORY_TEK_3MOUNTH',$type_result);

                }
                // slog($sql);


            }
        }
    }

}