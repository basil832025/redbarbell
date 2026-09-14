var globalServerAdress = new URL('web.php', window.location.href).href;
$(document).on('submit', '#massSendForm', function(e) {
    e.preventDefault();

    const form = document.getElementById('massSendForm');
    const formData = new FormData(form);

    $('#send_button').prop('disabled', true).text('Отправка...');
    //alert(globalServerAdress)
  //  console.log(formData);
    $.ajax({
        url: globalServerAdress,
        type: 'POST',
        data: formData,
        dataType : "json",
        processData: false,
        contentType: false,
        success: function(response) {
           // alert(response.status)
            if (response.status === 'ok') {
                alert('Розсилка відправлена!');
            } else {
                alert('Помилка: ' + (response.message || 'Не вдалося відправити'));
            }
            $('#send_button').prop('disabled', false).text('Відправити');
          
        },
        error: function(xhr) {
            alert('Ошибка AJAX: ' + xhr.statusText);
            $('#send_button').prop('disabled', false).text('Відправити');
        }
    });
});

$(document).off('submit', '#massSendForm');
$(document).on('submit', '#massSendForm', function(e) {
    e.preventDefault();

    const form = document.getElementById('massSendForm');
    const baseFormData = new FormData(form);
    const state = {
        last_id: 0,
        sent: 0,
        success: 0,
        fail: 0,
        file_url: ''
    };

    $('#send_button').prop('disabled', true).text('\u041e\u0442\u043f\u0440\u0430\u0432\u043a\u0430...');

    function sendBatch() {
        const formData = new FormData();

        baseFormData.forEach(function(value, key) {
            if (key !== 'image') {
                formData.append(key, value);
            }
        });

        if (!state.file_url && baseFormData.get('image')) {
            formData.append('image', baseFormData.get('image'));
        }

        formData.append('last_id', state.last_id);
        formData.append('sent', state.sent);
        formData.append('success', state.success);
        formData.append('fail', state.fail);

        if (state.file_url) {
            formData.append('file_url', state.file_url);
        }

        $.ajax({
            url: globalServerAdress,
            type: 'POST',
            data: formData,
            dataType : "json",
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status !== 'ok') {
                    alert('\u041e\u0448\u0438\u0431\u043a\u0430: ' + (response.message || '\u041d\u0435 \u0443\u0434\u0430\u043b\u043e\u0441\u044c \u043e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c'));
                    $('#send_button').prop('disabled', false).text('\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c');
                    return;
                }

                state.last_id = response.last_id || state.last_id;
                state.sent = response.sent || state.sent;
                state.success = response.success || state.success;
                state.fail = response.fail || state.fail;
                state.file_url = response.file_url || state.file_url;

                $('#send_button').text('\u041e\u0442\u043f\u0440\u0430\u0432\u043a\u0430 ' + state.sent + '/' + (response.total || state.sent));

                if (response.done) {
                    alert('\u0420\u0430\u0441\u0441\u044b\u043b\u043a\u0430 \u0437\u0430\u0432\u0435\u0440\u0448\u0435\u043d\u0430. \u0423\u0441\u043f\u0435\u0448\u043d\u043e: ' + state.success + ', \u043e\u0448\u0438\u0431\u043e\u043a: ' + state.fail);
                    $('#send_button').prop('disabled', false).text('\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c');
                    return;
                }

                sendBatch();
            },
            error: function(xhr) {
                alert('\u041e\u0448\u0438\u0431\u043a\u0430 AJAX: ' + xhr.statusText);
                $('#send_button').prop('disabled', false).text('\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c');
            }
        });
    }

    sendBatch();
});

$(document).on('change','#sendType',function(event) {
    const type = $(this).val();
    $('#textContainer').toggleClass('d-none', type !== 'text');
    $('#imageContainer').toggleClass('d-none', type !== 'image');
});




$(document).on('click','.klient_chat',function() {
    href = $(this).attr('data-href');
   // alert(href);
    window.location.href = href
});
 //alert('s')
    // Функція для збору параметрів та надсилання запиту
    function fetchFilteredData() {
        const filters = {
            search_name: $("input[name='search_name']").val(),
            search_phone: $("input[name='search_phone']").val(),
            filter_club: $("select[name='filter_club']").val(),
            filter_role: $("select[name='filter_role']").val(),
            action : 'spr_sotr'
        };

        $.ajax({
            url: globalServerAdress,
            type: "POST",
            data: filters,
            success: function (data) {
                $("#content").html(data); // замінюємо вміст таблиці
            }
        });
    }




