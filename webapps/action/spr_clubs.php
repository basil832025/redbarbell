<?php
class spr_clubs
{
    function __construct()
    {
        //  s('get_check');

    }
    function body()
    {

        $params=[];
        $sql = "SELECT * FROM spr_clubs  ORDER BY id ";
        $clubs = db()->query($sql, $params) ? db()->fetchAll() : [];

    s($clubs);
        $content_html = '';

        if ($clubs) {


            $content_html .= '


<div class="text-center">
<h4>Виберіть клуб:</h4>

</div>
<div class="table-responsive">
<table class="table table-hover table-sm  w-100">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" style="width: 150px;" class="text-center wt-150">Клуб</th>
        <th scope="col" >Телефон</th>
        <th scope="col" class="text-center"><span class="rotate_nomin-sm-90">Актив-<br>ність</span></th>
      
    
    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
            $dat_time = '';
            $doc = '';

            $n = 0;

            foreach ($clubs as $elem) {
                $n++;
                $club_name = str_replace("REDBARBELL", "", $elem['name']);
                if (!empty($elem['name'])) {
                $active =$elem['active'] ? '✅' : '❌';
                    $content_html .= '<tr class="club_vibor"  club_id="' . $elem['id'] . '">
       
        <td class="text-start small"> ' . $club_name. '</td>
        <td class="text-start acc_name small" scope="row"> ' . $elem['phone'] . '</td>
  
 
        <td class="text-center small"> ' . $active . '</td>
      
        </tr>
        <tr>
      ';
                }

            }
            $content_html .= '
       </tbody>
</table>
</div>

';


            /*<h6 class="text-center">Виберіть дію: <br>1) тільки начислити по чеку бонуси для накопичення.<br>
      2) для списання ваших бонусів введіть суму в полі нижче на натисніть кнопку СПИСАТИ</h6>*/
        } else
            $content_html .= '<div class="p-3 mb-2 bg-danger text-white">Співробітника не знайдено</div>';


        return $content_html;
    }
    function init()
    {
        if (!empty($_POST)) {
            echo $this->body(); exit;
        }
        $this->view_html();

    }

    function header()
    {
        $html = '<!doctype html>
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
      <script src="js/main.js?ver=3027"></script>
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

    function footer()
    {
        $html = '
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
  $(document).ready(function () {
    $("#toggleFilters").on("click", function () {
      const $filterBlock = $("#filtersBlock");
      const isVisible = $filterBlock.is(":visible");

      $filterBlock.toggle();
      $(this).text(isVisible ? "Показати фільтри" : "Сховати фільтри");
    });
        // Відстеження змін у всіх полях
    $("#filterForm input, #filterForm select").on("input change", function () {
        fetchFilteredData();
    });

  });
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




    function view_html()
    {
        $html = $this->header();
        $html .= '<section id="content" class="container">';
        $html .= $this->body();
        $html .= '</section>';
        $html .= $this->footer();

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);

        echo $html;
    }



}