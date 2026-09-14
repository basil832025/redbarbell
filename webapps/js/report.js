$(document).ready(function() {

    $.fn.datepicker.dates['en'] = {
        days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
        daysShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
        daysMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
        months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
        monthsShort: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
        today: "Сегодня",
        clear: "Очистить",
        format: "dd.mm.yyyy",
        titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
        weekStart: 1,

        monthsTitle: "Месяцы"
    };
});
$(function(){
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd.mm.yyyy"
    });

});
$(document).on('click','#report_itog',function(){
    date_from=$('#date_from').val();
    date_to=$('#date_to').val();
    clubId=$('#clubId').val();
    podr=$('#podr').val();
    admin = $(this).attr('admin');
    sotr=$('#SotrSelect').val();
    if (clubId!='' && date_from!='' && date_to!=''){
        post_string_ = 'action_report=report_view&admin='+admin+'&date_from='+date_from+'&date_to='+date_to+'&ip_club='+clubId+'&podr='+podr+'&sotr='+sotr;
        // alert(post_string_);
        content = ajax_content('report_pers_itog',post_string_);


        var modal = $('#staticBackdrop');
        var modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
        $('#content').html(content);
    } else {
        alert('Виберіть обов"язкові параметри')
    }

});
$(document).on('click', '#report_pers_detal', function () {
    let date_from = $('#date_from').val();
    let date_to = $('#date_to').val();
    clubId=$('#clubId').val();
    let podr = $('#podr').val();
    admin = $(this).attr('admin');
    sotr=$('#SotrSelect').val();

    if (clubId !== '' && date_from !== '' && date_to !== '') {
        post_string_ = 'action_report=report_view&admin='+admin+'&date_from='+date_from+'&date_to='+date_to+'&ip_club='+clubId+'&podr='+podr+'&sotr='+sotr;
      //  alert(post_string_)
        // Показать сообщение ожидания
        $('#content').html('<div class="text-center py-4"><span class="spinner-border text-primary" role="status"></span><br>Звіт формується, зачекайте...</div>');

        // Закрыть модальное окно
        let modal = $('#staticBackdrop');
        let modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();

        // Загрузка данных
        setTimeout(function () {
            let content = ajax_content('report_pers_detal', post_string_);
            $('#content').html(content);
        }, 100); // небольшая задержка, чтобы spinner успел отобразиться

    } else {
        alert('Виберіть обов"язкові параметри');
    }
});

$(document).on('click', '#report_info_acc', function () {
    const dateFrom = $('#date_from').val();
    const dateTo = $('#date_to').val();
    const clubId = $('#clubId').val();
    const podr = $('#podr').val();
    const acc = $('#AccSelect').val();
    const admin = $(this).attr('admin');

    if (!clubId || !dateFrom || !dateTo || !acc || acc === '0') {
        alert('Виберіть обов"язкові параметри та клієнта');
        return;
    }

    const postString = 'action_report=report_view'
        + '&admin=' + encodeURIComponent(admin)
        + '&date_from=' + encodeURIComponent(dateFrom)
        + '&date_to=' + encodeURIComponent(dateTo)
        + '&ip_club=' + encodeURIComponent(clubId)
        + '&podr=' + encodeURIComponent(podr)
        + '&acc=' + encodeURIComponent(acc);

    const modalElement = document.getElementById('staticBackdrop');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }

    $('#content').html('<div class="text-center py-4"><span class="spinner-border text-primary" role="status"></span><br>Звіт формується, зачекайте...</div>');
    setTimeout(function () {
        const content = ajax_content('report_info_acc', postString);
        $('#content').html(content);
    }, 100);
});

$(document).on('click', '#report_find_acc', function () {
    const query = $('#report_acc_search').val().trim();
    if (!query) {
        alert('Введіть номер телефону або частину ПІБ');
        return;
    }

    const admin = $(this).attr('admin');
    const searchType = $('#report_acc_search_type').val();
    const clubIp = $('#clubId').val();

    if (!clubIp) {
        alert('Спочатку виберіть клуб');
        return;
    }

    const postString = 'phone=' + encodeURIComponent(query)
        + '&search_phone_card=' + encodeURIComponent(searchType)
        + '&admin=' + encodeURIComponent(admin)
        + '&club_ip=' + encodeURIComponent(clubIp);

    $('#report_acc_results').html('<div class="text-center py-2"><span class="spinner-border spinner-border-sm text-primary"></span></div>');
    const content = ajax_content('get_check', postString);
    $('#report_acc_results').html(content);
    $('#report_acc_results a[href*="action=work_acc"]').remove();
});

$(document).on('change', '#clubId', function () {
    if ($('#AccSelect').length === 0) {
        return;
    }

    $('#AccSelect').val('0');
    $('#report_acc_selected').removeClass('alert-success').addClass('alert-secondary').text('Клієнта не вибрано');
    $('#report_acc_results').empty();
});

