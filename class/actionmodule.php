<?php
// класс возвращает и обрабатывает действия по нажатию по кнопкам
class ActionModule 
{
   // use action\actionmyzapis;
    //, action\actioncardnumber
  protected  $action = 'start';
  public static  $textMessage = '';
  public static  $LastTimeOper = '';
  protected  $arrDataAnswer = array();
  public static  $UserInfo = array();
  public static  $LastOper = array();
  public static  $accId = 0;
  public static  $inline_keyboard = array();
  public static  $ip_club = array();
  public static  $club = 0;

  
  function __construct()
    {
        wLog('🚀 Init ActionModule');
        self::$textMessage = SystemClass::getTextMessage();
        $this->arrDataAnswer = SystemClass::getarrDataAnswer();
         // проверим есть ли клиент в базе сайта
         self::isSignIn();

    }
   function init()
    {
        wLog("▶ Запуск ActionModule::init()");
    if (!empty(SystemClass::$DataСallback))
       $this->actionCallback();
     else 
    // slog('init');
       $this->actionOperation();  
    }
    
    function actionOperation()
    {
        $Command='-';
     if (!empty($this->arrDataAnswer["message"]["contact"]["phone_number"] ) ) self::$textMessage = 'signIn';
      //  slog('$this->textMessage='.$this->textMessage);
      //  slog('\xF0\x9F\x92\xB3 Моя картка...');
      if (substr(self::$textMessage,0,1)=='/')
            { 
            //    slog($this->textMessage);
                $Command = trim(substr(self::$textMessage,1,strpos(self::$textMessage,' ')));
                if (empty($Command))
                 $Command = trim(substr(self::$textMessage,1));
               
               // slog('tyt');
            }
        else{

            if (!empty(self::$UserInfo['next_command'])){
                $Command =self::$UserInfo['next_command'];
                $sql = 'update spr_users set next_command="",text_user="",photo="" where id='.self::$UserInfo['id'];
                db_query($sql);
            }else
                $Command  = (self::$textMessage!='signIn')  ?  substr(self::$textMessage,0,-4) : self::$textMessage;

        }
              slog('$Command='.$Command);
     // slog('$NoPictagram='.$NoPictagram);
      $this->getLastOper();
         $class_action='';$param_class='';
      if (self::$textMessage=='signIn'  || !empty(self::$UserInfo)){
       switch  ($Command )
       {
         case 'myphone' : $class_action='writePhone'; break;
           case 'allsend' :  $class_action='allsend'; $param_class='allsend'; break;
           case 'allsend2' : $class_action='allsend';$param_class='allsend2'; break;
         case 'liqpay' : $this->testLiqpay(); break;
         case 'new_reg_user' : $class_action='new_reg_user'; break;
         case 'add_vidguk' : $class_action='add_vidguk'; break;
         case 'add_vacancy_resume' : $class_action='add_vacancy_resume'; break;
         case 'test' : $this->test(); break;
         case 'writeMenu' : $this->SetMenu(); break;
         case 'signIn' :  $class_action='actionAvtoriz';  break;
         case 'mycard' :
         case 'MyCard...' :  $class_action='actionCardNumber';  break;
         case 'Моя картка...' : $class_action='actionCardNumber';  break;
         case 'Виберіть профіль' :  $this->setActiveAcc(); break;
         case 'Історія платежів ' :
         case 'historyshop' : $class_action='historyshop'; break;
         case 'mypack' :
         case 'Мій абонемент ':
         case 'myposlug' :
         case 'Активні абонементи ' : $class_action='actionPredSale'; break;
         case 'Історія візитів ' : $class_action='historyVisits'; break;
         case 'Записатися на тренування ' : $class_action='zapis_grp_tren'; break;
         case 'Мої записи ' : $class_action='actionmyzapis'; break;
         case 'Зворотній зв’язок / відгук ' : $class_action='callback'; break;
         case 'Зареєструватися в боті! ' : $class_action='newreg'; break;
         case 'Налаштування ' : $class_action='nastr'; break;
       //  case 'Активні абонементи ' : $class_action='actionPack'; break;
        case 'mybonus' :
         case 'Мої бонуси ' : $class_action='actionMyBonus'; break;

           case 'Звіти ' : $class_action='reports'; break;
          case 'Списання/начислення бонусів ' : $class_action='actionSpisBonus'; break;
         case 'Замовити зворотній дзвінок ' : $class_action='returnCall';  break;
         case 'Записи/відміна на відпрацювання ' : $class_action='Zapis_to_Vidprac';  break;
        default : if (!empty(self::$UserInfo)) $this->actionWork(); else $this->actionStart();
        }
            if (file_exists(TRAIT_DIR  .strtolower($class_action) .'.php')) {
              $obAct = new $class_action();
              $obAct->init($param_class);

          }
       }
        else $this->actionStart();    
    }
    function actionCallback()
    {
        $Command='';$class_action='';$param_class='';
        if (substr(SystemClass::$DataСallback,0,1)=='/')
        {
            $Command = trim(substr(SystemClass::$DataСallback,1,strpos(SystemClass::$DataСallback,'_')-1));
            self::$accId = trim(substr(SystemClass::$DataСallback,strpos(SystemClass::$DataСallback,'_')+1));


            switch  ($Command )
            {
                case 'acccard' : $class_action='AccCardCode'; break;
                case 'zapis' : $class_action='AccZapis'; break;
                case 'zapistrener' :  $class_action='SotrZapis'; break;
                case 'detalpred' : $class_action='detalpred'; break;
                case 'historypredshop' : $class_action='history_pred_3mounth'; break;
                case 'historytekshop' : $class_action='history_tek_3mounth'; break;
                case 'zapistovidprac' : $class_action='Zapis_to_Vidprac';  break;
                case 'zapisvidprac' : $class_action='ZapisVidprac'; break;
                case 'vidminavidprac' : $class_action='VidminaVidp'; break;
                case 'sendallsend' : $class_action='allsend';$param_class='sendallsend';  break;

                default : self::sendMessText('Немає такої команди');
            }
           // wlog($class_action);
            if (file_exists(TRAIT_DIR  .strtolower($class_action) .'.php')) {
                $obAct = new $class_action();
                $obAct->init($param_class);
            }
        }

        // slog('$Command='.$Command);
        // slog('this->accId='.$this->accId);
    }
    function setMenu()
    {
        slog('setmenu');
         $comandos = [
["command" => "mycard", "description" => "Моя картка \xF0\x9F\x92\xB3"],
["command" => "mypack", "description" => "Активні абонементи \xF0\x9F\x85\xB0"],
//["command" => "historyshop", "description" => "Історія платежів \xF0\x9F\x93\x9D"],
//["command" => "myzapis", "description" => "Мої записи \xF0\x9F\x93\x85"],
["command" => "returncall", "description" => "Замовити зворотній дзвінок \xF0\x9F\x93\x9E"],
];
/*
$comandos = [
["command" => "a1", "description" => "aa22a"],
["command" => "b1", "description" => "bb  44b"],
["command" => "c2", "description" => "cc55c"],
["command" => "d3", "description" => "dd66d"],
];*/
 SystemClass::defineMenuOptions($comandos);
        self::sendMessText('Перазайдіть в бот для змін меню');
    }

