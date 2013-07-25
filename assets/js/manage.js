$(document).ready(function () {
    $("select#userId").change(function(){
            selectedVal = $(this).find(":selected").text();
            array = selectedVal.split(" ");
            if (array.length > 1) {
                $('#secondName').val(array[0]);
                $('#firstName').val(array[1]);
            } else {
                $('#secondName').val('');
                $('#firstName').val('');
            }
        });
        var isOther = false;
        $('form#user').submit(function(e){
            if (($('#secondName').val().length == 0 || $('#firstName').val().length == 0 
                    || $('#middleName').val().length == 0 
                    || $('#department').val() == 0
                    || $('#position').val() == 0
                    || $('#email-val').val().length == 0)
                    && !isOther ) {
                    e.preventDefault();
                    alert("Поля отмеченные * обезательные");
            } else {
                if($('#secondName').val().length == 0 || $('#firstName').val().length == 0 
                    || $('#middleName').val().length == 0 
                    || $('#position').val() == 0){
                    e.preventDefault();
                    alert("Поля отмеченные * обезательные");
                }
            }
        });
        if ($('#is_shown').val() == 0){
            var a = 1;
        } else {
            var a = 0;
        }
        $('#is_shown').click(function(){
            if(a % 2 == 0){
                $('#tid').addClass('hidden')
            } else {
                $('#tid').removeClass('hidden')
            }
                a++
            });

        $('#other').click(function(){
            isOther = true
            $('#tid').addClass('hidden')
            $('#depart').addClass('hidden')
            $('#email').addClass('hidden')
            $('#permission').addClass('hidden')
            $('#birthday').addClass('hidden')
            $('#startwork').addClass('hidden')
            $('#endwork').addClass('hidden')
            $('#halftime').addClass('hidden')
            $('#show-in-timesheet').addClass('hidden')
        });

        $('#outside-worker').click(function(){
            isOther = false
            $('#tid').addClass('hidden')
            $('#depart').removeClass('hidden')
            $('#email').removeClass('hidden')
            $('#permission').removeClass('hidden')
            $('#birthday').removeClass('hidden')
            $('#startwork').removeClass('hidden')
            $('#endwork').removeClass('hidden')
            $('#halftime').removeClass('hidden')
            $('#show-in-timesheet').addClass('hidden')
        });

        $('#worker').click(function(){
            isOther = false
            $('#tid').removeClass('hidden')
            $('#depart').removeClass('hidden')
            $('#email').removeClass('hidden')
            $('#permission').removeClass('hidden')
            $('#birthday').removeClass('hidden')
            $('#startwork').removeClass('hidden')
            $('#endwork').removeClass('hidden')
            $('#halftime').removeClass('hidden')
            $('#show-in-timesheet').removeClass('hidden')
        });
});