$(document).on('click', '#report_acc_results .acc_vibor', function () {
    const acc = $(this).attr('kod');
    const name = $(this).find('.acc_name').text().trim();

    $('#AccSelect').val(acc);
    $('#report_acc_selected').removeClass('alert-secondary').addClass('alert-success').text(name);
    $('#report_acc_results').empty();
});
$(document).on('click','#report_abon_mounts',function(){
    date_from=$('#date_from').val();
    date_to=$('#date_to').val();
    clubId=$('#clubId').val();

    if (clubId!='' && date_from!='' && date_to!=''){
        post_string_ = 'action_report=report_view&date_from='+date_from+'&date_to='+date_to+'&ip_club='+clubId;
        // alert(post_string_);
        content = ajax_content('report_abon_mounth',post_string_);


        var modal = $('#staticBackdrop');
        var modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
        $('#content').html(content);
    } else {
        alert('Виберіть обов"язкові параметри')
    }

});
$(document).on('click','#report_klients_treners',function(){
    date_from=$('#date_from').val();
    date_to=$('#date_to').val();
    clubId=$('#clubId').val();
    podr=$('#podr').val();
    sotr=$('#SotrSelect').val();
    if (clubId!='' && date_from!='' && date_to!=''){
        post_string_ = 'action_report=report_view&date_from='+date_from+'&date_to='+date_to+'&ip_club='+clubId+'&podr='+podr+'&sotr='+sotr;
        // alert(post_string_);
        content = ajax_content('report_klients_treners',post_string_);


        var modal = $('#staticBackdrop');
        var modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
        $('#content').html(content);
    } else {
        alert('Виберіть обов"язкові параметри')
    }

});
$(document).on('click','#butt_tov_skald',function(){
    tov=$('#tov').val();
     post_string_ = 'action_report=report_view&tov='+tov;
   // alert(post_string_);
   content = ajax_content('report_tov_sklad',post_string_);


    var modal = $('#staticBackdrop');
    var modalInstance = bootstrap.Modal.getInstance(modal);
    modalInstance.hide();
   $('#content').html(content);

});
$(document).on('click','.buttonOK[bool="true"]',function(){
    date_from=$('#date_from').val();
    date_to=$('#date_to').val();
    clubId=$('#clubId').val();
    if (clubId!='' && date_from!='' && date_to!=''){
        post_string_ = 'action_report=report_view&date_from='+date_from+'&date_to='+date_to+'&ip_club='+clubId;
        // alert(post_string_);
        content = ajax_content('report_kassa',post_string_);


        var modal = $('#staticBackdrop');
        var modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
        $('#content').html(content);
    } else {
        alert('Всі параметри обов"язково')
    }


});

var globalServerAdress = 'https://'+location.hostname+'/webapps/web.php';
//alert(globalServerAdress);
var status_ajax_func = '';
// новая функция аякс СИНХРОННАЯ которая не заметно выполняет действия аякс и возвращает текст какой-то плюс может выполнить
// по возвращению любые javascript функциии
function ajax_content(action_,post_string_){
   // alert(globalServerAdress);
   //   alert('ajax_content');
    action = '';
    post_string = '';

    action_ = (typeof action_ == 'undefined' ? '' : action_);
    action = (action_ == '' ? action : action_);

    post_string_ = (typeof post_string_ == 'undefined' ? '' : post_string_);
    post_string = ( post_string_ == '' ? post_string : post_string_);
    $('#slugeb_info').addClass('d-none');



    inputValue = "ajax_method=2&action=" + action + "&" + post_string;
     content_ajax='hello112';
   //  alert(inputValue);
    $.ajax({
        url: globalServerAdress,             // указываем URL и
        type: "POST",
        async: false, // выполняем синхронно, по умолчанию true асинхронно
        dataType : "json",      // тип загружаемых данных
        data: inputValue,
        success: function (json, textStatus) { // вешаем свой обработчик на функцию success

            post_return = (typeof json.post_return == 'undefined' ? '' : json.post_return);
            content_ajax = (typeof json.content == 'undefined' ? '' : json.content);
          //  console.log('content_ajax='+content_ajax);
            message_user = (typeof json.message_user == 'undefined' ? '' : json.message_user);
            error_ajax = (typeof json.error == 'undefined' ? '' : json.error);
            if (error_ajax)
            {
                error_fun(message_user);
            }
            status_ajax_func = (typeof json.status == 'undefined' ? '' : json.status);
            java_script = (typeof json.java_script == 'undefined' ? '' : json.java_script);
            // alert(java_script);
            if (message_user)   $('#message_user').html(message_user);  // вывести сообщение
            if (java_script!=''){    eval(java_script); }

        },
        error: function(){
            text ='Виникли проблеми при передачі в мережі. Спробуйте ще раз! '
            error_fun(text);
        }
    });
    return content_ajax;
}
function error_fun(text){
    $('#slugeb_info').removeClass('d-none');
    $('#slugeb_info').html(text);
}
function select2Vibor(club_def_ip) {
    //   alert('select2Vibor22')
    if (club_def_ip!='') {
     //   alert('club_def_ip='+club_def_ip)
        select_club(club_def_ip);
    }else {
        $('#clubId').on('change', function() {
            const clubVal = $(this).val();
            //  alert('Вы выбрали: ' + clubVal);
            select_club(clubVal);

        });
    }


}
function select_club(clubVal)
{
    $.post(globalServerAdress, { action: 'select_sotr', club: '-',club_ip : clubVal}, function(data) {
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
            dropdownParent: $('#staticBackdrop'), // укажите ID вашего модального окна
            templateResult: formatRow,
            templateSelection: formatSelection,
            escapeMarkup: m => m
        });

    }, 'json');
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
