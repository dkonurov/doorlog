{extends "protected/views/index.tpl"}
{block name="title"} Отдел {$depName['name']} {/block}
    {block name="breadcrumbs"}
        <ul class="breadcrumb">
            <li><a href="{$_root}/"> Главная </a> <span class="divider">/</span></li>
            <li class="active"> Отдел {$depName['name']} </li>
        </ul>
    {/block}

    {block name="pagetitle"}<h1>Просмотр отдела "{$depName['name']}"</h1>{/block}

    {block name="content"}
    
    {assign var="isAllowed" value='users_private_info'|checkRolePermission ||
        'users_private_info'|checkDepartmentPermission:$depName['id']}
    
    <div class="span7">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th> Имя </th>
                    <th> Должность </th>
                    {if $isAllowed}
                        <th> Отработано за неделю </th>
                    {/if}
                    <th> Статус</th>
                </tr>
            </thead>

            <tbody>
            {foreach from=$users item=user}
                <tr>
                    <td>
                    {if 'users_profile'|checkPermission}
                    <a href='{$_root}/users/profile?id={$user['id']}'> {$user['s_name']} {$user['f_name']}</a>
                    {else}
                    {$user['name']}
                    {/if} 
                    </td>
                    <td>{$user['position']}</td>
                    
                    {if $isAllowed}
                        <td>{$user['time']['total_sum']|formatDate}</td>
                    {/if}
                    <td>
                        {if {$user['status']} == 2 }
                            <span class="label label-success">В офисе</span>
                        {else}
                            <span class="label">Не в офисе</span>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
       </table>
    </div>
    {/block}

{/extends}