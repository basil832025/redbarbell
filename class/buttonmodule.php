<?php
// класс возвращает и обрабатывает кнопки клавиатуры
class ButtonModule 
{
    public function buttonRegistr()
    {
        $buttons_munu = json_encode(array(
            'keyboard' => array(
                [
                    ['text' => "Зареєструватися в боті! \xF0\x9F\x92\x81"],
                ],
            ),
            'one_time_keyboard' => false,
            'resize_keyboard' => TRUE,
        ));
        return $buttons_munu;
    }

    public function buttonNoAvtoriz()
  {
    $buttons_munu = json_encode(array(
    'keyboard' => array(
        array(
	    array(
		'text' => 'Авторизація',
        'request_contact' => true
	    ),
       ),
    ),
       'one_time_keyboard' => false,
    'resize_keyboard' => TRUE,
));  
   return $buttons_munu; 
  }  
    public function buttonAvtoriz()
  {
      $Nastr='';
      slog('SystemClass::$cntAccounts');
      slog(SystemClass::$cntAccounts);
    if (SystemClass::$cntAccounts>1){
            $profile = ['text' => "Виберіть профіль\xF0\x9F\x91\xAA"] ;
}

    else
        $profile = '';
    if (!empty(SystemClass::$activeSotr))
        $sotr = "Записи на мої процедури або тренування \xF0\x9F\x91\xAF";
    else
        $sotr = '';
      if (!empty(SystemClass::$admin)){
        //  $Nastr = "Настройки \xF0\x9F\x93\xB2";
          if (SystemClass::$admin==1)
          $Nastr = [  'text' => "Налаштування \xE2\x84\xB9", "web_app"=> ["url"=> URL."webapps/web.php?action=work_users"]];

          $report = "Звіти \xF0\x9F\x93\x8A";
      }

      else{
          $Nastr = '';
          $report = '';
      }
      $butt_find_acc= '';
     if (SystemClass::$admin>0)
         $butt_find_acc= [ 'text' => "Знайти клієнта \xE2\x84\xB9", "web_app"=> ["url"=> "https://timeshop.com.ua/telegram/webapps/web.php?action=work_acc"]
         ];

  if  (SystemClass::$admin==0)  $buttons_munu = $this->getButtonKlient($profile);
  if  (SystemClass::$admin==2)  $buttons_munu = $this->getButtonAdmin();
  if  (SystemClass::$admin==1)  $buttons_munu = $this->getButtonKeriv();
  if  (SystemClass::$admin==3)  $buttons_munu = $this->getButtonMainAdmin();
  if  (SystemClass::$admin==4)  $buttons_munu = $this->getButtonMainTrener();
  if  (SystemClass::$admin==5)  $buttons_munu = $this->getButtonTrener();

  //  s('$vidpr='.$vidpr);


/*
  array(


                $Nastr,
              array(
            'text' => $report,
        ),
            array(
                'text' => $sotr,
            ),
        ),

*/

   return $buttons_munu; 
  } 
   public function buttonInline($inline_keyboard)
  {
    
    $keyboard=array("inline_keyboard"=>$inline_keyboard);
    $buttons_munu = json_encode($keyboard); 
    
 
   return $buttons_munu; 
  }