  /*  function test()
    {
        $sql ='select active_account as acc from spr_users where club=2 and active_account>0';
        $aUsers =db_list($sql);
        s($aUsers);
        foreach ($aUsers as $acc)
        {   s('acc='.$acc['acc']);
            $params=array('acc'=>$acc['acc'],'command'=>'set_bot_user');
            list($status,$type_result,$ress)=send_data_b52($params,self::$ip_club);
        }

        self::sendMessText('тест 222');
    }*/


 
    function testLiqpay()
    {
        $text= '04.09.2023 дата поповнення Вашого абонементу. До сплати '.number_format(1775,2, '.', '').'грн. Бажаєте продовжити абонемент.';

        $inline_button3 = array("text"=>'Так сплатити 1175грн',
            "url"=>'https://trystyhii.com.ua/formreg/?action=liqpay&phone=0976114853&acc=32785&tov=317015&summa=1785&tp=predop&pred_abon=79699');
        $this->inline_keyboard=array();
        $this->inline_keyboard[][] = $inline_button3;
        self::sendMessText($text,$this->inline_keyboard);

    }




 public  static  function sendMessText($textMessage,$inlineButtons=array(),$chat_id='')
    {
            $ObjBut = new ButtonModule();
           if (!empty($inlineButtons))
        SystemClass::$Button = $ObjBut->buttonInline($inlineButtons);

        elseif (empty(self::$UserInfo['is_reg']) || empty(SystemClass::$activeAccount))
            SystemClass::$Button = $ObjBut->buttonRegistr();
        else
        SystemClass::$Button = $ObjBut->buttonAvtoriz();
        $chat_id = !empty($chat_id) ? $chat_id :SystemClass::getChatId();

        SystemClass::$textMessage_bot = $textMessage;
        SystemClass::$arrayQuery = array(
	'chat_id' 		=> $chat_id,
	'text'			=> SystemClass::$textMessage_bot,
	'parse_mode'	=> "html",
    'reply_markup' => SystemClass::$Button,
    );
     return   SystemClass::TG_sendMessage(SystemClass::$arrayQuery);
    }

