<?php
class chart
{
    public $chatadmin;
    public $who_user_write;
    public $chatid;
    public $acc;
    public $ip_club;
    public $club;
    public $admin;
    public $acc_name;
    public $acc_phone;
    public $user;
    function __construct()
    {
        $this->chatadmin = !empty($_GET['chatadmin']) ? $_GET['chatadmin'] : '';
        $this->who_user_write = !empty($_GET['who_user_write']) ? $_GET['who_user_write'] : '';
        $this->chatid = !empty($_GET['chatid']) ? trim($_GET['chatid']) : '';
        $this->acc = !empty($_GET['acc']) ? $_GET['acc'] : '0';
        $this->ip_club = !empty($_GET['ip_club']) ? $_GET['ip_club'] : '0';
        $this->club = !empty($_GET['club']) ? (int)$_GET['club'] : 0;
        $this->admin = !empty($_GET['admin']) ? (int)$_GET['admin'] : 0;
        //  s($_GET);
      /*  $sql = 'SELECT a.name FROM `spr_acc` a, spr_users u WHERE u.active_account=a.acc
                                              and u.chat_id=a.chat_id and a.acc='.$this->acc .'
and u.club=(select id from spr_clubs where ip_club="'.$this->ip_club.'") limit 1';*/
        $this->acc_name = '';
        $this->acc_phone = '';
        if ($this->chatid !== '' && $this->club > 0) {
            $sql = 'SELECT a.name, a.phone
                    FROM spr_acc a
                    JOIN spr_users u ON u.active_account = a.acc AND u.chat_id = a.chat_id
                    WHERE a.chat_id = ? AND u.club = ?
                    LIMIT 1';
            if (db()->query($sql, [$this->chatid, $this->club])) {
                $row = db()->fetch();
                $this->acc_name = $row['name'] ?? '';
                $this->acc_phone = $row['phone'] ?? '';
            }
        }
        $this->user = db()->selectOne('spr_acc', 'chat_id = :id', ['id' => $this->chatid]);
      //  $admin = db()->selectOne('spr_acc', 'chart_id = :id', ['id' => $this->chatid]);
        // получим все смс в момент загрузки и найдем последний id смс

      //  s('$lastMess');
      //  s($lastMess);
    }

    function init()
    {

        $this->view_html();

    }
    function view_html(){
        $html='<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/bootstrap-formhelpers.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js?ver=3001"></script>
    <script src="js/chart.js?ver=366"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/chart.css?ver=126" rel="stylesheet"  crossorigin="anonymous">

    <title>Telegram app</title>
</head>
<body>';

//$date = new \DateTime('next Sunday');
        $date = new \DateTime('+0 day');
        $day_next = $date->format("d.m.Y");
//$pon = $date->format("d.m.Y");

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);
        $zagl = !empty($this->admin) ? '<div class="text-center"><a class="btn btn-primary " href="web.php?action=chats_clients&club='.$this->club.'&chat_id='.$this->who_user_write.'">ВСІ ЧАТИ КЛІЄНТІВ</a></div><br>' : '';

        $html.='
      
        <div class="chat-box">
        '.$zagl.'
  <div class="chat-header">Чат з адміністратором клубу</div>
  <div class="chat-header">Клієнт: '.htmlspecialchars($this->acc_name, ENT_QUOTES, 'UTF-8').'<br>
  '.htmlspecialchars($this->acc_phone, ENT_QUOTES, 'UTF-8').' · Telegram ID: '.htmlspecialchars((string)$this->chatid, ENT_QUOTES, 'UTF-8').'</div>
<div class="chat-container" id="chat"  chatadmin="'.$this->chatadmin.'" who_user_write="'.$this->who_user_write.'"
    chatid="'.$this->chatid.'" club="'.$this->club.'">';
        $last_id = 0;


$html.='</div>


<div class="input-container" id="last_id" >
  <button type="button" class="emoji-toggle" title="Смайли">
    😄
  </button>

 
  <div class="emoji-picker" id="emoji-picker">
   <span>😀</span>
  <span>😁</span>
  <span>😂</span>
  <span>🤣</span>
  <span>😍</span>
  <span>😎</span>
  <span>🥳</span>
  <span>😢</span>
  <span>😡</span>
  <span>👍</span>
  <span>👎</span>
  <span>❤</span>
  <span>🔥</span>
  <span>🎉</span>
  <span>🙌</span>
 
  </div>
  <textarea rows="2" id="chat-input" placeholder="Напишіть повідомлення..."></textarea>
  <button type="button" class="send-button"  id="send_sms" title="Надіслати">
    <svg viewBox="0 0 24 24">
      <path d="M2 21l21-9L2 3v7l15 2-15 2z" />
    </svg>
  </button>
</div>
</div>
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
      let tg      = window.Telegram;


    if(tg != undefined){
        if (tg.WebApp != undefined && tg.WebApp.initData != undefined){



            let safe    = tg.WebApp.initData;
            let safeOb    = tg.WebApp.initDataUnsafe ;
       
            tg.WebApp.backgroundColor = "#3d3d3d";
            tg.WebApp.headerColor = "#212121";
         //  tg.WebApp.expand();
            coolButton = window.Telegram.WebApp.MainButton;
            coolButton.show();
            coolButton.text = "Закрити!";
           

            tg.WebApp.onEvent("mainButtonClicked", function(){
            
                window.Telegram.WebApp.close();

            });

        }
}


</script>
</body>
</html>';
        echo $html;
    }
}

