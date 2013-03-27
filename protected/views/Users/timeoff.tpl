<script type="text/javascript">
    $(function() {
        $( "#from" ).datepicker({
            defaultDate: "+1w",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                $( "#to" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#to" ).datepicker({
            defaultDate: "+1w",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
                $( "#from" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    });


</script>

<script>
           $(document).ready(function()
                   {
                          $('#delete').bind('click',function(){
                                if(confirm("действительно хотите удалить?")){
                                        return true;
                                  }
                                else{
                                        return false;
                                   }
                           });
            });
        </script>
<div>
    <h2> Добавить отгул </h2>
    <form method="POST" action='/dsaddas/'>
        <p>
            <label for="from"> Выберите начало периода: </label>
            <input type="text" id="from" name="from" />
            <label for="to"> Выберите окончание периода: </label>
            <input type="text" id="to" name="to" />
            <label for="timeoff_type"> Тип: </label>
            <select id="timeoff_type">
                <option> Отпуск </option>
                <option> Заболел </option>
                <option> За свой счёт </option>
            </select>
        </p>

        <input type="submit" value="Добавить" name="submit" id="delete" class="btn btn-success">
    </form>
</div>