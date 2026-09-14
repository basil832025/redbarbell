<?php
class TelegramAntispamFilter
{
    // Список заблокированных chat_id вручную
    private static array $blockedChatIds = [
        7013813752,
        // сюда можно добавить вручную других
    ];

    public static function check(array $arrData): bool
    {
        $chatId = $arrData['message']['chat']['id'] ?? null;
        $firstName = $arrData['message']['chat']['first_name'] ?? '';

        if (!$chatId) return false;

        // 1. Проверка по черному списку
        if (in_array($chatId, self::$blockedChatIds)) {
            sLog("⛔ Заблокированный chat_id: $chatId");
            return false;
        }

        // 2. Подозрительное имя
        if (mb_strlen($firstName) > 100 || preg_match('/[^а-яА-Яa-zA-Z0-9ёЁіІїЇєЄ\s]/u', $firstName)) {
            sLog("⛔ Подозрительное имя от $chatId: $firstName");
            // можно добавить в базу
            db()->insert('spr_blocked_users', [
                'chat_id' => $chatId
            ]);
            return false;
        }

        return true; // всё ок, можно продолжать обработку
    }
}
