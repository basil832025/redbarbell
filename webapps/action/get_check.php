<?php
class get_check
{
    protected $phone;

    /** @var string|int */
    protected $admin;

    /** @var string|int */
    protected $search_phone_card;

    /** @var string */
    protected $club_ip;

    function __construct()
    {
      //  s('get_check');
        $this->phone = !empty($_POST['phone']) ? trim($_POST['phone']) : '';
        $this->admin = !empty($_POST['admin']) ? (int)$_POST['admin'] : 0;
        $this->search_phone_card = !empty($_POST['search_phone_card']) ? (int)$_POST['search_phone_card'] : 0;
        $this->club_ip = !empty($_POST['club_ip']) ? trim($_POST['club_ip']) : '';

    }

    function init()
    {

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
    //       $params = array('phone'=>$this->phone,'search_phone_card'=>$this->search_phone_card, 'command'=>'GET_ACC');
    //       s($params);
     //   list($status,$type_result,$msg_res)=jwt_request($params);
        $phoneTail = '%' . $this->phone;
        $nameLike = '%' . $this->phone . '%';
        if ($this->search_phone_card == 2) {
            $where = 'a.phone LIKE ?';
            $params = [$phoneTail];
        } elseif ($this->search_phone_card == 3) {
            $where = 'a.name LIKE ?';
            $params = [$nameLike];
        } else {
            $where = '(a.phone LIKE ? OR a.name LIKE ?)';
            $params = [$phoneTail, $nameLike];
        }

        if ($this->club_ip !== '') {
            $where .= ' AND u.club = (SELECT id FROM spr_clubs WHERE ip_club = ? LIMIT 1)';
            $params[] = $this->club_ip;
        }

        $sql = "SELECT (SELECT name FROM spr_clubs WHERE id = u.club) AS club_name, a.*
                FROM spr_acc a
                JOIN spr_users u ON a.chat_id = u.chat_id
                WHERE $where
                ORDER BY u.id DESC";
        s($sql);
        $accs = db()->query($sql, $params) ? db()->fetchAll() : [];
       // s($accs);
        $content_html='';

        if ($accs){


        $content_html.='
<div class="table-responsive">
<div class="text-center">
<h4>Виберіть який клієнт вас цікавить:</h4>

</div>
<div class="table-responsive-lg">
<table class="table table-hover table-sm">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" style="width: 150px;" class="text-center wt-150">ПІБ<br>Телефон</th>
        <th scope="col">Клуб</th>
        <th scope="col">Карта</th>
      
    
    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
        $dat_time='';$doc='';

            $n=0; $all_sum_out=0; $all_sm_nal=0; $all_sm_visa=0;$all_sm_kredit=0;$all_sm_sale=0;$all_cnt=0;
            foreach ($accs as $elem)
            {
                $n++;

                if(!empty($elem['name'])){

                    $content_html.='<tr class="acc_vibor" kod="'.$elem['acc'].'" admin="'.$this->admin.'" chat_id="'.$elem['chat_id'].'">
        <td class="text-start acc_name small" scope="row"> '.$elem['name'].'<br>'.$elem['phone'].'</td>
        
        <td class="text-start small"> '.$elem['club_name'].'</td>
        <td class="text-start small"> '.$elem['card'].'</td>
      
        </tr>
        <tr>
      ';
                }

                }
            $content_html.='
       </tbody>
</table>
</div>';


        /*<h6 class="text-center">Виберіть дію: <br>1) тільки начислити по чеку бонуси для накопичення.<br>
  2) для списання ваших бонусів введіть суму в полі нижче на натисніть кнопку СПИСАТИ</h6>*/
        }else
                $content_html.= '<div class="p-3 mb-2 bg-danger text-white">Клієнта не знайдено</div>';

        $content_html.='
</div>
  <hr class="my-4">
                <div class="col-sm-6 mb-3" >
                 <div class="col-sm-6 mb-3" >
                 <a class="btn btn-primary btn-lg" href="'.URL.'/webapps/web.php?action=work_acc"  role="button"><< Назад</a>
                
               </div> 
                   </div>
';


        return $content_html;
    }
}
