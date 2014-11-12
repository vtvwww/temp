{capture name="mainbox"}
    {assign var="i" value=$detail}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.detail_id}
            {assign var="name" value=$i.detail_name}
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
                f_name="detail_id"
                f_value=$id
            }

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="detail_name"
                        f_value=$i.detail_name
                        f_description="Наименование"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="detail_no"
                        f_value=$i.detail_no
                        f_description="Обозначение по докум."
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="dcategories_plain"
                        f_required=true f_integer=false f_integer_more_0=true
                        f_name="dcat_id"
                        f_options=$dcategories_plain
                        f_option_id="dcat_id"
                        f_option_value="dcat_name"
                        f_option_target_id=$i.dcat_id
                        f_description=$lang.uns_detail_categories
                        f_blank=true
                        f_blank_name="---"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="detail_status"
                        f_value=$i.detail_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="detail_position"
                        f_value=$i.detail_position
                        f_default="0"
                        f_description="Позиция"
                    }
                    <hr/>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id=$id
                        f_value=$i.checked
                        f_name="checked"
                        f_description="Проверена ли деталь?"
                        f_tooltip="Если птичка установлена, то деталь считается проверенной (по чертежам, у механических цехов, у литейного цеха), и поэтому в балансе мех. цехов и склада комплектующих не будет отражен красный разделитель.<br><img src='skins/basic/admin/images/tooltips/details_checked.png'>"
                    }
                    <hr/>
                    {*Режим отображения принадлежности*}
                    <div class="form-field">
                        <label for="accessory_view_{$id}" class="cm-required">Режим отображения принадлежности{include file="common_templates/tooltip.tpl" tooltip="<b>По сериям насосов</b><br>Во многих случаях стоит выбирать именно этот режим. Так как он является информативным и компактным.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_s.png'><br><br><b>По насосам</b> Может быть слишком избыточно.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_p.png'><br><br><b>Указать вручную</b><br>Бывают случаи, когда невозможно рассчитать применяемость детали, тогда следует установить ее вручную в следующем поле для ввода.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_m.png'>"}:</label>
                        <select id="accessory_view_{$id}" name="data[accessory_view]">
                            <option {if $i.accessory_view == "S"} selected="selected" {/if} value="S">по сериям насосов</option>
                            <option {if $i.accessory_view == "P"} selected="selected" {/if} value="P">по насосам</option>
                            <option {if $i.accessory_view == "M"} selected="selected" {/if} value="M">указать вручную</option>
                        </select>
                    </div>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="accessory_manual"
                        f_row=1
                        f_value=$i.accessory_manual
                        f_description="Применяемость в насосах вручную"
                        f_tooltip="Текст, который будет отображен как применяемость в насосах, если в качестве <Режима отображения принадлежности> был выбран <указать вручную>.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_m_1.png'><br><br><img src='skins/basic/admin/images/tooltips/details_accessory_view_m.png'>"
                    }
                    <hr/>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="detail_comment"
                        f_row=1
                        f_value=$i.detail_comment
                        f_description="Комментарий"
                    }
                </div>
                <div id="content_accounting" class="hidden">
                    {include file="addons/uns/views/components/get_form_accounting.tpl" item_type="D"}
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
    {assign var="title" value="Редактирование: `$name` [`$i.detail_no`]"}
{else}
    {assign var="title" value="Добавить"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}