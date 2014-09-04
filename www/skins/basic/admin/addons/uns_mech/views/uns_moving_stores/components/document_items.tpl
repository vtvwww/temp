{assign var="doc_type" value=$d.type}

{* Тип позиции *}
{assign var="item_type_detail"      value=false}
{assign var="item_type_material"    value=false}

{* Типоразмер позиции *}
{assign var="typesize_disabled"     value=false}
{assign var="item_type_material"    value=true}
{assign var="typesize_disabled"     value=true}


<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th width="10px" class="cm-non-cb">№</th>
            <th class="cm-non-cb" width="10px">Тип</th>
            <th class="cm-non-cb" width="90px">Категория/Серия</th>
            <th class="cm-non-cb" width="140px">Наименование</th>
            <th class="cm-non-cb" width="50px">Кол-во</th>
            <th class="cm-non-cb" width="10px">Ед. изм.</th>
            <th class="cm-non-cb" width="1px">&nbsp;</th>
            <th class="cm-non-cb" width="1px">&nbsp;</th>
        </tr>
    </tbody>

    {if is__array($d.items)}
        {foreach from=$d.items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.di_id}
            {assign var="e_n" value="data_store[document_items][`$num`]"}
            {if is__more_0($id)}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center">
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>

                        {*Тип позиции*}
                        <td class="cm-non-cb">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[di_id]"
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

                        {*Категория позиции*}
                        <td class="cm-non-cb">
                            {if $i.item_type == "D"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="dcategories_plain"
                                    f_required=true f_integer=false
                                    f_name="`$e_n`[item_cat_id]"
                                    f_options=$dcategories_plain
                                    f_option_id="dcat_id"
                                    f_option_value="dcat_name"
                                    f_option_target_id=$i.item_info.dcat_id|default:"0"
                                    f_simple=true
                                }
                            {elseif $i.item_type == "M"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="mcategories_plain"
                                    f_required=true f_integer=false
                                    f_name="`$e_n`[item_cat_id]"
                                    f_options=$mcategories_plain
                                    f_option_id="mcat_id"
                                    f_option_value="mcat_name"
                                    f_with_q_ty=false
                                    f_option_target_id=$i.item_info.mcat_id|default:"0"
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
                                    f_option_target_id=$i.item_info.ps_id|default:"0"
                                    f_simple=true
                                }
                            {/if}
                        </td>

                        {*Позиция*}
                        <td class="cm-non-cb">
                            {if $i.items|is__array}
                                {if $i.item_type == "D"}
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_id=$id
                                        f_type="select"
                                        f_required=true f_integer=false
                                        f_name="`$e_n`[item_id]"
                                        f_options=$i.items
                                        f_option_id="detail_id"
                                        f_option_value="format_name"
                                        f_option_target_id=$i.item_id
                                        f_simple=true
                                    }
                                {elseif $i.item_type == "M"}
                                    {include file="addons/uns/views/components/get_form_field.tpl"
                                        f_id=$id
                                        f_type="select"
                                        f_required=true f_integer=false
                                        f_name="`$e_n`[item_id]"
                                        f_options=$i.items
                                        f_option_id="material_id"
                                        f_option_value="format_name"
                                        f_option_target_id=$i.item_id
                                        f_simple=true
                                    }
                                {else}
                                    {$smarty.const.UNS_ERROR}
                                {/if}
                            {else}
                                {if $i.item_type == "D"}
                                    <select name="{$e_n}[item_id]">
                                        <option value="{$i.item_info.detail_id}">{$i.item_info.detail_name}{if $i.item_info.detail_no|strlen} [{$i.item_info.detail_no}]{/if}</option>
                                    </select>
                                {elseif $i.item_type == "M"}
                                    <select name="{$e_n}[item_id]">
                                        <option value="{$i.item_info.material_id}">{$i.item_info.material_name}{if $i.item_info.material_no|strlen} [{$i.item_info.material_no}]{/if}</option>
                                    </select>
                                {elseif $i.item_type == "P" or $i.item_type == "PF" or $i.item_type == "PA"}
                                    <select name="{$e_n}[item_id]">
                                        <option value="{$i.item_info.p_id}">{$i.item_info.p_name}{if $i.item_info.p_no|strlen} [{$i.item_info.p_no}]{/if}</option>
                                    </select>
                                {/if}
                            {/if}
                        </td>

                        {*Количество*}
                        {assign var="q" value=$i.quantity}
                        {if $i.change_type == 'NEG'}
                            {assign var="q" value="-`$i.quantity`"}
                        {/if}
                        <td class="cm-non-cb" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[quantity]"
                                f_value=$q|fn_fvalue
                                f_autocomplete="off"
                                f_number=true
                                f_simple=true
                                f_attr="q"
                                f_attr_val=$q|fn_fvalue
                                f_title=$q|fn_fvalue
                            }
                        </td>

                        {* Единицы измерения *}
                        <td class="cm-non-cb" align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="select"
                                f_required=true f_integer=false
                                f_class=$series_item_class
                                f_name="`$e_n`[u_id]"
                                f_options=$i.units
                                f_option_id="u_id"
                                f_option_value="u_name"
                                f_option_target_id=$i.u_id
                                f_simple=true
                            }
                        </td>

                        {*Текущий остаток позиции*}
                        <td class="cm-non-cb" align="right">
                            <img class="hand" border="0" title="Получить текущий остаток позиции" src="skins/basic/admin/addons/uns_acc/images/refresh.png" onclick="var s=$(this).parent().prev().prev().prev().find('select'); if (s.val()>0) s.change();">
                        </td>
                        <td class="cm-non-cb" align="right">
                            <div class="balance" style="display:block; float:right;"></div>
                        </td>

                        <td class="right cm-non-cb" style="border-left: 1px solid #808080;">
                            {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}

    {math assign="num" equation="x + 1" x=$num|default:0}
    {assign var="e_n" value="data_store[document_items][`$num`]"}
    <tbody class="hover cm-row-item" id="box_add_{$num}">
        <tr>
            <td class="cm-non-cb" align="center">&nbsp;</td>

            {*Тип позиции*}
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[di_id]"
                    f_value=0
                }
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="item_type"
                    f_material=$item_type_material
                    f_detail=$item_type_detail
                    f_p   =     $item_type_p
                    f_pf  =     $item_type_pf
                    f_pa  =     $item_type_pa
                    f_name="`$e_n`[item_type]"
                    f_simple=true
                }
            </td>

            {*Категория*}
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="`$e_n`[item_cat_id]"
                    f_simple=true
                }
            </td>

            {*Позиция*}
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_name="`$e_n`[item_id]"
                    f_simple=true
                }
            </td>

            {*Количество*}
            <td class="cm-non-cb" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[quantity]"
                    f_value=""
                    f_autocomplete="off"
                    f_number=true
                    f_simple=true
                }
            </td>

            {*Единицы измерения*}
            <td class="cm-non-cb" align="center">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="`$e_n`[u_id]"
                    f_simple=true
                }
            </td>

            {*Текущий остаток позиции*}
            <td class="cm-non-cb" align="right">
                <img class="hand" border="0" title="Получить текущий остаток позиции" src="skins/basic/admin/addons/uns_acc/images/refresh.png" onclick="var s=$(this).parent().prev().prev().prev().find('select'); if (s.val()>0) s.change();">
            </td>
            <td class="cm-non-cb" align="right">
                <div class="balance" style="display:block; float:right;"></div>
            </td>
            <td class="right cm-non-cb" style="border-left: 1px solid #808080;">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2"}
            </td>
        </tr>
    </tbody>
</table>