{extends "protected/views/index.tpl"}
{block name="javascript"}
    <script src="{$_root}/assets/js/reportsDatepicker.js"></script>
    <script src="{$_root}/assets/js/reportsAutocomplete.js"></script>
{/block}
{block name="title"} Отчет по посещаемости отдела{/block}
    {block name="breadcrumbs"}
        <ul class="breadcrumb">
          <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
          <li class="active"> Отчет по посещаемости отдела</li>
        </ul>
    {/block}
    {block name="pagetitle"}<h1> Отчет по посещаемости отдела</h1>{/block}
    {block name="content"}

        <form id = "reports" type='GET' action = "{$_root}/reports/timeoffsdep">

        <div id="dep">
            <select id='dep_id' name='dep_id'>
            {foreach from=$allDep item=dep}
            {if {$dep['id']} == {$smarty.get.dep_id}}
                <option value = "{$dep['id']}" {$depSelected=$dep['id']} selected> {$dep['name']} </option>
            {else}
                <option value = "{$dep['id']}"> {$dep['name']} </option>
            {/if}
            {/foreach}
            </select>
        </div>


        <label for = "datepicker"> Дата </label>
        <input name = "date" type="text" id="datepicker" class='withoutDays' value = "{$timeoffsAttr['date']|date_format:"%m.%Y"}" />

    </form>
    <input form = "reports" type="submit" id="add" value = "Сформировать" class="btn btn-success" >
    <br>
    <br>
    <div class="span7">
    
    {if $totalDepInfo}
        <br>
        <br>
        <table class="table table-bordered table-striped table-hover reports">
            <thead>
                <th> Имя </th>
                <th> Часы </th>
                {foreach from=$totalDepInfo['statuses'] item=totalUserInfo}
                    <th>{$totalUserInfo['name']}</th>
                {/foreach}
            </thead>
            {foreach from=$totalDepInfo['totalUserStats'] item=user}
            <tr>
                <td><a href="{$_root}/reports/timeoffsuser?user_id={$user['id']}&date={$totalDepInfo['date']}">{$user['name']}</a></td>
                {foreach from=$user['stats'] item=userStats}
                <td>{$userStats}</td>
                {/foreach}
            </tr>
            {/foreach}
        </table>
    {/if}
    </div>
    {/block}
{/extends}