{capture name="mainbox"}
    {assign var="i" value=$dcategory}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.dcat_id}
            {assign var="name" value=$i.dcat_name}
        {else}
            {assign var="id" value=0}
            {assign var="copy" value=true}
        {/if}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="" name="selected_section">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="dcat_id"
                f_value=$id}

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="dcat_name"
                        f_value=$i.dcat_name
                        f_description="Наименование"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="dcategories_plain"
                        f_required=true f_integer=false
                        f_name="dcat_parent_id"
                        f_blank=true
                        f_options=$dcategories_plain
                        f_option_id="dcat_id"
                        f_option_value="dcat_name"
                        f_target=$i
                        f_exclude=$id
                        f_description="Расположение"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="dcat_status"
                        f_value=$i.dcat_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="dcat_position"
                        f_value=$i.dcat_position
                        f_default="0"
                        f_description="Позиция"
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