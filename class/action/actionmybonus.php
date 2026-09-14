<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class actionMyBonus extends ActionModule
{
    function __construct (){
               
    }
    function init (){
        $status='NO';$msg_res='WAIT';$type_result='NO';
     //   slog('command=actionMyBonus555');
     ///   actionmodule::sendMessText('ghello');
        $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);
     //   s(actionmodule::$UserInfo);
        $phone=actionmodule::$UserInfo['phone'];
//   $phone='380639136400';
     //   slog('$phone=actionMyBonus555=='.$phone);
        list($status,$type_result,$msg_res)= $socket->GET_BONUSES($phone);
      //  slog('command=actionMyBonus667');
        s($msg_res);
        // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
        if ($status=='OK')
        {
            $textMessage = 'У вас на даний час є '. round($msg_res[0]['BONUS']). '(грн) бонусів! Ти крутий! Ходи частіше в ресторан та отримуй більше бонусів. А загальна сума чеків '. round($msg_res[0]['SUM_ALL_SALES']).' грн.';
        }else $textMessage = 'Ваші бонуси в повному порядку! Зверніться до адміністратора ресторана, щоб дізнатися кількість ваших бонусів.'; // если не ОК то какая то проблема и ошибка



        actionmodule::sendMessText($textMessage);

    }

}