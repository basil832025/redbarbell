<?php

class report_registered_clients
{
    private $admin;
    private $ipClub;
    private $dateFrom;
    private $dateTo;
    private $type;
    private $displayMode;
    private $submitted;

    public function __construct()
    {
        $this->admin = (int)($_GET['admin'] ?? $_POST['admin'] ?? 0);
        $this->ipClub = trim((string)($_GET['ip_club'] ?? $_POST['ip_club'] ?? ''));
        $this->dateFrom = $this->validDate($_GET['date_from'] ?? $_POST['date_from'] ?? '') ?: date('Y-m-01');
        $this->dateTo = $this->validDate($_GET['date_to'] ?? $_POST['date_to'] ?? '') ?: date('Y-m-d');
        $type = (string)($_GET['client_type'] ?? $_POST['client_type'] ?? 'all');
        $this->type = in_array($type, ['all', 'new', 'existing'], true) ? $type : 'all';
        $displayMode = (string)($_GET['display_mode'] ?? $_POST['display_mode'] ?? 'table');
        $this->displayMode = in_array($displayMode, ['table', 'cards'], true) ? $displayMode : 'table';
        $this->submitted = isset($_GET['show_report']);
        // Должности 2 и 3 бачать тільки свій клуб, навіть якщо підмінити параметр запиту.
        if (in_array($this->admin, [2, 3], true)) {
            $this->ipClub = trim((string)($_GET['ip_club'] ?? $_POST['ip_club'] ?? ''));
        }
    }

    public function init()
    {
        $clubs = db()->query('SELECT id, name, ip_club FROM spr_clubs WHERE active = 1 ORDER BY name') ? db()->fetchAll() : [];
        if ($this->admin === 1 && $this->ipClub === '' && $clubs) $this->ipClub = (string)$clubs[0]['ip_club'];
        if (in_array($this->admin, [2, 3], true) && $this->ipClub === '') {
            echo '<p class="alert alert-danger">Не вдалося визначити клуб користувача.</p>';
            return;
        }

        $rows = $this->submitted ? $this->getRows() : [];
        echo $this->render($clubs, $rows);
    }

    private function validDate($value)
    {
        $value = trim((string)$value);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return '';
        [$year, $month, $day] = array_map('intval', explode('-', $value));
        return checkdate($month, $day, $year) ? $value : '';
    }

    private function getRows()
    {
        $params = ['date_from' => $this->dateFrom, 'date_to' => $this->dateTo];
        $clubWhere = '';
        if ($this->ipClub !== '') {
            $clubWhere = ' AND c.ip_club = :ip_club';
            $params['ip_club'] = $this->ipClub;
        }
        $rows = [];
        if ($this->type !== 'existing') {
            $sql = 'SELECT c.name AS club_name, r.surname, r.name, r.lastname, NULL AS full_name, r.phone, r.birthday, r.sex, r.type_doc, r.cer, r.number, r.lead_source, r.date_reg AS registered_at, "Новий" AS client_type
                    FROM bs_reg_new_users r LEFT JOIN spr_clubs c ON c.id = r.club
                    WHERE r.date_reg >= :date_from AND r.date_reg < DATE_ADD(:date_to, INTERVAL 1 DAY)'.$clubWhere.'
                    ORDER BY r.date_reg DESC';
            if (db()->query($sql, $params)) $rows = array_merge($rows, db()->fetchAll());
        }
        if ($this->type !== 'new') {
            $sql = 'SELECT DISTINCT c.name AS club_name, a.name AS full_name, a.phone, NULL AS surname, NULL AS name, NULL AS lastname, NULL AS birthday, NULL AS sex, NULL AS type_doc, NULL AS cer, NULL AS number, NULL AS lead_source, u.is_reg AS registered_at, "Існуючий" AS client_type
                    FROM spr_users u JOIN spr_acc a ON a.chat_id = u.chat_id
                    LEFT JOIN spr_clubs c ON c.id = u.club
                    WHERE u.is_reg >= :date_from AND u.is_reg < DATE_ADD(:date_to, INTERVAL 1 DAY)'.$clubWhere.'
                    AND NOT EXISTS (SELECT 1 FROM bs_reg_new_users r WHERE r.phone = a.phone AND r.club = u.club)
                    ORDER BY u.is_reg DESC';
            if (db()->query($sql, $params)) $rows = array_merge($rows, db()->fetchAll());
        }
        usort($rows, static function ($a, $b) { return strcmp((string)$b['registered_at'], (string)$a['registered_at']); });
        return $rows;
    }

