<?php
class ajax_get_tren
{
    public $ip_club;
    public $dat;
    public $acc;
    function __construct()
    {
        //  s('  ajax_get_trentty');
        $this->ip_club = !empty($_POST['ip_club']) ? $_POST['ip_club'] : '';
        $this->dat = !empty($_POST['dat']) ? $_POST['dat'] : '';
        $this->acc = !empty($_POST['acc']) ? $_POST['acc'] : '';
        // s('$_POST');
        //  s($_POST);
    }

    function init()
    {

        $content= $this->get_html();
        $message_user='$message_user$message_user '.$this->dat;
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
         $params = array('dat'=>$this->dat,'acc'=>$this->acc,  'command'=>'GET_TRAININGSTODAY');
         list($status,$type_result,$msg_res)=send_data_b52($params,$this->ip_club);
        s($msg_res);
        $content_html='';
        if ($status=='OK') {
            $content_html .= '
<div class="table-responsive-lg">
<table class="table table-hover table-sm">
    <thead class="align-middle text-center">
    <tr class="table-primary">
        <th scope="col" class="text-center wt-200">Тренування</th>
        <th scope="col">Час</th>
        <th scope="col"><span class="rotate_nomin-sm-90">Записано</span> </th>
        <th scope="col"><span class="rotate_nomin-sm-90">Максимально</span></th>
        <th scope="col"><span class="rotate_nomin-sm-90">Резерв</span></th>
     

    </tr>
    </thead>
    <tbody  class="align-middle text-center">';
            if (!empty($msg_res[0])) {
                $all_sum_out = 0;
                $all_sm_nal = 0;
                $all_sm_visa = 0;
                $all_sm_kredit = 0;
                $all_sm_sale = 0;
                $all_cnt = 0;
                foreach ($msg_res as $elem) {
                    s($elem);
                    if (!empty($elem['TOV_NAME'])) {

                        if (!empty($elem['CLIENTS_CNT']))
                            $ost_all = round($elem['CLIENTS_CNT']) - round($elem['CNTTRAN']);
                        else
                            $ost_all = 100;
                        $content_html .= '<tr class="trenerovka" acctozapis="' . $elem['ACC'] . '" ost_all="'.$ost_all.'"   ip_club="' . $this->ip_club . '" time_from="' . $elem['TIME_FROM'] . '"   trenid="' . $elem['KOD'] . '">
        <td scope="row" class="text-start tov_name"> ' . $elem['TOV_NAME'] . '</td>
        <td class="time_period">' . $elem['TIME_FROM'] . '-' . $elem['TIME_TO'] . '</td>
        <td>' . round($elem['CNTTRAN']) . '</td>
        <td>' . round($elem['CLIENTS_CNT']) . '</td>
        <td>' . round($elem['CNTTRANRESERV']) . '</td>
      
    </tr>';
                    }

                }

            }
            $content_html.='
  
    </tbody>
</table>
</div>';
        }

        else
            if ($type_result=='NO_RESULT')
                $content_html.= '<div class="p-3 mb-2 bg-danger text-white">Записів на цей день не знайдено</div>';
            else
                $content_html .= '<div class="p-3 mb-2 bg-danger text-white">На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!</div>'; // если не ОК то какая то проблема и ошибка

        $content_html.='
  <hr class="my-4">
                <div class="col-sm-6 mb-3" >
                    <button class="w-100 btn btn-primary btn-lg " id="back_fitness" type="button"><< Назад</button>
                </div>
';


        return $content_html;
    }
}