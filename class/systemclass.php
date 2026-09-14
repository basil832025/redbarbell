<?php

class SystemClass
{
	private static $isAvtoris = true;  // авторизирован ли пользователь
	private static $arrDataAnswer = array();  // авторизирован ли пользователь
	public static $DataСallback = '';  // авторизирован ли пользователь
	public static $cntAccounts = 1;  // количество профилей
	public static $activeAccount = 0;  // активный профиль
	public static $buttonVidprac = 0;  // активный кнопка отработки
//	public static $cntVidprac = 0;  // сколько осталось отработок
    private static $aPhoto = '';  // масив фото
    public static $admin = 0;  //клиент есть админом
	public static $activeSotr = 0;  //клиент есть сотрудником
    public static   $resultReturn = '';  // авторизирован ли пользователь
	private static $textMessage = '';  // авторизирован ли пользователь
    public static $chatId = 0;  // авторизирован ли пользователь
	public static $Button = '';  // авторизирован ли пользователь
	public static $textMessage_bot = '';  // авторизирован ли пользователь
	public static $arrayQuery = array();  // авторизирован ли пользователь
	public static $typeReturn = 'text';  // авторизирован ли пользователь
	public static $ip_club = '';  // ip по клубам
	public static $chat_admin = '';  // ip по клубам
	public static $chat_group = '';  // ip по клубам
    public static $messageID = 0;  // авторизирован ли пользователь
    public static $club= 0;  // авторизирован ли пользователь
    public static $phone= '';  // авторизирован ли пользователь
    public static $id= '';  // id клиента spr_users
    public function __construct()
    {
       
    //   self::$chatId = '2147483647'; // Милева мария 
       
    }
    public static function getPhone() {
        return self::$phone ?? null;
    }
    public function init($data='')
    {

        self::$arrDataAnswer = $data;
         //форма реєстрації
        if (!empty($_POST['action']) && $_POST['action']=='new_reg_user'){
            self::$arrDataAnswer = $_POST;
            self::$arrDataAnswer["message"]["text"]='/new_reg_user';
            self::$arrDataAnswer["message"]["chat"]["id"]=$_POST['user_id'];
        }
        // форма відгук
        if (!empty($_POST['action']) && $_POST['action']=='add_vidguk'){
            self::$arrDataAnswer = $_POST;
            self::$arrDataAnswer["message"]["text"]='/add_vidguk';
            self::$arrDataAnswer["message"]["chat"]["id"]=$_POST['user_id'];
        }
        if (!empty($_POST['action']) && $_POST['action']=='add_vacancy_resume'){
            self::$arrDataAnswer = $_POST;
            self::$arrDataAnswer["message"]["text"]='/add_vacancy_resume';
            self::$arrDataAnswer["message"]["chat"]["id"]=$_POST['user_id'];
        }

        if (!empty(self::$arrDataAnswer['callback_query']["data"]))
        {
            self::$DataСallback = self::$arrDataAnswer['callback_query']["data"];
            self::$chatId = self::$arrDataAnswer['callback_query']["message"]["chat"]["id"];
            self::$messageID = self::$arrDataAnswer['callback_query']["message"]["message_id"];
        }
        else{
        if (!empty(self::$arrDataAnswer["message"]["text"])){
            self::$aPhoto='';
            self::$textMessage = (self::$arrDataAnswer["message"]["text"]);
        }
            if (!empty(self::$arrDataAnswer["message"]["photo"])){
                self::$aPhoto = (self::$arrDataAnswer["message"]["photo"]);
                self::$textMessage=(!empty(self::$arrDataAnswer["message"]["caption"])) ? self::$arrDataAnswer["message"]["caption"] :'';
            }

       self::$chatId = !empty(self::$arrDataAnswer["message"]["chat"]["id"]) ? self::$arrDataAnswer["message"]["chat"]["id"] : 0;
       self::$messageID = !empty(self::$arrDataAnswer["message"]["message_id"]) ? self::$arrDataAnswer["message"]["message_id"] : 0;
       }

         wlog(self::$arrDataAnswer);
        $objAction = new ActionModule(); // созадем объект обработчика действий
        $objAction->init(); // иницилиазируем 
        wlog('$resultReturn') ;
        wlog(self::$resultReturn);
        self::$arrayQuery=array();
        self::$arrDataAnswer=array();

    } 
  public function init_cron($action='')
  {
  //  sCron('init_cron action='.$action);
      $objAction = new CronActionModule($action); // созадем объект обработчика действий  
      $objAction->init(); // иницилиазируем 
  }
    public static  function Send ()
  {
    switch (self::$typeReturn )
    {
       // case 'photo' : self::$resultReturn = self::TG_sendPhoto(self::$arrayQuery); break;

     //   case 'photo' : $this->resultReturn = self::TG_sendPhoto(self::$arrayQuery); break;
     //   default : $this->resultReturn = self::TG_sendMessage(self::$arrayQuery);
    }
    
  }  
  public static function TG_sendMessage($getQuery) {
   // slog('TGGG   '.TG_TOKEN);
  //  s("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
      wLog("📤 Отправка сообщения Telegram: " . json_encode($getQuery, JSON_UNESCAPED_UNICODE), 'info');

      $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
      $rawRes = curl_exec($ch);
      if ($rawRes === false) {
          wLog('📤 Telegram cURL error: ' . curl_error($ch), 'error');
          $res = null;
      } else {
          wLog('📤 Telegram response: ' . $rawRes, 'info');
          $res = json_decode($rawRes, true);
      }
 //   s("https://api.telegram.org/bot". TG_TOKEN ."/sendMessage?" . http_build_query($getQuery));
   // slog('+++++++++++++++++++++++++++++++++++++');
   // s($res);
    curl_close($ch);
    
       return $res;
}
    public static function TG_delMessage($getQuery) {

        $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/deleteMessage?" . http_build_query($getQuery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = json_decode(curl_exec($ch), true);
        //slog($res);
        curl_close($ch);
        return $res;
    }
public static function sendText($textMessage='',$chat_id='')
{
    if (!empty($textMessage))
    {
       $chat_id = $chat_id=='' ? self::getChatId() : $chat_id;
        $arrayQuery = array(
	'chat_id' 		=> $chat_id,
	'text'			=> $textMessage,
	'parse_mode'	=> "html",
   
    );
    self::TG_sendMessage($arrayQuery);
    }
}