$(document).on('click','#vidmina_zapis',function() {
    acc = $(this).attr('acc');
    tp = $(this).attr('tp');
    train = $(this).attr('train');
    idzapis = $(this).attr('idzapis');
    cnt_day = $(this).attr('cnt_day');
    no_vidm = $(this).attr('no_vidm');
    sotr_name = $(this).attr('sotr_name');
    ip_club = $(this).attr('ip_club');

    //   post_string_ = 'train='+train+'&acc='+acc+'&tp='+tp+'&idzapis='+idzapis;
    // alert(post_string_)
    if (no_vidm==1){
        alert('Вибачте. Можливості скасувати запис із збереженням візиту немає.  Візит, який не був скасований вчасно, буде списано з абонементу.');
    }else
    {
        name = $('#zapis_'+idzapis).find('.tov_name').text();
        // alert('#zapis_'+idzapis)
        time_period = $('#zapis_'+idzapis).find('.time_period').text();
        $('#dialog_text').html(name+' <br>( ' +time_period+')');
        $('#staticBackdrop').modal({backdrop:'static', keyboard:true});
        $('#staticBackdrop').modal('show');
    }

    //content = ajax_content('ajax_del_tren',post_string_);
    //$('#content').html(content);

})
$(document).on('click','.buttonDELZapis',function(){
    $('#staticBackdrop').modal('hide');
    post_string_ = '&ip_club='+ip_club+'&acc='+acc+'&trenid='+train+'&tp='+tp+'&cnt_day='+cnt_day+'&name='+name+'&time_period='+time_period+'&sotr_name='+sotr_name;
    //    alert(post_string_)
    content = ajax_content('ajax_del_zapis',post_string_);
    $('#content').html(content);
    //  alert('zapis='+trenid +'   ==osttren='+osttren)
});
$(document).on('click','#zapis_zan',function() {
    reserv = $(this).attr('reserv');
    post_string_ = 'trenid='+trenid+'&acc='+acc+'&reserv='+reserv+'&ip_club='+ip_club;
    //   alert(post_string_)
    content = ajax_content('ajax_zapis_tren',post_string_);
    $('#content').html(content);

})
$(document).on('click','#vidm_zan',function() {
    post_string_ = 'trenid='+trenid+'&acc='+acc+'&ip_club='+ip_club;

    content = ajax_content('ajax_del_tren',post_string_);
    $('#content').html(content);

})
$(document).on('click','.buttonOK',function(){
    $('#staticBackdrop').modal('hide');
    post_string_ = '&dat='+dat+'&acc='+acc+'&trenid='+trenid+'&ost_all='+ost_all+'&ip_club='+ip_club+'&acctozapis='+acctozapis+'&time_from='+time_from;
    // alert(post_string_)
    content = ajax_content('ajax_fitness_zapis',post_string_);
    $('#content').html(content);
    //  alert('zapis='+trenid +'   ==osttren='+osttren)
});
$(document).on('click','#back',function(){

    $('#vik').show();
    coolButton.hide();
    $('#trenerovka').hide();
})
$(document).on('click','.trenerovka',function() {
    trenid = $(this).attr('trenid');
    acctozapis = $(this).attr('acctozapis');
    ip_club = $(this).attr('ip_club');
    ost_all = $(this).attr('ost_all');

    time_from = $(this).attr('time_from');
    //   alert(price)
    //   alert('split='+split);
    name = $(this).find('.tov_name').text();
    time_period = $(this).find('.time_period').text();
    day_vibor = $('#onlydat').text();
    // alert(trenid+' osttren='+osttren)
    $('#dialog_text').html(name+' <br>('+day_vibor+' ' +time_period+')');
    $('#staticBackdrop').modal({backdrop:'static', keyboard:true});
    $('#staticBackdrop').modal('show');
})
$(document).on('click','.but_day',function() {
    dat = $(this).attr('dat');
    acc = $(this).attr('acc');
    ip_club = $(this).attr('ip_club');
    post_string_ = 'dat='+dat+'&acc='+acc+'&ip_club='+ip_club;
//alert(post_string_)
    content_first =  $('#content').html();
    content = ajax_content('ajax_get_tren',post_string_);
    $('#content').html(content);
    $('#day_vibor').html('Вибраний Вами день: <span id="onlydat">'+dat+'</span>');
})
$(document).on('click','#back_fitness',function(){
    $('#content').html(content_first);

})
$(document).on('change','#type_document',function(event) {
    if ($(this).val()!=1 ) {
        $('#CERdivDor').addClass('d-none');
        if ($('#CER').val()=='')   $('#CER').val('-')

    }else
    {
        $('#CER').val('');
        $('#CERdivDor').removeClass('d-none');
    }

});
function select2Vibor() {
 //   alert('select2Vibor22')
    const clubVal = $('#clubSelect').val();
    
    const selectedStaffId = $('#SotrSelect').attr('sotrSelected');
    if (selectedStaffId) {
        const selectedName = $('#SotrSelect').attr('sotrNameSelected');
        const option = new Option(selectedName, selectedStaffId, true, true);
        $('#SotrSelect').append(option).trigger('change');

    }
    $.post(globalServerAdress, { action: 'select_sotr', club: clubVal }, function(data) {
        staffList = data.map(item => ({
            id: item.ID,
            text: item.NAME,
            name: item.NAME,
            role: item.ROLE
        }));



        $('#SotrSelect').select2({
            placeholder: 'Оберіть співробітника',
            allowClear: true,
            data: staffList,
            templateResult: formatRow,
            templateSelection: formatSelection,
            escapeMarkup: m => m
        });

        // Встановлюємо вибір
        if (selectedStaffId !== "0" && selectedStaffId !== "" && selectedStaffId !== null) {
            $('#SotrSelect').val(selectedStaffId).trigger('change');
        } else {
            $('#SotrSelect').val('').trigger('change');
        }
    }, 'json');


    $('#clubSelect').select2({
        placeholder: "Оберіть клуб",
        allowClear: true,
        width: '100%'
    });
}
function formatRow(item) {
    if (!item.id || item.id === "0") {
        return 'Співробітника не вибрано';
    }

    return `
    <div style="
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 6px 10px;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
    ">
      <div style="font-weight: 500; color: #212529;">${item.name}</div>
      <div style="font-size: 13px; color: #6c757d; white-space: nowrap;">${item.role}</div>
    </div>
  `;
}

