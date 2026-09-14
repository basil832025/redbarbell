<?php

class work_acc
{
    protected $admin;
    function __construct()
    {
        $this->admin = !empty($_GET['admin']) ? $_GET['admin'] : '0';
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
      <script src="js/main.js?ver='.filemtime('js/main.js').'"></script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js?ver=5"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/checkout.css?ver=11" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/report.css?ver=3" rel="stylesheet"  crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css?ver=5" rel="stylesheet" />

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

        $content_html.='
<div class="table-responsive-lg">
<h6 class="text-center">Для пошуку клієнта введіть 4 останні цифри номера телефона або частину ПІБ :</h6>
 <form id="formacc" class="needs-validation" novalidate action="?" method="post" enctype="multipart/form-data">
       
   <div class="col-sm-4">
                <label for="firstName" class="form-label">4 останні цифри номеру телефону або частину ПІБ *</label>
                <input type="text" class="form-control"    name="form[phone]" id="phone" placeholder=""  value="" required>
                <div class="invalid-feedback">
                    тільки цифри (останні 4 цифри)
                </div>
            </div><br>
                   <div class="col-md-5">
                <label for="child-type_document" class="form-label">Шукати як:</label>
                <select class="form-select" id="search_phone_card"  required>
                   <option  value="1">По телефону та ПІБ</option>
                     <option  value="2">Тільки по телефону</option>
                    <option  value="3">Тільки по ПІБ</option>
                 
                </select>
                <div class="invalid-feedback">
                    Виберіть склад.
                </div>
            </div><br>
<button class="w-100 btn btn-primary btn-lg " admin="'.$this->admin.'"  tp="1" id="find_acc" type="submit">Знайти клієнта</button>
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

