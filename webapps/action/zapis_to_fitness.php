<?php
class Zapis_to_fitness
{
    public string $tp = '';
    public string $acc = '0';
    public string $ip_club = '0';
    function __construct()
    {

        $this->tp = !empty($_GET['tp']) ? $_GET['tp'] : '';
        $this->acc = !empty($_GET['acc']) ? $_GET['acc'] : '0';
        $this->ip_club = !empty($_GET['ip_club']) ? $_GET['ip_club'] : '0';
        //  s($_GET);
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

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);
        $zagl = 'на фітнес-тренування';
        $html.='
<div id="root"></div>
<div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>
<div class="py-2 text-center">
    <h3> Реєстрація '.$zagl.': </h3>
    <h6> Оберіть зручний день та вид тренування, яке відповідає вашим цілям і графіку.</h6>
    <h6 id="day_vibor"></h6>
</div>
<div class="container">
    <div class="row gx-1">

        <div class="col-md-10 col-lg-10">
            <div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>

                <section id="content">';

        $d=1;
        $txt = '';

        for ($d=1;$d<8;$d++)
        {
            $day_week = getDayofWeekUkr($date->format("w"));
            $txt .= ' <div class="col-sm-6 mb-3" >
                        <button class="w-100 btn btn-primary btn-lg but_day" ip_club="'.$this->ip_club.'" acc="'.$this->acc.'"  dat="'.$day_next.'" type="button"> '.$day_week.' '.$day_next.'</button>
                    </div>';
            $date->modify('+1 day');
            $day_next = $date->format("d.m.Y");
        }
        $html.= $txt;

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

