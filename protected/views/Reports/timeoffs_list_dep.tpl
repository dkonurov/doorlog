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

        <form id="reports" action="{$_root}/reports/timeoffsdep">

        <div id="dep">
            {if isset($allDeps) && count($allDeps) > 0}
                <select id='dep_id' name='dep_id'>
                {foreach from=$allDeps item=dep}
                    {if {$dep['id']} == {$smarty.get.dep_id}}
                        <option value="{$dep['id']}" {$depSelected=$dep['id']} selected> {$dep['name']} </option>
                    {else}
                        <option value="{$dep['id']}"> {$dep['name']} </option>
                    {/if}
                {/foreach}
                </select>
            {/if}
        </div>

        <label for="datepicker"> Дата </label>
        <input name="date" type="text" id="datepicker"  value="{$reportParams['date']}" />

    </form>
    <input form="reports" type="submit" id="add" value="Сформировать" class="btn btn-success" >
    <br>
    <br>
    <div class="span9">
    
    {if $reportResults}
        <br>
        <br>
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <th> Имя </th>
                <th> Часы </th>
                {foreach from=$allStatuses item=status}
                    <th>{$status['name']}</th>
                {/foreach}
            </thead>
            {foreach from=$reportResults item=user}
            <tr>
                <td><a href="{$_root}/reports/timeoffsuser?user_id={$user['id']}&date={$reportParams['date']}">{$user['name']}</a></td>
                {foreach from=$user['stats'] item=value}
                <td>{$value}</td>
                {/foreach}
            </tr>
            {/foreach}
        </table>
    {/if}
    </div>
    {/block}
{/extends}