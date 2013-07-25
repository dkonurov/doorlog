{extends "protected/views/index.tpl"}

    {block name="title"}
        {if isset($userId)}
            Изменить пользователя {$userInfo['name']}
        {else}
            Добавить пользователя
        {/if}
    {/block}

    {block name="breadcrumbs"}
        <ul class="breadcrumb">
          <li><a href="{$_root}/"> Главная </a> <span class="divider"> / </span></li>
          <li><a href="{$_root}/users/"> Пользователи </a> <span class="divider"> / </span></li>
          {if isset($userId)}
              <li class="active"> Редактировать </li>
          {else}
              <li class="active"> Создать </li>
          {/if}
        </ul>
    {/block}

    {block name="javascript"}
        <script src="{$_root}/assets/js/birthdayDatepicker.js"></script>
        <script src="{$_root}/assets/js/manage.js"></script>
    {/block}

    {block name="pagetitle"}
        {if isset($userId)}
            <h1>Изменить пользователя {$userInfo['name']}</h1>
        {else}
           <h1>Добавить пользователя</h1>
        {/if}
    {/block}

    {block name="content"}
        {include file='protected/views/dialog.tpl'}

        <form method="POST" id="user">
            <input type="radio" id="worker" name="workertype" value="1"{if !isset($userId)} checked="checked"{/if}/>
            <label for="worker">Сотрудник</label>

            <input type="radio" id="outside-worker" name="workertype" value="2"{if isset($userId) && $userInfo['timesheetid'] == 0}checked="checked"{/if}/>
            <label for="outside-worker">Внештатный сотрудник</label>

            <input type="radio" id="other" name="workertype" value="3"{if isset($userId) && $userInfo['email'] == ""}checked="checked"{/if}/>
            <label for="other">Другой</label>

            <div class="span7">
                <table class='table table-bordered'>
                    {if !isset($userId)}
                        <tr>
                            <td>Пользователь</td>
                            <td>
                                <select form='user' name="userId" id="userId">
                                    {html_options options=$users}
                                </select>
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td>Фамилия*</td>
                        <td>
                            <input type="text" value="{if isset($userId)}{$userInfo['second_name']}{/if}" name="secondName" id="secondName">
                        </td>
                    </tr>
                    <tr>
                        <td>Имя*</td>
                        <td>
                            <input type="text" value="{if isset($userId)}{$userInfo['first_name']}{/if}" name="firstName" id="firstName">
                        </td>
                    </tr>
                    <tr>
                        <td>Отчество*</td>
                        <td>
                            <input type="text" name="middleName" value="{if isset($userId)}{$userInfo['middle_name']}{/if}" id="middleName">
                        </td>
                    </tr>
                    <tr id="depart" class="other worker">
                        <td>Отдел*</td>
                        <td>
                            <select form='user' name="department" id="department">
                                <option value=0></option>
                                {html_options options=$departments selected={$userInfo['department_id']}}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Должность*</td>
                        <td>
                            <select name="position" id="position">
                                <option value=0></option>
                                {html_options options=$positions selected={$userInfo['position_id']}}
                            </select>
                        </td>
                    </tr>
                    <tr id="email" class="other worker" {if isset($userId) && $userInfo['email'] == "" } class="hidden" {/if}>
                        <td>Email*</td>
                        <td>
                            <input type="text" maxlength="45" size="40" name="email" id="email-val"
                                {if isset($userId)}
                                value={$userInfo['email']}
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="show-in-timesheet" class="other outside-worker worker" {if isset($userId) && $userInfo['timesheetid'] == 0 } class="hidden" {/if}>
                        <td>Выводить в табеле</td>
                        <td>
                            {if isset($userId)}
                                <p>
                                    <input name="is_shown" id='is_shown' type="checkbox"
                                    {if $userInfo['is_shown'] != 0} value="1" checked="checked" {else} value="1"{/if}/>
                                    Выводить в табеле
                                </p>
                            {else}
                                <p>
                                    <input name="is_shown" id='is_shown' type="checkbox" value="1" checked/>
                                </p>
                            {/if}
                        </td>
                    </tr>
                    <tr id = 'tid' class="other outside-worker worker" {if isset($userId) && $userInfo['timesheetid'] == 0 } class="hidden" {/if}>
                        <td>Табельный номер*</td>
                        <td>
                            <input type="text" maxlength="6" size="40" name="timesheetid" id="timesheetid"
                                {if isset($userId) && $userInfo['timesheetid'] != 0}
                                value={$userInfo['timesheetid']}
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="permission" class="other worker" {if isset($userId) && $userInfo['email'] == "" } class="hidden" {/if}>
                        <td>Права доступа</td>
                        <td>
                            <select name="role">
                                {html_options options=$roles selected={$userRole['0']['id']}}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Телефон</td>
                        <td>
                            <input type="text" maxlength="11" name="phone" id="phone"
                            {if isset($userId)}
                            value={$userInfo['phone']}
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="birthday" class="other worker" {if isset($userId) && $userInfo['email'] == ""} class="hidden" {/if}>
                        <td>Дата рождения</td>
                        <td>
                            <input name="birthday" id="datepicker" type="text"
                            {if isset($userId)}
                                value="{$userInfo['birthday']|date_format:"%d.%m.%Y"}"
                            {else}
                                value=""
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="startwork" class="other worker" {if isset($userId) && $userInfo['email'] == ""} class="hidden" {/if}>
                        <td>Дата принятия</td>
                        <td>
                            <input name="startwork" id="datepicker-start" type="text"
                            {if isset($userId)}
                                value="{$userInfo['startwork']|date_format:"%d.%m.%Y"}"
                            {else}
                                value=""
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="endwork" class="other worker" {if isset($userId) && $userInfo['email'] == ""} class="hidden" {/if}>
                        <td>Дата увольнения</td>
                        <td>
                            <input name="endwork" id="datepicker-end" type="text"
                            {if isset($userId)}
                                value="{$userInfo['endwork']|date_format:"%d.%m.%Y"}"
                            {else}
                                value=""
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="halftime" class="other worker" {if isset($userId) && $userInfo['email'] == "" } class="hidden" {/if}>
                        <td>На полставки </td>
                        <td>
                            {if isset($userId)}
                                <p>
                                    <input name="halftime" id='halftime-val' type="checkbox" value="1"{if $userInfo['halftime']} checked{/if}>
                                </p>
                            {else}
                                <p>
                                    <input name="halftime" id='halftime-val' type="checkbox" value="1">
                                </p>
                            {/if}
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <div class="clear">
            {if 'users_edit'|checkPermission || 'users_add'|checkPermission}
                <button type=submit class="btn btn-success" form="user">
                    {if isset($userId)}
                        Сохранить
                    {else}
                        Добавить
                    {/if}
                </button>
                <a class="btn" href="{$_root}/users"> Отмена </a>
            {else}
                <a class="btn" href="{$_root}/users"> Назад </a>
            {/if}

            {if isset($userId) && 'users_delete'|checkPermission}
                <a href="#myModal" role="button" class="btn btn-danger" data-toggle="modal" form="delete">Удалить</a>
                <form action = "{$_root}/users/delete" method='post' id="delete">
                    <input type="hidden" name="id" value="{$userId}">
                </form>
            {/if}
        </div>
    {/block}
{/extends}
