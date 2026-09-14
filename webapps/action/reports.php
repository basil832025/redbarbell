<?php
class reports
{
    public $admin;
    public $ip_club;
    public $sotr;
    public $chat_id;
    public $is_phone_ukr ;
    function __construct()
    {
        $this->admin = $_GET['admin'] ?? $_POST['admin'] ?? null;
        $this->ip_club = $_GET['ip_club'] ?? $_POST['ip_club'] ?? null;
        $this->sotr = $_GET['sotr'] ?? $_POST['sotr'] ?? null;
        $this->chat_id = $_GET['chat_id'] ?? $_POST['chat_id'] ?? null;
        wlog('reports1');
        wlog($_GET);
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
    <script src="js/main.js?ver=206"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/checkout.css" rel="stylesheet"  crossorigin="anonymous">

    <title>Telegram app</title>
</head>
<body>';

//$date = new \DateTime('next Sunday');
        $date = new \DateTime('+0 day');
        $day_next = $date->format("d.m.Y");
//$pon = $date->format("d.m.Y");
 $ip_club  = $this->ip_club;

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);
        $zagl = 'на фітнес-тренування';
        wlog(URL.'webapps/web.php?action=report_pers_itog&ip_club='.$ip_club.'&admin='.$this->admin.'&sotr='.$this->sotr);
        $html.='
<div class="container mt-4">
  <h4 class="mb-3">📊 Доступні звіти </h4>';

 $html.='
  <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_pers_itog&ip_club='.$ip_club.'&admin='.$this->admin.'&sotr='.$this->sotr.'" 
      class="btn btn-outline-info w-100 text-start">
        🧑‍🤝‍🧑 Звіт по проведенню групових та персональних тренувань
      </a>
    </div>
';
       // if ($this->admin<5 )
  $html .='  
   <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_pers_detal&ip_club='.$ip_club.'&admin='.$this->admin.'&sotr='.$this->sotr.'" class="btn btn-outline-secondary w-100 text-start">
        🧑‍🏫 Звіт по груповим та персональним тренуванням по всім тренерам
      </a>
    </div>
 ';
        $html.='
  <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_info_acc&ip_club='.$ip_club.'&admin='.$this->admin.'&sotr='.$this->sotr.'" 
      class="btn btn-outline-info w-100 text-start">
        🧑‍🤝‍🧑 Звіт інфо по клієнту
      </a>
    </div>
';
        if ($this->admin<5 )
   $html .=' <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_klients_treners&ip_club='.$ip_club.'&admin='.$this->admin.'" class="btn btn-outline-danger w-100 text-start">
        👥 Звіт «Клієнти по тренерам»
      </a>
    </div>
 ';
     //   if ($this->admin==1 )
        if ($this->admin<5 )
  $html .='     <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_abon_mounth&ip_club='.$ip_club.'&admin='.$this->admin.'" class="btn btn-outline-success w-100 text-start">
        💳 Відомість продажу абонементів по місяцям
      </a>
    </div>
  ';
        if ($this->admin==1 || $this->admin==3)
  $html .='


      <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_kassa&ip_club='.$ip_club.'&admin='.$this->admin.'" class="btn btn-outline-dark w-100 text-start">
        💰 Фінансовий огляд по клубах (звіт по касі)
      </a>
    </div>';
        if ($this->admin==1)
            $html .='
    <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_bot_users&ip_club='.$ip_club.'&admin='.$this->admin.'" class="btn btn-outline-info w-100 text-start">
        🤖 Звіт про активність користувачів у боті
      </a>
    </div>';

        $report_access = '';
        if (!empty($this->chat_id) && preg_match('/^\d+$/', (string)$this->chat_id)) {
            $report_user = db()->selectOne('spr_users', 'chat_id = :chat_id', ['chat_id' => $this->chat_id]);
            if ($report_user && (int)$report_user['admin'] === (int)$this->admin) {
                $report_access = hash_hmac('sha256', (string)$this->chat_id, SECRET_KEY);
            }
        }
        if ($this->admin < 5 && $report_access !== '')
            $html .='
    <div class="col">
      <a href="'.URL.'webapps/web.php?action=report_lead_sources&ip_club='.$ip_club.'&admin='.$this->admin.'&chat_id='.$this->chat_id.'&access='.$report_access.'" class="btn btn-outline-primary w-100 text-start">
        📣 Звіт «Звідки клієнти дізналися про нас»
      </a>
    </div>';

 $html .='
</div>
</div>';


        $html.='
                </section>

        </div>
    </div></div>
    <div class="modal fade" id="staticBackdrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="dialog_header">
               Бажаєте записатися на:
            </div>
            <div class="modal-body" id="dialog_text">
                
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-default btn-myClose" data-bs-dismiss="modal">Скасувати</button>
                <a class="btn btn-danger buttonOK">Записатися</a>
            </div>
        </div>
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

