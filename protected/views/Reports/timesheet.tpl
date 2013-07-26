{extends "protected/views/index.tpl"}
{block name="javascript"}
    <script src="{$_root}/assets/js/reportsDatepicker.js"></script>
{/block}
{block name="title"} Табель {/block}
{block name="breadcrumbs"}
    <ul class="breadcrumb">
        <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
        <li class="active"> Табель </li>
    </ul>
{/block}
{block name="content"}
Отчет за месяц:
<form id="timesheet" action="{$_root}/reports/timesheet">
    <input form="timesheet" name="date" type="text" id="datepicker" class="withoutDays" value="{$date}" />
</form>

<input form = "timesheet" type="submit" id="add" value = "Сформировать" class="btn btn-success" >

<form id='timesheetsave' type='GET' action='{$_root}/reports/timesheetsave'>
    <input form = "timesheetsave" name = "date" type="text" id="datepickerforsave" class='hidden' value = "{$date}" />
</form>
    <input form = "timesheetsave" type="submit" value = "Сохранить" class="btn btn-primary" >
<br>
<h3>Отчет за {$date}</h3>
<table id="report-timesheet" class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="3"> Номер по порядку </th>
            <th rowspan="3"> Фамилия, инициалы, должность(специальность, профессия) </th>
            <th rowspan="3"> Табельный номер </th>
            <th colspan="16"> Отметки о явках и неявках на работу по числам месяца </th>
        </tr>
        <tr>    {for $day = 1 to 15}
                    {if $days[$day-1] == 0}
                        <th>
                    {else}
                        <th class="days-off">
                    {/if}
                    {if $day < 10}
                        0{$day}
                    {else}
                        {$day}
                    {/if}
                    </th>
                {/for}
               <td>
        </tr>
        <tr>
                {for $day=16 to $dayCount}
                    {if $days[$day-1] == 0}
                        <th>
                    {else}
                        <th class="days-off">
                    {/if}
                    {$day} </th>
                {/for}
                {for $td=0 to 31-$dayCount-1}
                   <th></th>
                {/for}
        </tr>
    </thead>
    {$id=1}
    {foreach from=$timesheet item=currentUser}
        <tr>
            <td rowspan="4"> {$id} </td>
            <td rowspan="4"> {$currentUser['name']}, {$currentUser['position']} </td>
            <td rowspan="4"> {$currentUser['timesheetid']} </td>
            {$partitionDate = 1}
               {foreach from=$currentUser['report'] item=report}
               {if $partitionDate <= 15}
                    {if $report['dayType'] == 0}
                        <td class="workday"> {$report['status_name']} </td>
                    {else}
                        <td class="days-off"> {$report['status_name']} </td>
                    {/if}
                {$partitionDate=$partitionDate+1}
                {/if}
                {/foreach}
            <td></td>
        </tr>
        <tr>
            {$partitionDate = 1}
            {foreach from=$currentUser['report'] item=report}
            {if $partitionDate <= 15}
                {if $report['dayType'] == 0}
                        <td class="workday"> {$report['time']} </td>
                {else}
                    <td class="days-off"> {$report['time']} </td>
                {/if}
            {$partitionDate=$partitionDate+1}
            {/if}
            {/foreach}
            <td></td>
        </tr>
        <tr>
            {$partitionDate = 1}
            {foreach from=$currentUser['report'] item=report}
               {if $partitionDate > 15}
                    {if $report['dayType'] == 0}
                        <td class="workday"> {$report['status_name']} </td>
                    {else}
                        <td class="days-off"> {$report['status_name']} </td>
                    {/if}
                {/if}
                {$partitionDate=$partitionDate+1}
            {/foreach}
            {for $td=0 to 30-$dayCount}
                <td></td>
            {/for}
        </tr>
        <tr>
            {$partitionDate = 1}
            {foreach from=$currentUser['report'] item=report}
               {if $partitionDate > 15}
                    {if $report['dayType'] == 0}
                        <td class="workday"> {$report['time']} </td>
                    {else}
                        <td class="days-off"> {$report['time']} </td>
                    {/if}
                {/if}
                {$partitionDate=$partitionDate+1}
            {/foreach}
            {for $td=0 to 30-$dayCount}
                <td></td>
            {/for}
        </tr>
        {$id=$id+1}
    {/foreach}

<table>
{/block}