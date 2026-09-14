<?php
class send_mess
{
    protected $club;

    /** @var string|int */
    protected $chat_id;

    /** @var string|null */
    protected $oper;

    /** @var string */
    protected $acc_name = ''; // на будущее, если вернёшь выборку имени

    function __construct()
    {
         $this->club = $_GET['club'] ?? $_POST['club'] ?? '';
        $this->chat_id = $_GET['chat_id'] ?? $_POST['chat_id'] ?? '';
        $this->oper = $_POST['oper'] ?? null;
        sAllSend('constr');
        sAllSend($_POST);
    //    $sql = 'select name from spr_acc where chat_id='.$this->chat_id .' limit 1';
        // s($sql);
    //    $this->acc_name = db_field($sql,'name');
     //   s($_GET);
      //  s($this->acc_name);
    }

    function init()
    {
        $allowedMethods = ['send'];

        if (in_array($this->oper, $allowedMethods) && method_exists($this, $this->oper)) {
            $content= $this->{$this->oper}();
            $message_user='$message_user$message_user ';
            $error='';
            //  s($message_user);
            Ajax(array('content' => $content,
                'message_user' => $message_user,
                'error' => $error,
                'java_script' => '',
                'post_return' => '',
            ));exit;
        }
        $this->view_html();

    }
    function send(){
        $club_id = $this->club;
        $admin_id = $this->chat_id;
        $send_type = $_POST['send_type'] ?? 'text';
        $content = trim($_POST['message'] ?? '');
        $file_url = $_POST['file_url'] ?? null;
        $last_id = (int)($_POST['last_id'] ?? 0);
        $sent_before = (int)($_POST['sent'] ?? 0);
        $success_before = (int)($_POST['success'] ?? 0);
        $fail_before = (int)($_POST['fail'] ?? 0);
        $batch_size = 20;

        if ($send_type === 'image' && !$file_url && isset($_FILES['image'])) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $file_url = $targetPath;
            }
        }

        if ($send_type === 'text' && $content === '') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Введите текст сообщения']);
        }

        if ($send_type === 'image' && !$file_url) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Не удалось загрузить изображение']);
        }

// Получить пользователей этого клуба, не заблокированных

    /*    $users = db()->selectAll("
    SELECT chat_id, id 
    FROM spr_users 
    WHERE club = :club_id AND chat_id IS NOT NULL AND block != 1 and admin>0
", ['club_id' => $club_id]);*/
        $total_sql = 'SELECT COUNT(*) as cnt
    FROM spr_users
    WHERE club = :club_id AND chat_id IS NOT NULL AND block != 1';
        $total_rows = db()->query($total_sql, ['club_id' => $club_id]) ? db()->fetchAll() : [];
        $total_all = (int)($total_rows[0]['cnt'] ?? 0);

        $sql = 'SELECT chat_id, id 
    FROM spr_users 
    WHERE club = :club_id AND chat_id IS NOT NULL AND block != 1 AND id > :last_id
    ORDER BY id ASC
    LIMIT '.$batch_size; //and admin>0
        $params=['club_id' => $club_id, 'last_id' => $last_id];
        $users =   db()->query($sql, $params) ? db()->fetchAll() : [];


        sAllSend(['mass_send_batch' => count($users), 'last_id' => $last_id]);
        $total = 0;
        $success = 0;
        $fail = 0;
        $max_id = $last_id;

        foreach ($users as $user) {
            $chat_id = $user['chat_id'];
            $max_id = max($max_id, (int)$user['id']);
            $ok = false;
            $res = 'ok';
            if ($send_type === 'text') {
                sAllSend('$chat_id='.$chat_id. ' $content='.$content);
                $arrayQuery = array(
                    'chat_id' 		=> $chat_id,
                    'text'			=> $content,
                    'parse_mode'	=> "html",

                );
                $res =   TG_sendMessage($arrayQuery);

            } else {
                sAllSend('$chat_id='.$chat_id. ' $file_url='.$file_url);
                $arrayQuery = [
                    'chat_id' => $chat_id,
                    'photo' => new CURLFile($file_url),
                    // 'reply_markup' => SystemClass::$Button,
                ];
                $res = TG_sendPhoto($arrayQuery);
            }

            $result = json_decode($res, true);
            $msg = isset($result['description']) ? $result['description'] : 'Send failed';

          //  $result['ok'] = true;

            if ($result['ok'] ?? false) {
                $success++;
                $ok = true;
            } else {
                $fail++;
                if (strpos($msg, 'bot was blocked') !== false || strpos($msg, 'user is deactivated') !== false) {
                    db()->query("UPDATE spr_users SET block = 1 WHERE chat_id = :chat_id", ['chat_id' => $chat_id]);
                }
            }

            $total++;
        }

        $sent_total = $sent_before + $total;
        $success_total = $success_before + $success;
        $fail_total = $fail_before + $fail;
        $done = count($users) < $batch_size || $sent_total >= $total_all;

        if ($done) {
            db()->insert('tg_mass_send_history', [
                'admin_id'      => $admin_id,
                'message_type'  => $send_type,
                'content'       => $content,
                'file_url'      => $file_url ? $file_url : null,
                'total_sent'    => $sent_total,
                'success_count' => $success_total,
                'fail_count'    => $fail_total,
                'club_id'       => $club_id,
            ]);
        }

        $this->jsonResponse([
            'status' => 'ok',
            'done' => $done,
            'last_id' => $max_id,
            'sent' => $sent_total,
            'success' => $success_total,
            'fail' => $fail_total,
            'total' => $total_all,
            'file_url' => $file_url,
        ]);

