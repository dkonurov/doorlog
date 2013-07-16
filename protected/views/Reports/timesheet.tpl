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
<form id='timesheet' type='GET' action='{$_root}/reports/timesheet'>
    <input name = "date" type="text" id="datepicker" class='withoutDays' value = "{$date}" />
    <br>
    <input form = "timesheet" type="submit" id="add" value = "Сформировать" class="btn btn-success" >
</form>
<br>
<h3>Отчет за {$date}</h3>
<table class='table table-bordered'>
    <tr>
        <td rowspan='3'> Номер по порядку </td>
        <td rowspan='3'> Фамилия, инициалы, должность(специальность, профессия) </td>
        <td rowspan='3'> Табельный номер </td>
        <td colspan='16'> Отметки о явках и неявках на работу по числам месяца </td>
    </tr>
    <tr>    {for $day = 1 to 15}
                {if $days[$day-1] == 0}
                    <td>
                {else}
                    <td bgcolor='red'>
                {/if}
                {if $day < 10}
                    0{$day} </td>
                {else}
                    {$day} </td>
                {/if}
            {/for}
           <td>
    </tr>
    <tr>
            {for $day=16 to $dayCount}
                {if $days[$day-1] == 0}
                    <td>
                {else}
                    <td bgcolor='red'>
                {/if}
                {$day} </td>
            {/for}
            {for $td=0 to 31-$dayCount-1}
               <td></td>
            {/for}
    </tr>
    {$id = 1}
    {foreach from=$timesheet item=currentUser}
        <tr>
            <td rowspan='4'> {$id} </td>
            <td rowspan='4'> {$currentUser['name']}, {$currentUser['position']} </td>
            <td rowspan='4'>  </td>
            {$partitionDate = 1}
               {foreach from=$currentUser['report'] item=report}
               {if $partitionDate <= 15}
                    {if $report['dayType'] == 0}
                        <td id = 'workday'> {$report['status_name']} </td>
                    {else}
                        <td bgcolor='red'> {$report['status_name']} </td>
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
                        <td id = 'workday'> {$report['time']} </td>
                {else}
                    <td bgcolor='red'> {$report['time']} </td>
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
                        <td id = 'workday'> {$report['status_name']} </td>
                    {else}
                        <td bgcolor='red'> {$report['status_name']} </td>
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
                        <td id = 'workday'> {$report['time']} </td>
                    {else}
                        <td bgcolor='red'> {$report['time']} </td>
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