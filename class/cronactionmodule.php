<?php
// класс возвращает и обрабатывает действия по нажатию по кнопкам
class CronActionModule
{
    protected $action = 'start';
    protected $textMessage = '';
    protected $LastTimeOper = '';
    protected $arrDataAnswer = array();
    protected $UserInfo = array();
    protected $LastOper = array();
    protected $accId = 0;
    protected $inline_keyboard = array();

    function __construct($action = '')
    {
        $this->action = $action;
        sCron('cron_action action=' . $action);
    }

    function init()
    {
        $this->actionOperation();
    }

    /**
     * $clubId = 0  -> выполнить по всем клубам
     * $clubId > 0  -> выполнить только по одному клубу
     */
    function actionOperation($action = '', $repeat = 0, $clubId = 0)
    {
        $action = $action ? $action : $this->action;

        switch ($action) {
            case 'zapiczaday':
                $this->zapiczaday($repeat, $clubId);
                break;

            case 'abonaza1day':
                $this->ABONZA3DAY($repeat, $clubId);
                break;

            case 'birthday':
                $this->BIRTHDAY($repeat, $clubId);
                break;

            case 'getrepeat':
                $this->getrepeat();
                break;

            case 'zapictovidpac':
                $this->zapictovidpac($repeat, $clubId);
                break;

            case 'noabon21day':
                $this->NOABON_21_DAY($repeat, $clubId);
                break;

            case 'predprodza1day':
                $this->PREDPRODZA1DAY($repeat, $clubId);
                break;

            case 'predprodza3day':
                $this->PREDPRODZA3DAY($repeat, $clubId);
                break;
        }
    }

    /**
     * Возвращает список клубов:
     * - если $clubId > 0, только один клуб
     * - иначе все активные клубы
     */
    protected function getClubs($clubId = 0)
    {
        if ((int)$clubId > 0) {
            $sql = "SELECT * FROM spr_clubs WHERE active = 1 AND id = :id ORDER BY id DESC";
            return db()->query($sql, ['id' => (int)$clubId]) ? db()->fetchAll() : [];
        }

        $sql = "SELECT * FROM spr_clubs WHERE active = 1 ORDER BY id DESC";
        return db()->query($sql) ? db()->fetchAll() : [];
    }

    function getrepeat_oper()
    {
        $today = date("Y-m-d");

        $sql = 'select oper, club, cnt_poputok
                from spr_cron_oper
                where dat="' . $today . '"
                  and time_start <= DATE_SUB(NOW(), INTERVAL 3 MINUTE)
                  and repeat_oper=1
                  and time_finish is null';

        sCron($sql);
        $aOper = db_list($sql);

        if (!empty($aOper)) {
            // повторно запускаем только конкретный клуб, который не завершился
            foreach ($aOper as $oper) {
                $this->actionOperation($oper['oper'], 1, (int)$oper['club']);
            }
        }
    }

