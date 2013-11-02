{capture name="mainbox"}
{assign var="i" value=$feature}
{if is__array($i)}
    {if $smarty.request.copy != "Y"}
        {assign var="id" value=$i.feature_id}
        {assign var="name" value=$i.feature_name}
    {else}
        {assign var="id" value=0}
        {assign var="copy" value=true}
    {/if}
{else}
    {assign var="id" value=0}
{/if}

<div id="content_group_{$id}">
    <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="feature_id"
            f_value=$id}
        {capture name="tabsbox"}
            <div id="content_general_{$id}">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="feature_name"
                    f_value=$i.feature_name
                    f_description="Наименование"}

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="feature_no"
                    f_value=$i.feature_no
                    f_description="Символ"}

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="uc_id"
                    f_options=$unit_categories
                    f_option_id="uc_id"
                    f_option_value="uc_name"
                    f_option_target_id=$i.uc_id
                    f_description="Категория единиц измерений"}

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="status"
                    f_required=true f_integer=false
                    f_name="feature_status"
                    f_value=$i.feature_status
                    f_description="Статус"}

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="feature_position"
                    f_value=$i.feature_position
                    f_default="0"
                    f_description="Позиция"}

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="textarea"
                    f_required=true f_integer=false
                    f_name="feature_comment"
                    f_value=$i.feature_comment
                    f_description="Комментарий"}

            </div>
        {/capture}

        {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}

        <div class="buttons-container cm-toggle-button buttons-bg">
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
        </div>
    </form>
</div>
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить "}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}