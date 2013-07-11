$(document).ready(function () {
        if ($("select#timeoff_type").val() == 1){
           $("div#office").hide(); 
        }
        $("select#timeoff_type").change(function(){
            selectedVal = $(this).find(":selected").val();
            if (selectedVal == otherOffice) {
                $("div#office").show();
            }
            else{
                $("div#office").hide();
            }
        });
});
