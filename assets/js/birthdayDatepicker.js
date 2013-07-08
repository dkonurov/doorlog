$(function() {
  $( "#datepicker" ).datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "-80:-16",
    dateFormat: 'dd.mm.yy',
    defaultDate: '-16y'
  });
});

$(function() {
    $( "#datepicker-start" ).datepicker({
        monthNamesShort: ['Янв','Февр','Март','Апр','Май','Июнь', 'Июль','Авг','Сент','Окт','Нояб','Дек'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        defaultDate: "+1w",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $( "#datepicker-end" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#datepicker-end" ).datepicker({
        monthNamesShort: ['Янв','Февр','Март','Апр','Май','Июнь', 'Июль','Авг','Сент','Окт','Нояб','Дек'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        defaultDate: "+1w",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $( "#datepicker-start" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
