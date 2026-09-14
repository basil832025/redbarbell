<?php
//namespace action;
//s('FILE++++++++++++++++++++++++++ActionMyZapis_TRAIT');
class add_vidguk extends ActionModule
{
    protected $userinfo = [];

    /** @var array */
    protected $arrDataAnswer = [];

    /** @var array|null */
    protected $club_info = null;
    function __construct (){
        //        s('actionMYYY_CONSTR');
        $this->userinfo = ActionModule::$UserInfo;
        $this->arrDataAnswer = SystemClass::getarrDataAnswer();
    }
    function init (){
        slog('add_vidguk');

        if (!empty($this->arrDataAnswer['form'])){
            $form = $this->arrDataAnswer['form'];

            slog($form);
            $rating = (int)($form['rating'] ?? 0);
            // Простая валидация
            if ( $rating >= 1 && $rating <= 5 && $form['message']) {
                $result = db()->insert('club_feedbacks', [
                    'chat_id' => $this->userinfo['chat_id'],
                    'rating' => $rating,
                    'message' => $form['message'],
                    'club_id' => $form['club'],
                    'created_at' => ['RAW' => 'NOW()'] // используем встроенный RAW
                ]);
                $this->club_info = db()->selectOne('spr_clubs', 'id = :id', ['id' => $form['club']]);
            }
            if ($result) {
                switch ($rating) {
                    case "1":
                        $rat_txt = 'Жахливо';
                        break;

                    case "2":
                        $rat_txt = 'Слабо';
                        break;

                    case "3":
                        $rat_txt = 'Нормально';
                    case "4":
                            $rat_txt = 'Добре';
                        break;
                    case "5":
                        $rat_txt = "Чудово";
                        break;

                    default:
                        $rat_txt = "---";
                }
                $textMessage = 'Клієнт '.actionmodule::$UserInfo['account_name'].'  телефон: '.actionmodule::$UserInfo['phone'].' 
                    залишив відгук про клуб "'.$this->club_info['name'].'"!
                    Оцінка: '.$rating.' - '.$rat_txt.'
                    Відгук: "'.$form['message'].'"
                    ';
  
                actionmodule::sendMessText('Ви успішно відправили відгук про клуб! Дякуємо Вам за відвертість');
                $arrayQuery = array(
                    'chat_id' 		=> IDCHAT_MENEGERS,
                    'text'			=> $textMessage,
                    'parse_mode'    	=> "html",

                );
                //   s($arrayQuery);
                SystemClass::TG_sendMessage($arrayQuery);
                // отправить админу клубу чат для ответа

                $inline_button1 = array("text"=>'ЧАТ З КЛІЄНТОМ', "web_app"=> ["url"=> URL.'webapps/web.php?action=chart&club='.SystemClass::$club.'&admin=1&who_user_write='.SystemClass::$chat_admin.'&chatid='.SystemClass::$chatId]);
                $inline_keyboard[][] = $inline_button1;

                SystemClass::sendMessTextButt($textMessage,$inline_keyboard,SystemClass::$chat_admin);



            } else {
                actionmodule::sendMessText('Помилка при збереженні відгука! Спробуйте пізніше');

            }

        }



    }

}