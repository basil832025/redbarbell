<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class AccCardCode extends ActionModule
{
    function __construct (){
        //        s('actionMYYY_CONSTR');
    }
    function init (){
        // присваеваем активный профиль для пользователя
        $sql = 'update spr_users set active_account='.actionmodule::$accId. '  where club='.SystemClass::$club.' and id='.actionmodule::$UserInfo['id'];
        db_query($sql);
      //  s($sql);
        self::$UserInfo['active_account'] = actionmodule::$accId;
        SystemClass::$activeAccount = actionmodule::$UserInfo['active_account'];
        $sql = 'select a.name,a.card from spr_acc a,spr_users u where u.club='.SystemClass::$club.' and u.chat_id=a.chat_id and a.acc='.self::$accId.'   limit 1';
        $infoAcc = db_row($sql);
        //s($sql);
        actionmodule::sendMessText('Вибраний профіль '.$infoAcc['name'].' ('.$infoAcc['card'].') буде тепер за замовчуванням!');



    }

}