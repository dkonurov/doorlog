$(document).ready(function () {
    $("select#userId").change(function(){
            var selectedVal = $(this).find(":selected").text();
            var array = selectedVal.split(" ");
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
                    alert("Поля отмеченные * обязательные");
            } else {
                if($('#secondName').val().length == 0 || $('#firstName').val().length == 0 
                    || $('#middleName').val().length == 0 
                    || $('#position').val() == 0){
                    e.preventDefault();
                    alert("Поля отмеченные * обязательные");
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
            $('.other').each(function (i) {
                $(this).addClass('hidden');
            });
        });

        $('#outside-worker').click(function(){
            isOther = false
            $('.worker').each(function (i) {
                $(this).removeClass('hidden');
            });
            $('.outside-worker').each(function (i) {
                $(this).addClass('hidden');
            });

        });

        $('#worker').click(function(){
            isOther = false
            $('.worker').each(function (i) {
                $(this).removeClass('hidden');
            });
        });
});