<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class AllSend extends ActionModule
{
    protected $action='';
    function __construct (){
        //        s('actionMYYY_CONSTR');

    }
    function init ($param='no'){

        $this->action=$param;
      //  s(' $this->action='. $this->action);
        switch ($this->action){
            case  'allsend2': $this->allsend2(); break;
            case  'sendallsend': $this->sendallsend(); break;
            default: $this->allsend();
        }

    }
    function allsend2(){
      //  s('allsend2');
        $text_user='';
        $text_user = SystemClass::getTextMessage();
        $path='';
        $photo = SystemClass::getAPhoto();
        if (!empty($photo))
            list($res,$path) =  $this->getPhoto($photo);

        $tsxt = $text_user."\n\nБажаєте відправити цей текст всім користувачам бота?";
        $buttons[0][0] =
            [
                "text" => 'Так' ,   "callback_data" => "/sendallsend_1"
            ];
        $buttons[0][1] =
            [
                "text" => 'НІ' ,   "callback_data" => "/sendallsend_0"
            ];
        $sql = 'update spr_users set text_user="'.$text_user.'",photo="'.$path.'" where id='.ActionModule::$UserInfo['id'];
        db_query($sql);
        if (!empty($path)){
            $img_path=ROOT_A.'img/'.$path;
            $this->sendMessPhotoText($tsxt,$img_path,$buttons);
        }

        else
            ActionModule::sendMessText($tsxt,$buttons);
        sLogAllSend('$tsxt='.$tsxt);
    }
    function allsend(){

        slog('allllseend');
        // s($usersInfo);
        $chat_id =SystemClass::getChatId();
        $sql = 'update spr_users set next_command="allsend2" where id='.ActionModule::$UserInfo['id'];
        db_query($sql);
        ActionModule::sendMessText('Введіть текст аба фото для відправки всім користувачам і натисніть кнопку Відправить!');

    }
    function sendallsend(){
        if (!empty(ActionModule::$accId))
        {
            $sql = 'select chat_id, phone,text_user,photo from spr_users ';//ыдщпwhere admin=1
            sLogAllSend($sql);
            $users = db_list($sql);
            $sql = 'update spr_users set next_command="",text_user="",photo="" where id='.ActionModule::$UserInfo['id'];
            db_query($sql);

            $cnt = count($users);
            foreach ($users as $user)
            {
                $text=ActionModule::$UserInfo['text_user'];
                if (!empty(ActionModule::$UserInfo['photo']))
                {
                    $img_path=ROOT_A.'img/'.ActionModule::$UserInfo['photo'];
                    // s($img_path);


                    $this->sendMessPhotoText($text,$img_path,[],$user['chat_id']);
                    //  SystemClass::Send();
                }else
                {
                    ActionModule::sendMessText($text,[],$user['chat_id']);
                    // SystemClass::Send();
                }

            }
            sLogAllSend('Ваш текст відправився '.$cnt . ' клієнтам!');
            ActionModule::sendMessText('Ваш текст відправився '.$cnt . ' клієнтам!');
        }else{
            ActionModule::sendMessText('Введіть тоді корректний текст і спробуйте знову');
        }

    }


    function sendMessPhotoText($textMessage,$filePath1,$inlineButtons=array(),$chat_id='')
    {
        $ObjBut = new ButtonModule();
        if (!empty($inlineButtons))
            SystemClass::$Button = $ObjBut->buttonInline($inlineButtons);
        else
            SystemClass::$Button = $ObjBut->buttonAvtoriz();
        SystemClass::$typeReturn = 'photo';
        $chat_id = !empty($chat_id) ? $chat_id :SystemClass::getChatId();
        //  s('$filePath1='.$filePath1);
        //  s( new CURLFile($filePath1));
        //   s('$chat_id='.$chat_id);
        SystemClass::$arrayQuery = array(
            'chat_id' 		=> $chat_id,
            'caption'			=> $textMessage,
            "photo" => new CURLFile($filePath1),

//	'parse_mode'	=> "html",
            'reply_markup' => SystemClass::$Button,
        );
        sLogAllSend(SystemClass::$arrayQuery);
        SystemClass::Send();
        SystemClass::$arrayQuery=[];
    }
    // общая функция загрузки картинки
    private function getPhoto($data)
    {
        // берем последнюю картинку в массиве
        $file_id = $data[count($data) - 1]['file_id'];
       // s($file_id);
        // получаем file_path
        $file_path = $this->getPhotoPath($file_id);
      //  s('$file_path='.$file_path);
        list($res,$nameImg) =$this->copyPhoto($file_path);
        // возвращаем результат загрузки фото
        return [$res,$nameImg];
    }

    // функция получения метонахождения файла
    private function getPhotoPath($file_id) {
        // получаем объект File
        $array = json_decode($this->requestToTelegram(['file_id' => $file_id], "getFile"), TRUE);
        //  s('$array');
        //   s($array);
        // возвращаем file_path
        return  $array['result']['file_path'];
    }
    private function requestToTelegram($data, $type)
    {
        $result = null;

        if (is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . TG_TOKEN . '/' . $type);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;
    }
    // копируем фото к себе
    private function copyPhoto($file_path) {
        // ссылка на файл в телеграме
        $file_from_tgrm = "https://api.telegram.org/file/bot".TG_TOKEN."/".$file_path;
        // достаем расширение файла
        $ext =  explode(".", $file_path);
        // назначаем свое имя здесь время_в_секундах.расширение_файла
        $name_our_new_file = time().".".$ext[1];
        return [copy($file_from_tgrm, ROOT_A."img/".$name_our_new_file),$name_our_new_file];
    }

}