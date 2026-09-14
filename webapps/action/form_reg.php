<?php

class form_reg
{
    public $who_klient;
    public $phone;
    public $aClubs;
    public $is_phone_ukr ;
    function __construct()
    {
          // $this->who_klient = !empty($_POST['who_klient']) ? $_POST['who_klient'] : '';
           $this->who_klient = !empty($_GET['who_klient']) ? $_GET['who_klient'] : '';
           $this->phone = !empty($_GET['phone']) ? $_GET['phone'] : '';

         //  s('who_klient='.$this->who_klient );
        //$this->type_sale = !empty($_GET['type_sale']) ? $_GET['type_sale'] : '0';
        $sql='select * from spr_clubs where active=1';
        $this->aClubs = db_list($sql);
        $this->is_phone_ukr = validatePhone($this->phone) ? true : false;
    }

    function init()
    {

        $this->view_html();

    }
    function header(){
        $html='<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="js/jquery-3.6.4.min.js?ver=12"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/bootstrap-formhelpers.min.js?ver=2"></script>
    <script src="js/bootstrap.min.js?ver=2"></script>
      <script src="js/main.js?ver='.filemtime('js/main.js').'"></script>
      <!-- Inputmask JS -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="css/checkout.css" rel="stylesheet"  crossorigin="anonymous">


    <title>Telegram app</title>
</head>
<body>';
        return $html;
    }
    function footer(){
        $html='
   <div class="modal fade" id="staticBackdrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="dialog_header">
               Бажаєте видалити запис:
            </div>
            <div class="modal-body" id="dialog_text">
                
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-default btn-myClose" data-bs-dismiss="modal">Скасувати</button>
                <a class="btn btn-danger buttonDELZapis">Видалити</a>
            </div>
        </div>
    </div>
</div>
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
   let tg      = window.Telegram;


    if(tg != undefined){
        if (tg.WebApp != undefined && tg.WebApp.initData != undefined){



            let safe    = tg.WebApp.initData;
            let safeOb    = tg.WebApp.initDataUnsafe ;
            tg.WebApp.backgroundColor = "#3d3d3d";
            tg.WebApp.headerColor = "#212121";
           coolButton = window.Telegram.WebApp.MainButton;
            coolButton.show();
            let alreadyClicked = false;
   
            ';

        if (empty($this->who_klient))  $html.=' coolButton.text = "Закрити!";
              tg.WebApp.onEvent("mainButtonClicked", function(){
                 window.Telegram.WebApp.close();

            });     ';
        elseif ($this->who_klient=='klient_is') {
            $html .= 'coolButton.text = "Зареєструватися!";
                      $(function(){
    $("#phone").inputmask({
      mask: "(999) 999-99-99",
      placeholder: " " ,
      showMaskOnHover: false,
      showMaskOnFocus: true,
    });
});
tg.WebApp.onEvent("mainButtonClicked", async function () {
    if (alreadyClicked) return;

    let form = document.getElementById("regis");
    if (!form) {
        alert("❗️Форма не знайдена");
        return;
    }

    let dataForm = new FormData(form);

    // Валидация
    const status_valid = isValidFieldForms("regis");
    if (!status_valid) {
        alert("Будь ласка, заповніть всі обов\'язкові поля");
        return;
    }

    dataForm.append("action", "new_reg_user");
    dataForm.append("user_id", safeOb.user.id);
    dataForm.append("user_first_name", safeOb.user.first_name);
    dataForm.append("user_username", safeOb.user.username);

    alreadyClicked = true;
    coolButton.setParams({ is_active: false });

    try {
        const response = await postData("", dataForm);
        //alert(response)
        if (response && response.status === "ok") {
            alert("✅ Реєстрація пройшла успішно");
            tg.WebApp.close();
        } else if (response && response.status === "error") {
          alert("❌" + response.mess)
            tg.WebApp.close();
          } else {
           throw new Error("Помилка на сервері або невірна відповідь");
        
         }
        
    } catch (e) {
        alert("❌ Помилка при реєстрації: " + e.message);
        alreadyClicked = false;
        coolButton.setParams({ is_active: true });
    }
});';
        }
        else
        {
            $html.='  coolButton.text = "Зареєструватися!";
                               $(function(){
    $("#phone").inputmask({
      mask: "(999) 999-99-99",
      placeholder: " " ,
      showMaskOnHover: false,
      showMaskOnFocus: true,
    });
});
tg.WebApp.onEvent("mainButtonClicked", async  function(){
    if (alreadyClicked) return;

    //dataForm =   new FormData(reg)
    let form = document.getElementById("reg");
    if (!form) {
        alert("❗️Форма не знайдена");
        return;
    }
    let dataForm = new FormData(form);



    birtd = $("#birthday").val();

    corrBirt =  getBirthday(birtd);
    ////проверка полей
    status_valid =  isValidFieldForms("reg");
    
    //  alert(status_valid)
    //   alert(corrBirt)
    if (corrBirt==false) alert("Діти молодше 5 років не можуть реєструватися в клубі.!")
    if (!status_valid) {
        alert("Будь ласка, заповніть всі обов\'язкові поля");
        return;
    }

alreadyClicked = true;

    dataForm.append("action","new_reg_user");
    dataForm.append("user_id",safeOb.user.id);
    dataForm.append("user_first_name",safeOb.user.first_name);
    dataForm.append("user_username",safeOb.user.username);

    try {
        const response = await postData("", dataForm);
       // console.log("✅ Відповідь сервера:", response);

        if (response && response.status === "ok") {
            alert("✅ Реєстрація пройшла успішно");
            tg.WebApp.close();
        }   else if (response && response.status === "error") {
            alert("❌" + response.mess)
            tg.WebApp.close();
          } else {
           throw new Error("Помилка на сервері або невірна відповідь");
        
         }
    } catch (e) {
        alert("❌ Помилка при реєстрації: " + e.message);
        alreadyClicked = false;
        coolButton.setParams({ is_active: true });
    }

}); ';

        }


    $html.='   }
}

// Определяем функцию которая принимает в качестве параметров url и данные которые необходимо обработать:
const postData = async (url = "", data = {}) => {
     // Формируем запрос
     url = "'.URL.'bot.php?";
 //    alert(url)
    const response = await fetch(url, {
            // Метод, если не указывать, будет использоваться GET
            method: "POST",
            // Заголовок запроса
            body: data
        });
     //   window.Telegram.WebApp.close();
        return response.json();
    }


</script>
</body>
</html>';
        return $html;
    }
    function body(){
        $content_html='';

        $content_html.='
        <div class="container-sm"><div class="text-center">
<h6 class="text-center">Ви вже існуючий клієнт одного із наших клубів та маєте картку клубу?</h6>
<div class="text-center">
 <a class="btn btn-success btn-lg" href="'.URL.'webapps/web.php?action=form_reg&who_klient=klient_is&phone='.$this->phone.'"  role="button">Так. Я маю картку клуба</a>
  </div>
<br>
<div class="text-center">
<a class="btn btn-primary btn-lg" href="'.URL.'webapps/web.php?action=form_reg&who_klient=klient_new&phone='.$this->phone.'"  role="button">Ні. Я новий клієнт</a>

</div>
</div>
</div>
        ';
        return $content_html;
    }
    function body_is(){
                //phone_orig
        $input_html='';
         if (!$this->is_phone_ukr){
             $input_html = ' 
 <div class="alert alert-warning text-center mt-2 px-2 py-2 fw-semibold" role="alert">
 Некоректний номер ('.$this->phone.').! Введіть, будь ласка, номер у форматі +38(0ХХ)ХХХ-ХХ-ХХ
   
</div>
  <div class="col-sm-4">
  
                <label for="firstName" class="form-label">Український номер телефону</label>
               <div class="input-group">
        <span class="input-group-text">+38</span>
        <input
          type="text"
          id="phone"
          required
          name="form[phone]"
          pattern="\(\d{3}\) \d{3}-\d{2}-\d{2}"
          class="form-control"
          minlength="15"
        maxlength="15"
          placeholder="(XXX) XXX-XX-XX"
        />
      </div>
      <div id="phoneHelp" class="form-text">
        Формат: <code>+38 (XXX) XXX-XX-XX</code>
      </div>
            </div>';
         }
        $content_html='
<h6 class="text-center">Виберіть основний клуб, який Ви відвідуєте та маєте картку </h6>
<form class="needs-validation" novalidate action="?action=save" method="post" id="regis" enctype="multipart/form-data">
    <input type="hidden" name="form[action]" value="reg_user">
    <div class="col-md-5">
    '.$input_html.'
    <label for="child-type_document" class="form-label">Клуб *</label>
    <select class="form-select" id="child-type_document" name="form[club]" required>
        <option  value="">Виберіть клуб</option>';
        foreach ($this->aClubs as $club)
        {
            $content_html.='<option  value="'.$club['id'].'">'.$club['name'].'</option>';
        }
        $content_html.='</select>
    <div class="invalid-feedback">
        Виберіть клуб.
    </div>
    
</div>
</div>
</form>
';
        return $content_html;
    }
    function body_new(){
        $tp=1;
        $idZapis=0;
        $input_html='';
        if (!$this->is_phone_ukr){
            $input_html = ' 
 <div class="alert alert-warning text-center mt-2 px-2 py-2 fw-semibold" role="alert">
  Некоректний номер ('.$this->phone.').! Введіть, будь ласка, номер у форматі +38(0ХХ)ХХХ-ХХ-ХХ
</div>
  <div class="col-sm-4">
  
                <label for="firstName" class="form-label">Український номер телефону</label>
               <div class="input-group">
        <span class="input-group-text">+38</span>
        <input
          type="text"
          id="phone"
          required
          name="form[phone]"
          pattern="\(\d{3}\) \d{3}-\d{2}-\d{2}"
          class="form-control"
          minlength="15"
        maxlength="15"
          placeholder="(XXX) XXX-XX-XX"
        />
      </div>
      <div id="phoneHelp" class="form-text">
        Формат: <code>+38 (XXX) XXX-XX-XX</code>
      </div>
            </div>';
        }

        $content_html='';

        $content_html.='
<form class="needs-validation" novalidate action="?action=save" method="post" id="reg" enctype="multipart/form-data">
    <input type="hidden" name="form[action]" value="reg_user">

    <div class="row g-3 m-2">
        <div class="row g-3">
            <div class="col-sm-4">
                <label for="firstName" class="form-label">Прізвище *</label>
                <input type="text" class="form-control"   name="form[surname]" id="firstName" placeholder="" pattern=".{2,}" value="" required>
                <div class="invalid-feedback">
                    "прізвище" повинно бути заповнено.
                </div>
            </div>

            <div class="col-sm-4">
                <label for="lastName" class="form-label">Ім\'я *</label>
                <input type="text" class="form-control"  name="form[name]"  id="Name" placeholder="" pattern=".{2,}" required value="" >
<div class="invalid-feedback">
    "ім\'я" повинно бути заповнено.
</div>
</div>
<div class="col-sm-4">
    <label for="lastName" class="form-label">По батькові *</label>
    <input type="text" class="form-control"  name="form[lastname]"  id="lastName" placeholder="" pattern=".{2,}" required value="" >
    <div class="invalid-feedback">
        "по ботькові" повинно бути заповнено.
    </div>
</div>
'.$input_html.'
<div class="col-12">
    <label for="birthday" class="form-label">Дата народження <span class="text-body-secondary"></span></label>
    <div class="ui-widget"><div class="position-relative">
            <input type="date" required="" name="form[birthday]" id="birthday" class="form-control" value=" 00.00.0000">
        </div>
    </div>
</div>
<div class="col-md-5">
    <label for="child-type_document" class="form-label">Стать *</label>
    <select class="form-select" id="child-type_document" name="form[sex]" required>
        <option  value="">Виберіть стать</option>
        <option  value="1">чоловік</option>
        <option  value="2">жінка</option>
    </select>
    <div class="invalid-feedback">
        Виберіть стать.
    </div>
</div>
<div class="col-12">
    <label for="leadSource" class="form-label">Звідки ви про нас дізналися? *</label>
    <select class="form-select" id="leadSource" name="form[lead_source]" required>
        <option value="">Виберіть варіант</option>
        <option value="nearby">Побачили зал / живете поруч</option>
        <option value="social">Соцмережі</option>
        <option value="city_ads">Реклама у місті (банери, ліфти)</option>
        <option value="mall_radio">Реклама у ТЦ / радіо</option>
    </select>
    <div class="invalid-feedback">Виберіть, звідки ви про нас дізналися.</div>
</div>
        <div class="col-sm-4" >
                    <label for="type_document" class="form-label">Тип документу *</label>
                    <select class="form-select" id="type_document" name="form[type_doc]" required>
                        <option  value="">Виберіть тип документу</option>
                        <option  value="1">паспорт</option>
                        <option  value="2">ID картка</option>
                        <option  value="3">ІПН</option>
                    </select>
                    <div class="invalid-feedback">
                        Виберіть тип документу.
                    </div>
                </div>
                <div class="col-sm-2" id="CERdivDor">
                    <label for="CER" class="form-label">Серія *</label>
                    <input type="text" class="form-control" name="form[cer]"  id="CER" placeholder="" required  value="">
                    <div class="invalid-feedback">
                        Серія обов\'язкова.
                    </div>
                </div>
                <div class="col-sm-5">
                    <label for="number" class="form-label">Номер *</label>
                    <input type="text" class="form-control" name="form[number]"  id="number" placeholder="" required pattern=".{2,}" value="">
                    <div class="invalid-feedback">
                        Номер обов\'язковий.
                    </div>
                </div>
                   <p class="lead fw-bold">***документ потрібно мати з собою при першому візиті, щоб звірити внесені дані</p>

<div class="col-md-5">
    <label for="child-type_document" class="form-label">Клуб *</label>
    <select class="form-select" id="child-type_document" name="form[club]" required>
        <option  value="">Виберіть клуб</option>';
        foreach ($this->aClubs as $club)
        {
            $content_html.='<option  value="'.$club['id'].'">'.$club['name'].'</option>';
        }
        $content_html.='</select>
<div class="invalid-feedback">
    Виберіть клуб.
</div>
</div>
<div class="form-check mt-3">
  <input class="form-check-input" type="checkbox" id="agreementCheck" required>
  <label class="form-check-label" for="agreementCheck">
    Я ознайомлений і погоджуюсь з <strong>Договором публічної оферти</strong> і зобовʼязуюсь виконувати правила Спорткомплексу*<br>
    <small class="text-muted">*(документи для ознайомлення надає адміністратор)</small>
  </label>
</div>
</div>
</form>

';
  return $content_html;
    }
    function view_html(){
        $html=$this->header();
        $html.='<section id="content" class="container">';
        if (empty($this->who_klient))  $html.=$this->body();
        elseif ($this->who_klient=='klient_is') {

            $html.=$this->body_is();

        }
        else {
            $html.=$this->body_new();
         //   s($content);

        }
       // $html.=$this->body_new();
        $html.='</section>';
        $html.=$this->footer();

// $now_date = getdate();
// $date = new DateTime($now_date['year'] . '-' . $now_date['mon'] . '-' . $now_date['mday']);

        echo $html;
    }
}