  function getButtonAdmin(){
      $buttons_munu = json_encode(array(
          'keyboard' => array(
              array(

                   ['text' => "Чати з клієнтами 📩 ", "web_app"=> ["url"=> URL."webapps/web.php?action=chats_clients&club=".SystemClass::$club.'&chat_id='.SystemClass::$chatId] ],
                   ['text' => "Розсилка 📢 ", "web_app"=> ["url"=> URL."webapps/web.php?action=send_mess&club=".SystemClass::$club.'&chat_id='.SystemClass::$chatId] ],
                  [ 'text' => "Знайти клієнта 🔍", "web_app"=> ["url"=> URL."webapps/web.php?action=work_acc&admin=".SystemClass::$admin]],


              ),
              array(
                  ['text' => "Записатися на тренування \xF0\x9F\x93\x85"],
                  ['text' => "Контакти  ☎️ ", "web_app"=> ["url"=> URL."webapps/web.php?action=contacts"] ],
                  ['text' => "Вакансії", "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy&chatid=".SystemClass::$chatId] ],


              ),




          ),
          'one_time_keyboard' => false,
          'resize_keyboard' => TRUE,
      ));
      return $buttons_munu;
  }
  function getButtonMainAdmin(){
      $buttons_munu = json_encode(array(
          'keyboard' => array(
              array(
                  ['text' => "Моя картка...\xF0\x9F\x92\xB3 "],
                   ['text' => "Чати з клієнтами \xF0\x9F\x86\x98 ", "web_app"=> ["url"=> URL."webapps/web.php?action=chats_clients&club=".SystemClass::$club.'&chat_id='.SystemClass::$chatId] ],
                  [ 'text' => "Знайти клієнта \xE2\x84\xB9", "web_app"=> ["url"=> URL."webapps/web.php?action=work_acc&admin=".SystemClass::$admin]],


              ),
              array(
                  ['text' => "Записатися на тренування \xF0\x9F\x93\x85"],
                  ['text' => "Контакти \xF0\x9F\x86\x98 ", "web_app"=> ["url"=> URL."webapps/web.php?action=contacts"] ],
                  ['text' => "Вакансії", "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy&chatid=".SystemClass::$chatId] ],


              ),
              array(

                  [   'text' => "Звіти \xF0\x9F\x93\x8A", "web_app"=> ["url"=> URL."webapps/web.php?action=reports&ip_club=".actionmodule::$ip_club."&admin=".SystemClass::$admin]]

              ),



          ),
          'one_time_keyboard' => false,
          'resize_keyboard' => TRUE,
      ));
      return $buttons_munu;
  }
  function getButtonKeriv(){
      $buttons_munu = json_encode(array(
          'keyboard' => array(

              array(
                  ['text' => "Записатися на тренування \xF0\x9F\x93\x85"],
                  ['text' => "Мої записи \xF0\x9F\x93\x85",]

              ),
              array(

                  ['text' => "Контакти \xF0\x9F\x86\x98 ", "web_app"=> ["url"=> URL."webapps/web.php?action=contacts"] ],
                  ['text' => "Зворотній зв’язок / відгук \xF0\x9F\x86\x98",],
                  ['text' => "Вакансії", "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy&chatid=".SystemClass::$chatId] ]

              ),
              array(

                  [ 'text' => "Знайти клієнта \xE2\x84\xB9", "web_app"=> ["url"=> URL."webapps/web.php?action=work_acc&admin=".SystemClass::$admin]],
                  [  'text' => "Налаштування \xF0\x9F\x91\xAE"],
                  [   'text' => "Звіти \xF0\x9F\x93\x8A", "web_app"=> ["url"=> URL."webapps/web.php?action=reports&admin=".SystemClass::$admin]]

              ),


          ),
          'one_time_keyboard' => false,
          'resize_keyboard' => TRUE,
      ));
      return $buttons_munu;
  }
  function getButtonMainTrener(){
      $buttons_munu = json_encode(array(
          'keyboard' => array(

              array(
                  ['text' => "Записатися на тренування \xF0\x9F\x93\x85"],
                  ['text' => "Мої записи \xF0\x9F\x93\x85",]

              ),
              array(

                  ['text' => "Контакти \xF0\x9F\x86\x98 ", "web_app"=> ["url"=> URL."webapps/web.php?action=contacts"] ],
                  ['text' => "Зворотній зв’язок / відгук \xF0\x9F\x86\x98",],
                  ['text' => "Вакансії", "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy&chatid=".SystemClass::$chatId] ]

              ),
              array(

                    [   'text' => "Звіти \xF0\x9F\x93\x8A", "web_app"=> ["url"=> URL."webapps/web.php?action=reports&ip_club=".actionmodule::$ip_club."&admin=".SystemClass::$admin]]

              ),


          ),
          'one_time_keyboard' => false,
          'resize_keyboard' => TRUE,
      ));
      return $buttons_munu;
  }
  function getButtonTrener(){
      $buttons_munu = json_encode(array(
          'keyboard' => array(

              array(
                  ['text' => "Записатися на тренування \xF0\x9F\x93\x85"],
                  ['text' => "Мої записи \xF0\x9F\x93\x85",]

              ),
              array(

                  ['text' => "Контакти \xF0\x9F\x86\x98 ", "web_app"=> ["url"=> URL."webapps/web.php?action=contacts"] ],
                  ['text' => "Зворотній зв’язок / відгук \xF0\x9F\x86\x98",],
                  ['text' => "Вакансії", "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy&chatid=".SystemClass::$chatId] ]

              ),
              array(

                    [   'text' => "Звіти \xF0\x9F\x93\x8A", "web_app"=> ["url"=> URL."webapps/web.php?action=reports&ip_club=".actionmodule::$ip_club."&admin=".SystemClass::$admin.'&sotr='.SystemClass::$activeSotr]]

              ),


          ),
          'one_time_keyboard' => false,
          'resize_keyboard' => TRUE,
      ));
      return $buttons_munu;
  }
  function getButtonKlient($profile){
      $buttons_munu = json_encode(array(
          'keyboard' => array(
              array(
                  $profile,
                  ['text' => "Моя картка...\xF0\x9F\x92\xB3 "],
                  ['text' => "Історія візитів \xF0\x9F\x93\x9D "],


              ),
              array(
                  ['text' => "Активні абонементи \xF0\x9F\x85\xB0",],
                  ['text' => "Записатися на тренування \xF0\x9F\x93\x85"],
                  ['text' => "Мої записи \xF0\x9F\x93\x85",]

              ),
              array(

                  ['text' => "Контакти \xF0\x9F\x86\x98 ", "web_app"=> ["url"=> URL."webapps/web.php?action=contacts"] ],
                  ['text' => "Зворотній зв’язок / відгук \xF0\x9F\x86\x98",],
                  ['text' => "Вакансії", "web_app"=> ["url"=> URL."webapps/web.php?action=vacancy&chatid=".SystemClass::$chatId] ]

              ),



          ),
          'one_time_keyboard' => false,
          'resize_keyboard' => TRUE,
      ));
      //slog('tytt1');
    //  slog(json_decode($buttons_munu));
      return $buttons_munu;
  }

}
