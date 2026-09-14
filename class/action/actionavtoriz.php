<?php
class actionAvtoriz extends ActionModule
{
function __construct() {
$this->arrDataAnswer = SystemClass::getarrDataAnswer();
}

function init() {

$contact = $this->arrDataAnswer["message"]["contact"]["phone_number"] ?? '';
if (!empty($contact)) {
$phone = str_replace("+", "", $contact);
if (strlen($phone) >= 10) {

//$username = $this->arrDataAnswer["message"]["chat"]["username"] ?? '';
//$name = $this->arrDataAnswer["message"]["contact"]["first_name"] ?? '';
$ChatId = $this->arrDataAnswer["message"]["chat"]["id"];


    $name = strip4byte($this->arrDataAnswer["message"]["contact"]["first_name"] ?? '');
    $username = strip4byte($this->arrDataAnswer["message"]["chat"]["username"] ?? '');
    if (trim($name) === '') $name = 'Telegram user';
//$user = db()->selectOne('spr_users', 'chat_id = ?', [$ChatId]);
//    db()->query('SELECT * FROM spr_users WHERE chat_id = ?', [$ChatId]);
    $sql = ' select chat_id from spr_users where chat_id=' . $ChatId . ' limit 1';
    $user = db_field($sql, 'chat_id');

 //   $user = db()->fetch();

if (!$user) {
db()->insert('spr_users', [
'chat_id'     => $ChatId,
'phone'       => $phone,
'name'        => $name,
'username'    => $username,
'date_create' => ['RAW' => 'NOW()']
]);
}

actionmodule::isSignIn();
sLog('STARTAVTOR');
slog('command=AccZapis');

if (SystemClass::$ip_club) {
$StatusReadAcc = actionmodule::readAccounts(0);

if ($StatusReadAcc == 'OK' || $StatusReadAcc == 'OFFLINE') {
if (SystemClass::$cntAccounts > 1 && SystemClass::$activeAccount == 0) {
actionmodule::setActiveAcc(0);
} else {
$unixTime = time();
$isNoWait = true;

if (actionmodule::$LastTimeOper > $unixTime && actionmodule::$LastOper['status'] == 'NO_INTERNET') {
$isNoWait = false;
}

if ($isNoWait) {
// Авторизация успешна, можно отправить приветствие
// $this->send('Вітаю ви авторизувалися! Можемо працювати!');
}
}
} elseif ($StatusReadAcc == 'NO_RESULT') {
$this->send('Щоб зареєструватися в боті, натисніть кнопку "Зареєструватися в боті" та заповніть форму вашими персональними даними!', 2);
}
} else {
$this->send('Щоб зареєструватися в боті, натисніть кнопку "Зареєструватися в боті" та заповніть форму вашими персональними даними!', 2);
}
} else {
$this->send('У Вас не вийшло відправити контакт. (Номер телефону менше 10 цифр) Попробуйте ще!');
}
} else {
$this->send('У Вас не вийшло відправити контакт. Попробуйте ще!');
}
}

function send($text, $butt = 1) {
$ObjBut = new ButtonModule();

if ($butt == 1) {
SystemClass::$Button = $ObjBut->buttonNoAvtoriz();
} elseif ($butt == 2) {
SystemClass::$Button = $ObjBut->buttonRegistr();
} else {
SystemClass::$Button = $ObjBut->buttonAvtoriz();
}

SystemClass::$arrayQuery = [
'chat_id'      => SystemClass::getChatId(),
'text'         => $text,
'parse_mode'   => "html",
'reply_markup' => SystemClass::$Button,
];

SystemClass::TG_sendMessage(SystemClass::$arrayQuery);
}
}
function strip4byte($s) {
    if ($s === null) return '';
    // убираем все символы вне BMP (туда входят большинство эмодзи)
    return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $s);
}