    function getLastOper()
    {
        $sql = ' select * from spr_oper where chat_id='.SystemClass::getChatId().' order by id desc limit 1';
        self::$LastOper = db_row($sql);
       // slog($this->LastOper);
       if (!empty(self::$LastOper))
       {
        $timeOper = self::$LastOper['time_oper'];
           self::$LastTimeOper = strtotime( $timeOper. ' +3 minute');
       } else self::$LastTimeOper=null;
              
    }
    function setLastOper($oper,$status,$mess='')
    {
          $sql = 'insert into spr_oper (chat_id,oper,status,time_oper) values("'.SystemClass::getChatId().'","'.$oper.'","'.$status.'",now())';
        // db_query($sql);
        $mess = $mess ? $mess : SystemClass::$messageID;
        db()->insert('spr_oper', [
            'chat_id' => SystemClass::getChatId(),
            'oper' => $oper,
            'status' => $status,
            'mess_id' => $mess,
            'time_oper' => ['RAW' => 'NOW()']
        ]);
         
    }
    function setLastOperReturn($mess)
    {
         // $mess = $mess ? $mess : SystemClass::$messageID;
        db()->update('spr_oper',
            ['mess_id_return' => $mess],
            'chat_id = :chat_id  and mess_id=:mess_id'  ,
            ['chat_id' => SystemClass::getChatId(), 'mess_id'=>SystemClass::$messageID]
        );



    }
    function DelLastOper($oper,$status='')
    {

        $Oper = db()->selectOne('spr_oper', 'oper = :oper and chat_id = :chat_id  order by id desc',
            ['oper' => $oper, 'chat_id' => SystemClass::getChatId()]);

        if (!empty($Oper['mess_id']))
        SystemClass::TG_delMessage(['chat_id' => SystemClass::getChatId(), 'message_id' => $Oper['mess_id']]);
        if (!empty($Oper['mess_id_return']))
            SystemClass::TG_delMessage(['chat_id' => SystemClass::getChatId(), 'message_id' => $Oper['mess_id_return']]);


    }
    public static   function isSignIn ()
   {  //SystemClass::getChatId()
    //   SystemClass::$ip_club = HOST_SOCKET; // для схем с одним клубом
       self::$ip_club = '';
      $sql = 'select * from spr_users where chat_id="'.SystemClass::getChatId(). '" limit 1';
      self::$UserInfo = db_row($sql);
         if (!empty(self::$UserInfo))
      {
      if (substr(self::$UserInfo['phone'],0,2)=='38')
          self::$UserInfo['phone']= substr(self::$UserInfo['phone'],2);
      SystemClass::$phone =    self::$UserInfo['phone'];
      SystemClass::$activeAccount = self::$UserInfo['active_account'];
      SystemClass::$activeSotr = self::$UserInfo['sotr'];
      SystemClass::$admin = self::$UserInfo['admin'];
      SystemClass::$club = self::$UserInfo['club'];
      SystemClass::$id = self::$UserInfo['id'];



      // если не пустой клуб по клиенту то найдем IP адрес клуба
      if (!empty(self::$UserInfo['club'])){
          $sql='select ip_club,chat_admin,chat_group from spr_clubs where id='.self::$UserInfo['club'];
           $aClubs= db_row($sql);
          self::$ip_club = $aClubs['ip_club'];
          SystemClass::$chat_admin = $aClubs['chat_admin'];
          SystemClass::$chat_group = $aClubs['chat_group'];
          //  s(self::$ip_club);
      }
      if (self::$UserInfo['active_account']>0)
      {
          SystemClass::$cntAccounts=2;
          $sql = 'select acc,type_sale,name from spr_acc where acc="'.self::$UserInfo['active_account'].'"  limit 1';
          $aACC = db_row($sql);


          if (empty($aACC)) {
              $sql = 'select acc,type_sale,name from spr_acc where chat_id="'.self::$UserInfo['chat_id'].'"  limit 1';
              $aACC = db_row($sql);
          }
      }
      else
      {
       
         $sql = 'select acc,type_sale,name from spr_acc where chat_id="'.self::$UserInfo['chat_id'].'"  limit 1';
            $aACC = db_row($sql);
      }
    if (!empty($aACC)){
        self::$UserInfo['active_account'] = $aACC['acc'];
         if (empty(SystemClass::$activeAccount)) {
            db_query('update spr_users set active_account='.$aACC['acc'] . ' where chat_id='.SystemClass::getChatId());
         }
        SystemClass::$activeAccount = $aACC['acc'];
        self::$UserInfo['account_name'] = $aACC['name'];
      //  self::$UserInfo['type_sale'] = $aACC['type_sale'];
    }

      }

   } 

