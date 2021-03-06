{extends "protected/views/index.tpl"}
{block name="javascript"}
    <script src="{$_root}/assets/js/rangeDatepicker.js"></script>
    <script>
        var otherOffice={$otherOffice}
    </script>
    <script src="{$_root}/assets/js/userProfileAutocomplete.js"></script>
{/block}
{block name="title"}{if $isOwner} Мой профиль {else} Просмотр профиля {/if}{/block}

    {block name="breadcrumbs"}
        <ul class="breadcrumb">
            <li><a href="{$_root}/"> Главная </a> <span class="divider"> / </span></li>
            <li class="active"> Профиль </li>
        </ul>
    {/block}

    {block name="pagetitle"}<h1>{if $isOwner} Мой профиль {else} Просмотр профиля {/if}</h1>{/block}

    {block name="content"}
    
    {assign var="isAllowedUsersPrivateInfo" value='users_private_info'|checkRolePermission ||
        'users_private_info'|checkDepartmentPermission:$userInfo['department_id'] ||
        $isOwner}
        
    {assign var="isAllowedTimeoffsAdd" value='timeoffs_add'|checkRolePermission ||
        'timeoffs_add'|checkDepartmentPermission:$userInfo['department_id']}
        
    <div class="span7">
        <table class="table table-bordered">
            <colgroup>
                <col class="col-small">
            </colgroup>
                <tr>
                    <td> Имя </td>
                    <td>
                        {$userInfo['s_name']} {$userInfo['f_name']}
                        {if $userInfo['status']==2}
                            <span class="label label-success">В офисе</span>
                        {else}
                            <span class="label">Не в офисе</span>
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td> Отдел </td>
                    <td>
                        {if (isset($userInfo['department']))}
                            <a href="{$_root}/departments/show?id={$userInfo['department_id']}">{$userInfo['department']}</a>
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td> Должность </td>
                    <td>
                        {if (isset($userInfo['position']))}
                            {$userInfo['position']}
                        {/if}
                    </td>
                </tr>
                {if $isAllowedUsersPrivateInfo}
                    <tr>
                        <td> Телефон </td>
                        <td> {$userInfo['phone']} </td>
                    </tr>
                    {if $userInfo['birthday']}
                        <tr>
                            <td> Дата рождения </td>
                            <td> {$userInfo['birthday']|date_format:"%d.%m.%Y"} </td>
                        </tr>
                    {/if}
                    {if $userInfo['birthday']}
                        <tr>
                            <td> Дата устройства </td>
                            <td> {$userInfo['startwork']|date_format:"%d.%m.%Y"} </td>
                        </tr>
                    {/if}
                    {if $userInfo['birthday']}
                        <tr>
                            <td> Дата увольнения </td>
                            <td> {$userInfo['endwork']|date_format:"%d.%m.%Y"} </td>
                        </tr>
                    {/if}
                {/if}
                <tr>
                    <td> Дата регистрации </td>
                    <td> {$userInfo['created']|date_format:"%d.%m.%Y"} </td>
                </tr>
                <tr>
                    <td> Работа на полставки </td>
                    <td> {if {$userInfo['halftime']}} Да {else} Нет {/if}</td>
                </tr>
        </table>
    </div>
        {if $isOwner }
            <div class="span4 additional">
                {include file='protected/views/Users/changePassword.tpl'}
            </div>
        {/if}
        {if $isAllowedTimeoffsAdd}
            <div class="span4 additional">
                {include file='protected/views/Users/timeoff.tpl'}
            </div>
        {/if}
    {/block}
{extends}
