<?php
class get_check_admin
{
    protected $num;

    /** @var string|int */
    protected $admin;

    /** @var string */
    protected $phone;

    /** @var string|int */
    protected $bonus;

    /** @var string|int */
    protected $type_sale;

    /** @var string|int */
    protected $acc;
    function __construct()
    {
        //  s('get_check');
        $this->num = !empty($_POST['num']) ? $_POST['num'] : '';
        $this->admin = !empty($_POST['admin']) ? (int)$_POST['admin'] : 0;
        $this->phone = !empty($_POST['phone']) ? trim($_POST['phone']) : '';
        $this->bonus = !empty($_POST['bonus']) ? $_POST['bonus'] : '';
        $this->type_sale = !empty($_POST['type_sale']) ? $_POST['type_sale'] : '';
        $aAcc = db()->selectOne('spr_acc', 'phone = :phone', ['phone' => $this->phone]);
        $this->acc = !empty($aAcc['acc']) ? (int)$aAcc['acc'] : 0;

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
        $socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);
        //s(HOST_SOCKET);
        list($status,$type_result,$msg_res)= $socket->GET_CHECK($this->num);
        s($status);
        $content_html='';
        s($type_result);
        s($msg_res);
        if ($status=='OK'){


            $content_html.='
<div class="table-responsive-lg">
<div class="text-center">
<h4>КАФЕ "Tennessee"</h4>
<h6 >м. Київ, вул. Степана Рудницького, буд.19/14</h6>
</div>
<table class="table table-hover table-sm">
   
    <tbody  class="align-middle text-center">';
            $dat_time='';$doc='';
            if (!empty($msg_res[0])){
                $n=0; $all_sum_out=0; $all_sm_nal=0; $all_sm_visa=0;$all_sm_kredit=0;$all_sm_sale=0;$all_cnt=0;
                foreach ($msg_res as $elem)
                {
                    $n++;

                    if(!empty($elem['NAME'])){
                        $dat_time=$elem['TIME_OPEN'];
                        $doc=$elem['DOC'];
                        $all_sum_out+=round($elem['SUMMA']);
                        $content_html.='<tr >
        <td class="text-start"> '.round($elem['CNT']).' x</td>
        <td class="text-right">'.round($elem['PRICE']).'</td>
        </tr>
        <tr>
        <td scope="row" class="text-start"> '.$elem['NAME'].'</td>
        <td class="text-right">'.round($elem['SUMMA']).'</td>
      
    </tr>';
                    }

                }
                $content_html.='<tr>
 <td scope="row" class="text-start font-weight-bold"><h3> Сума по чеку:</h3></td>
        <td class="text-right font-weight-bold"><h3>'.$all_sum_out.'</h3></td>
</tr>
';
            }



            $this->bonus = !empty($this->bonus) ? $this->bonus : 0;

            {
                $txt = '<form id="formsale" class="needs-validation" novalidate action="?" method="post" enctype="multipart/form-data">
      
   <div class="col-sm-4">
                <label for="firstName" class="form-label">Введіть знижку по чеку *</label>
                <input type="text" class="form-control" acc="'.$this->acc.'" doc="'.$doc.'" phone="'.$this->phone.'"   name="form[sale_prc]" id="sale_prc" placeholder="" pattern="[0-9\']{1,}" value="10" required>
                <div class="invalid-feedback">
                    тільки цифри 
                </div>
            </div><br>
            <div>
<button class="w-100 btn btn-primary btn-lg "  tp="1" id="put_sale" type="submit">ЗАСТОСУВАТИ ЗНИЖКУ</button>
</div>
</form>';
            }

            $content_html.='
  
    </tbody>
</table>
<div class="text-center">'.($dat_time).'</div>
<div class="text-center">Рахунок № '.($doc).'</div>
</div>
  <hr class="my-4">
    <hr class="my-4">
<div class="table-responsive-lg">
'.$txt.'
 
<br>';
            /*<h6 class="text-center">Виберіть дію: <br>1) тільки начислити по чеку бонуси для накопичення.<br>
      2) для списання ваших бонусів введіть суму в полі нижче на натисніть кнопку СПИСАТИ</h6>*/
        }else
            if ($type_result=='NO_RESULT')
                $content_html.= '<div class="p-3 mb-2 bg-danger text-white">Чек не знайдений або вже закритий!</div>';
            else
                $content_html .= '<div class="p-3 mb-2 bg-danger text-white">На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!</div>'; // если не ОК то какая то проблема и ошибка

        $content_html.='
</div>
  <hr class="my-4">
                <div class="col-sm-6 mb-3" >
                    <button class="w-100 btn btn-primary btn-lg " id="back_findcheck" type="button"><< Назад</button>
                </div>
';


        return $content_html;
    }
}
