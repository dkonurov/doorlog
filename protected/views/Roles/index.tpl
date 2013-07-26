{extends "protected/views/index.tpl"}
    {block name="title"}Права доступа{/block}
    {block name="pagetitle"}<h1>Права доступа</h1>{/block}

    {block name="breadcrumbs"}
        <ul class="breadcrumb">
            <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
            <li class="active"> Права доступа </li>
        </ul>
    {/block}

    {block name="content"}
    <div class="span7">
        <table class="table table-bordered table-striped table-hover">
            <colgroup>
                <col class="col-large">
                <col class="col-small">
            </colgroup>
            <thead>
                <tr>
                    <th>Роль</th>
                    <th>Сотрудников</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$roles item=role}
                    <tr>
                       <td>
                       {if 'roles_edit'|checkPermission}
                       <a href="{$_root}/roles/edit?id={$role['id']}"> {$role['name']} </a>
                       {else}
                       {$role['name']}
                       {/if}
                       </td>
                       <td> {$role['users_count']} </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {if 'roles_add'|checkPermission}
    <div class="span4 additional">
        {include file='protected/views/Roles/add.tpl'}
    </div>
    {/if}
    {/block}

{/extends}