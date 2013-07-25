
{extends "protected/views/index.tpl"}
{block name="title"} Отделы {/block}

{block name="breadcrumbs"}
    <ul class="breadcrumb">
        <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
        <li class="active"> Отделы </li>
    </ul>
{/block}

{block name="pagetitle"}<h1>Отделы</h1>{/block}

{block name="content"}
    <div class="span7">
        <table class="table table-bordered table-striped table-hover">
            <colgroup>
                <col class="col-large">
                <col class="col-small">
            </colgroup>
            <thead>
                <tr>
                    <th> Название отдела </th>
                    <th> Количество сотрудников </th>
                    <th> Начальник </th>
                </tr>
            </thead>

            <tbody>
            {foreach from=$departments item=department}
                <tr>
                    <td>
                    {if 'departments_edit'|checkPermission}
                    <a href="{$_root}/departments/edit?id={$department['id']}">{$department['name']}</a>
                    {else}
                    {$department['name']}
                    {/if}
                    </td>
                    <td> {$department['total_users']} </td>
                    <td> {$department['chief_name']} </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    
    {if 'departments_add'|checkPermission}
    <div class="span4 additional">
        {include file='protected/views/Departments/add.tpl'}
    </div>
    {/if}
    
{/block}

{/extends}