{assign var="doc_type" value=$d.type}


{* Тип позиции *}
{assign var="item_type_detail"      value=false}
{assign var="item_type_material"    value=false}

{* Типоразмер позиции *}
{assign var="typesize_disabled"     value=false}

{if $doc_type == 1} {* Лит.цех *}
    {assign var="item_type_detail"      value=false}
    {assign var="item_type_material"    value=true}

    {assign var="typesize_disabled"     value=true}

{/if}




<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th width="10px" class="cm-non-cb">№</th>
            <th class="cm-non-cb" width="250px">Наименование</th>
            <th class="cm-non-cb" width="10px">Исп.{include file="common_templates/tooltip.tpl" tooltip="Исполнение детали:<br><b>Номинальное / исп. А / исп. Б</b>"}</th>
            <th class="cm-non-cb" width="10px">Кол-во</th>
            <th class="cm-non-cb" width="10px">Ед. изм.</th>
            <th class="cm-non-cb" width="10px">Вес{include file="common_templates/tooltip.tpl" tooltip="Вес одной единицы материала, кг"}</th>
            <th class="cm-non-cb" width="10px">Вес{include file="common_templates/tooltip.tpl" tooltip="Общий вес всего количества, кг"}</th>
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
                            {$i.items[$i.item_id].material_name} {if strlen($i.items[$i.item_id].material_no)}[{$i.items[$i.item_id].material_no}]{/if}
                        </td>
                        <td class="cm-non-cb" align="center">
                            {if $typesize_disabled}-{else}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="typesize"
                                    f_name="`$e_n`[typesize]"
                                    f_target='M'
                                    f_simple_text=true
                                }
                            {/if}
                        </td>
                        <td class="cm-non-cb" align="right">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_value=$i.quantity|fn_fvalue
                                f_simple_text=true
                            }
                        </td>
                        <td class="cm-non-cb" align="center">
                            {$i.units[$i.u_id].u_name}
                        </td>
                        <td class="cm-non-cb" align="right">
                            {$i.weight|fn_fvalue}
                        </td>
                        <td class="cm-non-cb" align="right">
                            {assign var="total_weight" value=$i.quantity*$i.weight}
                            {$total_weight|fn_fvalue}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}
</table>