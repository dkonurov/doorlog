{extends "protected/views/index.tpl"}
{block name="title"}Результаты поиска{/block}
    {block name="breadcrumbs"}
        <ul class="breadcrumb">
            <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
            <li class="active"> Результаты поиска </li>
        </ul>
    {/block}
    {block name="pagetitle"}<h1>Результаты поиска</h1>{/block}
    {block name="content"}
    <div class="span7">
        <table class="table table-bordered table-striped table-hover">
            {if $search}
                <thead>
                    <tr>
                        <th> Имя </th>
                        <th> Статус </th>
                        <th> Отдел </th>
                    </tr>
                </thead>
                {foreach from=$search item=user key=key}
                    <tr>
                        <td>
                            {if 'users_profile'|checkPermission}
                                <a href='{$_root}/users/profile?id={$search[$key]['id']}'> {$search[$key]['name']}</a>
                            {else}
                                {$search[$key]['name']}
                            {/if}
                        </td>
                        <td>
                            {if {$user['status']} == 2}
                                <span class="label label-success">В офисе</span>
                            {else}
                                <span class="label">Не в офисе</span>
                            {/if}
                        </td>
                        <td>
                            <a href="{$_root}/departments/show?id={$search[$key]['dep_id']}">{$search[$key]['dep']}</a>
                        </td>
                    </tr>
                {/foreach}
            {else}
                <h3>По запросу "{$text}" пользователей не найдено</h3>
            {/if}
       </table>
    </div>
    {/block}
{/extends}
