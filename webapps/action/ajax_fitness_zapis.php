<?php
class ajax_fitness_zapis
{
    public $dat;
    public $time_from;
    public $chatid;
    public $acc;
    public $ip_club;
    public $trenid;
    public $acctozapis;
    public $ost_all;
    function __construct()
    {

        $this->ip_club = !empty($_POST['ip_club']) ? $_POST['ip_club'] : '';
        $this->dat = !empty($_POST['dat']) ? $_POST['dat'] : '';
        $this->acc = !empty($_POST['acc']) ? $_POST['acc'] : '';
        $this->trenid = !empty($_POST['trenid']) ? $_POST['trenid'] : '';
        $this->acctozapis = !empty($_POST['acctozapis']) ? $_POST['acctozapis'] : '';
        $this->time_from = !empty($_POST['time_from']) ? $_POST['time_from'] : '';
        $this->ost_all = !empty($_POST['ost_all']) ? $_POST['ost_all'] : '';
        s($_POST);
    }

    function init()
    {

        $content= $this->get_html();
        $message_user='$message_user$message_user '.$this->dat;
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
        if (!empty($this->acctozapis)){ // якщо вже записаний то відмінити
            $txt = '<h5>Ви вже записані на дане заняття. Хочете відмінити?</h5>
                <button class="w-100 btn btn-primary btn-lg "  id="vidm_zan" type="button">Відмінити запис!</button>
             
            ';
        }else
            if ($this->ost_all<=0){ // если свободных нет то это резерв
                $txt = '<h5>Упс, всі місця на це групове тренування вже зайняті. Записати Вас в резерв?</h5>
                <button class="w-100 btn btn-primary btn-lg " reserv="1" id="zapis_zan" type="button">ТАК!</button>
             
            ';
            }else
            {
                      $txt = '<h5>Бажаєте записатися на заняття?</h5>
                <button class="w-100 btn btn-primary btn-lg " reserv="0" id="zapis_zan" type="button">ТАК!</button>
             
            ';

            }
//onclick="javascript:Telegram.WebApp.openLink(\''.$href.'\')"

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