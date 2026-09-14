<?php
class ajax_del_zapis
{
    protected $trenid;
    protected $acc;
    protected $tp;
    protected $cnt_day;
    protected $name;
    protected $time_period;
    protected $sotr_name;
    protected $ip_club;

    /** @var string|null */
    protected $acc_name;
    protected $chat_id;

    function __construct()
    {


        $this->trenid = !empty($_POST['trenid']) ? $_POST['trenid'] : '';
        $this->acc = !empty($_POST['acc']) ? (int)$_POST['acc'] : 0;
        $this->tp = !empty($_POST['tp']) ? $_POST['tp'] : '';
        $this->cnt_day = !empty($_POST['cnt_day']) ? $_POST['cnt_day'] : '';
        $this->name = !empty($_POST['name']) ? $_POST['name'] : '';
        $this->time_period = !empty($_POST['time_period']) ? $_POST['time_period'] : '';
        $this->sotr_name = !empty($_POST['sotr_name']) ? $_POST['sotr_name'] : '';
        $ipClubRaw = !empty($_POST['ip_club']) ? trim($_POST['ip_club']) : '';
        $this->ip_club = filter_var($ipClubRaw, FILTER_VALIDATE_IP) ? $ipClubRaw : '';
        $this->acc_name = '';
        $this->chat_id = '';
        if ($this->acc > 0 && $this->ip_club !== '') {
            $sql = 'SELECT a.name, a.chat_id
                    FROM spr_acc a
                    JOIN spr_users u ON u.active_account = a.acc AND u.chat_id = a.chat_id
                    JOIN spr_clubs c ON c.id = u.club
                    WHERE a.acc = ? AND c.ip_club = ?
                    LIMIT 1';
            if (db()->query($sql, [$this->acc, $this->ip_club])) {
                $aAcc = db()->fetch();
                $this->acc_name = $aAcc['name'] ?? '';
                $this->chat_id = $aAcc['chat_id'] ?? '';
            }
        }
        s('del_zapis_acc='.$this->acc);
        s('del_zapis_ip='.$this->ip_club);
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

        $params = array('traning'=>$this->trenid,'acc'=>$this->acc,'tp'=>$this->tp,  'command'=>'DEL_TRAN');
        list($status,$type_result,$msg_res)=send_data_b52($params,$this->ip_club);

        if ($status=='OK'){
            $aClubs = db()->selectOne('spr_clubs', 'ip_club = :ip_club', ['ip_club' => $this->ip_club]);
            $chat_admin = $aClubs['chat_group'];
            s('проблема з видаленням ');
            s($aClubs);
            s($this->ip_club);
            s($this->acc);
            s($this->trenid);
           // $club_id = $aClubs['id'];

            $tp_zapys = $this->tp==1 ? ' (індивідуальний) ' : ' на групове заняття  ';
            $sotr_name = !empty($this->sotr_name) ? ' до '.$this->sotr_name : '';
            $textMessage_bot='Клієнт '.$this->acc_name. ' Скасував запис '.$tp_zapys. ': '.$this->name.$sotr_name. ' на дату: '.$this->time_period;
            $arrayQuery = array(
                'chat_id' 		=> $chat_admin,
                'text'			=> $textMessage_bot,
                'parse_mode'	=> "html",

            );
            TG_sendMessage($arrayQuery);
            $arrayQuery = array(
                'chat_id' 		=> $this->chat_id,
                'text'			=> $textMessage_bot,
                'parse_mode'	=> "html",

            );
            TG_sendMessage($arrayQuery);

            $txt = '<div class="p-3 mb-2 bg-success text-white">Ви успішно відмінили Ваш запис!. Чекаємо Вас іншого разу.</div>';
        }else{
            $txt = '<div class="p-3 mb-2 bg-danger text-white">Не вдалося видалити запис! Спробуйте пізніше!</div>';

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
  <hr class="my-4">
                <div class="col-sm-6 mb-3 text-center" >
                <a class="btn btn-primary" href="'.URL.'webapps/web.php?action=my_zapis&acc='.$this->acc.'&cnt_day='.$this->cnt_day.'&ip_club='.$this->ip_club.'" role="button"><< Назад</a>
                  
                </div>
';


        return $content_html;
    }
}
