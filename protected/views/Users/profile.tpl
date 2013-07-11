{extends "protected/views/index.tpl"}
{block name="javascript"}
    <script src="{$_root}/assets/js/rangeDatepicker.js"></script>
    <script>
        var otherOffice={$otherOffice}
    </script>
    <script src="{$_root}/assets/js/userProfileAutocomplete.js"></script>
{/block}
{block name="title"}Настройки профиля{/block}

    {block name="breadcrumbs"}
        <ul class="breadcrumb">
            <li><a href="{$_root}/"> Главная </a> <span class="divider"> / </span></li>
            <li class="active"> Профиль </li>
        </ul>
    {/block}

    {block name="pagetitle"}<h1>Настройки профиля</h1>{/block}

    {block name="content"}
    <div class="span7">
        <table class="table table-bordered">
            <colgroup>
                <col class="col-small">
            </colgroup>
                <tr>
                    <td> Имя </td>
                    <td>{$userInfo['name']}</td>
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
                {if ('users_private_info'|checkPermission) || $isOwner}
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
                <tr>
                    <td> Статус </td>
                    <td>
                        {if $userInfo['status']==2}
                            <span class="label label-success">В офисе</span>
                        {else}
                            <span class="label">Не в офисе</span>
                        {/if}
                    </td>
                </tr>
        </table>
    </div>
        {if $isOwner }
            <div class="span4 additional">
                {include file='protected/views/Users/changePassword.tpl'}
            </div>
        {/if}
        {if ('timeoffs_add'|checkPermission)}
            <div class="span4 additional">
                {include file='protected/views/Users/timeoff.tpl'}
            </div>
        {/if}
    {/block}
{extends}