function formatSelection(item) {
    if (!item.id || item.id === "0") {
        return 'Співробітника не вибрано';
    }
    return item.name || item.text;
}
$(document).on('click','#save_club',function(e) {
    e.preventDefault(); // не дозволяємо стандартну відправку форми

    club_id = $(this).attr('club_id');
    const form = $(this).closest('form');
    const data = form.serialize() + '&action=edit_club&club_id='+club_id;


    $.post(globalServerAdress, data, function(response) {
         // Якщо потрібно оновити щось без перезавантаження
        if (response.status === 'ok') {
            alert('Дані збережені.')
        } else alert('Виникла помилка при збережені.')

        location.reload(); // або без reload — оновити тільки частину
    }).fail(function(xhr) {
        alert('Помилка при збереженні: ' + xhr.statusText);
    });
});


$(document).on('click','#but_zmina_prav',function() {
    chat_id = $(this).attr('chat_id');
    spivrob = $(this).attr('spivrob');
    clubSelect = $('#clubSelect').val();
    accessLevel = $('#accessLevel').val();
    SotrSelect = $('#SotrSelect').val();

    SotrSelectName = $('#SotrSelect option:selected').text();
   // alert(SotrSelectName)
if (accessLevel==4 && SotrSelect==0) alert('Для тренерів вибрати співробітника обов"язково')
    else {
    $.post(globalServerAdress, {
        action: 'update_prava',
        chat_id: chat_id,
        clubSelect: clubSelect,
        accessLevel: accessLevel,
        SotrSelect: SotrSelect,
        SotrSelectName : SotrSelectName,
        admin : admin,
        spivrob : spivrob
    }, function (response) {
        if (response.status === 'ok') {
            alert('Дані збережені.')
        } else alert('Виникла помилка при збережені.')

    }, 'json');
}



 //   content_first =  $('#content').html();
 //   content = ajax_content('ajax_get_checks_acc',post_string_);
  //  $('#content').html(content);
})
$(document).on('click','.acc_vibor',function() {
    acc = $(this).attr('kod');
    admin = $(this).attr('admin');
    spivrob = $(this).attr('spivrob') || "0";
    chat_id = $(this).attr('chat_id');

    //   alert(price)
    //   alert('split='+split);
    name = $(this).find('.acc_name').text();
    post_string_ = 'name='+name+'&acc='+acc+'&spivrob='+spivrob+'&admin='+admin+'&chat_id='+chat_id;
//alert(post_string_)
    content_first =  $('#content').html();
    content = ajax_content('ajax_get_checks_acc',post_string_);
    $('#content').html(content);
})




