<div>
    <h3> Добавить отгул </h3>

    <form method="POST" action='{$_root}/users/vacation' id="timeoffs">
        <input type="hidden" id="id" name="id" value='{$id}'/>
        <label for="from"> Выберите начало периода: </label>
        <input type="text" id="from" name="from" />
        <label for="to"> Выберите окончание периода: </label>
        <input type="text" id="to" name="to" />
        
        <label for="timeoff_type"> Тип: </label>
        <select id="timeoff_type" name = "vtype">
            {foreach from=$statuses item=stat}
                <option value = "{$stat['type_id']}"> {$stat['name']} </option>
            {/foreach}
        </select>
        <div id="office">
            <label for ="other_office"> Рабочее время: </label>
            <input type="text" id="other_office" name="other_office"/>
        </div>
        <br>
        <input type="submit" value="Добавить" name="submit" id="add" class="btn btn-success">
    </form>
</div>
