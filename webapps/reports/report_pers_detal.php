<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class report_pers_detal
{
    public $admin;
    public $action_report;
    public $date_from;
    public $date_to;
    public $ip_club;
    public $sotr;
    public $podr;
    function __construct (){
        $this->action_report = !empty($_POST['action_report']) ? $_POST['action_report'] : '';
        $this->date_from = !empty($_POST['date_from']) ? $_POST['date_from'] : '';
        $this->date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : '';
        $this->ip_club = $_GET['ip_club'] ?? $_POST['ip_club'] ?? null;
        $this->podr = $_GET['podr'] ?? $_POST['podr'] ?? 'all';
        $this->admin = $_GET['admin'] ?? $_POST['admin'] ?? '0';
        $this->sotr = $_GET['sotr'] ?? $_POST['sotr'] ?? '0';
        $this->sotr = $this->sotr=='null' ? '0' : $this->sotr;
          s($_POST);
    }
    function init (){
        if ($this->action_report=='report_view'){
            $this->report_view();
        }else
            $this->report_param();
        $message_user='';
        $error='';


    }
    function report_param(){
        // клуби списко+++++++++++++++++++++++
        $params=[];
        $sql = "SELECT * FROM spr_clubs WHERE active =1  ORDER BY id DESC";
        $clubs = db()->query($sql, $params) ? db()->fetchAll() : [];
        $select_club = '<select name="club_id" id="clubId" class="form-select" required>';
        $select_club .= '<option value="">Оберіть клуб</option>';

        foreach ($clubs as $club) {
            $club_name = str_replace("REDBARBELL", "", $club['name']);
            //  $selected = ($club['ip_club'] == $this->ip_club && $this->admin>1) ? ' selected' : '';
            $select_club .= '<option value="' . htmlspecialchars($club['ip_club']) .'">' .
                htmlspecialchars($club_name) . '</option>';
        }
        $select_club .= '</select>';
        // підрозділи вибори
        $select_podr =   '<select name="podr_id" id="podr" class="form-select" required>
                        <option value="all">Всі підрозділи</option>
                        <option value="grp">Групові тренування</option>
                        <option value="pt">ПТ</option>
                        </select>
                        ';
// вибор сотрудніків
        $params=[];
        $sql = "SELECT * FROM spr_clubs WHERE active =1  ORDER BY id DESC";
        $clubs = db()->query($sql, $params) ? db()->fetchAll() : [];
        $html_sotr='<input type="hidden" value="0" id="SotrSelect">';
        $html_sotr = '        <div class="mb-3">
  <label for="SotrSelect" class="form-label">Виберіть  тренера:</label>
  <select class="form-select" id="SotrSelect" sotrSelected="" sotrNameSelected="">
  <option value="0">Тренера не вибрано</option>
   </select>
  
</div>    ';
        if ( $this->admin>1 ){
            $club_def_ip =  $this->ip_club;
            $html_js = 'select2Vibor("'.$this->ip_club.'");';
            $html_clubs = '<input type="hidden" value="'.$this->ip_club.'" id="clubId">';
            if ($this->admin==5){
                $html_js = '';
                $html_sotr = '<input type="hidden" value="'.$this->sotr.'" id="SotrSelect">';
            }

        }else{
            $html_js = 'select2Vibor("");';
            $html_clubs = ' <div class="col-md-5">
                <label for="child-type_document" class="form-label">Клуб *</label>
                '.$select_club.'
                <div class="invalid-feedback">
                    Виберіть клуб.
                </div>
            </div>';

        }



        $html='<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title></title>

   <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css?ver=5" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/report.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css  " rel="stylesheet" type="text/css" />



</head>
<body >
<div class="d-grid gap-2">
<button class="btn btn-primary" type="button"  data-bs-toggle="modal" data-bs-target="#staticBackdrop">Параметри звіту</button>
</div>
<!-- Модальное окно -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Виберіть параметри звіту :</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form class="row">
                    <div class="w-100">
                    <label for="date_from" class="col-4 col-form-label">Дата з *</label>
                    <div class="col-8">
                        <div class="input-group date datepicker" id="datepicker">
                            <input type="text" class="form-control" id="date_from"/>
                            <span class="input-group-append">
          <span class="input-group-text bg-light d-block">
            <i class="fa fa-calendar"></i>
          </span>
        </span>
                        </div>
                    </div>
                    </div>
                    <div class="w-100">
                    <label for="date_to" class="col-4 col-form-label">Дата по *</label>
                    <div class="col-8">
                        <div class="input-group date datepicker" id="datepicker2">
                            <input type="text" class="form-control" id="date_to"/>
                            <span class="input-group-append">
          <span class="input-group-text bg-light d-block">
            <i class="fa fa-calendar"></i>
          </span>
        </span>
                        </div>
                    </div>
                    </div>
                         <div class="col-md-5">
                <label for="child-type_document" class="form-label">Підрозділ *</label>
                '.$select_podr.'
                <div class="invalid-feedback">
                    Виберіть клуб.
                </div>
            </div>     
                 '.$html_clubs.$html_sotr.'
        
            
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary " admin="'.$this->admin.'" id="report_pers_detal" bool="true">Сформувати звіт</button>
                <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Закрить</button>

            </div>
        </div>
    </div>
</div>
<div id="slugeb_info" class="text-danger"></div>
<div id="content">

</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" language="JavaScript" src="js/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="js/report.js?ver='.filemtime('js/report.js').'""></script>
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js?ver=5"></script>
  
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
    let tg      = window.Telegram;

'.$html_js.'
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
</html>
';
        echo $html;
    }

    function report_view(){
        $date_from = $this->date_from;
        $date_to = $this->date_to;
        $from = \DateTime::createFromFormat('d.m.Y', trim($date_from));
        $to   = \DateTime::createFromFormat('d.m.Y', trim($date_to));

        $fromValid = $from && $from->format('d.m.Y') === trim($date_from);
        $toValid   = $to && $to->format('d.m.Y') === trim($date_to);
        //     s($date_from.' ='. $date_to);
        if (!empty($date_from) && !empty($date_to)) {
            if ($fromValid<=$toValid) {
                $message_user='';
                $error='';
                $content = $this->get_html();

            } else{
                $error = 'error';
                $content='';
                $message_user = 'Дата кінця періода повинна бути більшой дати початку!';


            }

        }
        else {
            $error = 'error';
            $content='';
            $message_user = 'Ви не заповнили обов"язкові поля Дапізон дат!';

        }
        //   s($content);
        Ajax(array('content' => $content,
            'message_user' => $message_user,
            'error' => $error,
            'java_script' => '',
            'post_return' => '',
        ));
    }
    function get_html(){
        //  s('reports1');
        $status='OK';$type_result='';
        $params = array(
            'dbeg'=>$this->date_from,
            'dend'=>$this->date_to,
            'podr'=>$this->podr,
            'sotr'=>$this->sotr,
            'command'=>'GET_REPORT2_PERS_DETAIL');
        //   s($params);
 //   s('IPPP='.$this->ip_club);
        list($status,$type_result,$data)=send_data_b52($params,$this->ip_club);
        if ($status=='OK') {

     {
         // Групуємо по тренерам
         $grouped = [];
         //slog($data);
         foreach ($data as $row) {
             $trainer = $row['SOTR_NAME'];
             $grouped[$trainer][] = $row;
         }

         $content_html = '
<div class="container my-4">
  <h5 class="text-center">Посещения персоналок</h5>
  <p class="text-center">з '.$this->date_from.' по '.$this->date_to.'</p>
';

         foreach ($grouped as $trainer => $visits) {
             $visit_total = 0;
             $sum_total = 0;
             $fact_total = 0;

             $content_html .= '
    <div class="card mb-3 border-primary">
      <div class="card-header bg-primary text-white">
        ' . htmlspecialchars($trainer) . '
      </div>
      <div class="card-body p-2">';

             foreach ($visits as $v) {
                 $visit_total += $v['CNT'];
                 $sum_total += $v['SUMMA'];
                 $fact_total += $v['SUM_PREDOPL'];

                 $content_html .= '
        <div class="card mb-2 shadow-sm">
          <div class="card-body">
            <h6 class="card-title fw-bold">' . htmlspecialchars($v['KLIENT_NAME']) . '</h6>
            <p class="mb-1">📅 <strong>Дата:</strong> ' . date('d.m.Y', strtotime($v['DAT'])) . '</p>
            <p class="mb-1">📍 <strong>Підрозділ:</strong> ' . htmlspecialchars($v['PODR_NAME']) . '</p>
            <p class="mb-1">🏋️ <strong>Тренування:</strong> ' . htmlspecialchars($v['TOV_NAME']) . '</p>
            <hr class="my-2">
            <div class="d-flex flex-wrap justify-content-between small">
              <span><strong>Кількість:</strong> ' . intval($v['CNT']) . '</span>';
                 if ($this->admin!=5) {
                     $content_html .= '  <span><strong>Ціна:</strong> ' . number_format($v['PRICE'], 0, ',', ' ') . '</span>
              <span><strong>Сума:</strong> ' . number_format($v['SUMMA'], 0, ',', ' ') . '</span>
              <span><strong>Факт:</strong> ' . number_format($v['SUM_PREDOPL'], 0, ',', ' ') . '</span>';
                 }
                     $content_html .= '
            </div>
            <p class="small mt-2 text-muted">📌 <strong>Телефон:</strong> ' . htmlspecialchars($v['PHONE']) . '</p>
          </div>
        </div>';
             }

             $content_html .= '
      <div class="bg-light p-2 border rounded mt-2">
        <strong>Підсумок:</strong> ' . intval($visit_total) . ' відвідувань • ';
             if ($this->admin!=5) {
                 $content_html .=
                     number_format($sum_total, 0, ',', ' ') . ' грн • факт: ' .
                     number_format($fact_total, 0, ',', ' ') . ' грн';
             }
             $content_html .= '
      </div>
    </div>
  </div>';
         }

         $content_html .= '</div>';
            }
        }else if ($type_result=='NO_RESULT')
            $content_html = ' По вибраним параметрам даних немає';
        else
            $content_html = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка



        //   s($content_html);
        return $content_html;
    }
}