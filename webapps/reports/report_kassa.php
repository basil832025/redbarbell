<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class report_kassa
{
    public $admin;
    public $action_report;
    public $date_from;
    public $date_to;
    public $ip_club;
    public $sotr;
    public $podr;
    public $kasa;
    function __construct (){
        $this->action_report = !empty($_POST['action_report']) ? $_POST['action_report'] : '';
        $this->date_from = !empty($_POST['date_from']) ? $_POST['date_from'] : '';
        $this->date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : '';
        $this->ip_club = $_GET['ip_club'] ?? $_POST['ip_club'] ?? null;
        $this->admin = $_GET['admin'] ?? $_POST['admin'] ?? '0';
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

        $params=[];
        $sql = "SELECT * FROM spr_clubs WHERE active =1  ORDER BY id DESC";
        $clubs = db()->query($sql, $params) ? db()->fetchAll() : [];
        $select_club = '<select name="club_id" id="clubId" class="form-select" required>';
        $select_club .= '<option value="">Оберіть клуб</option>';

        foreach ($clubs as $club) {
            $club_name = str_replace("REDBARBELL", "", $club['name']);
          //  $selected = ($club['id'] == $acc['club_id']) ? ' selected' : '';
            $select_club .= '<option value="' . htmlspecialchars($club['ip_club']) . '">' .
                htmlspecialchars($club_name) . '</option>';
        }
        $select_club .= '</select>';
        if ( $this->admin>1 ){
            $club_def_ip =  $this->ip_club;
            $html_clubs = '<input type="hidden" value="'.$this->ip_club.'" id="clubId">';


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
        $select_club .= '</select>';
        $html='<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title></title>


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
                <h5 class="modal-title" id="staticBackdropLabel">Виберіть параметри звіту:</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form class="row">
                    <div class="w-100">
                    <label for="date_from" class="col-4 col-form-label">Дата з</label>
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
                    <label for="date_to" class="col-4 col-form-label">Дата по</label>
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
                  
                '.$html_clubs.'
             
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary buttonOK" bool="true">Сформувати звіт</button>
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
<script src="js/report.js?ver=1124"></script>
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
        s('reports1');

        $params = array('dbeg'=>$this->date_from,'dend'=>$this->date_to, 'kasa'=>$this->kasa, 'command'=>'GET_REPORT1_KASSA');
        s($params);

        list($status,$type_result,$msg_res)=send_data_b52($params,$this->ip_club);


       //  s($msg_res);
        $content_html='<h2 class="text-center">Hадходження</h2>
<h4 class="text-center">Виторг</h4>
<div class="table-responsive-lg">
<table class="table table-hover table-sm">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" class="text-center wt-200">Послуги/товари</th>
        <th scope="col">К-ть</th>
        <th scope="col">Сума<br>виторгу</th>
        <th scope="col">Сума<br>готівки</th>
        <th scope="col">Сума<br>VISA</th>
        <th scope="col">Сума<br>депоз.</th>
        <th scope="col">Сума<br>знижки</th>

    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
        if (!empty($msg_res[0])){
            $all_sum_out=0; $all_sm_nal=0; $all_sm_visa=0;$all_sm_kredit=0;$all_sm_sale=0;$all_cnt=0;
            foreach ($msg_res as $elem)
            {
              //  s($elem);
                if (!empty($elem['TYPE_VUTORG']) && $elem['TYPE_VUTORG']!=1) continue;
                if ($elem['SUM_NAL']>0 || $elem['SUM_VISA'] || $elem['SUM_KREDIT']){
                    $cnt = $elem['TOV']==1 ? 0 :round($elem['CNT']);
                    $sum_out = $elem['TOV']==1 ? 0 :round($elem['SUM_OUT']);
                    $all_sum_out=$all_sum_out+$sum_out;
                    $all_cnt=$all_cnt+$cnt;
                    $all_sm_nal=$all_sm_nal+$elem['SUM_NAL'];
                    $all_sm_visa=$all_sm_visa+$elem['SUM_VISA'];
                    $all_sm_kredit=$all_sm_kredit+$elem['SUM_KREDIT'];
                    $all_sm_sale=$all_sm_sale+$elem['SUM_SALE'];
                    $tov_name = $elem['MONTH_CNT']>0 ? $elem['TOV_NAME']. ' '.round($elem['MONTH_CNT']).' міс.' : $elem['TOV_NAME'];
                    $content_html.='<tr>
        <td scope="row" class="text-start">'.$tov_name.'</td>
        <td>'.$cnt.'</td>
        <td>'.$sum_out.'</td>
        <td>'.round($elem['SUM_NAL']).'</td>
        <td>'.round($elem['SUM_VISA']).'</td>
        <td>'.round($elem['SUM_KREDIT']).'</td>
        <td>'.round($elem['SUM_SALE']).'</td>
    </tr>';
                }

            }
        }

        $content_html.='
    <tr>
        <th scope="row" class="text-start"> Разом по виторгу:</th>
        <th>'.$all_cnt.'</th>
        <th>'.$all_sum_out.'</th>
        <th>'.$all_sm_nal.'</th>
        <th>'.$all_sm_visa.'</th>
        <th>'.$all_sm_kredit.'</th>
        <th>'.$all_sm_sale.'</th>
    </tr>
    </tbody>
</table>
</div>';

        $content_html.='<h4 class="text-center">Доходи по касі</h4>
<div class="table-responsive-lg">
    <table class="table table-hover table-sm">
        <thead class="align-middle text-center">
        <tr class="table-primary">
        <th scope="col" class="text-center wt-200">Стаття</th>
            <th scope="col">Сума<br> готівка</th>
            <th scope="col">Сума<br> кредитка</th>
            <th scope="col" class="text-center w-20">Дата</th>
            <th scope="col" class="text-center wt-200">Примітка</th>
            <th scope="col">Співробітник</th>

      </tr>
        </thead>
        <tbody  class="align-middle text-center">';
        if (!empty($msg_res[0])) {
            $all_sum_out = 0;
            $all_sm_nal = 0;
            $all_sm_visa = 0;
            $all_sm_kredit = 0;
            $all_sm_sale = 0;
            $all_cnt = 0;
            foreach ($msg_res as $elem) {
                if ($elem['TYPE_VUTORG'] == 2 || $elem['TYPE_VUTORG'] == 4) {


                    $all_sm_nal = $all_sm_nal + $elem['SUM_NAL'];
                    $all_sm_visa = $all_sm_visa + $elem['SUM_VISA'];

                    $content_html .= '<tr>
<td scope="row" class="text-start">' . $elem['TOV_NAME'] . '</td>
<td>' . round($elem['SUM_NAL']) . '</td>
            <td>' . round($elem['SUM_VISA']) . '</td>
            <td scope="row">' . $elem['DAT1'] . '</td>
            <td>' . $elem['PRIM'] . '</td>
            <td>' . $elem['SOTR_NAME'] . '</td>

        </tr>';
                }
            }
        }
        $content_html.='
<tr class="table-primary">
            <th scope="col" class="text-center wt-200">Разом по  доходу (готівка):</th>
            <th scope="col">' . $all_sm_nal . '</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>

        </tr>
</tbody>
    </table>
</div>
<h4 class="text-center">Розходи по касі</h4>
<div class="table-responsive-lg">
    <table class="table table-hover table-sm ">
        <thead class="align-middle text-center">
        <tr class="table-primary">
            <th scope="col" class="text-center wt-200">Стаття</th>
            <th scope="col">Сума</th>
           <th scope="col" class="text-center wt-50">Дата</th>
            <th scope="col" class="text-center wt-200">Примітка</th>
            <th scope="col">Співробітник</th>

      </tr>
        </thead>
        <tbody  class="align-middle text-center">';
        if (!empty($msg_res[0])) {
            $all_sum_out = 0;
            $all_sm_nal = 0;
            $all_sm_visa = 0;
            $all_sm_kredit = 0;
            $all_sm_sale = 0;
            $all_cnt = 0;
            foreach ($msg_res as $elem) {
                if ($elem['TYPE_VUTORG'] == 3){
                    $all_sm_nal = $all_sm_nal + $elem['SUM_NAL'];
                    $all_sm_visa=$all_sm_visa+$elem['SUM_VISA'];

                    $content_html .= '<tr>
          
            <td  class="text-start">' . $elem['TOV_NAME'] . '</td>
         <td>' . round($elem['SUM_NAL']) . '</td>      
    
             <td >' . $elem['DAT1'] . '</td>
            <td>' . $elem['PRIM'] . '</td>
            <td>' . $elem['SOTR_NAME'] . '</td>

        </tr>';
                }
            }
        }

        $content_html .=   '
        <tr class="table-primary">
            <th scope="col" class="text-center wt-200">Разом по  розходу:</th>
            <th scope="col">'.$all_sm_nal.'</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>

        </tr>
        </tbody>
    </table>
</div>';
     //   s($content_html);
        return $content_html;
    }
}