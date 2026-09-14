<?php
class chat_mark_isread
{
    private $ids;
    private $current_user;

    function __construct()
    {
        $this->ids = $_POST['ids'] ?? [];
        $this->current_user = $_POST['who_user_write'] ?? null;
    }

    function init()
    {
        header('Content-Type: application/json');

        if (!is_array($this->ids) || empty($this->ids) || !$this->current_user) {
            echo json_encode(['status' => 'empty']);
            exit;
        }

        // Виділяємо лише ті ID, які не належать поточному користувачу
        $placeholders = implode(',', array_fill(0, count($this->ids), '?'));
        $sqlSelect = "SELECT id FROM bs_chats WHERE id IN ($placeholders) AND who_user_write != ?";
        $params = array_merge($this->ids, [$this->current_user]);
        $rows = db()->query($sqlSelect, $params) ? db()->fetchAll() : [];

        $updatableIds = array_column($rows, 'id');

        if (empty($updatableIds)) {
            echo json_encode(['status' => 'nothing_to_update']);
            exit;
        }

        $updatePlaceholders = implode(',', array_fill(0, count($updatableIds), '?'));
        $sqlUpdate = "UPDATE bs_chats SET is_read = 1 WHERE id IN ($updatePlaceholders)";
        $success = db()->query($sqlUpdate, $updatableIds);

        echo json_encode([
            'status' => $success ? 'ok' : 'error',
            'updated_ids' => $updatableIds
        ]);
        exit;
    }
}
?>