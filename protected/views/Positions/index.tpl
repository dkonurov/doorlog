{extends "protected/views/index.tpl"}

{block name="title"}Должности{/block}

{block name="breadcrumbs"}
    <ul class="breadcrumb">
        <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
        <li class="active"> Должности </li>
    </ul>
{/block}

{block name="pagetitle"}<h1>Должности</h1>{/block}

{block name="content"}
    <div class="span7">
        <table class="table table-bordered table-striped table-hover">
            <colgroup>
                <col class="col-large">
                <col class="col-small">
            </colgroup>
            <thead>
                <tr>
                    <th> Название </th>
                    <th> Сотрудников </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$positions item=position}
                    <tr>
                        <td> 
                        {if 'positions_edit'|checkPermission}
                        <a href="{$_root}/positions/edit?id={$position['id']}"> {$position['name']} </a>
                        {else}
                        {$position['name']}
                        {/if}
                        </td>
                        <td> {$position['total_position']}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {if 'positions_add'|checkPermission}
    <div class="span4 additional">
        {include file='protected/views/Positions/add.tpl'}
    </div>
    {/if}
{/block}

{/extends}