    function getrepeat()
    {
        sCron('getrepeat!!!');

        // проверяем операции которые не выполнились
        $this->getrepeat_oper();

        $today = date("Y-m-d");

        // Шаг 1: Выбираем чаты с новыми сообщениями от пользователей
        $sql = "
            SELECT chat_id_klient, club, COUNT(*) AS unread_count,
                   (select name from spr_acc where chat_id=chat_id_klient limit 1) as name
            FROM bs_chats
            WHERE is_read = 0 AND who_user_write = chat_id_klient AND is_notified = 0
            GROUP BY chat_id_klient, club
        ";

        sCron($sql);
        $chats = db()->query($sql) ? db()->fetchAll() : [];

        if (!empty($chats)) {
            sCron($chats);

            $allIdsToMark = [];

            foreach ($chats as $row) {
                $chatId = $row['chat_id_klient'];
                $count = $row['unread_count'];

                $ArrAdmin = db()->selectOne('spr_clubs', 'id = :id', ['id' => $row['club']]);
                if (empty($ArrAdmin)) {
                    sCron('Не найден клуб для уведомления админа. club=' . $row['club']);
                    continue;
                }

                $text = 'Не прочитані ( ' . $count . ' шт) повідомлення від "' . $row['name'] . '"';

                $inline_keyboard = array();
                $inline_button1 = array(
                    "text" => 'ЧАТ З КЛІЄНТОМ',
                    "web_app" => [
                        "url" => URL2 . 'webapps/web.php?action=chart&club=' . $row['club'] . '&admin=1&who_user_write=' . $ArrAdmin['chat_admin'] . '&chatid=' . $chatId
                    ]
                );
                $inline_keyboard[][] = $inline_button1;

                SystemClass::sendMessTextButt($text, $inline_keyboard, $ArrAdmin['chat_admin']);

                $idsResult = db()->query("
                    SELECT id
                    FROM bs_chats
                    WHERE chat_id_klient = :chat_id
                      AND is_read = 0
                      AND is_notified = 0
                      AND who_user_write = chat_id_klient
                ", ['chat_id' => $chatId]) ? db()->fetchAll() : [];

                foreach ($idsResult as $idRow) {
                    $allIdsToMark[] = $idRow['id'];
                }
            }

            if (!empty($allIdsToMark)) {
                db()->markFieldByIds('bs_chats', 'is_notified', 1, $allIdsToMark);
            }
        }

        /** Уведомление ДЛЯ ПОЛЬЗОВАТЕЛЕЙ **/
        $sql = "
            SELECT chat_id_klient, club, COUNT(*) AS unread_count,
                   (select name from spr_acc where chat_id=chat_id_klient limit 1) as name
            FROM bs_chats
            WHERE is_read = 0 AND who_user_write <> chat_id_klient AND is_notified = 0
            GROUP BY chat_id_klient, club
        ";

        sCron($sql);
        $userNotifications = db()->query($sql) ? db()->fetchAll() : [];

        foreach ($userNotifications as $row) {
            $chatId = $row['chat_id_klient'];
            $count = $row['unread_count'];

            $text = 'Не прочитані ( ' . $count . ' шт) повідомлення від адміністратору клубу';

            $inline_keyboard = array();
            $inline_button1 = array(
                "text" => 'ЧАТ З АДМІНОМ',
                "web_app" => [
                    "url" => URL2 . 'webapps/web.php?action=chart&club=' . $row['club'] . '&who_user_write=' . $chatId . '&chatid=' . $chatId
                ]
            );
            $inline_keyboard[][] = $inline_button1;

            SystemClass::sendMessTextButt($text, $inline_keyboard, $chatId);

            $ids = db()->query("
                SELECT id
                FROM bs_chats
                WHERE chat_id_klient = :chat_id
                  AND is_read = 0
                  AND is_notified = 0
                  AND who_user_write <> chat_id_klient
            ", ['chat_id' => $chatId]) ? db()->fetchAll() : [];

            $idList = array_column($ids, 'id');

            if (!empty($idList)) {
                db()->markFieldByIds('bs_chats', 'is_notified', 1, $idList);
            }
        }
    }

    function logOper($reapet = 0, $club = 0)
    {
        $today = date("Y-m-d");
        $sql = 'insert into spr_cron_oper (oper,time_start,cnt_poputok,dat,repeat_oper,club)
                values("' . $this->action . '", now(),1,"' . $today . '",' . (int)$reapet . ',' . (int)$club . ')';
        db_query($sql);
    }

    function logOperUpdate($finish = 0, $cnt_poputok = 0, $action = '', $club = 0)
    {
        $today = date("Y-m-d");
        $action = $action ? $action : $this->action;

        $setParts = array();

        if ($cnt_poputok) {
            $setParts[] = 'cnt_poputok=cnt_poputok + 1';
        }

        if ($finish) {
            $setParts[] = 'time_finish=now()';
        }

        if (empty($setParts)) {
            return;
        }

        $sql = 'update spr_cron_oper
                set ' . implode(', ', $setParts) . '
                where oper="' . $action . '"
                  and dat="' . $today . '"
                  and club="' . (int)$club . '"';

        sCron($sql);
        db_query($sql);
    }

    function zapictovidpac($repeat = 0, $clubId = 0)
    {
        // пока пусто
    }

    function zapiczaday($repeat = 0, $clubId = 0) // запись за 2 часа
    {
        sCron('zapiczaday!!!');

        $clubs = $this->getClubs($clubId);

        foreach ($clubs as $club) {
            $club_id = (int)$club['id'];

            if ($repeat == 0) {
                $this->logOper(1, $club_id);
            }

            $params = array('command' => 'GET_CRON_ZAPISZA2HOUR');
            list($status, $type_result, $msg_res) = send_data_b52($params, $club['ip_club']);

            sCron($status);
            sCron($type_result);
            sCron('GET_CRON_ZAPISZADAY');
            sCron('$club=' . $club['name']);
            sCron($msg_res);

            if ($status == 'OK') {
                foreach ($msg_res as $abon) {
                    if (!empty($abon['ACC'])) {
                        $sql = 'select a.chat_id, a.phone, a.name, a.card
                                from spr_acc a, spr_users u
                                where acc=' . (int)$abon['ACC'] . '
                                  and u.chat_id=a.chat_id
                                  and u.club=' . $club_id;

                        $userInfo = db_list($sql);

                        if (!empty($userInfo)) {
                            foreach ($userInfo as $uInfo) {
                                sCron('$userInfo');
                                sCron($userInfo);

                                $text = 'Привіт! Не забудь про своє тренування в RedBarbell!
Твоє групове заняття "' . $abon['NAME'] . '" о ' . $abon['TIME_FROM'] . ' ' . $abon['DAT'] . ' розпочнеться зовсім скоро!
Якщо плани змінилися, ми будемо вдячні за відміну запису – тут чи за номером телефону ' . $club['phone'] . '
Чекаємо на тренуванні!';

                                SystemClass::sendText($text, $uInfo['chat_id']);
                            }
                        }
                    }
                }

                $this->logOperUpdate(1, $repeat ? 1 : 0, 'zapiczaday', $club_id);
            } elseif ($type_result == 'NO_RESULT') {
                $this->logOperUpdate(1, 0, 'zapiczaday', $club_id);
                sCron('Немає даних zapiczaday');
            } else {
                $this->logOperUpdate(0, 1, 'zapiczaday', $club_id);
                sCron('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!! $club_id=' . $club_id);
            }
        }
    }

    function PREDPRODZA3DAY($repeat = 0, $clubId = 0)
    {
        // эта операция сейчас глобальная, без разбивки по клубам
        // clubId принят для совместимости, но не используется

        if ($repeat == 0) {
            $this->logOper(1, 0);
        }

        $socket = new socketB52(HOST_SOCKET, PORT_SOCKET);
        list($status, $type_result, $msg_res) = $socket->GET_CRON_PREDPRODZA3DAY();

        if ($status == 'OK') {
            foreach ($msg_res as $abon) {
                if (!empty($abon['ACC'])) {
                    $sql = 'select chat_id, phone, name, card from spr_acc where acc=' . (int)$abon['ACC'];
                    $userInfo = db_list($sql);

                    if (!empty($userInfo)) {
                        foreach ($userInfo as $uInfo) {
                            if ($abon['GRP'] == 372 || $abon['GRP'] == 373 || $abon['GRP'] == 374 || $abon['GRP'] == 192) {
                                sCron($uInfo);

                                $text = 'Вам потрібно оплатити абонемент (' . $abon['TOV_NAME'] . ') до ' . $abon['DATE_STOP'] . '. До сплати ' . number_format($abon['PRICE'], 2, '.', '') . 'грн. Бажаєте оплатити абонемент?';
                                sCron($text);

                                $inline_button3 = array(
                                    "text" => 'Оплатити ' . number_format($abon['PRICE'], 2, '.', '') . 'грн',
                                    "url" => 'https://trystyhii.com.ua/formreg/?action=liqpay&phone=' . $abon['PHONE'] . '&acc=' . $abon['ACC'] . '&tov=' . $abon['TOV'] . '&summa=' . $abon['PRICE'] . '&tp=predop&pred_abon=' . $abon['PRED_ABON']
                                );

                                $inline_keyboard = array();
                                $inline_keyboard[][] = $inline_button3;

                                SystemClass::sendMessTextButt($text, $inline_keyboard, $uInfo['chat_id']);
                            } else {
                                $text = $abon['DATE_STOP'] . ' дата поповнення Вашого абонементу. До сплати ' . number_format($abon['PRICE'], 2, '.', '') . 'грн. Гарного вечора.';
                                SystemClass::sendText($text, $uInfo['chat_id']);
                            }
                        }
                    }
                }
            }

            $this->logOperUpdate(1, $repeat ? 1 : 0, 'predprodza3day', 0);
        } elseif ($type_result == 'NO_RESULT') {
            $this->logOperUpdate(1, 0, 'predprodza3day', 0);
            sCron('Немає даних predprodza3day');
        } else {
            $this->logOperUpdate(0, 1, 'predprodza3day', 0);
            sCron('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!');
        }
    }

    function PREDPRODZA1DAY($repeat = 0, $clubId = 0)
    {
        // эта операция сейчас глобальная, без разбивки по клубам
        // clubId принят для совместимости, но не используется

        if ($repeat == 0) {
            $this->logOper(1, 0);
        }

        $socket = new socketB52(HOST_SOCKET, PORT_SOCKET);
        list($status, $type_result, $msg_res) = $socket->GET_CRON_PREDPRODZA1DAY();

        if ($status == 'OK') {
            foreach ($msg_res as $abon) {
                if (!empty($abon['ACC'])) {
                    $sql = 'select chat_id, phone, name, card from spr_acc where acc=' . (int)$abon['ACC'];
                    $userInfo = db_list($sql);

                    if (!empty($userInfo)) {
                        foreach ($userInfo as $uInfo) {
                            if ($abon['GRP'] == 372 || $abon['GRP'] == 373 || $abon['GRP'] == 374 || $abon['GRP'] == 192) {
                                sCron($uInfo);

                                $text = 'Вам потрібно оплатити абонемент (' . $abon['TOV_NAME'] . ')  до ' . $abon['DATE_STOP'] . '. У разі відсутності оплати місце в групі не зберігається.
При бажанні відновити тренування, необхідно звернутись на рецепцію ( зателефонувати або замовити зворотній зв’язок в телеграм боті) для запису у групу, в якій є вільні місця. До сплати ' . number_format($abon['PRICE'], 2, '.', '') . 'грн. Бажаєте оплатити абонемент?';
                                sCron($text);

                                $inline_button3 = array(
                                    "text" => 'Оплатити ' . number_format($abon['PRICE'], 2, '.', '') . 'грн',
                                    "url" => 'https://trystyhii.com.ua/formreg/?action=liqpay&phone=' . $abon['PHONE'] . '&acc=' . $abon['ACC'] . '&tov=' . $abon['TOV'] . '&summa=' . $abon['PRICE'] . '&tp=predop&pred_abon=' . $abon['PRED_ABON']
                                );

                                $inline_keyboard = array();
                                $inline_keyboard[][] = $inline_button3;

                                SystemClass::sendMessTextButt($text, $inline_keyboard, $uInfo['chat_id']);
                            } else {
                                $text = $abon['DATE_STOP'] . ' дата поповнення Вашого абонементу. До сплати ' . number_format($abon['PRICE'], 2, '.', '') . 'грн. Гарного вечора.';
                                SystemClass::sendText($text, $uInfo['chat_id']);
                            }
                        }
                    }
                }
            }

            $this->logOperUpdate(1, $repeat ? 1 : 0, 'predprodza1day', 0);
        } elseif ($type_result == 'NO_RESULT') {
            $this->logOperUpdate(1, 0, 'predprodza1day', 0);
            sCron('Немає даних predprodza1day');
        } else {
            $this->logOperUpdate(0, 1, 'predprodza1day', 0);
            sCron('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!');
        }
    }

    function ABONZA3DAY($repeat = 0, $clubId = 0)
    {
        $clubs = $this->getClubs($clubId);

        foreach ($clubs as $club) {
            $club_id = (int)$club['id'];

            if ($repeat == 0) {
                $this->logOper(1, $club_id);
            }

            $params = array('command' => 'GET_CRON_ABONZA3DAY');
            list($status, $type_result, $msg_res) = send_data_b52($params, $club['ip_club']);

            sCron('GET_CRON_ABONZA3DAY');
            sCron('$club=' . $club['name']);
            sCron($msg_res);

            if ($status == 'OK') {
                foreach ($msg_res as $abon) {
                    if (!empty($abon['ACC'])) {
                        $sql = 'select a.chat_id, a.phone, a.name, a.card
                                from spr_acc a, spr_users u
                                where acc=' . (int)$abon['ACC'] . '
                                  and u.chat_id=a.chat_id
                                  and u.club=' . $club_id;

                        $userInfo = db_list($sql);

                        if (!empty($userInfo)) {
                            foreach ($userInfo as $uInfo) {
                                $text = 'Доброго дня!
Нагадуємо, що дія вашого абонементу закінчується ' . $abon['DATE_STOP'] . '
Зал працює кожного дня:
будні   7.00-23.00
вихідні 8.00-22.00
Гарного настрою!
Чекаємо в RedBarbell ❤';

                                SystemClass::sendText($text, $uInfo['chat_id']);
                            }
                        }
                    }
                }

                $this->logOperUpdate(1, $repeat ? 1 : 0, 'abonaza1day', $club_id);
            } elseif ($type_result == 'NO_RESULT') {
                $this->logOperUpdate(1, 0, 'abonaza1day', $club_id);
                sCron('Немає даних abonaza1day');
            } else {
                $this->logOperUpdate(0, 1, 'abonaza1day', $club_id);
                sCron('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!');
            }
        }
    }

    function BIRTHDAY($repeat = 0, $clubId = 0)
    {
        $clubs = $this->getClubs($clubId);

        foreach ($clubs as $club) {
            $club_id = (int)$club['id'];

            if ($repeat == 0) {
                $this->logOper(1, $club_id);
            }

            $params = array('command' => 'GET_CRON_BIRTHDAY');
            list($status, $type_result, $msg_res) = send_data_b52($params, $club['ip_club']);

            sCron('GET_CRON_BIRTHDAY');
            sCron('$club=' . $club['name']);
            sCron($msg_res);

            if ($status == 'OK') {
                foreach ($msg_res as $abon) {
                    if (!empty($abon['ACC'])) {
                        $sql = 'select a.chat_id, a.phone, a.name, a.card
                                from spr_acc a, spr_users u
                                where acc=' . (int)$abon['ACC'] . '
                                  and u.chat_id=a.chat_id
                                  and u.club=' . $club_id;

                        $userInfo = db_list($sql);

                        if (!empty($userInfo)) {
                            foreach ($userInfo as $uInfo) {
                                $text = 'Вітаємо з Днем народження!
Нехай у житті буде стільки енергії, скільки після найкращого тренування! Сили – як у чемпіона, витримки – як у марафонця, і радості – як після досягнутої мети. Тренуйся, розвивайся, будь щасливим разом із RedBarbell!
';

                                SystemClass::sendText($text, $uInfo['chat_id']);
                            }
                        }
                    }
                }

                $this->logOperUpdate(1, $repeat ? 1 : 0, 'birthday', $club_id);
            } elseif ($type_result == 'NO_RESULT') {
                $this->logOperUpdate(1, 0, 'birthday', $club_id);
                sCron('Немає даних birthday');
            } else {
                $this->logOperUpdate(0, 1, 'birthday', $club_id);
                sCron('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!');
            }
        }
    }

    function NOABON_21_DAY($repeat = 0, $clubId = 0)
    {
        // эта операция сейчас глобальная, без разбивки по клубам
        // clubId принят для совместимости, но не используется

        if ($repeat == 0) {
            $this->logOper(1, 0);
        }

        $socket = new socketB52(HOST_SOCKET, PORT_SOCKET);
        list($status, $type_result, $msg_res) = $socket->GET_CRON_NOABON_21_DAY();

        if ($status == 'OK') {
            foreach ($msg_res as $abon) {
                if (!empty($abon['ACC'])) {
                    $sql = 'select chat_id, phone, name, card from spr_acc where acc=' . (int)$abon['ACC'];
                    $userInfo = db_list($sql);

                    if (!empty($userInfo)) {
                        foreach ($userInfo as $uInfo) {
                            $text = 'Шановний(а) ' . $abon['NAME'] . '!
Вас вітає InterFit 🤗
Ми хочемо переконатися що надаємо Вам відмінні послуги!
На Вас чекає спеціальна пропозиція, кодове слово #сонечко
Дякуємо, що Ви з нами.';

                            SystemClass::sendText($text, $uInfo['chat_id']);
                        }
                    }
                }
            }

            $this->logOperUpdate(1, $repeat ? 1 : 0, 'noabon21day', 0);
        } elseif ($type_result == 'NO_RESULT') {
            $this->logOperUpdate(1, 0, 'noabon21day', 0);
            sCron('Немає даних noabon21day');
        } else {
            $this->logOperUpdate(0, 1, 'noabon21day', 0);
            sCron('На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!');
        }
    }

    function sendMessText($textMessage, $inlineButtons = [], $chat_id = '')
    {
        $keyboard = array("inline_keyboard" => $inlineButtons);
        $buttons_munu = json_encode($keyboard);

        $arrayQuery = array(
            'chat_id'      => $chat_id,
            'text'         => $textMessage,
            'parse_mode'   => "html",
            'reply_markup' => $buttons_munu,
        );

        SystemClass::TG_sendMessage($arrayQuery);
    }
}
?>