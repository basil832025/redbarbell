<?php
function db(): Database {
    return Database::getInstance();
}

class Database {
private static $instance = null;
private $pdo;
private $stmt;
private $errorLogPath = __DIR__ . '/db_errors.log';

private $host = DB_HOST;
private $dbname = DB_NAME;
private $username = DB_USER;
private $password = DB_PASS;

private function __construct() {
try {
$dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
$this->pdo = new PDO($dsn, $this->username, $this->password, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
]);
} catch (PDOException $e) {
$this->logError("Connection error: " . $e->getMessage());
die("Ошибка подключения к базе данных.");
}
}

public static function getInstance() {
if (self::$instance === null) {
self::$instance = new self();
}
return self::$instance;
}

public function query($sql, $params = []) {
try {
$this->stmt = $this->pdo->prepare($sql);
foreach ($params as $key => $value) {
$this->stmt->bindValue(':' . $key, $value);
}
return $this->stmt->execute();
} catch (PDOException $e) {
$this->logError("Query error: " . $e->getMessage() . " | SQL: $sql | Params: " . json_encode($params));
return false;
}
}

public function insert($table, $data) {
$columns = implode(', ', array_keys($data));
$placeholders = ':' . implode(', :', array_keys($data));
$sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
return $this->query($sql, $data);
}

public function update($table, $data, $where, $params = []) {
$setPart = implode(', ', array_map(fn($key) => "$key = :set_$key", array_keys($data)));
$updateParams = [];
foreach ($data as $key => $value) {
$updateParams["set_$key"] = $value;
}
$sql = "UPDATE $table SET $setPart WHERE $where";
return $this->query($sql, array_merge($updateParams, $params));
}

public function delete($table, $where, $params = []) {
$sql = "DELETE FROM $table WHERE $where";
return $this->query($sql, $params);
}

public function selectOne($table, $where, $params = []) {
$sql = "SELECT * FROM $table WHERE $where LIMIT 1";
$this->query($sql, $params);
return $this->fetch();
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
$this->query($sql, $params);
return $this->fetchAll();
}

public function fetchAll() {
return $this->stmt ? $this->stmt->fetchAll() : [];
}

public function fetch() {
return $this->stmt ? $this->stmt->fetch() : null;
}

public function lastInsertId() {
return $this->pdo->lastInsertId();
}

private function logError($message) {
$date = date('Y-m-d H:i:s');
s($message);
}
}
/*
 Подключение
 $db = Database::getInstance();
Использует константы DB_HOST, DB_NAME, DB_USER, DB_PASS (определи в config.php или другом файле)

 Метод query($sql, $params = [])
Универсальный метод выполнения любого SQL-запроса.
$db->query("DELETE FROM users WHERE id = :id", ['id' => 1]);

Метод insert($table, $data)
Добавление строки в таблицу.
$db->insert('users', [
    'name' => 'Alice',
    'email' => 'alice@example.com'
]);

Метод update($table, $data, $where, $params = [])
Обновление строки с параметрами WHERE.
$db->update('users',
    ['status' => 'inactive'],
    'email = :email',
    ['email' => 'alice@example.com']
);

Метод delete($table, $where, $params = [])
Удаление строк по условию.
$db->delete('users', 'status = :status', ['status' => 'inactive']);

Метод selectOne($table, $where, $params = [])
Получение одной строки (первой найденной).

$user = $db->selectOne('users', 'id = :id', ['id' => 5]);

Метод selectAll($table, $where = '',  $params = [], $options = [],)
Получение списка строк с возможностью сортировки и пагинации.

Пример с фильтрацией и сортировкой:

$users = $db->selectAll('users',
    'status = :status',
    ['status' => 'active']
    ['orderBy' => 'created_at', 'orderDir' => 'DESC', 'limit' => 10, 'offset' => 0],
);
Метод fetch() и fetchAll()
Для получения результата после вызова query() вручную.
$db->query("SELECT * FROM users WHERE id = :id", ['id' => 1]);
$user = $db->fetch();

Метод lastInsertId()
Возвращает ID последней вставленной строки.
$id = $db->lastInsertId();



$db = Database::getInstance();

// Вставка
$db->insert('products', [
    'title' => 'MacBook Air',
    'price' => 1299
]);

// Обновление
$db->update('products', ['price' => 1199], 'title = :title', ['title' => 'MacBook Air']);

// Получение одной записи
$product = $db->selectOne('products', 'price < :max', ['max' => 2000]);

// Удаление
$db->delete('products', 'price < :min', ['min' => 500]);


 * */