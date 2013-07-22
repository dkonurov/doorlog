{block name="main_menu"}
<div class="navbar">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">
                <li><a href="{$_root}/"><i class="icon-home"></i></a></li>
                
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">
                        Отделы <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {if isset($_menu)}
                            {foreach from=$_menu item=departments}
                                <li><a href="{$_root}/departments/show?id={$departments['id']}">{$departments['name']}</a></li>
                            {/foreach}
                        {/if}
                    </ul>
                </li>

                {if 'timeoffs_reports'|checkPermission || 'officeload_reports'|checkPermission}
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">
                        Отчёты <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {if 'timeoffs_reports'|checkPermission}
                        <li><a href="{$_root}/reports/timeoffsuser">По посещаемости пользователя</a></li>
                        <li><a href="{$_root}/reports/timeoffsdep">По посещаемости отделов</a></li>
                        {/if}
                        {if 'officeload_reports'|checkPermission}
                        <li><a href="{$_root}/reports/officeload">По загрузке</a></li>
                        {/if}
                        <li><a href="{$_root}/reports/timesheet"> Табель </a></li>
                    </ul>
                </li>
                {/if}

                {if 'departments_view'|checkPermission
                    || 'roles_view'|checkPermission
                    || 'users_view'|checkPermission
                    || 'positions_view'|checkPermission 
                    || 'holiday_view'|checkPermission}
                <li class="dropdown">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">
                        Настройки <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {if 'departments_view'|checkPermission}
                        <li><a href="{$_root}/departments">Отделы</a></li>
                        {/if}
                        {if 'roles_view'|checkPermission}
                        <li><a href="{$_root}/roles">Права доступа</a></li>
                        {/if}
                        {if 'users_view'|checkPermission}
                        <li><a href="{$_root}/users">Пользователи</a></li>
                        {/if}
                        {if 'positions_view'|checkPermission}
                        <li><a href="{$_root}/positions">Должности</a></li>
                        {/if}
                        {if 'holiday_edit'|checkPermission}
                        <li><a href="{$_root}/holidays">Выходные дни</a></li>
                        {/if}
                    </ul>
                </li>
                {/if}

            </ul>
            <ul class="nav pull-right">
                <li><a href="{$_root}/users/profile">Мой профиль</a></li>
                <li><a href="{$_root}/users/logout">Выйти</a></li>
            </ul>

                <form class="navbar-form pull-left" action="{$_root}/users/search">
                    <input type="text" class="span2" id="autocomplete" name="text" value={if isset($text)}{$text}{/if}>
                    <input type="hidden" id="id" name="id">
                    <button type="submit" class="btn">Поиск</button>
                </form>
        </div>
    </div>
</div>
{include file="protected/views/AutocompleteUser.tpl"}
{/block}


