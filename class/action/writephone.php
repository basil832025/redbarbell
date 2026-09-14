<?php
//namespace action;

class writePhone extends ActionModule
{
    function __construct (){
        $this->textMessage =   SystemClass::getTextMessage();
    }
    function init (){

        slog('writePhone');
        $newPhone = trim(str_replace('/myphone', '', $this->textMessage));
        $sql= 'update spr_users set phone="'.$newPhone.'", active_account=0,sotr="" where chat_id='.SystemClass::getChatId();
        db_query($sql);
        $sql = 'delete from spr_acc where  chat_id="'.SystemClass::getChatId().'" ';
        db_query($sql);
        $this->sendMessText('Номер телефону успішно перезаписаний!');
        slog('$newPhone='.$newPhone);
        $this->setLastOper('MyPhone',$newPhone);

    }

}