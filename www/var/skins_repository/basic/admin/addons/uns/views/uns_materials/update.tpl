{capture name="mainbox"}
    {assign var="i" value=$material}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.material_id}
            {assign var="name" value=$i.material_name}
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
                f_name="material_id"
                f_value=$id
            }

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="material_name"
                        f_value=$i.material_name
                        f_description="Наименование"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=false f_integer=false
                        f_name="material_no"
                        f_value=$i.material_no
                        f_description=$smarty.const.L_material_no
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="select"
                        f_required=true f_integer=false f_integer_more_0=true
                        f_name="mclass_id"
                        f_options=$mclasses
                        f_option_id="mclass_id"
                        f_option_value="mclass_name"
                        f_option_target_id=$i.mclass_id
                        f_description=$lang.uns_material_classes
                        f_blank=true
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="mcategories_plain"
                        f_required=true f_integer=false  f_integer_more_0=true
                        f_name="mcat_id"
                        f_options=$mcategories_plain
                        f_option_id="mcat_id"
                        f_option_value="mcat_name"
                        f_option_target_id=$i.mcat_id
                        f_description=$lang.uns_material_categories
                        f_blank=true
                        f_blank_name="---"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="material_status"
                        f_value=$i.material_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="material_position"
                        f_value=$i.material_position
                        f_default="0"
                        f_description="Позиция"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="material_comment"
                        f_value=$i.material_comment
                        f_description="Комментарий"
                    }
                </div>
                <div id="content_accounting" class="hidden">
                    {include file="addons/uns/views/components/get_form_accounting.tpl"}
                </div>
                <div id="content_features" class="hidden">
                    {include file="addons/uns/views/components/get_form_features.tpl"}
                </div>
                <div id="content_options" class="hidden">
                    {include file="addons/uns/views/components/get_form_options.tpl"}
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
    {assign var="title" value="Редактирование: `$name` [`$i.material_no`]"}
{else}
    {assign var="title" value="Добавить"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}