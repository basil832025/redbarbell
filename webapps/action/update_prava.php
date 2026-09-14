<?php
class update_prava
{
    private $ids;
    private $current_user;

    function __construct()
    {
        $this->chat_id = $_POST['chat_id'] ?? [];
        $this->clubSelect = $_POST['clubSelect'] ?? null;
        $this->accessLevel = $_POST['accessLevel'] ?? null;
        $this->SotrSelect = $_POST['SotrSelect'] ?? null;
        $this->SotrSelectName = $_POST['SotrSelectName'] ?? null;
        $this->admin = $_POST['admin'] ?? null;
        $this->spivrob = $_POST['spivrob'] ?? null;
        s($_POST);
    }

    function init()
    {
        header('Content-Type: application/json');


        if ($this->admin==1 || $this->spivrob>0) {
            $data = [
                'club' => $this->clubSelect,
                'admin' => $this->accessLevel,
                'sotr' => $this->SotrSelect,
                'sotr_name' => $this->SotrSelectName
            ];
        }
        else
            $data = [
                'club' => $this->clubSelect,

            ];


        $where = 'chat_id = :chat_id';
        $params = ['chat_id' => $this->chat_id];

        $success = db()->update('spr_users', $data, $where, $params);


        echo json_encode([
            'status' => $success ? 'ok' : 'error'
                ]);
        exit;
    }
}
?>