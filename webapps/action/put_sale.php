<?php
class put_sale
{
    protected $sale_prc;

    /** @var string|int */
    protected $acc;

    /** @var string|int */
    protected $doc;

    /** @var string */
    protected $acc_name;

    /** @var string|int */
    protected $chat_id;
    function __construct()
    {

        $this->sale_prc = !empty($_POST['sale_prc']) ? $_POST['sale_prc'] : '0';
        $this->acc = !empty($_POST['acc']) ? (int)$_POST['acc'] : 0;
        $this->doc = !empty($_POST['doc']) ? $_POST['doc'] : '';

        $this->acc_name = '';
        $this->chat_id = 0;
        if ($this->acc > 0 && db()->query('SELECT name, chat_id FROM spr_acc WHERE acc = ? LIMIT 1', [$this->acc])) {
            $aAcc = db()->fetch();
            $this->acc_name = $aAcc['name'] ?? '';
            $this->chat_id = !empty($aAcc['chat_id']) ? (int)$aAcc['chat_id'] : 0;
        }
    }

    function init()
    {

        $content= $this->get_html();
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
    function get_html(){
        $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);
        //s(HOST_SOCKET);
        $status='';
        $param=['doc'=>$this->doc,'acc'=>$this->acc,'sale_prc'=>$this->sale_prc];
        list($status,$type_result,$msg_res)= $socket->SET_BONUS($param);
        if ($status=='OK'){

            $textMessage_bot='Ви успішно успішно застосували знижку '.$this->sale_prc. ' грн.';

            $arrayQuery = array(
                'chat_id' 		=> $this->chat_id,
                'text'			=> $textMessage_bot,
                'parse_mode'	=> "html",

            );
            TG_sendMessage($arrayQuery);

                $txt = '<div class="p-3 mb-2 bg-success text-white">Ви успішно застосували знижку!</div>';

        }else{
            $txt = '<div class="p-3 mb-2 bg-danger text-white">Не вдалося застосували знижку! Спробуйте пізніше!</div>';

        }
        //   s($msg_res);
        $content_html='
<div class="container">
    <div class="row gx-1">

        <div class="col-md-10 col-lg-10">
            <div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>

                
                    '.$txt.'
                    
            
        </div>
    </div>
</div>
 
';


        return $content_html;
    }
}
