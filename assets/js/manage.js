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
         $('form#user').submit(function(e){
             if ($('#secondName').val().length == 0 || $('#firstName').val().length == 0 
                     || $('#middleName').val().length == 0 
                     || $('#department').val() == 0
                     || $('#position').val() == 0
                     || $('#email').val().length == 0) {
                     e.preventDefault();
            alert("Поля, отмеченные *, обязательные");
             }
             });

         a = 0;
         $('#is_shown').click(function(){
            if(a % 2 == 0){
              $('#tid').addClass('hidden')
            } else {
              $('#tid').removeClass('hidden')
            }
              a++
         });
});