<?php
function db(): Database {
    return Database::getInstance();
}
// Helper-функция для проверки ассоциативности массива
function is_assoc(array $arr) {
    if ([] === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}
class Database {
private static $instance = null;
private $mysqli;
private $stmt;
private $errorLogPath = __DIR__ . '/db_errors.log';

private $host = DB_HOST;
private $dbname = DB_NAME;
private $username = DB_USER;
private $password = DB_PASS;

private function __construct() {
$this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->dbname);

if ($this->mysqli->connect_error) {
$this->logError("Connection failed: " . $this->mysqli->connect_error);
die("Ошибка подключения к базе данных.");
}

$this->mysqli->set_charset("utf8mb4");
}

public static function getInstance() {
if (self::$instance === null) {
self::$instance = new self();
}
return self::$instance;
}

    public function query($sql, $params = []) {
        // Обработка именованных параметров
        if (!empty($params) && is_assoc($params)) {
            $sqlParams = [];
            foreach ($params as $key => $value) {
                $sql = preg_replace('/:' . preg_quote($key, '/') . '\b/', '?', $sql);
                $sqlParams[] = $value;
            }
            $params = $sqlParams;
        }

        $this->stmt = $this->mysqli->prepare($sql);
        if (!$this->stmt) {
            $this->logError("❌ Prepare failed: {$this->mysqli->error} [SQL: $sql]");
            return false;
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            if (!@$this->stmt->bind_param($types, ...$params)) {
                $this->logError("❌ Bind param failed: {$this->stmt->error}");
                return false;
            }
        }

        if (!$this->stmt->execute()) {
            $this->logError("❌ Execute failed: {$this->stmt->error} [SQL: $sql]");
            return false;
        }

        return true;
    }

    public function insertOrUpdate($table, $data, $updateFields = []) {
        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $key => $value) {
            $columns[] = $key;
            if (is_array($value) && isset($value['RAW'])) {
                $placeholders[] = $value['RAW'];
            } else {
                $placeholders[] = '?';
                $params[] = $value;
            }
        }

        $updates = [];
        foreach ($updateFields as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field]) && isset($data[$field]['RAW'])) {
                    $updates[] = "$field = {$data[$field]['RAW']}";
                } else {
                    $updates[] = "$field = VALUES($field)";
                }
            }
        }

        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")
            ON DUPLICATE KEY UPDATE " . implode(', ', $updates);

        return $this->query($sql, $params);
    }
 public function insert($table, $data) {
        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $key => $value) {
            $columns[] = $key;
            if (is_array($value) && isset($value['RAW'])) {
                $placeholders[] = $value['RAW'];
            } else {
                $placeholders[] = '?';
                $params[] = $value;
            }
        }

        $columnsStr = implode(', ', $columns);
        $placeholdersStr = implode(', ', $placeholders);
        $sql = "INSERT INTO $table ($columnsStr) VALUES ($placeholdersStr)";

        return $this->query($sql, $params);
    }

    public function update($table, $data, $where, $params = []) {
        $setParts = [];
        $updateParams = [];

        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value['RAW'])) {
                $setParts[] = "$key = {$value['RAW']}";
            } else {
                $setParts[] = "$key = ?";
                $updateParams[] = $value;
            }
        }

        $sql = "UPDATE $table SET " . implode(', ', $setParts) . " WHERE $where";
      //  s($sql);
        return $this->query($sql, array_merge($updateParams, $params));
    }

public function delete($table, $where, $params = []) {
$sql = "DELETE FROM $table WHERE $where";
return $this->query($sql, $params);
}

    public function selectOne($table, $where, $params = []) {
        $sql = "SELECT * FROM $table WHERE $where LIMIT 1";
        if ($this->query($sql, $params)) {
        //    s('selectOne='.$sql);
            $results = $this->fetchAll();
            return $results[0] ?? null;
        }
        return null;
    }

    public function selectAll($table, $where = '',  $params = [], $options = []) {
        $sql = "SELECT * FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        if (!empty($options['orderBy'])) {
            $direction = strtoupper($options['orderDir'] ?? 'ASC');
            $sql .= " ORDER BY {$options['orderBy']} " . ($direction === 'DESC' ? 'DESC' : 'ASC');
        }
        if (!empty($options['limit'])) {
            $sql .= " LIMIT " . intval($options['limit']);
            if (!empty($options['offset'])) {
                $sql .= " OFFSET " . intval($options['offset']);
            }
        }

        if ($this->query($sql, $params)) {
            return $this->fetchAll();
        }
        return [];
    }
    public function fetch() {
        $result = $this->fetchAll();
        return $result[0] ?? null;
    }
    public function fetchAll_old() {
        $results = [];
        if ($this->stmt) {
            $meta = $this->stmt->result_metadata();
            if (!$meta) return [];

            $row = [];
            $refs = [];

            while ($field = $meta->fetch_field()) {
                $name = $field->name ?: uniqid('field_');
                $row[$name] = null;
                $refs[] = &$row[$name];
            }

            if (!call_user_func_array([$this->stmt, 'bind_result'], $refs)) {
                $this->logError("❌ bind_result не выполнен.");
                return [];
            }

            // ✅ Копируем результат вручную в каждый проход
            while ($this->stmt->fetch()) {
                $results[] = array_map(fn($v) => $v, $row); // или: $results[] = $row + [];
            }
        }
        return $results;
    }

    public function fetchAll(): array
    {
        return iterator_to_array($this->fetchGenerator());
    }

    public function fetchGenerator(): \Generator
    {
        $stmt = $this->stmt;
        if (! $stmt) {
            return;
        }

        // buffer metadata only, not the whole result set
        $stmt->store_result();

        // get column names
        $meta = $stmt->result_metadata();
        $columns = [];
        while ($field = $meta->fetch_field()) {
            $columns[] = $field->name;
        }
        $meta->free();

        // bind each column to $row[key]
        $row = [];
        $refs = [];
        foreach ($columns as $col) {
            $row[$col] = null;
            $refs[]    = &$row[$col];
        }
        $stmt->bind_result(...$refs);

        // fetch one row at a time
        while ($stmt->fetch()) {
            // yield a _copy_ of the row array
            yield array_map(fn($v) => $v, $row);
        }

        $stmt->free_result();
    }





