{extends "protected/views/index.tpl"}
{block name="javascript"}
    <script src="{$_root}/assets/js/reportsDatepicker.js"></script>
    <script src="{$_root}/assets/js/reportsAutocomplete.js"></script>
{/block}
{block name="title"} Отчет по посещаемости пользователя {/block}
    {block name="breadcrumbs"}
        <ul class="breadcrumb">
          <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
          <li class="active"> Отчет по посещаемости пользователя </li>
        </ul>
    {/block}
    {block name="pagetitle"}<h1> Отчет по посещаемости пользователя </h1>{/block}
    {block name="content"}

        <form id="reports" action="{$_root}/reports/timeoffsuser">

        <div id="user">
            {$userSelected=0}
            <select id="user_id" name="user_id">
            {foreach from=$allUsers item=user}
            {if {$user['id']} == {$smarty.get.user_id}}
                <option value = "{$user['id']}" {$userSelected=$user['id']} selected> {$user['s_name']} {$user['f_name']}</option>
            {else}
                <option value = "{$user['id']}"> {$user['s_name']} {$user['f_name']} </option>
            {/if}
            {/foreach}
            </select>
        </div>

        <label for="datepicker"> Дата </label>
        <input name="date" type="text" id="datepicker" value="{$timeoffsAttr['date']|date_format:"%m.%Y"}" />

    </form>
    <input form="reports" type="submit" id="add" value="Сформировать" class="btn btn-success" >
    <br>
    <br>
    <div class="span7">
    {if $reportAllDaysArray}
        <h3>{$name['user']}</h3>
        {include file='protected/views/Reports/timeoffs.tpl' reportAllDaysArray = $reportAllDaysArray}
    {/if}
    </div>
    {/block}
{/extends}