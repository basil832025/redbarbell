<?php
class vidguk
{
    protected $chatid;

    /** @var string|int */
    protected $acc;

    /** @var string */
    protected $ip_club;

    /** @var string|int */
    protected $club;

    /** @var array|null */
    protected $user;

    /** @var array|null */
    protected $club_info;
    function __construct()
    {
        $this->chatid = !empty($_GET['chatid']) ? $_GET['chatid'] : '';
        $this->acc = !empty($_GET['acc']) ? $_GET['acc'] : '0';
        $this->ip_club = !empty($_GET['ip_club']) ? $_GET['ip_club'] : '0';
        $this->club = !empty($_GET['club']) ? $_GET['club'] : '0';
        //  s($_GET);
        $this->user = db()->selectOne('spr_acc', 'chat_id = :id', ['id' => $this->chatid]);
        $this->club_info = db()->selectOne('spr_clubs', 'id = :id', ['id' => $this->club]);
        //  $admin = db()->selectOne('spr_acc', 'chart_id = :id', ['id' => $this->chatid]);
        // получим все смс в момент загрузки и найдем последний id смс

        //  s('$lastMess');
        //  s($lastMess);
    }

    function init()
    {

        $this->view_html();

    }
    function view_html(){
        $html='<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/bootstrap-formhelpers.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js?ver=3004"></script>
     <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
  
    <title>Telegram app</title>
</head>
<body>';

//$date = new \DateTime('next Sunday');
        $date = new \DateTime('+0 day');
        $day_next = $date->format("d.m.Y");
        $html.='
      
      <div class="container mt-5">
  <h3 class="mb-4 text-center">Залишити відгук про клуб "'.$this->club_info['name'].'"</h3>

  <form iclass="needs-validation" novalidate id="vidguk">
  <input type="hidden" name="form[club]" id="club" value="'.$this->club.'">
    <div class="mb-3">
      <label for="rating" class="form-label">Оцінка клубу</label>
      <select class="form-select" id="rating" name="form[rating]"  required>
        <option value="" disabled selected>Оберіть оцінку</option>
        <option value="5">⭐⭐⭐⭐⭐ – Чудово</option>
        <option value="4">⭐⭐⭐⭐ – Добре</option>
        <option value="3">⭐⭐⭐ – Нормально</option>
        <option value="2">⭐⭐ – Слабо</option>
        <option value="1">⭐ – Жахливо</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="message" class="form-label">Ваш відгук</label>
      <textarea class="form-control" id="message" name="form[message]" rows="4" placeholder="Напишіть свій коментар..." required></textarea>
    </div>

  </form>
</div>';



        $html.='
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
      let tg      = window.Telegram;


    if(tg != undefined){
        if (tg.WebApp != undefined && tg.WebApp.initData != undefined){

 

            let safe    = tg.WebApp.initData;
            let safeOb    = tg.WebApp.initDataUnsafe ;
       
            tg.WebApp.backgroundColor = "#3d3d3d";
            tg.WebApp.headerColor = "#212121";
         //  tg.WebApp.expand();
            coolButton = window.Telegram.WebApp.MainButton;
            coolButton.show();
            coolButton.text = "Надіслати!";
           

            tg.WebApp.onEvent("mainButtonClicked", function(){
            
                   dataForm =   new FormData(vidguk)
        dataForm.append("action","add_vidguk");
        dataForm.append("user_id",safeOb.user.id);
        dataForm.append("user_first_name",safeOb.user.first_name);
        dataForm.append("user_username",safeOb.user.username);
       
        ////проверка полей
        status_valid =  isValidFieldForms("vidguk");
       // alert(status_valid)
      //  alert(corrBirt)
         if (status_valid)
            postData("", dataForm)
                .then((data) => {
                });
  

            });

        }
}
// Определяем функцию которая принимает в качестве параметров url и данные которые необходимо обработать:
const postData = async (url = "", data = {}) => {
     // Формируем запрос
     url = "'.URL.'/bot.php?";
    const response = await fetch(url, {
            // Метод, если не указывать, будет использоваться GET
            method: "POST",
            // Заголовок запроса
            body: data
        });
        window.Telegram.WebApp.close();
        return response.json();
    }


</script>
</body>
</html>';
        echo $html;
    }
}

