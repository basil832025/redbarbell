<?php

class report_lead_sources
{
    private const SOURCES = [
        'nearby' => 'Побачили зал / живете поруч',
        'social' => 'Соцмережі',
        'city_ads' => 'Реклама у місті (банери, ліфти)',
        'mall_radio' => 'Реклама у ТЦ / радіо',
        '' => 'Не вказано',
    ];

    private $admin;
    private $ipClub;
    private $dateFrom;
    private $dateTo;

    public function __construct()
    {
        $this->admin = (int)($_GET['admin'] ?? $_POST['admin'] ?? 0);
        $this->ipClub = trim((string)($_GET['ip_club'] ?? $_POST['ip_club'] ?? ''));
        $this->dateFrom = $this->validDate($_GET['date_from'] ?? $_POST['date_from'] ?? '') ?: date('Y-m-01');
        $this->dateTo = $this->validDate($_GET['date_to'] ?? $_POST['date_to'] ?? '') ?: date('Y-m-d');
    }

    public function init()
    {
        echo $this->render($this->getRows($this->ipClub));
    }

    private function validDate($value)
    {
        $value = trim((string)$value);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return '';
        [$year, $month, $day] = array_map('intval', explode('-', $value));
        return checkdate($month, $day, $year) ? $value : '';
    }

    private function getRows($ipClub)
    {
        $where = ['r.date_reg >= :date_from', 'r.date_reg < DATE_ADD(:date_to, INTERVAL 1 DAY)'];
        $params = ['date_from' => $this->dateFrom, 'date_to' => $this->dateTo];
        if ($ipClub !== '') {
            $where[] = 'c.ip_club = :ip_club';
            $params['ip_club'] = $ipClub;
        }

        $sql = 'SELECT c.id AS club_id, c.name AS club_name, r.lead_source, COUNT(*) AS total
                FROM bs_reg_new_users r
                LEFT JOIN spr_clubs c ON c.id = r.club
                WHERE '.implode(' AND ', $where).'
                GROUP BY c.id, c.name, r.lead_source
                ORDER BY c.name, r.lead_source';
        return db()->query($sql, $params) ? db()->fetchAll() : [];
    }

    private function render($rows)
    {
        $locations = [];
        $totals = array_fill_keys(array_keys(self::SOURCES), 0);
        foreach ($rows as $row) {
            $source = array_key_exists((string)$row['lead_source'], self::SOURCES) ? (string)$row['lead_source'] : '';
            $clubId = (string)($row['club_id'] ?? '0');
            if (!isset($locations[$clubId])) {
                $locations[$clubId] = ['name' => $row['club_name'] ?: 'Локація не вказана', 'values' => array_fill_keys(array_keys(self::SOURCES), 0)];
            }
            $count = (int)$row['total'];
            $locations[$clubId]['values'][$source] += $count;
            $totals[$source] += $count;
        }

        $esc = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
        $html = '<!doctype html><html lang="uk"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="css/bootstrap.min.css" rel="stylesheet"><title>Звіт по джерелах</title></head><body><div class="container my-4">';
        $html .= '<h4 class="mb-3">📣 Звідки клієнти дізналися про нас</h4><form method="get" class="row g-2 mb-4"><input type="hidden" name="action" value="report_lead_sources"><input type="hidden" name="admin" value="'.$esc($this->admin).'"><input type="hidden" name="ip_club" value="'.$esc($this->ipClub).'">';
        $html .= '<div class="col-sm-4"><label class="form-label">Від</label><input type="date" name="date_from" class="form-control" value="'.$esc($this->dateFrom).'" required></div><div class="col-sm-4"><label class="form-label">До</label><input type="date" name="date_to" class="form-control" value="'.$esc($this->dateTo).'" required></div><div class="col-sm-4 d-flex align-items-end"><button class="btn btn-primary w-100">Показати</button></div></form>';
        $html .= '<p class="text-muted">Період: '.$esc($this->dateFrom).' — '.$esc($this->dateTo).'</p><div class="table-responsive"><table class="table table-bordered table-sm align-middle"><thead class="table-light"><tr><th>Локація</th>';
        foreach (self::SOURCES as $label) $html .= '<th class="text-end">'.$esc($label).'</th>';
        $html .= '<th class="text-end">Всього</th></tr></thead><tbody>';
        foreach ($locations as $location) {
            $html .= '<tr><td>'.$esc($location['name']).'</td>';
            foreach ($location['values'] as $value) $html .= '<td class="text-end">'.number_format($value, 0, ',', ' ').'</td>';
            $html .= '<td class="text-end fw-bold">'.number_format(array_sum($location['values']), 0, ',', ' ').'</td></tr>';
        }
        if (!$locations) $html .= '<tr><td colspan="'.(count(self::SOURCES) + 2).'" class="text-center">За вибраний період даних немає.</td></tr>';
        $html .= '</tbody><tfoot class="table-light fw-bold"><tr><td>Всього</td>';
        foreach ($totals as $value) $html .= '<td class="text-end">'.number_format($value, 0, ',', ' ').'</td>';
        $html .= '<td class="text-end">'.number_format(array_sum($totals), 0, ',', ' ').'</td></tr></tfoot></table></div></div></body></html>';
        return $html;
    }
}
