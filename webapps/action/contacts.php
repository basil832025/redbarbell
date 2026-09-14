<?php

class contacts
{
    function __construct()
    {
        //   $this->phone = !empty($_GET['phone']) ? $_GET['phone'] : '0';
        //$this->type_sale = !empty($_GET['type_sale']) ? $_GET['type_sale'] : '0';


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
    <script src="js/jquery-3.6.4.min.js?ver=12"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/bootstrap-formhelpers.min.js?ver=2"></script>
    <script src="js/bootstrap.min.js?ver=2"></script>
      <script src="js/main.js?ver=2067"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/checkout.css?ver=16" rel="stylesheet"  crossorigin="anonymous">

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
        $content_html='';
// Отримуємо всі активні клуби
        $clubs = db()->selectAll('spr_clubs', 'active >= 0 ', [], ['orderBy' => 'name']);

        $content_html = '';
        $clubs_html = '';

        foreach ($clubs as $club) {
            $name = htmlspecialchars($club['name']);        // Назва району (на Позняках тощо)
            $address = htmlspecialchars($club['adress']);  // Адреса
           // $phone = preg_replace('/[^0-9]/', '', $club['phone']); // лише цифри для tel:
            $phone =  $club['phone']; // лише цифри для tel:
            $phone_display = htmlspecialchars($club['phone']);     // для відображення
            $club_name = str_replace("REDBARBELL", "", $name);
            $clubs_html .= "<ul><li class='title'>{$club_name}</li><ul>";
            $clubs_html .= "<li><a href='#'>{$address}</a></li>";
            $clubs_html .= "<li><a href='tel:{$phone}'>{$phone_display}</a></li>";
            $clubs_html .= "</ul></ul>";
        }

        echo $content_html;
        $content_html.='
<div class="container">
<div class="row">
<div class="column descr"><div class="title-form">
<h4>Наші соціальні мережі та сайт:</h4>
</div>
<div class="title-form">
<a href="https://www.instagram.com/redbarbell_gym/" target="_blank"> <img src="img/instagram-48.png"></a>
<a href="https://www.facebook.com/redbarbell.gym" target="_blank"> <img src="img/facebook-48.png"></a>
<a href="https://www.tiktok.com/@redbarbell_gym" target="_blank"> <img src="img/tiktok-48.png"></a>
<a href="https://redbarbell.com.ua/" target="_blank"> <img width="48px" src="img/red.png"></a>
</div>
<div class="column descr"><div class="title-form">
<h4>Зв\'яжіться з нами, ми готові відповісти на всі ваші питання</h4>
</div>
<div class="form-rows contact-rows">
'.$clubs_html.'
</div></div><div class="column image">
<img src="img/logo_red.jpg" alt=""></div>
</div>
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

