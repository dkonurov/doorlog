{extends "protected/views/index.tpl"}

    {block name="content"}

    <div class="role">
        <form method="POST">
            <p>Название роли:</p> <input type="text" maxlength="45" size="40" name="roleName">
            <input type=submit value="Добавить" class="btn">
            <a class="btn" href="{$_root}/roles">Отмена</a>
        </form>
    </div>


    {/block}

{/extends}  