{include file="common_templates/subheader.tpl" title="Позиции заказа"}
<div class="subheader_block">

{* Тип позиции *}
{assign var="item_type_detail"  value=true}
{assign var="item_type_p"       value=true}
{assign var="item_type_pf"      value=true}
{assign var="item_type_pa"      value=true}


<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th width="10px" class="cm-non-cb b1_l center">№</th>
            <th class="cm-non-cb b1_l center" width="10px">Тип</th>
            <th class="cm-non-cb b1_l center" width="90px">Категория/Серия</th>
            <th class="cm-non-cb b1_l center" width="140px">Наименование</th>
            <th class="cm-non-cb b1_l center" width="110px">Кол-во</th>
            <th class="cm-non-cb b1_l center" colspan="2" style="text-transform: none;" width="100px">Вес, кг</th>
            <th class="cm-non-cb b1_l center" width="220px">Комментарий</th>
            <th class="cm-non-cb b1_l center">&nbsp;</th>
        </tr>
    </tbody>

    {if is__array($o.items)}
        {foreach from=$o.items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.oi_id}
            {assign var="e_n" value="order_data[document_items][`$num`]"}
            {if is__more_0($id)}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center">
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>

                        {*ITEM_TYPE*}
                        <td class="cm-non-cb b1_l">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[oi_id]"
                                f_value=$id
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="item_type"
                                f_required=true f_integer=false
                                f_material= $item_type_material
                                f_detail  = $item_type_detail
                                f_p   =     $item_type_p
                                f_pf  =     $item_type_pf
                                f_pa  =     $item_type_pa
                                f_name="`$e_n`[item_type]"
                                f_value=$i.item_type
                                f_simple=true
                            }
                        </td>

                        {*CATEGORY*}
                        <td class="cm-non-cb b1_l">
                            {if $i.item_type == "D"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="dcategories_plain"
                                    f_required=true f_integer=false
                                    f_name="`$e_n`[item_cat_id]"
                                    f_options=$dcategories_plain
                                    f_option_id="dcat_id"
                                    f_option_value="dcat_name"
                                    f_option_target_id=$i.dcat_id|default:"0"
                                    f_simple=true
                                }
                            {elseif $i.item_type == "P" or $i.item_type == "PF" or $i.item_type == "PA"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="select_by_group"
                                    f_required=true f_integer=false
                                    f_name="`$e_n`[item_cat_id]"
                                    f_options="pump_series"
                                    f_option_id="ps_id"
                                    f_option_value="ps_name"
                                    f_optgroups=$pump_series
                                    f_optgroup_label="pt_name_short"
                                    f_with_q_ty=false
                                    f_option_target_id=$i.ps_id|default:"0"
                                    f_simple=true
                                }
                            {/if}
                        </td>

                        {*ITEM_NAME*}
                        <td class="cm-non-cb b1_l">
                            {if $i.item_type == "D"}
                                <select name="{$e_n}[item_id]">
                                    <option value="{$i.detail_id}">{$i.detail_name}{if $i.detail_no|strlen} [{$i.detail_no}]{/if}</option>
                                </select>
                            {elseif $i.item_type == "P" or $i.item_type == "PF" or $i.item_type == "PA"}
                                <select name="{$e_n}[item_id]">
                                    <option value="{$i.p_id}">{$i.p_name}{if $i.p_no|strlen} [{$i.p_no}]{/if}</option>
                                </select>
                            {/if}
                        </td>

                        {assign var="q" value=$i.quantity}
                        <td class="cm-non-cb b1_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_name="`$e_n`[quantity]"
                                f_from=0
                                f_to=200
                                f_value=$q|fn_fvalue
                                f_simple=true
                                f_plus_minus=true
                            }
                        </td>

                        <td class="cm-non-cb b1_l weight_{$num}" align="center">
                            {$i.weight|fn_fvalue}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[weigh]"
                                f_value=$i.weight|fn_fvalue
                            }
                        </td>

                        <td class="cm-non-cb b1_l bold total_weight_{$num}" align="center">
                            {$q*$i.weight|fn_fvalue}
                        </td>

                        <td class="cm-non-cb b1_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="textarea"
                                f_row=1
                                f_col=30
                                f_full_name="`$e_n`[comment]"
                                f_value=$i.comment
                                f_simple=true
                                f_style="width:97%"
                            }
                        </td>

                        <td class="right cm-non-cb b1_l">
                            {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}

    {math assign="num" equation="x + 1" x=$num|default:0}
    {assign var="e_n" value="order_data[document_items][`$num`]"}
    <tbody class="hover cm-row-item" id="box_add_{$num}">
        <tr>
            <td class="cm-non-cb" align="center">&nbsp;</td>
            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[oi_id]"
                    f_value=0
                }
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="item_type"
                    f_required=true f_integer=false
                    f_material= $item_type_material
                    f_detail  = $item_type_detail
                    f_p   =     $item_type_p
                    f_pf  =     $item_type_pf
                    f_pa  =     $item_type_pa
                    f_name="`$e_n`[item_type]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="`$e_n`[item_cat_id]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_name="`$e_n`[item_id]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb b1_l" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_name="`$e_n`[quantity]"
                    f_from=0
                    f_to=200
                    f_value=0
                    f_simple=true
                    f_plus_minus=true
                }
            </td>

            <td class="cm-non-cb b1_l weight_{$num}" align="left">
                &nbsp;
            </td>

            <td class="cm-non-cb b1_l bold total_weight_{$num}" align="center">
                &nbsp;
            </td>
            <td class="cm-non-cb b1_l" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="textarea"
                    f_row=1
                    f_col=30
                    f_full_name="`$e_n`[comment]"
                    f_value=""
                    f_simple=true
                    f_style="width:97%"
                }
            </td>
            <td class="right cm-non-cb b1_l">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2"}
            </td>
        </tr>
    </tbody>

</table>
</div>
