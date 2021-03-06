{extends "protected/views/index.tpl"}

    {block name="title"}
        {if isset($userId)}
            Изменить пользователя {$userInfo['s_name']} {$userInfo['f_name']}
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
            <h1>Изменить пользователя {$userInfo['s_name']} {$userInfo['f_name']}</h1>
        {else}
           <h1>Добавить пользователя</h1>
        {/if}
    {/block}

    {block name="content"}
        {include file='protected/views/dialog.tpl'}

        <form method="POST" id="user">
            <div class="span7">
                <div class="pre-table">
                    <input type="radio" id="worker" name="workertype" value="1"{if !isset($userId)} checked="checked"{else}
                        {if isset($userId) && $userInfo['timesheetid'] != 0}
                            checked="checked"
                        {/if}
                    {/if}/>
                    <label for="worker">Сотрудник</label>

                    <input type="radio" id="outside-worker" name="workertype" value="2"{if isset($userId) && $userInfo['timesheetid'] == 0}checked="checked"{/if}/>
                    <label for="outside-worker">Внештатный сотрудник</label>

                    <input type="radio" id="other" name="workertype" value="3"{if isset($userId) && $userInfo['email'] == ""}checked="checked"{/if}/>
                    <label for="other">Другой</label>
                </div>

                <table class="table table-bordered">
                    {if !isset($userId)}
                        <tr>
                            <td>Пользователь</td>
                            <td>
                                <select form="user" name="userId" id="userId" class="form-element">
                                    {html_options options=$users}
                                </select>
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td>Фамилия*</td>
                        <td>
                            <input type="text" value="{if isset($userId)}{$userInfo['second_name']}{/if}" name="secondName" id="secondName" class="form-element">
                            <a class="btn btn-success btn-swap" id="swap"><i class="icon-arrow-up icon-white"></i><i class="icon-arrow-down icon-white"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="adjacent-td">Имя*</td>
                        <td class="adjacent-td">
                            <input type="text" value="{if isset($userId)}{$userInfo['first_name']}{/if}" name="firstName" id="firstName" class="form-element">
                        </td>
                    </tr>
                    <tr>
                        <td>Отчество*</td>
                        <td>
                            <input type="text" name="middleName" value="{if isset($userId)}{$userInfo['middle_name']}{/if}" id="middleName" class="form-element">
                        </td>
                    </tr>
                    <tr id="depart" {if isset($userId) && !$userInfo['department_id']} class="other worker hidden" {else} class="other worker" {/if}>
                        <td>Отдел*</td>
                        <td>
                            <select form="user" name="department" id="department" class="form-element">
                                <option value="0"></option>
                                {html_options options=$departments selected={$userInfo['department_id']}}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Должность*</td>
                        <td>
                            <select name="position" id="position" class="form-element">
                                <option value="0"></option>
                                {html_options options=$positions selected={$userInfo['position_id']}}
                            </select>
                        </td>
                    </tr>
                    <tr id="email" {if isset($userId) && $userInfo['email'] == "" } class="other worker hidden" {else} class="other worker" {/if}>
                        <td>Email*</td>
                        <td>
                            <input type="text" maxlength="45" size="40" name="email" id="email-val" class="form-element"
                                {if isset($userId)}
                                value="{$userInfo['email']}"
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="show-in-timesheet" {if isset($userId) && $userInfo['timesheetid'] == 0 } class="other outside-worker worker hidden" {else} class="other outside-worker worker" {/if}>
                        <td>Выводить в табеле</td>
                        <td>
                            {if isset($userId)}
                                <input name="is_shown" id="is_shown" type="checkbox" class="form-element"
                                {if $userInfo['is_shown'] != 0} value="1" checked="checked" {else} value="1"{/if}/>
                                Выводить в табеле
                            {else}
                                <input name="is_shown" id="is_shown" type="checkbox" class="form-element" value="1" checked/>
                            {/if}
                        </td>
                    </tr>
                    <tr id="tid" {if isset($userId) && $userInfo['timesheetid'] == 0 } class="other outside-worker worker hidden" {else} class="other outside-worker worker" {/if}>
                        <td>Табельный номер*</td>
                        <td>
                            <input type="text" maxlength="6" size="40" name="timesheetid" id="timesheetid" class="form-element"
                                {if isset($userId) && $userInfo['timesheetid'] != 0}
                                value="{$userInfo['timesheetid']}"
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="permission" {if isset($userId) && $userInfo['email'] == "" } class="other worker hidden" {else} class="other worker hidden" {/if}>
                        <td>Права доступа</td>
                        <td>
                            <select name="role" class="form-element">
                                {html_options options=$roles selected={$userRole['0']['id']}}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Телефон</td>
                        <td>
                            <input type="text" maxlength="11" name="phone" id="phone" class="form-element"
                            {if isset($userId)}
                            value="{$userInfo['phone']}"
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="birthday" {if isset($userId) && $userInfo['email'] == ""} class="other worker hidden" {else} class="other worker" {/if}>
                        <td>Дата рождения</td>
                        <td>
                            <input name="birthday" id="datepicker" type="text" class="form-element"
                            {if isset($userId)}
                                value="{$userInfo['birthday']|date_format:"%d.%m.%Y"}"
                            {else}
                                value=""
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="startwork" {if isset($userId) && $userInfo['email'] == ""} class="other worker hidden" {else} class="other worker" {/if}>
                        <td>Дата принятия</td>
                        <td>
                            <input name="startwork" id="datepicker-start" type="text" class="form-element"
                            {if isset($userId)}
                                value="{$userInfo['startwork']|date_format:"%d.%m.%Y"}"
                            {else}
                                value=""
                            {/if}/>
                        </td>
                    </tr>
                    <tr id="endwork" {if isset($userId) && $userInfo['email'] == ""} class="other worker hidden" {else} class="other worker hidden" {/if}>
                        <td>Дата увольнения</td>
                        <td>
                            <input name="endwork" id="datepicker-end" type="text" class="form-element"
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
                                <input name="halftime" id="halftime-val" type="checkbox" class="form-element" value="1"{if $userInfo['halftime']} checked{/if}>
                            {else}
                                <input name="halftime" id="halftime-val" type="checkbox" class="form-element" value="1">
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
                <form action="{$_root}/users/delete" method="post" id="delete">
                    <input type="hidden" name="id" value="{$userId}">
                </form>
            {/if}
        </div>
    {/block}
{/extends}
