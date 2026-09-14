<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/bootstrap-formhelpers.min.js"></script>
    <script src="js/main.js?ver=192"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">

    <title>Telegram app</title>
</head>
<body>
<?php
$chsn= false;
$chn = false;
$chln = false;
$chb = false;
$chcer= false;
$chnub =false;

?>
<div id="root"></div>
<div id="slugeb_info" class="alert alert-danger d-none"   role="alert"></div>
<form class="needs-validation" novalidate action="?action=save" method="post" id="reg" enctype="multipart/form-data">
    <input type="hidden" name="form[action]" value="reg_user">

    <div class="row g-3 m-2">
        <div class="row g-3">
            <div class="col-sm-4">
                <label for="firstName" class="form-label">Прізвище *</label>
                <input type="text" class="form-control" <?= $chsn ? 'readonly' : '' ?>  name="form[surname]" id="firstName" placeholder="" pattern=".{2,}" value="<?= !empty($form['surname']) ?$form['surname'] :'' ?>" required>
                <div class="invalid-feedback">
                    "прізвище" повинно бути заповнено.
                </div>
            </div>

            <div class="col-sm-4">
                <label for="lastName" class="form-label">Ім'я *</label>
                <input type="text" class="form-control" <?= $chn ? 'readonly' : '' ?> name="form[name]"  id="Name" placeholder="" pattern=".{2,}" required value="<?= !empty($form['name']) ?$form['name'] :'' ?>" >
                <div class="invalid-feedback">
                    "ім'я" повинно бути заповнено.
                </div>
            </div>
            <div class="col-sm-4">
                <label for="lastName" class="form-label">По батькові *</label>
                <input type="text" class="form-control" <?= $chln ? 'readonly' : '' ?> name="form[lastname]"  id="lastName" placeholder="" pattern=".{2,}" required value="<?= !empty($form['lastname']) ?$form['lastname'] :'' ?>" >
                <div class="invalid-feedback">
                    "по ботькові" повинно бути заповнено.
                </div>
            </div>

            <div class="col-12">
                <label for="birthday" class="form-label">Дата народження <span class="text-body-secondary"></span></label>
                <div class="ui-widget"><div class="position-relative">
                        <input type="date" required name="form[birthday]" id="birthday" <?= $chb ? 'readonly' : '' ?>
                            <?= $chb ? '' : 'class="form-control"' ?>   value="<?= !empty($form['birthday']) ?$form['birthday'] :' 00.00.0000' ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <label for="child-type_document" class="form-label">Стать *</label>
                <select class="form-select" id="child-type_document" name="form[sex]" required>
                    <option <?= !empty($form['sex']) &&  $form['sex']==0 ? 'selected' :'' ?> value="">Виберіть стать</option>
                    <option <?= !empty($form['sex']) &&  $form['sex']==1 ? 'selected' :'' ?> value="1">чоловік</option>
                    <option <?= !empty($form['sex']) &&  $form['sex']==2 ? 'selected' :'' ?> value="2">жінка</option>
                </select>
                <div class="invalid-feedback">
                    Виберіть стать.
                </div>
            </div>

    </div>
</form>
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
    let tg      = window.Telegram;


    if(tg != undefined){
        if (tg.WebApp != undefined && tg.WebApp.initData != undefined){



            let safe    = tg.WebApp.initData;
            let safeOb    = tg.WebApp.initDataUnsafe ;
        //    alert(safeOb.user.id+' '+safeOb.user.first_name+' '+safeOb.user.username )
            tg.WebApp.backgroundColor = '#3d3d3d';
            tg.WebApp.headerColor = '#212121';
         //  tg.WebApp.expand();
            coolButton = window.Telegram.WebApp.MainButton;
            coolButton.show();
            coolButton.text = 'Зареєструватися!';
          //  alert('tyt1')
            tg.WebApp.onEvent('mainButtonClicked', function(){
             //   alert('tyt2')
                dataForm =   new FormData(reg)
                dataForm.append('action','new_reg_user');
                dataForm.append('user_id',safeOb.user.id);
                dataForm.append('user_first_name',safeOb.user.first_name);
                dataForm.append('user_username',safeOb.user.username);
                birtd = $('#birthday').val();

              corrBirt =  getBirthday(birtd);
             ////проверка полей
              status_valid =  isValidFieldForms('reg');
               /* $("#firstName").keyup(function(){
                    alert('ttt')
                    $("#reg").get(0).checkValidity();
                    $("#reg").addClass('was-validated');
                });
*/
//alert(status_valid);
//alert(corrBirt);
//alert(dataForm);
              //  seril =$("#reg"). serialize();
             //   alert(seril)
                if (corrBirt && status_valid)
                postData('https://redbarbell.com.ua/telegram/bot.php?', dataForm)
                    .then((data) => {

                     //   console.log(data);
                    });
             //
                //Отправляем методом POST, конвертируя объект в JSON
             /*   post('https://trystyhii.com.ua/telegram_interfit/bot.php', JSON.stringify(seril)).then(function(success){
                    window.Telegram.WebApp.close(); //Закрываем после ответа
                });*/
            });

        }
    }

    // Определяем функцию которая принимает в качестве параметров url и данные которые необходимо обработать:
    const postData = async (url = '', data = {}) => {
     //   alert('tyt3')
      //  alert(data.get('form[name]'))
        // Формируем запрос
        const response = await fetch(url, {
            // Метод, если не указывать, будет использоваться GET
            method: 'POST',
            // Заголовок запроса
        /*    headers: {
                'Content-Type':     'application/json'
            },*/
            // Данные
           // body: JSON.stringify(data)
            body: data
        });
        window.Telegram.WebApp.close();
        return response.json();
    }


</script>
</body>
</html>