public function lastInsertId() {
return $this->mysqli->insert_id;
}
public function markFieldByIds(string $table, string $field, $value, array $ids, string $extraWhere = '', array $extraParams = []): bool {
        if (empty($ids)) return false;

        // Побудова плейсхолдерів ?,?,?
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Основний SQL
        $sql = "UPDATE $table SET $field = ? WHERE id IN ($placeholders)";

        // Додаткові умови (наприклад: AND user_id = ?)
        if (!empty($extraWhere)) {
            $sql .= " AND ($extraWhere)";
        }

        // Формуємо параметри: [значення для оновлення, IDшники..., додаткові]
        $params = array_merge([$value], $ids, $extraParams);

        return $this->query($sql, $params);
    }
private function selectOnelogError($message) {
$date = date('Y-m-d H:i:s');
s($message);
}
}
/*
 Подключение
 $db = Database::getInstance();
или
db();
Использует константы DB_HOST, DB_NAME, DB_USER, DB_PASS (определи в config.php или другом файле)

 Метод query($sql, $params = [])
Универсальный метод выполнения любого SQL-запроса.
$db->query("DELETE FROM users WHERE id = :id", ['id' => 1]);

Метод insert($table, $data)
Добавление строки в таблицу.
db()->insert('users', [
    'name' => 'Alice',
    'email' => 'alice@example.com'
]);

Метод update($table, $data, $where, $params = [])
Обновление строки с параметрами WHERE.
db()->update('users',
    ['status' => 'inactive'],
    'email = :email',
    ['email' => 'alice@example.com']
);

Метод delete($table, $where, $params = [])
Удаление строк по условию.
db()->delete('users', 'status = :status', ['status' => 'inactive']);

Метод selectOne($table, $where, $params = [])
Получение одной строки (первой найденной).

$user = db()->selectOne('users', 'id = :id', ['id' => 5]);

Метод selectAll($table, $where = '',  $params = [], $options = [],)
Получение списка строк с возможностью сортировки и пагинации.

Пример с фильтрацией и сортировкой:

$users = db()->selectAll('users',
    'status = :status',
    ['status' => 'active'],
    ['orderBy' => 'created_at', 'orderDir' => 'DESC', 'limit' => 10, 'offset' => 0]
);
Метод fetch() и fetchAll()
Для получения результата после вызова query() вручную.
db()->query("SELECT * FROM users WHERE id = :id", ['id' => 1]);
$user = db()->fetch();

Метод lastInsertId()
Возвращает ID последней вставленной строки.
$id = db()->lastInsertId();



//$db = Database::getInstance();

// Вставка
db()insert('products', [
    'title' => 'MacBook Air',
    'price' => 1299
]);
// если нужно передать now() в базу
$db->insert('logs', [
    'user_id' => 5,
    'created_at' => ['RAW' => 'NOW()']
]);

// Обновление
db()update('products', ['price' => 1199], 'title = :title', ['title' => 'MacBook Air']);

// Получение одной записи
$product = db()selectOne('products', 'price < :max', ['max' => 2000]);

// Удаление
db()delete('products', 'price < :min', ['min' => 500]);

markFieldByIds(), який:
✅ Працює з будь-якою таблицею
✅ Оновлює будь-яке поле (is_read, status, active і т.д.)
✅ Працює по масиву id
🧠 Підтримує додаткові умови через WHERE (наприклад, AND user_id = ?)
Просте використання (is_read = 0):
db()->markFieldByIds('bs_chats', 'is_read', 0, [101, 102]);
 Із додатковою умовою:
db()->markFieldByIds(
    'bs_chats',
    'is_read',
    0,
    [101, 102],
    'chat_id_klient = ? AND who_user_write != ?',
    [$chat_id, $other_user_id]
);

// insert or update
db()->insertOrUpdate('club_feedbacks', [
    'id' => 1,
    'name' => 'Иван',
    'email' => 'ivan@example.com',
    'rating' => 5,
    'message' => 'Отличный клуб!',
    'created_at' => ['RAW' => 'NOW()']
], ['name', 'email', 'rating', 'message', 'created_at']);

или
Предположим, в таблице club_feedbacks ты хочешь, чтобы один пользователь
(email) мог оставить один отзыв на каждый клуб (club_id), но не более. а ID AUTO_INCREMENT
db()->insertOrUpdate('club_feedbacks', [
    'name' => 'Иван',
    'email' => 'ivan@example.com',
    'club_id' => 5,
    'rating' => 4,
    'message' => 'Обновлённый отзыв!',
    'created_at' => ['RAW' => 'NOW()']
], ['name', 'rating', 'message', 'created_at']);
 * */