    private function render($clubs, $rows)
    {
        $esc = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
        $html = '<!doctype html><html lang="uk"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link href="css/bootstrap.min.css" rel="stylesheet"><title>Зареєстровані клієнти в боті</title></head><body><div class="container my-4">';
        $html .= '<h4 class="mb-3">Зареєстровані клієнти в боті</h4><button class="btn btn-primary mb-3" type="button" data-bs-toggle="modal" data-bs-target="#reportParams">Параметри звіту</button>';
        $html .= '<div class="modal fade" id="reportParams" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form method="get"><div class="modal-header"><h5 class="modal-title">Виберіть параметри звіту</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрити"></button></div><div class="modal-body"><input type="hidden" name="action" value="report_registered_clients"><input type="hidden" name="admin" value="'.$this->admin.'"><input type="hidden" name="show_report" value="1">';
        if ($this->admin === 1) {
            $html .= '<div class="col-md-3"><label class="form-label">Клуб</label><select name="ip_club" class="form-select">';
            foreach ($clubs as $club) {
                $selected = (string)$club['ip_club'] === $this->ipClub ? ' selected' : '';
                $html .= '<option value="'.$esc($club['ip_club']).'"'.$selected.'>'.$esc(str_replace('REDBARBELL', '', $club['name'])).'</option>';
            }
            $html .= '</select></div>';
        } else {
            $html .= '<input type="hidden" name="ip_club" value="'.$esc($this->ipClub).'">';
        }
        $html .= '<div class="mb-3"><label class="form-label">Період з</label><input type="date" name="date_from" class="form-control" value="'.$esc($this->dateFrom).'" required></div><div class="mb-3"><label class="form-label">Період по</label><input type="date" name="date_to" class="form-control" value="'.$esc($this->dateTo).'" required></div><div class="mb-3"><label class="form-label">Тип клієнта</label><select name="client_type" class="form-select">';
        foreach (['all' => 'Усі', 'new' => 'Лише нові', 'existing' => 'Лише існуючі'] as $value => $label) {
            $html .= '<option value="'.$value.'"'.($this->type === $value ? ' selected' : '').'>'.$label.'</option>';
        }
        $html .= '</select></div><div class="mb-3"><label class="form-label">Режим відображення</label><select name="display_mode" class="form-select"><option value="table"'.($this->displayMode === 'table' ? ' selected' : '').'>Таблиця</option><option value="cards"'.($this->displayMode === 'cards' ? ' selected' : '').'>Картки</option></select></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button><button class="btn btn-primary">Сформувати звіт</button></div></form></div></div></div>';
        if ($this->submitted) $html .= '<p class="text-muted">Період: '.$esc($this->dateFrom).' — '.$esc($this->dateTo).'</p>';
        if ($this->submitted) {
            if ($this->displayMode === 'cards') {
                $html .= '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">';
            } else {
                $html .= '<div class="table-responsive"><table class="table table-bordered table-sm align-middle"><thead class="table-light"><tr><th>Тип клієнта</th><th>Клуб</th><th>Прізвище</th><th>Ім’я</th><th>По батькові</th><th>Телефон</th><th>Дата народження</th><th>Вік</th><th>Стать</th><th>Тип документа</th><th>Серія</th><th>Номер документа</th><th>Рекламне джерело</th><th>Дата реєстрації в боті</th></tr></thead><tbody>';
            }
            foreach ($rows as $row) {
                $birthday = trim((string)($row['birthday'] ?? ''));
                $age = '';
                if ($birthday !== '' && $birthday !== '0000-00-00' && $birthday !== '00.00.0000') {
                    try { $age = (new DateTime($birthday))->diff(new DateTime('today'))->y; } catch (Exception $e) { $age = ''; }
                }
                $sex = ['1' => 'Чоловік', '2' => 'Жінка'][(string)($row['sex'] ?? '')] ?? '';
                $document = ['1' => 'Паспорт', '2' => 'ID-картка', '3' => 'ІПН'][(string)($row['type_doc'] ?? '')] ?? '';
                $source = ['nearby' => 'Побачили зал / живете поруч', 'social' => 'Соцмережі', 'city_ads' => 'Реклама у місті (банери, ліфти)', 'mall_radio' => 'Реклама у ТЦ / радіо'][(string)($row['lead_source'] ?? '')] ?? '';
                $surname = $row['surname'] ?? '';
                $firstName = $row['name'] ?? '';
                $lastName = $row['lastname'] ?? '';
                if (($surname.$firstName.$lastName) === '') $surname = $row['full_name'] ?? '';
                if ($this->displayMode === 'cards') {
                    $fields = ['Тип клієнта' => $row['client_type'] ?? '', 'Клуб' => $row['club_name'] ?? '', 'Прізвище' => $surname, 'Ім’я' => $firstName, 'По батькові' => $lastName, 'Телефон' => $row['phone'] ?? '', 'Дата народження' => $birthday, 'Вік' => $age, 'Стать' => $sex, 'Тип документа' => $document, 'Серія' => $row['cer'] ?? '', 'Номер документа' => $row['number'] ?? '', 'Рекламне джерело' => $source, 'Дата реєстрації в боті' => $row['registered_at'] ?? ''];
                    $html .= '<div class="col"><div class="card shadow-sm h-100"><div class="card-body"><h6 class="card-title fw-bold">'.$esc(trim($surname.' '.$firstName.' '.$lastName)).'</h6><dl class="row mb-0">';
                    foreach ($fields as $label => $value) $html .= '<dt class="col-6">'.$esc($label).'</dt><dd class="col-6">'.$esc($value).'</dd>';
                    $html .= '</dl></div></div></div>';
                } else {
                    $html .= '<tr><td>'.$esc($row['client_type'] ?? '').'</td><td>'.$esc($row['club_name'] ?? '').'</td><td>'.$esc($surname).'</td><td>'.$esc($firstName).'</td><td>'.$esc($lastName).'</td><td>'.$esc($row['phone'] ?? '').'</td><td>'.$esc($birthday).'</td><td>'.$esc($age).'</td><td>'.$esc($sex).'</td><td>'.$esc($document).'</td><td>'.$esc($row['cer'] ?? '').'</td><td>'.$esc($row['number'] ?? '').'</td><td>'.$esc($source).'</td><td>'.$esc($row['registered_at'] ?? '').'</td></tr>';
                }
            }
            if (!$rows) {
                $emptyMessage = 'За вибраний період даних немає.';
                $html .= $this->displayMode === 'cards' ? '<div class="col-12 text-center py-3">'.$emptyMessage.'</div>' : '<tr><td colspan="14" class="text-center">'.$emptyMessage.'</td></tr>';
            }
            $html .= $this->displayMode === 'cards' ? '</div>' : '</tbody></table></div>';
            $html .= '<p class="text-muted mt-2">Усього: '.count($rows).'</p>';
        }
        $html .= '</div><script src="js/bootstrap.bundle.min.js"></script></body></html>';
        return $html;
    }
}
