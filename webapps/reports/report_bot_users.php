<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class report_bot_users
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
        //  s($_POST);
    }
    function init (){

            $this->report_param();
        $message_user='';
        $error='';


    }
    function report_param(){
        // клуби списко+++++++++++++++++++++++



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
<body >';
        $sql = "
SELECT 
  c.name AS club_name,
  COUNT(u.id) AS total_users
FROM spr_users u
LEFT JOIN spr_clubs c ON u.club = c.id
WHERE u.is_reg IS NOT NULL
GROUP BY c.name
ORDER BY total_users DESC
";

// Виконуємо запит через ваш клас
        db()->query($sql);
        $clubs = db()->fetchAll();
// Генерація HTML
        $html .= '
<div class="container my-4">
  <h5 class="text-center mb-4">Зареєстровані користувачі по клубах</h5>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
';

        foreach ($clubs as $row) {
            $club_name = $row['club_name'] ?: 'Без назви';
            $total = intval($row['total_users']);

            $html .= '
<div class="col">
  <div class="card shadow-sm h-100">
    <div class="card-body">
      <h6 class="card-title fw-bold mb-2">' . htmlspecialchars($club_name) . '</h6>
      <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted">👥 Кількість зареєстрованих:</span>
        <span class="fs-5 fw-semibold text-primary">' . $total . '</span>
      </div>
    </div>
  </div>
</div>';
        }

        $html .= '
  </div>
</div>';
$html.='<script src="js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" language="JavaScript" src="js/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="js/report.js?ver=1130"></script>
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js?ver=5"></script>
  
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
        $date_from = $this->date_from;
        $date_to = $this->date_to;

        if (!empty($date_from) && !empty($date_to)) {
            if ($date_from<=$date_to) {
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
            'command'=>'GET_REPORT2_ABON_MOUNTS');
        //   s($params);

        list($status,$type_result,$data)=send_data_b52($params,$this->ip_club);
        if ($status=='OK') {
            // Групуємо по місяцях
            $grouped = [];
            foreach ($data as $row) {
                $month = $row['MONTH_CNT'] . ' міс.';
                $grouped[$month][] = $row;
            }

            $total_cnt = 0;
            $total_summa = 0;

            $content_html = '
<div class="container my-4">
  <h5 class="text-center">Відомість продажу пакетів</h5>
  <p class="text-center">з 01.04.2025 по 23.04.2025 в грн</p>

  <div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Категорія</th>
          <th class="text-end">К-сть</th>
          <th class="text-end">Сума</th>
          <th class="text-end">Нові</th>
          <th class="text-end">Сума нові</th>
          <th class="text-end">Оплачено</th>
          <th class="text-end">Оплачено нові</th>
        </tr>
      </thead>
      <tbody>';

            foreach ($grouped as $month_label => $items) {
                $month_cnt = 0;
                $month_sum = 0;

                $content_html .= '
      <tr class="table-primary"><td colspan="7"><strong>' . $month_label . '</strong></td></tr>';

                foreach ($items as $item) {
                    $content_html .= '
          <tr>
            <td>' . htmlspecialchars($item['PACKAGE']) . '</td>
            <td class="text-end">' . intval($item['CNT']) . '</td>
            <td class="text-end">' . number_format($item['SUMMA'], 0, ',', ' ') . '</td>
            <td class="text-end">' . intval($item['CNT_NEW']) . '</td>
            <td class="text-end">' . number_format($item['SUMMA_NEW'], 0, ',', ' ') . '</td>
            <td class="text-end">' . number_format($item['SUM_OPL'], 0, ',', ' ') . '</td>
            <td class="text-end">' . number_format($item['SUM_OPL_NEW'], 0, ',', ' ') . '</td>
          </tr>';

                    $month_cnt += $item['CNT'];
                    $month_sum += $item['SUMMA'];
                    $total_cnt += $item['CNT'];
                    $total_summa += $item['SUMMA'];
                }

                $content_html .= '
      <tr class="table-light fw-bold">
        <td>Ітого:</td>
        <td class="text-end" style="white-space: nowrap;">' . number_format($month_cnt, 0, ',', ' ') . '</td>
        <td class="text-end" style="white-space: nowrap;">' . number_format($month_sum, 0, ',', ' ') . '</td>
        <td colspan="4"></td>
      </tr>';
            }

// Загальний підсумок
            $content_html .= '
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td>Всього:</td>
          <td class="text-end" style="white-space: nowrap;">' . number_format($total_cnt, 0, ',', ' ') . '</td>
          <td class="text-end" style="white-space: nowrap;">' . number_format($total_summa, 0, ',', ' ') . '</td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>';
        }else if ($type_result=='NO_RESULT')
            $content_html = ' По вибраним параметрам даних немає';
        else
            $content_html = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!'; // если не ОК то какая то проблема и ошибка



        //   s($content_html);
        return $content_html;
    }
}