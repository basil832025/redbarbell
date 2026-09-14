<?php
class edit_club
{
    protected $club_id;

    /** @var string */
    protected $name;
    function __construct()
    {
       $this->club_id = $_POST['club_id'] ?? 0;
       $this->name = $_POST['name'] ?? '';

    }

    function init()
    {
        if (!empty($this->name)){
            $data = [
                'name' => $_POST['name'],                  // Назва клубу
                'ip_club' => $_POST['ip_club'],            // IP-адреса клубу
                'chat_admin' => $_POST['chat_admin'],      // ID адміністратора чату
                'chat_group' => $_POST['chat_group'],      // ID chat_group чату
                'adress' => $_POST['adress'],      // adress
                'active' => isset($_POST['active']) ? 1 : 0, // Чекбокс (true або false)
                'phone' => $_POST['phone'],                // Телефон
            ];
            $content= $this->upd_data($data);
        }
            $content= $this->get_html();




        $message_user='$message_user$message_user ';
        $error='';
        //  s($message_user);
        Ajax(array('content' => $content,
            'message_user' => $message_user,
            'error' => $error,
            'java_script' => '',
            'post_return' => '',
        ));

    }
    function upd_data($data){
        $success =   db()->update('spr_clubs', $data, 'id = :id', ['id' => $this->club_id]);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $success ? 'ok' : 'error'
        ]);
        exit;
    }
    function get_html(){

        $club = db()->selectOne('spr_clubs', 'id = :id', ['id' => $this->club_id]);

        // s($accs);
        $content_html='';
        if ($club) {
            $active_checked = $club['active'] ? 'checked' : '';

            $content_html .= '
    <form method="post" class="mt-4">
      <div class="mb-3">
        <label class="form-label">Назва клубу</label>
        <input type="text" name="name" class="form-control" value="' . htmlspecialchars($club['name']) . '" required>
      </div>

      <div class="mb-3">
        <label class="form-label">IP клубу</label>
        <input type="text" name="ip_club" class="form-control" value="' . htmlspecialchars($club['ip_club']) . '">
      </div>

      <div class="mb-3">
        <label class="form-label">Chat Admin ID</label>
        <input type="text" name="chat_admin" class="form-control" value="' . htmlspecialchars($club['chat_admin']) . '">
      </div>
  <div class="mb-3">
        <label class="form-label">Chat GROUP ID</label>
        <input type="text" name="chat_group" class="form-control" value="' . htmlspecialchars($club['chat_group']) . '">
      </div>
      <div class="mb-3">
        <label class="form-label">Телефон</label>
        <input type="text" name="phone" class="form-control" value="' . htmlspecialchars($club['phone']) . '">
      </div>
 <div class="mb-3">
        <label class="form-label">Адреса</label>
        <input type="text" name="adress" class="form-control" value="' . htmlspecialchars($club['adress']) . '">
      </div>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="active" id="activeSwitch" value="1" ' . $active_checked . '>
        <label class="form-check-label" for="activeSwitch">Активний клуб</label>
      </div>

      <button type="submit" class="btn btn-primary" id="save_club" club_id="'.$this->club_id.'">Зберегти</button>
    </form>';
        } else {
            $content_html .= "<div class='alert alert-danger'>Клуб не знайдено.</div>";
        }

                $butt = ' <button class="w-100 btn btn-primary btn-lg " id="back_findacc" type="button"><< Назад</button>';

        $content_html.='
</div>
  <hr class="my-4">
                <div class="col-sm-6 mb-3" >
                    
                 '.$butt.' 
                </div>
               
';


        return $content_html;
    }
}