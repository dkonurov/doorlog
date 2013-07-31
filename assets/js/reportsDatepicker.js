$(function() {
    $('#datepicker').datepicker( {
    monthNamesShort: ['Янв','Февр','Март','Апр','Май','Июнь', 'Июль','Авг','Сент','Окт','Нояб','Дек'],
    yearRange: "c-2:",
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: 'mm.yy',
    closeText : "Готово",
    currentText: "Текущий месяц",
    maxDate: "defaultDate",
    beforeShow: function(input) {
        var date = $(input).val();
        var formattedDate = date.substr(0, 3) + '01.' + date.substr(3);
        return { defaultDate: new Date(formattedDate) }
    },

    onClose: function(dateText, inst) {
        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
        $(this).datepicker('setDate', new Date(year, month, 1));
        $("#datepickerforsave").val($('#datepicker').val());
    }
    });

    $('#ui-datepicker-div').addClass('disable-days');
});