$(document).on('click','.club_vibor',function() {
    club_id = $(this).attr('club_id');

    name = $(this).find('.acc_name').text();
    post_string_ = 'club_id='+club_id;
//alert(post_string_)
    content_first =  $('#content').html();
    content = ajax_content('edit_club',post_string_);
    $('#content').html(content);
})
$(document).on('click','#find_acc',function() {
    admin = $(this).attr('admin');
  //  alert(admin)
    isVaild = isValidFieldForms('formacc');
    if (isVaild) {

        find_acc();
        event.preventDefault();
    }else {
        //    alert('false')
        event.preventDefault();
    }

})
function find_acc(){
    phone =   $('#phone').val();
    search_phone_card =   $('#search_phone_card').val();
    //  alert(admin)
    post_string_ = '&phone='+phone+'&search_phone_card='+search_phone_card+'&admin='+admin;
  //  alert(post_string_)
    content_first =  $('#content').html();

    //   alert('tyt2')
    content = ajax_content('get_check',post_string_);



    $('#content').html(content);
}
$(document).on('click','#back_findacc',function(){
    $('#content').html(content_first);

})

function clear_content()
{  //alert('sddssd');

    secundomer.Timer.toggle();
    $("#content").html('<div></div>  .......');
}
// Common functions
function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {str = '0' + str;}
    return str;
}
function formatTime(time) {
    var min = parseInt(time / 6000),
        sec = parseInt(time / 100) - (min * 60),
        hundredths = pad(time - (sec * 100) - (min * 6000), 2);
    return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ":" + hundredths;
}
function getBirthday(birtd){

    if (birtd) {
        let today = new Date(); // Mon Nov 23 2020 15:23:46 GMT+0300 (Москва, стандартное время)
        let Thisyear = today.getFullYear();
        [year, month,day ] = birtd.split('-')
        //  console.log('day='+day+' month='+month+' year='+year)
        diff_y = Thisyear- year
        //  console.log('diff_y='+diff_y)
        if (diff_y>=4)
        {
            $('#slugeb_info').addClass('d-none');
            $('#slugeb_info').html('');
            return true;

        }else
        {
            $('#slugeb_info').removeClass('d-none');
            $('#slugeb_info').html('Некоректна дата народження. Вам не повинно бути менше 16 років!');
            return false;
            //    event.preventDefault();
            //   console.log('NO correct')
        }
    }else
    {
        $('#slugeb_info').removeClass('d-none');
        $('#slugeb_info').html('Некоректна дата народження. Дата народження повинна бути заповнена!');
        return false;
    }
}
function isValidFieldForms(form){
    // alert('isValidFieldForms')

    //  $("#reg").get(0).checkValidity();
    // $("#reg").addClass('was-validated');
    if($("#"+form)[0].checkValidity()) {
        return true;
    } else {
        $("#"+form).addClass('was-validated');
        return false;
        // $("#reg")[0].reportValidity()
    }
}

$(document).on('click','.ajax_send',function(){
    // alert('dddd');
    $('#start_synh').hide();
    post_string_ = 'action_syn=start';

    //  alert(post_string_);
    // content_first =  $('#content').html();
    send_ajax('synhron_action',post_string_);
});

var action_syn='';

