<?php
class ajax_get_checks_acc
{
    protected $name;

    /** @var string|int */
    protected $admin;

    /** @var string|int */
    protected $acc;

    /** @var string|int */
    protected $chat_id;

    /** @var string|int */
    protected $spivrob;

    function __construct()
    {
        //  s('get_check');
        $this->name= !empty($_POST['name']) ? $_POST['name'] : '';
        $this->admin= !empty($_POST['admin']) ? $_POST['admin'] : '';
        $this->acc= !empty($_POST['acc']) ? $_POST['acc'] : '';
        $this->chat_id= !empty($_POST['chat_id']) ? $_POST['chat_id'] : '';
        $this->spivrob= !empty($_POST['spivrob']) ? $_POST['spivrob'] : '';

    }

    function init()
    {
       // spr_sotr();

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
    function get_html(){
      /*  $params = array('acc'=>$this->acc, 'command'=>'GET_CHECKS_ACC');
        s($params);
        list($status,$type_result,$msg_res)=jwt_request($params);*/

        $sql = "SELECT (select name from spr_clubs where id=u.club) as club_name,u.club as club_id,u.admin,u.sotr,u.sotr_name, 
       a.* FROM spr_acc a,spr_users u WHERE a.chat_id=u.chat_id and  a.acc =  ?  and u.chat_id= ? ORDER BY u.id DESC ";
    //    slog($sql);
        $params = [$this->acc, $this->chat_id];
        $acc = db()->query($sql, $params) ? db()->fetch() : [];
       //  slog($acc);
        $content_html='';
        if ($acc) {
            $params=[];
            $sql = "SELECT * FROM spr_clubs WHERE active =1  ORDER BY id DESC";
            $clubs = db()->query($sql, $params) ? db()->fetchAll() : [];
         //   slog($clubs);
            $select_club = '<select name="club_id" id="clubSelect" class="form-select">';
            $select_club .= '<option value="">Оберіть клуб</option>';

            foreach ($clubs as $club) {
                $club_name = str_replace("REDBARBELL", "", $club['name']);
                $selected = ($club['id'] == $acc['club_id']) ? ' selected' : '';
                $select_club .= '<option value="' . htmlspecialchars($club['id']) . '"' . $selected . '>' .
                    htmlspecialchars($club_name) . '</option>';
            }

            $select_club .= '</select>';

            $num = 1;
            $content_html .= '

<div class="container mt-4">
  <div class="card shadow rounded-3 p-4">
    <h5 class="mb-3 fw-bold">Клієнт/співробітник:<br> '.$acc['name'].'</h5>
    
    <p><strong>Телефон:</strong> '.$acc['phone'].'</p>

    <form>
      <div class="mb-3">
        <label for="clubSelect" class="form-label">Клуб:</label>
       '.$select_club.'
        <small class="form-text text-muted">Виберіть до якого клубу належить клієнт/співробітник</small>
      </div>';
if ($this->admin==1 || $this->spivrob>0)
    $content_html.='  <div class="mb-3">
        <label for="accessLevel" class="form-label">Посада та права доступу:</label>
        <select class="form-select" id="accessLevel">
          <option value="0" '.($acc['admin']==0 ? 'selected' : '').' >Клієнт</option>
          <option value="2" '.($acc['admin']==2 ? 'selected' : '').'>Адміністратор</option>
          <option value="3" '.($acc['admin']==3 ? 'selected' : '').'>Старший Адміністратор</option>
          <option value="4" '.($acc['admin']==4 ? 'selected' : '').'>Старший Тренер</option>
          <option value="5" '.($acc['admin']==5 ? 'selected' : '').'>Тренер</option>
          <option value="1" '.($acc['admin']==1 ? 'selected' : '').'>Керуючий</option>
        </select>
        <small class="form-text text-muted">Виберіть посаду для  співробітника</small>
  
      </div>
<div class="mb-3">
  <label for="SotrSelect" class="form-label">Виберіть співробітники (для тренерів):</label>
  <select class="form-select" id="SotrSelect" sotrSelected="'.$acc['sotr'].'" sotrNameSelected="'.$acc['sotr_name'].'">
  <option value="0">Співробітника не вибрано</option>
   </select>
   <small class="form-text text-muted">Який співробітник відноситься до цього клієнта (обов"язково для тренерів)</small>
  
</div>';

     $content_html.=' <button type="button" id="but_zmina_prav" spivrob = "'.$this->spivrob.'"  chat_id="'.$acc['chat_id'].'" class="btn btn-primary">Змінити</button>
    </form>
  </div>
</div>';
        }
if ($this->spivrob){
    $butt = '   <a class="btn btn-primary btn-lg" href="'.URL.'/webapps/web.php?action=spr_sotr"  role="button"><< До співробітників</a>
           
               ';
}else
    if ($this->admin==1)
    $butt = ' <a class="btn btn-primary btn-lg" href="'.URL.'/webapps/web.php?action=spr_sotr"  role="button"><< До співробітників</a>
<br><br><button class="w-100 btn btn-primary btn-lg " id="back_findacc" type="button"><< Назад</button>';
    else
        $butt = ' <button class="w-100 btn btn-primary btn-lg " id="back_findacc" type="button"><< Назад</button>';

        $content_html.='
</div>
  <hr class="my-4">
                <div class="col-sm-6 mb-3" >
                    
                 '.$butt.' 
                </div>
                <script>
                select2Vibor();
                </script>
';


        return $content_html;
    }
}