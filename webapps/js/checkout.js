// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
    })
})()

$( function() {
    $.datepicker.regional['ua'] = {
        closeText: 'Закрить',
        prevText: 'Попередній місяць',
        nextText: 'Наступний місяць',
        currentText: 'Сьогодні',
        monthNames: ['Січень','Лютий','Березень','Квітень','Травень','Червень',
            'Липень','Серпень','Вересень','Жовтень','Листопад','Грудень'],
        monthNamesShort: ['Січ','Лют','Бер','Кві','Тра','Чер',
            'Лип','Сер','Вер','Жов','Лис','Гру'],
        dayNamesMin: ['Нд','Пн','Вт','Ср','Чт','Пт','Сб'],
        weekHeader: 'Нед',
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['ua']);
    $(".datepicker").datepicker({
        inline: true,
        firstDay: 1,
        dateFormat: "dd.mm.yy",
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        yearRange: "-80:+00"
    });
})