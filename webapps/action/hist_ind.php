<?php
class hist_ind
{
    public $acc;
    public $phone;
    public $ip_club;
    public $acc_name ;
    function __construct()
    {
        $this->acc = !empty($_GET['acc']) ? (int)$_GET['acc'] : 0;
        $ipClubRaw = !empty($_GET['ip_club']) ? trim($_GET['ip_club']) : '';
        $this->ip_club = filter_var($ipClubRaw, FILTER_VALIDATE_IP) ? $ipClubRaw : '';
        //  $this->cnt_day = !empty($_GET['cnt_day']) ? $_GET['cnt_day'] : '0';
        $this->acc_name = '';
        if ($this->acc > 0 && $this->ip_club !== '') {
            $sql = 'SELECT a.name
                    FROM spr_acc a
                    JOIN spr_users u ON u.active_account = a.acc AND u.chat_id = a.chat_id
                    JOIN spr_clubs c ON c.id = u.club
                    WHERE a.acc = ? AND c.ip_club = ?
                    LIMIT 1';
            if (db()->query($sql, [$this->acc, $this->ip_club])) {
                $row = db()->fetch();
                $this->acc_name = $row['name'] ?? '';
            }
        }
    }

    function init()
    {

        $this->view_html();

    }
    function header(){
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
    <script src="js/main.js?ver=192"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/checkout.css" rel="stylesheet"  crossorigin="anonymous">

    <title>Telegram app</title>
</head>
<body>';
        return $html;
    }
    function footer(){
        $html='
   <div class="modal fade" id="staticBackdrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="dialog_header">
               Бажаєте видалити запис:
            </div>
            <div class="modal-body" id="dialog_text">
                
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-default btn-myClose" data-bs-dismiss="modal">Скасувати</button>
                <a class="btn btn-danger buttonDELZapis">Видалити</a>
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
        return $html;
    }
    function body(){
        $status='OK';
        $date = new DateTime('-3 months');
        $dbeg= $date->format('d.m.Y');
        $dend='today';
        $params=array('dbeg'=>$dbeg,'dend'=>$dend,'acc'=>$this->acc,'command'=>'get_ind_history');
        s($params);
        s('ip='.$this->ip_club );
        list($status,$type_result,$msg_res)=send_data_b52($params,$this->ip_club );
        // s($status);
        //  s($type_result);
        // s($msg_res);
        if ($status=='OK')
        {

            $tp=1;
            $idZapis=0;
            $content_html='<div class="p-1 mb-2 bg-light text-center text-dark">Профіль: '
                .$this->acc_name.'</div>';

            // якщо  є персональні
            if (!empty($msg_res[0]['DAT']) )
            {
                $content_html.='
<div class="table-responsive-lg">

<div class="p-1 mb-2 bg-info text-white">Ваші персональні тренування за останні 3 місяці:</div>
<table class="table table-hover table-sm table-striped">
    <thead class="align-middle text-center">
    <tr class="table-primary">
         <th scope="col">Дата</th>
        <th scope="col" class="text-center wt-200">Заняття</th>
        <th scope="col" class="text-center wt-200">Тренер</th>
      
     </tr>
    </thead>
    <tbody  class="align-middle text-center">';
                if (!empty($msg_res[0])){
                    $idZapis=0;
                    foreach ($msg_res as $elem)
                    {
                        $idZapis++;

                        // s($elem);
                        if (!empty($elem['DAT'])){
                            //  $now = new DateTime('now');
                            //  $dat = date_for_sql_format($elem['DAT']);
                            $elem['SOTR_NAME']= !empty($elem['SOTR_NAME']) ? $elem['SOTR_NAME']: '';
                            $content_html.='<tr ">
        <td scope="row" class="text-start tov_name"> '.$elem['DAT'].'</td>
        <td scope="row" class="text-start tov_name"> '.$elem['TOV_NAME'].'</td>
        <td scope="row" class="text-start tov_name"> '.$elem['SOTR_NAME'].'</td>
       
      
    </tr>';
                        }

                    }

                }


                $content_html.='
  
    </tbody>
</table>
</div>

';
            }

        }else
            if ($type_result=='NO_RESULT'){
                $content_html='<div class="container">
    <div class="row gx-1">

        <div class="col-md-10 col-lg-10">
            <div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>
            <div class="p-3 mb-2 bg-warning text-dark">На даний час у Вас немає записів.</div>    
                    
        </div>
    </div>
</div>';
            }else
                $content_html='<div class="container">
    <div class="row gx-1">

        <div class="col-md-10 col-lg-10">
            <div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>
            <div class="p-3 mb-2 bg-danger text-white">На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!</div>
                
                    
        </div>
    </div>
</div>';


        return $content_html;
    }
    function view_html(){
        $html=$this->header();
        //   s($html);
        $html.='<section id="content">';
        $html.=$this->body();
        $html.='</section>';
        $html.=$this->footer();

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);

        echo $html;
    }
}

