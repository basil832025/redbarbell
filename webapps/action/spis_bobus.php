<?php

class spis_bobus
{
    function __construct()
    {
        $this->phone = !empty($_GET['phone']) ? $_GET['phone'] : '0';
        $this->type_sale = !empty($_GET['type_sale']) ? $_GET['type_sale'] : '0';
        $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);
        //   s(actionmodule::$UserInfo);

//   $phone='380639136400';
        //   slog('$phone=actionMyBonus555=='.$phone);
        list($status,$type_result,$msg_res)= $socket->GET_BONUSES($this->phone);
        //  slog('command=actionMyBonus667');
      //  s($msg_res);
        $this->bonus=-1;
        // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
        if ($status=='OK') {
            $this->bonus=round($msg_res[0]['BONUS']);
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
        $tp=1;
        $idZapis=0;
        $content_html='<div class="p-1 mb-2 bg-light text-center text-dark">У Вас бонусів: '
            .$this->bonus.' грн.</div>';

        $content_html.='
<div class="table-responsive-lg">
<h6 class="text-center">Для списання ваших бонусів введіть 4 останні цифри вашого чеку <br>(запитайте в офіціанта):</h6>
 <form id="formchek" class="needs-validation" novalidate action="?" method="post" enctype="multipart/form-data">
       
   <div class="col-sm-4">
                <label for="firstName" class="form-label">4 останні цифри чеку *</label>
                <input type="text" class="form-control" admin="0" phone="'.$this->phone.'"  bonus="'.$this->bonus.'" type_sale="'.$this->type_sale.'" name="form[num]" id="num" placeholder="" pattern="[0-9\']{4,}" value="" required>
                <div class="invalid-feedback">
                    тільки цифри (останні 4 цифри)
                </div>
            </div><br>
            <div>
<button class="w-100 btn btn-primary btn-lg "  tp="1" id="find_chek" type="submit">Знайти чек</button>
</div>
</form>
<br>

</div>

';





        return $content_html;
    }
    function view_html(){
        $html=$this->header();
        $html.='<section id="content" class="container">';
        $html.=$this->body();
        $html.='</section>';
        $html.=$this->footer();

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);

        echo $html;
    }
}

