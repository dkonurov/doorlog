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
            <p>Начальник отдела:</p>
            {if $users}
                <select name="chief">
                    <option value=0></option>
                    {html_options options=$users selected={$departments['chief_id']}}
                </select>
            {else}
                <select name="chief" disabled="disabled">
                </select>
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