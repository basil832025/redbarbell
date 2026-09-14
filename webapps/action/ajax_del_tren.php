<?php
class ajax_del_tren
{
    protected $trenid;

    /** @var string|int */
    protected $acc;

    /** @var string */
    protected $ip_club;
    function __construct()
    {

        $this->trenid = !empty($_POST['trenid']) ? $_POST['trenid'] : '';
        $this->acc = !empty($_POST['acc']) ? $_POST['acc'] : '';
        $this->ip_club = !empty($_POST['ip_club']) ? $_POST['ip_club'] : '';
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
       
        $params = array('traning'=>$this->trenid,'acc'=>$this->acc,  'command'=>'DEL_TRAN');
        list($status,$type_result,$msg_res)=send_data_b52($params,$this->ip_club);


        if ($status=='OK'){
            $txt = 'Ви успішно відмінили Ваш запис!. Чекаємо Вас іншого разу.';
        }else{
            $txt = 'Не вдалося видалити запис! Спробуйте пізніше!';

        }
        s($msg_res);
        $content_html='
<div class="container">
    <div class="row gx-1">

        <div class="col-md-10 col-lg-10">
            <div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>

                <section id="content">
                    '.$txt.'
                </section>
        </div>
    </div>
</div>
  <hr class="my-4">
                <div class="col-sm-6 mb-3" >
                    <button class="w-100 btn btn-primary btn-lg " id="back_fitness" type="button"><< Назад</button>
                </div>
';


        return $content_html;
    }
}