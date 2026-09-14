let lastId = 0;
let offset = 0;
let limit = 30;
let loadingOlder = false;
let isFetching = false;
let userIsAtBottom = true;
let hasMoreHistory = true;

$(document).ready(function () {
    // Перший запуск — завантажити останні повідомлення
    fetchMessages(false);

    // Регулярне оновлення нових
    setInterval(() => fetchMessages(false), 2000);


    // 🔄 4. Фонове оновлення статусів прочитання (кожні 10 секунд)
    setInterval(() => {
        const $chat = $('#chat');
        const chatid = $chat.attr('chatid');
        const who_user_write = $chat.attr('who_user_write');

        $.post(globalServerAdress, {
            action: 'chat_check_read',
            chatid: chatid,
            who_user_write: who_user_write
        }, function (readIds) {
            if (Array.isArray(readIds)) {
                readIds.forEach(id => {
                    const msgElem = $('.message[data-id="' + id + '"]');
                    msgElem.removeClass('unread');
                    msgElem.find('.status').text('✅✅');
                });
            }
        }, 'json');
    }, 10000);
    // ✅ ОБРАБОТЧИК ПРОКРУТКИ
    $('#chat').on('scroll', function () {
        const $this = $(this);
        const scrollTop = $this.scrollTop();
        const scrollHeight = this.scrollHeight;
        const clientHeight = $this.innerHeight();

        userIsAtBottom = (scrollTop + clientHeight >= scrollHeight - 10);

        // ✅ КРИТИЧЕСКАЯ ПРОВЕРКА
        if (scrollTop === 0 && !loadingOlder && hasMoreHistory) {
            loadingOlder = true;
            fetchMessages(true, offset); // обязательно передаём offset
        }
    });

});

function fetchMessages(init = false, pageOffset = 0) {
    if (isFetching) return; // блокуємо повторний виклик
    isFetching = true;

    const $chat = $('#chat');
    const club = $chat.attr('club');
    const chatadmin = $chat.attr('chatadmin');
    const chatid = $chat.attr('chatid');
    const who_user_write = $chat.attr('who_user_write');

    const postData = {
        action: 'chart_load', 
        club: club,
        chatid: chatid,
        who_user_write: who_user_write,
        last_id: lastId,
        init: init ? 1 : 0,
        offset: pageOffset,
        limit: limit
    };

    $.post(globalServerAdress, postData, function (response) {
        const data = response;
        // ✅ ЕСЛИ ИСТОРИЯ ЗАКОНЧИЛАСЬ — ОТМЕНА
        if (init && Array.isArray(data)) {
            if (data.length === 0) {
                hasMoreHistory = false;
                loadingOlder = false;
                isFetching = false;
                return;
            }

            if (data.length < limit) {
                hasMoreHistory = false;
            }
        }


        if (!response) return;

     //   if (init && data.length === 0) return;

        let unreadIds = [];
        let maxId = lastId;

        // Запам’ятати попередню висоту чату
        const prevHeight = $chat[0].scrollHeight;
        const messages = init ? [...data].reverse() : data;
        messages.forEach(msg => {
            const isRead = msg.is_read == 1 || msg.is_read === "1";
         //   if ($('.message[data-id="' + msg.id + '"]').length) return; // вже є
            const existing = $('.message[data-id="' + msg.id + '"]');
            if (existing.length) {
                const statusElem = existing.find('.status');

                // ✅ обновим статус, если он поменялся
                if (msg.is_read && statusElem.text() === '✅') {
                    statusElem.text('✅✅');
                    existing.removeClass('unread');
                }

                return; // не добавляем дубликат
            }
            const message = $('<div>')
                .addClass('message')
                .addClass(msg.who_user_write == who_user_write ? 'user1' : 'user2')
                .addClass(msg.is_read ? '' : 'unread')
                .attr('data-id', msg.id);

            const isMine = String(msg.who_user_write) === String(who_user_write);
            let status = '';

            if (isMine) {
                status = msg.is_read ? '✅✅' : '✅';
            } else if (msg.is_read) {
                status = '✅✅'; // показать что ты прочитал чужое
            }

            const dateObj = new Date(msg.time_send);
            const dateStr = dateObj.toLocaleDateString('uk-UA');
            const timeStr = dateObj.toLocaleTimeString('uk-UA', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            const timeText = `${dateStr} ${timeStr}`;

            const statusSpan = $('<span>').addClass('status').text(status);
            const time = $('<div>').addClass('timestamp').append(timeText, $('<span>').addClass('status').text(status));

            message.append(time).append(document.createTextNode(msg.text));

            if (msg.id > maxId) {
                maxId = msg.id;
            }
            if (init) {
                   $chat.prepend(message); // зверху
             } else {

                  $chat.append(message);
                if (!init && userIsAtBottom)   $chat.scrollTop($chat[0].scrollHeight);
            }

            if (msg.is_read == 0 && msg.who_user_write != who_user_write) {
                unreadIds.push(msg.id);
            }


        });
        lastId = maxId;
        if (!init && userIsAtBottom)         $chat.scrollTop($chat[0].scrollHeight);

        if (init) {
            // Встановлюємо scroll на ту ж позицію (щоб не скакало)
            const newHeight = $chat[0].scrollHeight;
            $chat.scrollTop(newHeight - prevHeight);
            offset += limit;
            loadingOlder = false;
        }

        if (unreadIds.length > 0) {
            $.post(globalServerAdress, {
                action: 'chat_mark_isread',
                ids: unreadIds,
                who_user_write: who_user_write
            }, function (response) {
                if (response.status === 'ok' && response.updated_ids) {
                    response.updated_ids.forEach(id => {
                        const msgElem = $('.message[data-id="' + id + '"]');
                        msgElem.removeClass('unread');
                        msgElem.find('.status').text('✅✅');
                    });

                    // І ще раз оновимо свої повідомлення, які були тільки з однією галочкою
                    $('.message.user1 .status').each(function () {
                        if ($(this).text() === '✅') {
                            $(this).text('✅✅');
                        }
                    });
                }
            }, 'json');
        }




        isFetching = false;
    }, 'json'); // ← вот тут ключевой момент!
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