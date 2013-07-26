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
                        <th> Отдел </th>
                        <th> Должность </th>
                        <th> Статус </th>
                    </tr>
                </thead>
                {foreach from=$search item=user key=key}
                    <tr>
                        <td><a href='{$_root}/users/profile?id={$search[$key]['id']}'> {$search[$key]['name']}</a></td>
                        <td> {$search[$key]['dep']} </td>
                        <td> {$search[$key]['pos']} </td>
                        <td> {if {$user['status']} == 2}
                                <span class="label label-success">В офисе</span>
                            {else}
                                <span class="label">Не в офисе</span>
                            {/if}
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