    public static  function readAccounts($sendMess=1)
 {
        $status='NO';$msg_res='WAIT';$type_result='NO';
    $unixTime = time();
    $isNoWait = 1;
       if (self::$LastTimeOper>$unixTime  && (self::$LastOper['status']=='NO_INTERNET' || self::$LastOper['status']=='OFFLINE' )) $isNoWait=0;
  // $phone=self::$UserInfo['phone'];
   $phone=SystemClass::$phone;
  // s('$phone='.$phone);
   if ($isNoWait) // если не прошло 3 минуты от отсутсиве интенета то пропускаем запрос на сервер ждем
   {

  list($status,$type_result,$msg_res)= self::get_acc($phone);
  }
 // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
    if ($status=='OK') 
    {
        $aAccounts = $msg_res;
        if (empty(self::$UserInfo['is_reg'])) {
            $sql = 'update spr_users set is_reg=now() where id='.self::$UserInfo['id'];
            db_query($sql);
            self::$UserInfo['is_reg']='1';
        }

     //   slog($aAccounts);
         //$cnt_acc = count($aAccounts)-1;
         $cnt_acc = count($aAccounts);
         SystemClass::$cntAccounts = $cnt_acc;
        // узнаем сколько было раньше аккаунтов, может в основной базу уже обеденили
        $sql = 'select count(*) as cn from spr_acc where phone="'.$phone.'" and chat_id="'.SystemClass::getChatId().'"' ; 
        $cn_old_acc = db_field($sql,'cn'); 
        // нсли не совпадает старое количество аккаунтов и то есть сейчас в базе удалим лишнее
        if ($cn_old_acc<>$cnt_acc) 
        {
            $sql = 'delete from spr_acc where phone="'.$phone.'" and chat_id="'.SystemClass::getChatId().'" ';
            db_query($sql);
        }
         $inline_keyboard = array();
        foreach($aAccounts as $user)
        {
      //      slog('$user');
       //     slog($user);
    if (!empty($user['ACC'])) 
    {
   // добавляем в базу данных для режима офлайн     
            $sql = 'INSERT INTO spr_acc
    (chat_id, phone, acc, name, card)
VALUES
    ( '.SystemClass::getChatId().', "'.$phone.'", '.$user['ACC'].',"'.escape($user['NAME']).'","'.$user['CARD_NUM'].'")
ON DUPLICATE KEY UPDATE
    chat_id = '.SystemClass::getChatId().',
    phone = "'.$phone.'",
    date_create = now(),
    name = "'.escape($user['NAME']).'",
    card = "'.$user['CARD_NUM'].'",
    type_sale = "'.$user['TYPE_SALE'].'",
     acc = '.$user['ACC'].';';
    db_query($sql);
 //   slog($sql);
        SystemClass::$activeAccount =$user['ACC'];
        db_query('update spr_users set active_account='.$user['ACC'] . ' where club='.SystemClass::$club.' and chat_id='.SystemClass::getChatId());

        // если несколько аккаунтов то отправим клавиутуру с вібором аккаунтом
if ($cnt_acc>1) 
{
    $inline_button = array("text"=>$user['NAME'].' ('.$user['CARD_NUM'].')',"callback_data"=>'/acccard_'.$user['ACC']);
    self::$inline_keyboard[][] = $inline_button;
} 
  // если клиент есть сотрудником
  if (!empty($user['SOTR']))
    {
        $sql = 'update spr_users set sotr="'.$user['SOTR'].'" where club='.SystemClass::$club.' and id='.self::$UserInfo['id'];
        db_query($sql);
    }
     }
        }
 
    return 'OK';
    }
    else // режим офлайн читаем з бази даних сайта
    { 
        
       if ($type_result=='NO_10_CIFR') 
       {
           if ($sendMess==1) self::sendMessText('Вибачте некоректний номер телефону');
         return 'NO_10_CIFR';
      
       }else
       if ($type_result=='NO_RESULT') 
       {
              // if ($sendMess==1)    self::sendMessText('Вибачте Вашого номеру немає в базі даних. Зверніться до адміністраторів клубу');
         return 'NO_RESULT';
       } 
       
       
       else
       {
        
       $sql = 'select * from spr_acc where chat_id='.SystemClass::getChatId().' and phone="'.$phone.'"';
       $aAccounts=db_list($sql);
       
   //  slog($sql);
   //  slog($aAccounts);
     $inline_keyboard = array();
       if (!empty($aAccounts))
       {  
        $cnt_acc = count($aAccounts);
       foreach($aAccounts as $user)
        {
        if ($cnt_acc>1) 
        {
            $inline_button = array("text"=>$user['name'].' ('.$user['card'].')',"callback_data"=>'/acccard_'.$user['acc']);
            self::$inline_keyboard[][] = $inline_button;
        }
        /* else 
         {
        //  если один профиль то выводим штрикод
            $this->returnCodeShtrih($user['card']);
         }*/
        }
        // если несколько аккауентов отравим клавиаутуру для выбора нужного профиля 
      /*  if (!empty($inline_keyboard))  
        {
            $this->sendMessText('Виберіть потрібний профіль:',$inline_keyboard); 
        }*/
        return 'OFFLINE';
     }else
           if ($sendMess==1)    self::sendMessText('Зараз сервер недоступний. Спробуйте пізніше.');
          return 'NO_INTERNET';
    }
   }
 }
 // выводим кнопки для выбора главного профиля
public static function setActiveAcc($isread=1)
 {
    if ($isread)
    {
     $StatusReadAcc = self::readAccounts();
    //    self::setLastOper('setActiveAcc',$StatusReadAcc);
   
if ($StatusReadAcc=='OK' || $StatusReadAcc=='OFFLINE')
{
    self::sendMessText('Виберіть потрібний профіль:',self::$inline_keyboard);
}
} else
        self::sendMessText('Виберіть потрібний профіль:',self::$inline_keyboard);
 }  


