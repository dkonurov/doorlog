$(function() {
  $( "#datepicker" ).datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange: "-80:-16",
    defaultDate: '-16y'
  });
});

$(function() {
  $( "#datepicker-start" ).datepicker({
    changeMonth: true,
    changeYear: true,
  });
});

$(function() {
  $( "#datepicker-end" ).datepicker({
    changeMonth: true,
    changeYear: true,
  });
});
