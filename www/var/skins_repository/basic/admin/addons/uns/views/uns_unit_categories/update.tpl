{capture name="mainbox"}
    {assign var="i" value=$unit_category}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.uc_id}
            {assign var="name" value=$i.uc_name}
        {else}
            {assign var="id" value=0}
            {assign var="copy" value=true}
        {/if}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="detailed" name="selected_section">
            <input type="hidden" name="uc_id" value="{$id}" />
            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="uc_name"
                        f_value=$i.uc_name
                        f_description=$lang.name
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="uc_position"
                        f_value=$i.uc_position
                        f_default="0"
                        f_description="Позиция"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="uc_comment"
                        f_value=$i.uc_comment
                        f_description="Комментарий"
                    }
                </div>
            {/capture}

            {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
            </div>
        </form>
    </div>
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить"}
{/if}


{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}