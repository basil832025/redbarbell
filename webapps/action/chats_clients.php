<?php
class chats_clients
{
    public $club;
    public $chat_id;
    public $acc_name;
    function __construct()
    {
        $this->club = !empty($_GET['club']) ? (int)$_GET['club'] : 0;
        
        $this->chat_id = !empty($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;
        $this->acc_name = '';
        if ($this->chat_id !== 0 && $this->club > 0) {
            $sql = 'SELECT a.name
                    FROM spr_acc a
                    JOIN spr_users u ON u.active_account = a.acc AND u.chat_id = a.chat_id
                    WHERE a.chat_id = ? AND u.club = ?
                    LIMIT 1';
            if (db()->query($sql, [$this->chat_id, $this->club])) {
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
    <script src="js/main.js?ver=214"></script>

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
        $searchName = trim($_GET['search_name'] ?? '');
        $searchPhone = trim($_GET['search_phone'] ?? '');

        $content_html = '
<div class="card mb-3">
    <div class="card-body">
        <div class="fw-bold mb-2">Знайти клієнта та розпочати чат</div>
        <form method="get" action="web.php" class="row g-2">
            <input type="hidden" name="action" value="chats_clients">
            <input type="hidden" name="club" value="'.(int)$this->club.'">
            <input type="hidden" name="chat_id" value="'.(int)$this->chat_id.'">
            <div class="col-md-5"><input type="text" class="form-control" name="search_name" value="'.htmlspecialchars($searchName, ENT_QUOTES, 'UTF-8').'" placeholder="Прізвище або ім’я"></div>
            <div class="col-md-5"><input type="text" class="form-control" name="search_phone" value="'.htmlspecialchars($searchPhone, ENT_QUOTES, 'UTF-8').'" placeholder="Номер телефону"></div>
            <div class="col-md-2 d-grid"><button type="submit" class="btn btn-primary">Знайти</button></div>
        </form>
    </div>
</div>';

        if ($searchName !== '' || $searchPhone !== '') {
            $where = ['u.club = :club', 'u.admin = 0', 'a.chat_id IS NOT NULL', 'a.chat_id <> 0', 'u.active_account = a.acc'];
            $params = ['club' => $this->club];

            if ($searchName !== '') {
                $where[] = 'a.name LIKE :search_name';
                $params['search_name'] = '%'.$searchName.'%';
            }

            if ($searchPhone !== '') {
                $phoneDigits = preg_replace('/\D+/', '', $searchPhone);
                $normalizedPhoneSql = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(a.phone, '+', ''), ' ', ''), '-', ''), '(', ''), ')', '')";

                if ($phoneDigits !== '' && str_starts_with($phoneDigits, '0')) {
                    // 068... та +38068... мають знаходити один і той самий номер.
                    $where[] = "($normalizedPhoneSql LIKE :search_phone OR $normalizedPhoneSql LIKE :search_phone_international)";
                    $params['search_phone'] = $phoneDigits.'%';
                    $params['search_phone_international'] = '38'.$phoneDigits.'%';
                } elseif ($phoneDigits !== '') {
                    $where[] = "$normalizedPhoneSql LIKE :search_phone";
                    $params['search_phone'] = $phoneDigits.'%';
                } else {
                    $where[] = 'a.phone LIKE :search_phone';
                    $params['search_phone'] = '%'.$searchPhone.'%';
                }
            }

            $sql = 'SELECT DISTINCT a.chat_id, a.name, a.phone
                    FROM spr_acc a
                    JOIN spr_users u ON u.chat_id = a.chat_id
                    WHERE '.implode(' AND ', $where).'
                    ORDER BY a.name ASC
                    LIMIT 100';
            $clients = db()->query($sql, $params) ? db()->fetchAll() : [];

            $content_html .= '<div class="table-responsive-lg mb-4"><div class="p-1 bg-success text-white">Результати пошуку: '.count($clients).'</div><table class="table table-hover table-sm table-striped"><thead><tr class="table-primary"><th>Клієнт</th><th>Телефон</th><th></th></tr></thead><tbody>';
            if (empty($clients)) {
                $content_html .= '<tr><td colspan="3" class="text-center text-muted">Клієнта не знайдено</td></tr>';
            } else {
                foreach ($clients as $client) {
                    $chatId = (int)$client['chat_id'];
                    $href = 'web.php?action=chart&club='.(int)$this->club.'&admin=1&who_user_write='.(int)$this->chat_id.'&chatid='.$chatId;
                    $safeHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
                    $content_html .= '<tr class="klient_chat" data-href="'.$safeHref.'"><td>'.htmlspecialchars($client['name'] ?? '', ENT_QUOTES, 'UTF-8').'</td><td>'.htmlspecialchars($client['phone'] ?? '', ENT_QUOTES, 'UTF-8').'</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="'.$safeHref.'">Написати</a></td></tr>';
                }
            }
            $content_html .= '</tbody></table></div>';
        }
        // получить все чаты с клиентами отсотрированые, сначала по не прочитаным, а потом по последним сообщениям
        db()->query("SELECT chat_id_klient,max(time_send) as max_time, 
       (select name from spr_acc a where a.chat_id=c.chat_id_klient limit 1) as name, 
                count(id) as cn_all, sum(is_read) as cn_read FROM bs_chats c WHERE club = :club 
GROUP BY chat_id_klient order by max(time_send) desc",
            ['club' => $this->club]);
        $users = db()->fetchAll();
        s($users);


                $content_html.='
<div class="table-responsive-lg">

<div class="p-1 mb-2 bg-info text-white">Чати з клієнтами:</div>
<table class="table table-hover table-sm table-striped">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" class="text-center wt-200">Клієнт</th>
        <th scope="col">Останнє смс</th>
        <th scope="col"><span class="rotate_nomin-sm-90">К-сть смс</span></th>
        <th scope="col"><span class="rotate_nomin-sm-90">К-сть не <br>прочитаних</span></th>
  

    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
                if (!empty($users)){
                    foreach ($users as $elem)
                    {
                        $date = new DateTime($elem['max_time']);
                        $dat = $date->format('d.m.Y H:i:s');
                        $elem['cn_read'] = !empty($elem['cn_read']) ? $elem['cn_read'] : 0;
                        $no_read_cnt = $elem['cn_all']-$elem['cn_read'];
                            $content_html.='<tr class="klient_chat" data-href="web.php?action=chart&club='.$this->club.'&admin=1&who_user_write='.$this->chat_id.'&chatid='.$elem['chat_id_klient'].'">
        <td scope="row" class="text-start "> '.$elem['name'].'</td>
        <td class="time_period">'.$dat.' </td>
        <td >'.$elem['cn_all'].' </td>
        <td >'.$no_read_cnt.' </td>
       
      
    </tr>';
                        }

                    }




                $content_html.='
  
    </tbody>
</table>
</div>

';



        return $content_html;
    }
    function view_html(){
        $html=$this->header();
        $html.='<section id="content">';
        $html.=$this->body();
        $html.='</section>';
        $html.=$this->footer();

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);

        echo $html;
    }
}