    public static   function sendMessTextButt($textMessage,$inlineButtons=array(),$chat_id='')
    {
     //   $ObjBut = new ButtonModule();
        if (!empty($inlineButtons))
        {
            $keyboard=array("inline_keyboard"=>$inlineButtons);
            $Button = json_encode($keyboard);

        }
        $chat_id = $chat_id=='' ? self::getChatId() : $chat_id;
     //   sCron($textMessage);
       // sCron('$chat_id='.$chat_id);
        $textMessage_bot = $textMessage;
           $arrayQuery = array(
            'chat_id' 		=> $chat_id,
            'text'			=> $textMessage_bot,
            'parse_mode'	=> "html",
            'reply_markup' => $Button,
        );
        self::TG_sendMessage($arrayQuery);
    }
/* для отправки изображений */
public static function TG_sendPhoto($arrayQuery) {
    wLog("🖼 Отправка фото Telegram: " . json_encode($arrayQuery), 'info');
    $ch = curl_init('https://api.telegram.org/bot'. TG_TOKEN .'/sendPhoto');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
 //   s($arrayQuery);
    return $res;
}

/* для получения данных о файле */
public static function TG_getFile($arrayQuery) {
    $ch = curl_init("https://api.telegram.org/bot". TG_TOKEN ."/getFile?" . http_build_query($arrayQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}
 public static function getTextMessage()
   {
        return self::$textMessage;
   }
    public static function getAPhoto()
    {
        return self::$aPhoto;
    }
 public static function getChatId()
   {
        return self::$chatId;
   }  
      
 public static function getarrDataAnswer()
   {
        return self::$arrDataAnswer;
   }     
  
 

public static function defineMenuOptions($comandos) {

$comandosDel = "/deleteMyCommands?";
$comandosEnc = "/setMyCommands?commands=" . json_encode($comandos);
$retorno = file_get_contents("https://api.telegram.org/bot". TG_TOKEN .$comandosDel);
$retorno = file_get_contents("https://api.telegram.org/bot". TG_TOKEN .$comandosEnc);
slog("https://api.telegram.org/bot". TG_TOKEN .$comandosEnc);
slog('$retorno='.$retorno);

}
   
}
?>
