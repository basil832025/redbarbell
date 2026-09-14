<?php
class ajax_zapis_tren
{
    public $ip_club;
    public $dat;
    public $acc;
    public $trenid;
    public $reserv;
    function __construct()
    {

        $this->dat = !empty($_POST['dat']) ? $_POST['dat'] : '';
        $this->acc = !empty($_POST['acc']) ? $_POST['acc'] : '';
        $this->trenid = !empty($_POST['trenid']) ? $_POST['trenid'] : '';
        $this->reserv = !empty($_POST['reserv']) ? $_POST['reserv'] : '';
        $this->ip_club = !empty($_POST['ip_club']) ? $_POST['ip_club'] : '';


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
        $params = array('traning'=>$this->trenid,'acc'=>$this->acc,'reserv'=>$this->reserv,  'command'=>'INS_TRANING_ACC');
        list($status,$type_result,$msg_res)=send_data_b52($params,$this->ip_club);


        if ($status=='OK'){

            if ($this->reserv)
                $txt = 'Ви успішно зробили запис в Резерв!. RedBarbellGym_bot обов’язково сповістить тебе, якщо звільниться місце.';
            else
                $txt = 'Ви успішно зробили запис!. *** скасувати запис із збереженням візиту можна до 09:30 у день тренування.';
        // отправить админу сообщение
            $aClubs = db()->selectOne('spr_clubs', 'ip_club = :ip_club', ['ip_club' => $this->ip_club]);
            $chat_admin = $aClubs['chat_group'];
            $club_id = $aClubs['id'];

            $aAcc = db()->selectOne('spr_users', 'active_account = :acc and club = :club', ['acc' => $this->acc, 'club' => $club_id]);
            $chat_id = $aAcc['chat_id'];


            $textMessage ='Клієнт якийся записався, але чомусь не повернулось який саме';
            $textMessageClient ='Ви записані на тренування';
            if (!empty($msg_res[0])) {
                $data = $msg_res[0];
                $textMessage =  'Клієнт '.$data['NAME'].'('.$data['PHONE'].') записався на тренування: '.$data['TOV_NAME'].' на дату '.$data['DAT']. ' '.$data['TIME_FROM'] .'-'.$data['TIME_TO'] ;
                $textMessageClient =  'Ви  записалися на тренування: '.$data['TOV_NAME'].
                        ' на дату '.$data['DAT']. ' '.$data['TIME_FROM'] .'-'.$data['TIME_TO'] ;
            }
            $arrayQuery = array(
                'chat_id' 		=> $chat_admin,
                'text'			=> $textMessage,
                'parse_mode'	=> "html",

            );
            TG_sendMessage($arrayQuery);
            $arrayQuery = array(
                'chat_id' 		=> $chat_id,
                'text'			=> $textMessageClient,
                'parse_mode'	=> "html",

            );
            //   s($arrayQuery);

            TG_sendMessage($arrayQuery);


        }else{
            $txt = 'Не вдалося зробити запис! Спробуйте пізніше!';

        }

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