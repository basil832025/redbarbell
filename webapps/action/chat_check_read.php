<?php
class chat_check_read
{
    private $ids;
    private $current_user;

    function __construct()
    {

    }

    function init()
    {
        header('Content-Type: application/json');

        $chat_id = $_POST['chatid'] ?? null;
        $current_user = $_POST['who_user_write'] ?? null;

        if (!$chat_id || !$current_user) {
            echo json_encode([]);
            exit;
        }

        $sql = "SELECT id FROM bs_chats WHERE chat_id_klient = ? AND who_user_write = ? AND is_read = 1";
        $params = [$chat_id, $current_user];
        $result = db()->query($sql, $params) ? db()->fetchAll() : [];

        $ids = array_column($result, 'id');

        echo json_encode($ids);
        exit;
    }
}
?>