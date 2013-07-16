$(document).ready(function () {
        if ($("select#timeoff_type").val() == 1) {
           $("div#office").hide();
        }
        $("select#timeoff_type").change(function() {
            selectedVal = $(this).find(":selected").val();
            if (selectedVal == otherOffice) {
                $("div#office").show();
            } else {
                $("div#office").hide();
            }
        });
        $('form#timeoffs').submit(function(e) {
            if ($("div#office").is(':visible')) {
                if ($("#other_office").val().length == 0) {
                    alert('Поле рабочее время обезательно для заполнения');
                    e.preventDefault();
                }
                if ($("#other_office").val() < 0 || $("#other_office").val() > 8) {
                    alert('Рабочее время не больше 8 и не меньше 0');
                    e.preventDefault();
                }
            }
        });
});
