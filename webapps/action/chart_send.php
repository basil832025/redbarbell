<?php
class chart_send
{
    protected $club;

    /** @var string|int */
    protected $chatadmin;

    /** @var string|int */
    protected $chatid;

    /** @var string|int */
    protected $who_user_write;

    /** @var string */
    protected $text;
    function __construct()
    {
        $this->club = !empty($_POST['club']) ? $_POST['club'] : '0';
        $this->chatadmin = !empty($_POST['chatadmin']) ? $_POST['chatadmin'] : '0';
        $this->chatid = !empty($_POST['chatid']) ? $_POST['chatid'] : '0';
        $this->who_user_write = !empty($_POST['who_user_write']) ? $_POST['who_user_write'] : '0';
        $this->text = !empty($_POST['text']) ? $_POST['text'] : '';
       // s($_POST);
    }

    function init()
    {

        $content= $this->ins_bd();
        $message_user='$message_user$message_user ';
        $error='';
        //  s($message_user);
        Ajax(array('content' => $content,
            'message_user' => $message_user,
            'error' => $error,
            'java_script' => '',
            'post_return' => '',
        ));

    }
    function ins_bd(){
        $content_html='OK';
        $sql ='insert into bs_chats set text="'.$this->text.'", chat_id_klient="'.$this->chatid.'", 
                club="'.$this->club.'",who_user_write="'.$this->who_user_write.'",time_send=now() ';
        //db_query($sql);
        db()->insert('bs_chats', [
            'text' => $this->text,
            'chat_id_klient' => $this->chatid,
            'club' => $this->club,
            'who_user_write' => $this->who_user_write,
            'time_send' => ['RAW' => 'NOW()']
        ]);


        return $content_html;
    }
}