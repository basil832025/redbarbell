<?php
class select_sotr
{
    private $ids;
    private $current_user;

    function __construct()
    {

    }

    function init()
    {
        header('Content-Type: application/json');
        $club = $_POST['club'] ?? '';
        $club_ip = $_POST['club_ip'] ?? '';
        s($_POST);
        if ($club_ip) $ip = $club_ip;
        else{
            if (!$club) {
                echo json_encode(['status' => 'ERROR', 'error' => 'NO_CLUB']);
                exit;
            }
            $aClubs = db()->selectOne('spr_clubs', 'id = :id', ['id' => $club]);
            if (!$aClubs || empty($aClubs['ip_club'])) {
                echo json_encode(['status' => 'ERROR', 'error' => 'NO_CLUB_IP']);
                exit;
            }
            $ip = $aClubs['ip_club'];
        }
        sLog('$ip='.$ip.' $club='.$club);
        $params=array('command'=>'spr_sotr');
        $sotr=[];
        list($status,$type_result,$sotr)=send_data_b52($params,$ip);
        s($sotr);
        if ($status=='OK')
        {
            header('Content-Type: application/json');
          //  $sotr[] = ['id'=>1, 'name' => 'Петренко Іван Іванич','role' => 'Тренер='.$club] ;
            //$sotr[] = ['id'=>2, 'name' => 'Сидоров Іван Іванич', 'role' =>'старший тренер тренер'];

        }
        echo json_encode($sotr);
        exit;

    }
}
?>
