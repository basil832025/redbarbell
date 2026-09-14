window.onload = function() {
    const container = document.querySelector('.chat-container');
    container.scrollTop = container.scrollHeight;
}



let lastId = 0;
let userToggle = true;


setInterval(fetchMessages, 2000); // каждые 2000 мс = 2 секунды


function fetchMessages() {
    let unreadIds = [];
    club = $("#chat").attr('club');
    chatadmin = $("#chat").attr('chatadmin');
    unreadIdsOld = $("#last_id").attr('unreadarr');
    chatid = $("#chat").attr('chatid');
    who_user_write = $("#chat").attr('who_user_write');
    if (lastId==0) lastId = $("#last_id").attr('last_id')
    post_string_ = 'club='+club+'&who_user_write='+who_user_write+'&chatid='+chatid+'&last_id='+lastId;
    //  alert(post_string_)
    //  alert(unreadIds)
    content = ajax_content('chart_load',post_string_);
    if (content){
        data = JSON.parse(content);
        data.forEach(msg => {
            if (msg.is_read == 0 && msg.who_user_write != who_user_write) {
                unreadIds.push(msg.id);
            }
            //    alert(unreadIds)
            //  alert(msg)
            const message = $('<div>')
                .addClass('message')
                .addClass(msg.who_user_write == who_user_write ? 'user1' : 'user2')
                .addClass(msg.is_read ? '' : 'unread')
                .attr('data-id', msg.id);
            // const time = $('<time>').text(new Date(msg.time_send).toLocaleTimeString());
            status = msg.is_read ? "✅✅" : "✅";
            const time = $('<div>').addClass('timestamp').html(new Date(msg.time_send).toLocaleTimeString()+'<span class="status">'+status+'</span>');
            message.append(time).append(document.createTextNode(msg.text));
            $('#chat').append(message);
            lastId = msg.id;
            $('#chat').scrollTop($('#chat')[0].scrollHeight);
        });
        // помечаем не прочитанные сообщения прочитанными
        if (unreadIds.length > 0) {
            if (unreadIdsOld.length > 0)
            {
                alert(unreadIdsOld);
                unreadIds.push(unreadIdsOld);
            }
            // alert(unreadIds)
            //post_string_ = 'club='+club+'&who_user_write='+who_user_write+'&chatid='+chatid+'&last_id='+lastId;

            post_string_ = 'ids='+unreadIds;
            alert(post_string_)
            content = ajax_content('chat_mark_isread',post_string_);
            if (content){
                unreadIds.forEach(id => {
                    alert('id='+id)
                    const msgElem = $('.message[data-id="' + id + '"]');
                    msgElem.removeClass('unread');
                    msgElem.find('.status').text('✅✅');
                });
            }

        }
    }

}

$(document).on('click','#send_sms',function(){
    ip_club = $("#chat").attr('ip_club');
    chatadmin = $("#chat").attr('chatadmin');
    club = $("#chat").attr('club');
    chatid = $("#chat").attr('chatid');
    who_user_write = $("#chat").attr('who_user_write');
    const text = $('#chat-input').val().trim();
    if (!text) return;
    post_string_ = 'club='+club+'&chatadmin='+chatadmin+'&chatid='+chatid+'&who_user_write='+who_user_write+'&text='+text;
    //  alert(post_string_)
    content = ajax_content('chart_send',post_string_);
    if (content=='OK') {
        $('#chat-input').val('');
    }
    // $('#content').html(content);
    //  alert('zapis='+trenid +'   ==osttren='+osttren)
});
$(function () {
    const $emojiBtn = $('.emoji-toggle');
    const $emojiBox = $('#emoji-picker');
    const $input = $('#chat-input');

    // Показать / скрыть emoji
    $emojiBtn.on('click', function (e) {
        e.stopPropagation();
        $emojiBox.toggle();
    });

    // Вставка смайла
    $emojiBox.on('click', 'span', function (e) {
        e.stopPropagation(); // предотвращаем закрытие
        const emoji = $(this).text();
        const cursorPos = $input.prop('selectionStart');
        const v = $input.val();
        const textBefore = v.substring(0, cursorPos);
        const textAfter = v.substring(cursorPos, v.length);
        $input.val(textBefore + emoji + textAfter).focus();
    });

    // Закрыть при клике вне
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#emoji-picker, .emoji-toggle').length) {
            $emojiBox.hide();
        }
    });
});