var status_ajax_func = '';
// новая функция аякс СИНХРОННАЯ которая не заметно выполняет действия аякс и возвращает текст какой-то плюс может выполнить
// по возвращению любые javascript функциии
function ajax_content(action_,post_string_){
    //  alert('ajax_content');
//    alert(globalServerAdress);
    action = '';
    post_string = '';

    action_ = (typeof action_ == 'undefined' ? '' : action_);
    action = (action_ == '' ? action : action_);

    post_string_ = (typeof post_string_ == 'undefined' ? '' : post_string_);
    post_string = ( post_string_ == '' ? post_string : post_string_);
    $('#slugeb_info').addClass('d-none');



    inputValue = "ajax_method=2&action=" + action + "&" + post_string;
    content_ajax='hello222';
    $.ajax({
        url: globalServerAdress,             // указываем URL и
        type: "POST",
        async: false, // выполняем синхронно, по умолчанию true асинхронно
        dataType : "json",      // тип загружаемых данных
        data: inputValue,
        success: function (json, textStatus) { // вешаем свой обработчик на функцию success

            post_return = (typeof json.post_return == 'undefined' ? '' : json.post_return);
            content_ajax = (typeof json.content == 'undefined' ? '' : json.content);
            // console.log('content_ajax='+content_ajax);
            message_user = (typeof json.message_user == 'undefined' ? '' : json.message_user);
            error_ajax = (typeof json.error == 'undefined' ? '' : json.error);
            action_syn = (typeof json.action_new == 'undefined' ? '' : json.action_new);

            if (error_ajax)
            {
                error_fun(message_user);
            }
            status_ajax_func = (typeof json.status == 'undefined' ? '' : json.status);
            java_script = (typeof json.java_script == 'undefined' ? '' : json.java_script);
            //    alert(java_script);
            if (message_user)   $('#message_user').html(message_user);  // вывести сообщение
            if (java_script!=''){    eval(java_script); }
            // добавил логику для цикл вывода

        },
        error: function(){
            text ='Виникли проблеми при передачі в мережі. Попробуйте ще раз! '
            error_fun(text);
        }
    });
    return content_ajax;
}

var funkc_return = 'content_return';
function send_ajax(action_,post_string_){
    //  alert('ajax_content');
//    alert(globalServerAdress);
    action = '';
    post_string = '';

    action_ = (typeof action_ == 'undefined' ? '' : action_);
    action = (action_ == '' ? action : action_);

    post_string_ = (typeof post_string_ == 'undefined' ? '' : post_string_);
    post_string = ( post_string_ == '' ? post_string : post_string_);
    $('#slugeb_info').addClass('d-none');



    inputValue = "ajax_method=2&action=" + action + "&" + post_string;
    content_ajax='hello222';
    $.ajax({
        url: globalServerAdress,             // указываем URL и
        type: "POST",
        async: true, // по умолчанию true асинхронно
        dataType : "json",      // тип загружаемых данных
        data: inputValue,
        success: function (json, textStatus) { // вешаем свой обработчик на функцию success
            window.Funkc = funkc_return;
            window.json = json;
            eval(window.Funkc+'()')
            // добавил логику для цикл вывода

        },
        error: function(){
            text ='Виникли проблеми при передачі в мережі. Попробуйте ще раз! '
            error_fun(text);
        }
    });
    return content_ajax;
}
function content_return(){
    json =window.json;
    post_return = (typeof json.post_return == 'undefined' ? '' : json.post_return);
    content_ajax = (typeof json.content == 'undefined' ? '' : json.content);
    // console.log('content_ajax='+content_ajax);
    message_user = (typeof json.message_user == 'undefined' ? '' : json.message_user);
    error_ajax = (typeof json.error == 'undefined' ? '' : json.error);
    action_syn = (typeof json.action_new == 'undefined' ? '' : json.action_new);
    //  alert('action_syn='+action_syn);

//alert(post_return)
    if (error_ajax)
    {
        error_fun(message_user);
    }
    status_ajax_func = (typeof json.status == 'undefined' ? '' : json.status);
    java_script = (typeof json.java_script == 'undefined' ? '' : json.java_script);


    //    alert(java_script);
    /// if (message_user)   $('#message_user').html(message_user);  // вывести сообщение
    if (java_script!=''){    eval(java_script); }
    obj=$("#content div:last");
    //  alert(content_ajax)
    obj.after(content_ajax);
    if (action_syn!='end' && action_syn !='error')
    {
        post_string_ = 'action_syn='+action_syn+'&'+post_return;
        // alert(post_string_)
        send_ajax('synhron_action',post_string_);
    }
    else
    {
        $('#start_synh').show();
    }

}
