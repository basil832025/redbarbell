<?php
class spr_sotr
{
    function __construct()
    {
        //  s('get_check');

    }
    function body()
    {
        $name = trim($_POST['search_name'] ?? '');
        $phone = trim($_POST['search_phone'] ?? '');
        $club = trim($_POST['filter_club'] ?? '');
        $role = trim($_POST['filter_role'] ?? '');
        s($_POST);
        // Будуємо WHERE
        $where = [];
        $params = [];

        if ($name !== '') {
            $where[] = '(a.name LIKE :name )';
            $params['name'] = '%' . $name . '%';
        }

        if ($phone !== '') {
            $where[] = 'a.phone LIKE :phone';
            $params['phone'] = '%' . $phone . '%';
        }

        if ($club !== '') {
            $where[] = 'u.club = :club';
            $params['club'] = $club;
        }

        if ($role !== '') {
            $where[] = 'admin = :role';
            $params['role'] = $role; // або передавати одразу ID
         //   $params['role'] = getRoleId($role); // або передавати одразу ID
        }

        $whereSql = count($where) ? ' ' . implode(' AND ', $where) : '1=1';

       // $sql = "SELECT * FROM spr_users $whereSql ORDER BY name ASC";

        $content_html = '';

        $sql = "SELECT (select name from spr_clubs where id=u.club) as club_name,u.club as club_id,u.sotr,u.sotr_name,u.admin, a.* 
FROM spr_acc a,spr_users u 
WHERE $whereSql and a.chat_id=u.chat_id and admin>0    ORDER BY a.name DESC ";
        s($sql);

        $accs = db()->query($sql, $params) ? db()->fetchAll() : [];
        s($accs);
        $content_html = '';

        if ($accs) {


            $content_html .= '


<div class="text-center">
<h4>Виберіть який співробітник вас цікавить:</h4>

</div>
<div class="table-responsive">
<table class="table table-hover table-sm  w-100">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" style="width: 150px;" class="text-center wt-150">ПІБ<br>Телефон</th>
        <th scope="col">Клуб</th>
        <th scope="col">Посада</th>
        <th scope="col">Співробітник</th>
      
    
    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
            $dat_time = '';
            $doc = '';

            $n = 0;

            foreach ($accs as $elem) {
                $n++;
                $club_name = str_replace("REDBARBELL", "", (string) ($elem['club_name'] ?? ''));
                $posada = getAccessLevelName($elem['admin']);
                if (!empty($elem['name'])) {
                    
                    $content_html .= '<tr class="acc_vibor" spivrob="1" kod="' . $elem['acc'] . '" admin="' . $elem['admin'] . '" chat_id="' . $elem['chat_id'] . '">
        <td class="text-start acc_name small" scope="row"> ' . $elem['name'] . '<br>' . $elem['phone'] . '</td>
        
        <td class="text-start small"> ' . $club_name. '</td>
        <td class="text-start small"> ' . $posada . '</td>
        <td class="text-start small"> ' . $elem['sotr_name'] . '</td>
      
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
      <script src="js/main.js?ver=3019"></script>
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



    function filter(){

        $params=[];
        $sql = "SELECT * FROM spr_clubs WHERE active =1  ORDER BY id DESC";
        $clubs = db()->query($sql, $params) ? db()->fetchAll() : [];

        $select_club = '<select name="filter_club"  class="form-select">';
        $select_club .= '<option value="">Оберіть клуб</option>';

        foreach ($clubs as $club) {
            $club_name = str_replace("REDBARBELL", "", (string) ($club['name'] ?? ''));
            $select_club .= '<option value="' . htmlspecialchars($club['id']) . '" >' .
                htmlspecialchars($club_name) . '</option>';
        }

        $select_club .= '</select>';
        $html = '<div class="container">
<!-- 🔘 Кнопка-перемикач -->
<div class="text-end mt-4 mb-2">
  <button type="button" class="btn btn-outline-secondary" id="toggleFilters">
    Показати фільтри
  </button>
</div>

<!-- 🧩 Блок фільтрів -->
<div id="filtersBlock" class="mb-3 mt-4" style="display: none;">
  <form id="filterForm">
    <div class="row g-2">
      <div class="col-md-3">
        <input type="text" class="form-control" name="search_name" placeholder="ПІБ або частина">
      </div>
      <div class="col-md-3">
        <input type="text" class="form-control" name="search_phone" placeholder="Телефон">
      </div>
      <div class="col-md-3">
        '.$select_club.'
      </div>
      <div class="col-md-3">
     <select class="form-select" name="filter_role">
  <option value="">Усі посади</option>
 
  <option value="1">Керуючий</option>
  <option value="2">Адміністратор</option>
  <option value="3">Старший Адміністратор</option>
  <option value="5">Тренер</option>
  <option value="4">Старший Тренер</option>
</select>
      </div>
    </div>
   </form>
</div>
</div>
';
        return $html;
    }
    function view_html()
    {
        $html = $this->header();
        $html .= $this->filter();
        $html .= '<section id="content" class="container">';
        $html .= $this->body();
        $html .= '</section>';
        $html .= $this->footer();

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);

        echo $html;
    }
    
}
function getAccessLevelName($level) {
    switch ((int)$level) {
        case 0:
            return 'Клієнт';
        case 1:
            return 'Керуючий';
        case 2:
            return 'Адміністратор';
        case 3:
            return 'Старший Адміністратор';
         case 4:
            return 'Старший Тренер';
        case 5:
            return 'Тренер';
        default:
            return 'Невідомо';
    }
}
