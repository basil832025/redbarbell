<?php
class chart_load
{
    function __construct()
    {

    }

    function init()
    {
        header('Content-Type: application/json');

        $chatid = $_POST['chatid'] ?? null;
        $club = $_POST['club'] ?? null;
        $who_user_write = $_POST['who_user_write'] ?? null;
        $last_id = intval($_POST['last_id'] ?? 0);
        $init = intval($_POST['init'] ?? 0);
        $offset = intval($_POST['offset'] ?? 0);
        $limit = intval($_POST['limit'] ?? 30);

        if (!$chatid || !$club || !$who_user_write) {
            echo json_encode([]);
            exit;
        }

// 1. Якщо це перше завантаження (без last_id) — беремо останні 30
        if ($last_id === 0 && !$init) {
            $sql = "SELECT * FROM bs_chats WHERE chat_id_klient = ? AND club = ? ORDER BY id DESC LIMIT ?";
            $params = [$chatid, $club, $limit];
            $rows = db()->query($sql, $params) ? db()->fetchAll() : [];
            echo json_encode(array_reverse($rows)); // перевертаємо для правильного порядку
            exit;
        }

// 2. Якщо scroll вгору (init = true), беремо сторінку з offset
  
        if ($init === 1) {
            $sql = "SELECT * FROM bs_chats WHERE chat_id_klient = ? AND club = ? ORDER BY id DESC LIMIT ? OFFSET ?";
            $params = [$chatid, $club, $limit, $offset];
            $rows = db()->query($sql, $params) ? db()->fetchAll() : [];
            echo json_encode(array_reverse($rows));
            exit;
        }
// 3. Якщо нові повідомлення (last_id > 0)
        if ($last_id > 0) {
            $sql = "SELECT * FROM bs_chats WHERE chat_id_klient = ? AND club = ? AND id > ? ORDER BY id ASC";
            $params = [$chatid, $club, $last_id];
            $rows = db()->query($sql, $params) ? db()->fetchAll() : [];
            echo json_encode($rows);
            exit;
        }

        echo json_encode([]);
        exit;
    }

}