    function getTren($kod,$acc=0)
    {
        $kod = str_replace('@','',$kod);
        $socket = new socketB52(HOST_SOCKET, PORT_SOCKET);
        list($status, $type_result, $msg_res) = $socket->GET_TRAININGONE($kod,$acc);
        $type_res = '';
        if ($status == 'OK') {

            $treining = $msg_res;
             if (!empty($treining['ACC']) ) $type_res = 'WRITE';
            if (!empty($treining['RESERV']) ) $txtBeg = 'Ви зробили резерв'; else $txtBeg = 'Ви записалися';

            $tsxt = $txtBeg.' на групове тренування "'.$treining['TOV_NAME'].'"'.' '.$treining['DAT1'].' з ' . $treining['TIME_FROM'] . ' до '. $treining['TIME_TO'];
             return ['OK',$type_res,$tsxt];
            //  $this->sendMessText($tsxt);
        } else
        {
            $textMessage = 'На даний момент немає зв\'язку з сервером, спробуйте пізніше!!!*'; // если не ОК то какая то проблема и ошибка
            //$this->sendMessText($textMessage);
            return ['ERROR',$type_res,$textMessage];
        }
    }






    public static  function addNewUsersToB52($UserInfo){

        $sql = 'select * from bs_reg_new_users where phone="'.SystemClass::$phone.'" limit 1';
         $aUser = db_row($sql);
        
         // ПРОВЕРИМ ЕСЛИ ЭТОТ ТОВАРИЩ В БАЗЕ в52 ЧТОБЫ НЕ ЗАДВОИТЬ
        list($status,$type_result,$msg_res)= self::get_acc(SystemClass::$phone);
        if ($type_result!='NO_RESULT') {
            $StatusReadAcc = self::readAccounts();
            $status = 'OK';
            slog('Дубликат пользователя');
            slog($aUser);
        }

        if (!empty($aUser) && $type_result=='NO_RESULT'){
            $num_site = $aUser['id'];

            $fio = $aUser['surname'].' '.$aUser['name'].' '.$aUser['lastname'];
            $date_birtday= ('00.00.0000'!=$aUser['birthday'] && trim($aUser['birthday'])!='') ? $aUser['birthday'] :'';
         //   $dat_vudachi_p= ('00.00.0000'!=$form['dat_vudachi'] && trim($form['dat_vudachi'])!='') ? $form['dat_vudachi'] :'';
            $BIRTHDAY = $date_birtday;
            $TYPE_DOC_ = $aUser['type_doc'];
            $sex= $aUser['sex'];
            $number = $aUser['number'];
            $cer = $aUser['cer'];
          //  $KUMVUDAN =$form['kumvudan'];
           // $dat_vudachi = $dat_vudachi_p;
            switch ($TYPE_DOC_)
            {
                case 1 : $TYPE_DOC ='паспорт'; break;
                case 2 : $TYPE_DOC ='ID картка'; break;
                case 3 : $TYPE_DOC ='ІПН'; break;
             }
            $TYPE_DOC =$aUser['type_doc'];
            $User_info=[
                'command'=>'INS_NEW_UPD_ACC',
                'ACC'=>0,
                'NUM_SITE'=>$num_site,
                'FIO'=>$fio,
                'BIRTHDAY'=>trim($BIRTHDAY),
                'PHONE'=>SystemClass::$phone,
                'TYPE_DOC'=>$TYPE_DOC,
                'SEX'=>$sex,
                'NUMBER'=>$number,
                'CER'=>$cer,
                'DAT_REGIST'=>date("d.m.Y")

            ];

            //$socket=  new socketB52(HOST_SOCKET,PORT_SOCKET);
          //  list($status,$type_result,$msg_res)= jwt_request($User_info);
            list($status,$type_result,$msg_res)= send_data_b52($User_info,self::$ip_club);
            // всегда возвращаються 3 параметра 1й параметр $status это статус 2й тип резульатат 3й - это массив данных иди комментарий если ошибка
            if ($status=='OK') {
                 slog('Ви успішно зареєструвалися в нашому клубі!');

                $StatusReadAcc = self::readAccounts();


            }
            else
            {
              //  slog($msg_res['ERROR_MSG']);
                if (!empty($msg_res['ERROR_MSG']))
                    slog($msg_res['ERROR_MSG']);
                else
                slog('Немає зв\'язку з сервером або помилка в базі. Спробуйте пізніше!');


            }

        }
        return $status;
    }
    function actionStart()
    {
        sLog('STARTFUN');
        $ObjBut = new ButtonModule();
        SystemClass::$Button = $ObjBut->buttonNoAvtoriz();
        SystemClass::$textMessage_bot = 'Вітаю я чат бот Redbarbell! Давай почнемо співпрацювати!';
        SystemClass::$arrayQuery = array(
	'chat_id' 		=> SystemClass::getChatId(),
	'text'			=> SystemClass::$textMessage_bot,
	'parse_mode'	=> "html",
    'reply_markup' => SystemClass::$Button,
    );

        SystemClass::TG_sendMessage(SystemClass::$arrayQuery);
    }
    function actionWork()
    {
        sLog('actWork');
        if (!empty(self::$ip_club))
        self::readAccounts(); // проверим на всяк члучай клиента
        $ObjBut = new ButtonModule();
        if (empty(self::$UserInfo['is_reg'])){
            SystemClass::$Button = $ObjBut->buttonRegistr();
            SystemClass::$textMessage_bot = 'Щоб зареєструватися в боті, натисніть кнопку "Зареєструватися в боті" та заповніть форму вашими персональними даними!';
            SystemClass::$arrayQuery = array(
                'chat_id' 		=> SystemClass::getChatId(),
                'text'			=> SystemClass::$textMessage_bot,
                'parse_mode'	=> "html",
                'reply_markup' => SystemClass::$Button,
            );

            SystemClass::TG_sendMessage(SystemClass::$arrayQuery);
        }else
          self::sendMessText( 'Чим я можу Вам допомогти? Натисніть необхідну кнопку знизу!');
      
     
    }
   function actionCardNumber1()
   {
   
     $filePath = ROOT_A.'barcodes/'.$this->UserInfo['phone'].'.png';  
      //   
     // создадим картинку штрих код
 if (file_exists($filePath)) 
 {
    
 }  else
 {   
require 'telegram/vendor/autoload.php';

$redColor = [0, 0, 0];

//$generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG();
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

file_put_contents($filePath, $generatorPNG->getBarcode($this->UserInfo['phone'], $generatorPNG::TYPE_CODE_128, 2, 100));
 }      
         $ObjBut = new ButtonModule();
        SystemClass::$Button = $ObjBut->buttonAvtoriz();
        SystemClass::$typeReturn = 'photo';
        SystemClass::$textMessage_bot = 'Твоя картка номер "'.$this->UserInfo['phone'].'"';
 
        SystemClass::$arrayQuery = array(
	'chat_id' 		=> SystemClass::getChatId(),
	'caption'			=> SystemClass::$textMessage_bot,
    "photo" => new CURLFile($filePath),
  
//	'parse_mode'	=> "html",
    'reply_markup' => SystemClass::$Button,
    );
       SystemClass::TG_sendMessage(SystemClass::$arrayQuery);
   }
    public static  function get_acc($Phone='')
    {
        if (!empty($Phone))
        {
            //проверяем на корректность мобильного номера передаваемого в БД
            list($status,$type_result,  $mess)= checkPhone($Phone);

            //если не корректный телефон, то выходимы
            if ($status=='ERROR') { return array('ERROR',$type_result,$mess ); }

            $params=array('phone'=>$Phone,'command'=>'get_acc');
            list($status,$type_result,$ress)=send_data_b52($params,self::$ip_club);

            if ($status=='OK' && !empty($ress))
            {
                return array('OK',$type_result,$ress);
            }else{
                if ( $type_result=='NO_RESULT')
                    return array($status,$type_result,'Нет такого клиента в БД, по данному номеру!');


                  return array($status,$type_result,$ress);

            }
            return array($status,$type_result,$ress);
        }
        else
            return array('ERROR','NO_PHONE_ACC','Не передался телефон клиента');
    }
}

?>
