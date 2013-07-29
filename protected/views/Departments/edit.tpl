{extends "protected/views/index.tpl"}
{block name="breadcrumbs"}
    <ul class="breadcrumb">
        <li><a href="{$_root}/"> Главная </a> <span class="divider"> / </span></li>
        <li><a href="{$_root}/departments/"> Отделы </a> <span class="divider"> / </span> </li>
        <li class="active"> Редактировать </li>
    </ul>
{/block}


{block name="pagetitle"}<h1>Редактировать отдел</h1>{/block}

{block name="content"}
    {include file='protected/views/dialog.tpl'}
    <div class="span7">
        <form method='POST' id="edit-department">
            <input type="text" name="depName" value="{$departments['name']}"><br>
            {if $users}
                <div class="span7">
                    <table class="table table-bordered">
                        <th id="width150">Имя</th>
                        {foreach from=$permissions item=permission}
                        <th>{$permission['name']}</th>
                        {/foreach}
                        {foreach from=$users key=user_id item=user}  
                        <tr>
                            <td>{$user['name']}</td>
                            {foreach from=$permissions item=permission}
                            <td id="center"><input type="checkbox" name="{$permission['key']}_{$user_id}" value="{$permission['id']}" {if isset($user[$permission['key']])}checked{/if}></td>
                            {/foreach}
                        </tr>
                        {/foreach}
                    </table>
            {/if}
        </form>

        <form action = "{$_root}/departments/delete" method='post' id="delete">
            <input type="hidden" name="id" value="{$departments['id']}">
        </form>
        <button type="submit" class="btn btn-success" form="edit-department"> Сохранить </button>
        <a class="btn" href="{$_root}/departments"> Отмена </a>
        {if 'departments_delete'|checkPermission}
        <a href="#myModal" role="button" class="btn btn-danger" data-toggle="modal" form="delete">Удалить</a>
        {/if}
    </div>
{/block}

{/extends}