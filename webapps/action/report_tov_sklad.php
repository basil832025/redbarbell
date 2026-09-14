<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class Report_tov_sklad
{
    function __construct (){
        $this->action_report = !empty($_POST['action_report']) ? $_POST['action_report'] : '';
        $this->tov = !empty($_POST['tov']) ? $_POST['tov'] : '';


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
                   <label for="firstName" class="form-label">Введіть артикул товару</label>
                <input type="text" class="form-control"    name="form[tov]" id="tov" placeholder="" pattern="[0-9\']{4,}" value="" required>
                <div class="invalid-feedback">
                    тільки цифри 
                </div>
                    </div>
                
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="butt_tov_skald" bool="true">Сформувати звіт</button>
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

        if (!empty($this->tov) ) {
               $message_user='';
                $error='';
                $content = $this->get_html();


        }
        else {
            $error = 'error';
            $content='';
            $message_user = 'Ви не заповнили обов"язкові поля артикул!';

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

        $params = array('tov'=>$this->tov, 'command'=>'GET_REPORT2_TOV_SKLAD');
        //   s($params);
        list($status,$type_result,$msg_res)=jwt_request($params);
        if ($status=='OK') {
            //  s($msg_res);
            $Tov_name = !empty($msg_res[0]['TOV_NAME']) ? $msg_res[0]['TOV_NAME'] : '';
            $content_html = '<h4 class="text-center">Модель: '.$Tov_name.'</h4>

<div class="table-responsive-lg">
<table class="table table-hover table-sm">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" class="text-center wt-200">Склад</th>
        <th scope="col">К-ть</th>
        <th scope="col">Сума</th>
      
    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
            if (!empty($msg_res[0])) {
                $all_cnt = 0;
                $all_sum = 0;

                foreach ($msg_res as $elem) {
                    if (!empty($elem['NAME'])) {
                        $all_cnt+=$elem['CNT'];
                        $all_sum+=$elem['SUMMA'];
                             $content_html .= '<tr>
        <td scope="row" class="text-start">' . $elem['NAME'] . '</td>
         <td>' . round($elem['CNT']) . '</td>
        <td class="text-right">' . round($elem['SUMMA']) . '</td>
       
    </tr>';
                    }

                }
            }

            $content_html .= '
  <tr>
        <th scope="row" class="text-start"> Разом по виторгу:</th>
        <th>'.$all_cnt.'</th>
        <th>'.$all_sum.'</th>
      
    </tr>
    </tbody>
</table>
</div>';
        }else
            if ($type_result=='NO_RESULT')
                $content_html= '<div class="p-3 mb-2 bg-danger text-white">Товар не знайдено або немає на залишку</div>';
            else
                $content_html = '<div class="p-3 mb-2 bg-danger text-white">На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!</div>'; // если не ОК то какая то проблема и ошибка


        //   s($content_html);
        return $content_html;
    }
}