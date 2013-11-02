{capture name="mainbox"}
    {assign var="i" value=$option}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.option_id}
            {assign var="name" value=$i.option_name}
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
                f_name="option_id"
                f_value=$id
            }

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="option_name"
                        f_value=$i.option_name
                        f_description="Наименование"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=false f_integer=false
                        f_name="option_no"
                        f_value=$i.option_no
                        f_description="Символ"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="option_status"
                        f_value=$i.option_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="option_position"
                        f_value=$i.option_position
                        f_default="0"
                        f_description="Позиция"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=true f_integer=false
                        f_name="option_comment"
                        f_value=$i.option_comment
                        f_description="Комментарий"
                    }

                </div>

                <div id="content_variants" class="hidden">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                            <tr class="first-sibling">
                                <th class="cm-non-cb">{$lang.position_short}</th>
                                <th class="cm-non-cb">{$lang.value}</th>
                                <th class="cm-non-cb">{$lang.uns_units}</th>
                                <th class="cm-non-cb">{$lang.status}</th>
                                <th class="cm-non-cb">&nbsp;</th>
                            </tr>
                        </tbody>
                        {foreach from=$option_variants item="vr" name="fe_v"}
                            {assign var="num" value=$smarty.foreach.fe_v.iteration}
                            <tbody class="hover cm-row-item" id="option_variants_{$id}_{$num}">
                            <tr>
                                <td class="cm-non-cb">
                                    {assign var="ov_id" value=$vr.ov_id}
                                    {if $copy}
                                        {assign var="ov_id" value=0}
                                    {/if}
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_type="hidden"
                                        f_name="data[variants][`$num`][ov_id]"
                                        f_value=$ov_id
                                    }
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_id=$id
                                        f_type="input"
                                        f_name="data[variants][`$num`][ov_position]"
                                        f_class="input-text-short"
                                        f_value=$vr.ov_position
                                        f_simple=true
                                    }
                                </td>
                                <td class="cm-non-cb">
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_id=$id
                                        f_type="input"
                                        f_class="input-text-long"
                                        f_name="data[variants][`$num`][ov_value]"
                                        f_value=$vr.ov_value
                                        f_simple=true
                                    }
                                </td>
                                <td class="cm-non-cb">
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_type="select_by_group"
                                        f_name="data[variants][`$num`][u_id]"
                                        f_options="units"
                                        f_option_id="u_id"
                                        f_option_value="u_name"
                                        f_option_target_id=$vr.u_id
                                        f_optgroups=$units
                                        f_optgroup_label="uc_name"
                                        f_simple=true
                                        f_blank=true
                                    }
                                </td>
                                <td class="cm-non-cb">
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_type="status"
                                        f_simple=true
                                        f_name="data[variants][`$num`][ov_status]"
                                        f_value=$vr.ov_status
                                        }
                                </td>
                                 <td class="right cm-non-cb">
                                    {include file="buttons/multiple_buttons.tpl" item_id="option_variants_`$id`_`$num`" tag_level="3" only_delete="Y"}
                                </td>
                            </tr>
                            </tbody>
                        {/foreach}

                        {math equation="x + 1" assign="num" x=$num|default:0}{assign var="vr" value=""}
                        <tbody class="hover cm-row-item " id="box_add_variant_{$id}">
                        <tr>
                            <td class="cm-non-cb">
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="hidden"
                                    f_name="data[variants][`$num`][ov_id]"
                                    f_value=0}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="input"
                                    f_name="data[variants][`$num`][ov_position]"
                                    f_class="input-text-short"
                                    f_value=0
                                    f_simple=true
                                }
                            </td>
                            <td class="cm-non-cb">
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="input"
                                    f_name="data[variants][`$num`][ov_value]"
                                    f_value=""
                                    f_simple=true
                                }
                            </td>
                            <td class="cm-non-cb">
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_by_group"
                                    f_name="data[variants][`$num`][u_id]"
                                    f_options="units"
                                    f_option_id="u_id"
                                    f_option_value="u_name"
                                    f_option_target_id=$vr.u_id
                                    f_optgroups=$units
                                    f_optgroup_label="uc_name"
                                    f_simple=true
                                    f_blank=true
                                }
                            </td>
                            <td class="cm-non-cb">
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="status"
                                    f_simple=true
                                    f_name="data[variants][`$num`][ov_status]"
                                    }
                            </td>
                            <td class="right cm-non-cb">
                                {include file="buttons/multiple_buttons.tpl" item_id="add_variant_`$id`" tag_level="2"}
                            </td>
                        </tr>
                        </tbody>
                        </table>
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