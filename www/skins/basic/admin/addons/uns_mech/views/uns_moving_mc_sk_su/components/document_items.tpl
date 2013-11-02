{assign var="doc_type" value=$d.type}


{* Тип позиции *}
{assign var="item_type_detail"      value=false}
{assign var="item_type_material"    value=false}

{* Типоразмер позиции *}
{assign var="typesize_disabled"     value=false}

{if $doc_type == $smarty.const.DOC_TYPE__VLC or $doc_type == $smarty.const.DOC_TYPE__MCP} {* Лит.цех *}
    {assign var="item_type_material"    value=true}
    {assign var="item_type_detail"      value=false}

    {assign var="typesize_disabled"     value=true}
{/if}

{if $doc_type == $smarty.const.DOC_TYPE__AIO
    or $doc_type == $smarty.const.DOC_TYPE__RO
    or $doc_type == $smarty.const.DOC_TYPE__AS_VLC
    or $doc_type == $smarty.const.DOC_TYPE__BRAK
    or $doc_type == $smarty.const.DOC_TYPE__MCP
}
    {assign var="item_type_material"    value=false}
    {assign var="item_type_detail"      value=true}
{/if}




<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th width="10px" class="cm-non-cb">№</th>
            <th class="cm-non-cb" width="10px">Тип</th>
            <th class="cm-non-cb" width="90px">Категория</th>
            <th class="cm-non-cb" width="140px">Наименование</th>
            {*<th class="cm-non-cb" width="10px">Исп.{include file="common_templates/tooltip.tpl" tooltip="Исполнение детали:<br><b>Номинальное / исп. А / исп. Б</b>"}</th>*}
            <th class="cm-non-cb" width="88px">Кол-во</th>
            <th class="cm-non-cb" width="10px">{include file="common_templates/tooltip.tpl" tooltip="<u><b>Статус обработки:</b></u><br><br><b>Обр.</b> - деталь пока еще обрабатывается;<br><b>Зав.</b> - деталь уже обработана;" tooltip_mark="Статус"}</th>
            {*<th class="cm-non-cb" width="10px">Вес{include file="common_templates/tooltip.tpl" tooltip="Вес одной единицы материала, кг"}</th>*}
            {*<th class="cm-non-cb" width="10px">Вес{include file="common_templates/tooltip.tpl" tooltip="Общий вес всего количества, кг"}</th>*}
        </tr>
    </tbody>

    {if is__array($d.items)}
        {foreach from=$d.items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.di_id}
            {assign var="e_n" value="data[document_items][`$num`]"}
            {if is__more_0($id)}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center">
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>
                        <td class="cm-non-cb">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[di_id]"
                                f_value=$id
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="item_type"
                                f_required=true f_integer=false
                                f_material=$item_type_material
                                f_detail  =$item_type_detail
                                f_name="`$e_n`[item_type]"
                                f_value=$i.item_type
                                f_simple=true
                            }
                        </td>
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
                            {/if}
                        </td>
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
                                {/if}
                            {/if}
                        </td>
{*
                        <td class="cm-non-cb" align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="typesize"
                                f_name="`$e_n`[typesize]"
                                f_value=$i.typesize
                                f_disabled=!$typesize_disabled
                                f_simple=true
                            }
                        </td>
*}
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
                                f_simple=true
                            }
                            <div class="balance" style="display:block; float:right;"></div>
                        </td>
                        {*<td class="cm-non-cb" align="center">*}
                            {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                {*f_id=$id*}
                                {*f_type="select"*}
                                {*f_required=true f_integer=false*}
                                {*f_class=$series_item_class*}
                                {*f_name="`$e_n`[u_id]"*}
                                {*f_options=$i.units*}
                                {*f_option_id="u_id"*}
                                {*f_option_value="u_name"*}
                                {*f_option_target_id=$i.u_id*}
                                {*f_simple_text=$is_series_item*}
                                {*f_simple=true*}
                            {*}*}
                        {*</td>*}
                        {*<td class="cm-non-cb" align="right">*}
                            {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                {*f_type="input"*}
                                {*f_required=true f_integer=false*}
                                {*f_name="`$e_n`[weight]"*}
                                {*f_value=$i.weight|fn_fvalue*}
                                {*f_readonly=true*}
                                {*f_simple=true*}
                            {*}*}
                        {*</td>*}
                        {*<td class="cm-non-cb" align="right">*}
                            {*{assign var="total_weight" value=$i.quantity*$i.weight}*}
                            {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                {*f_type="input"*}
                                {*f_required=true f_integer=false*}
                                {*f_value=$total_weight*}
                                {*f_simple_text=true*}
                            {*}*}
                        {*</td>*}

                        <td class="cm-non-cb" align="right">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="processing"
                                f_name="`$e_n`[processing]"
                                f_value=$i.processing
                                f_simple=true
                            }
                        </td>

                        <td class="right cm-non-cb">
                            {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}

    {math assign="num" equation="x + 1" x=$num|default:0}
    {assign var="e_n" value="data[document_items][`$num`]"}
    <tbody class="hover cm-row-item" id="box_add_{$num}">
        <tr>
            <td class="cm-non-cb" align="center">&nbsp;</td>
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
                    f_name="`$e_n`[item_type]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="`$e_n`[item_cat_id]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_name="`$e_n`[item_id]"
                    f_simple=true
                }
            </td>
{*            <td class="cm-non-cb" align="center">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="typesize"
                    f_name="`$e_n`[typesize]"
                    f_disabled=!$typesize_disabled
                    f_simple=true
                }
            </td>*}
            <td class="cm-non-cb" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[quantity]"
                    f_value=""
                    f_simple=true
                }
                <div class="balance" style="display:block; float:right;"></div>
            </td>
            {*<td class="cm-non-cb" align="center">*}
                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                    {*f_type="select"*}
                    {*f_required=true f_integer=false*}
                    {*f_name="`$e_n`[u_id]"*}
                    {*f_simple=true*}
                {*}*}
            {*</td>*}
            {*<td class="cm-non-cb" align="right">*}
                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                    {*f_type="input"*}
                    {*f_required=true f_integer=false*}
                    {*f_name="`$e_n`[weight]"*}
                    {*f_value=''*}
                    {*f_readonly=true*}
                    {*f_simple=true*}
                {*}*}
            {*</td>*}
            {*<td class="cm-non-cb" align="right">&nbsp;</td>*}
            <td class="cm-non-cb" align="right">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="processing"
                    f_name="`$e_n`[processing]"
                    f_value=""
                    f_simple=true
                }
            </td>
            <td class="right cm-non-cb">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2"}
            </td>
        </tr>
    </tbody>

</table>