// Записать в лог истории
        db()->insert('tg_mass_send_history', [
            'admin_id'      => $admin_id,
            'message_type'  => $send_type,
            'content'       => $content,
            'file_url'      => $file_url ? $file_url : null,
            'total_sent'    => $total,
            'success_count' => $success,
            'fail_count'    => $fail,
            'club_id'       => $club_id,
        ]);
        $success='ok';
      //  echo json_encode(['status' => 'ok']); // или ['status' => 'error', 'message' => '...']
        echo json_encode([
            'status' => $success ? 'ok' : 'error'
        ]);
        exit;
      //  echo "Успешно отправлено: $success / $total. Ошибок: $fail";

    }
    function jsonResponse($data){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
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
    <script src="js/main.js?ver='.filemtime('js/main.js').'"></script>

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
        // получить все чаты с клиентами отсотрированые, сначала по не прочитаным, а потом по последним сообщениям
      /*  db()->query("SELECT chat_id_klient,max(time_send) as max_time, (select name from spr_acc a where a.chat_id=c.chat_id_klient) as name,
                count(id) as cn_all, sum(is_read) as cn_read FROM bs_chats c WHERE club = :club 
GROUP BY chat_id_klient order by max(time_send) desc",
            ['club' => $this->club]);
        $users = db()->fetchAll();
        s($users);*/


        $content_html='
   <div class="container-sm"><div class="text-center">
<h6 class="text-center">Масова розсилка для клубу:</h6>
<form id="massSendForm" method="post" enctype="multipart/form-data">
 <input type="hidden" name="action" value="send_mess">
 <input type="hidden" name="oper" value="send">
 <input type="hidden" name="club" value="'.$this->club.'">
 <input type="hidden" name="chat_id" value="'.$this->chat_id.'">
  <div class="mb-3">
    <label for="sendType" class="form-label">Тип розсилки</label>
    <select class="form-select" name="send_type" id="sendType" required>
      <option value="text">Текст</option>
      <option value="image">Банер-зображення</option>
    </select>
  </div>

  <div class="mb-3" id="textContainer">
    <label for="message" class="form-label">Повідомлення</label>
    <textarea class="form-control" name="message" id="message" rows="4"></textarea>
  </div>

  <div class="mb-3 d-none" id="imageContainer">
    <label for="imageFile" class="form-label">Зображення (JPEG/PNG)</label>
    <input class="form-control" type="file" name="image" id="imageFile" accept="image/*">
  </div>

  <button type="submit" class="btn btn-primary" id="send_button">Відправити</button>
</form>

</div>
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

