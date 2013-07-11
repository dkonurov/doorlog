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
         $('form#user').submit(function(e){
             if ($('#secondName').val().length == 0 || $('#firstName').val().length == 0 
                     || $('#middleName').val().length == 0 
                     || $('#department').val() == 0
                     || $('#position').val() == 0
                     || $('#email').val().length == 0
                     || $('#phone').val().length == 0) {
                     e.preventDefault();
            alert("Поля отмеченные * обезательные");
             }
             });
});