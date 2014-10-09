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
            <th class="cm-non-cb b1_l" width="250px">Наименование</th>
            {*<th class="cm-non-cb" width="10px">Исп.{include file="common_templates/tooltip.tpl" tooltip="Исполнение детали:<br><b>Номинальное / исп. А / исп. Б</b>"}</th>*}
            <th class="cm-non-cb b1_l" width="30px">Кол</th>
            {*<th class="cm-non-cb" width="10px">ЕИ</th>*}
            <th class="cm-non-cb b1_l" width="10px">Вес{include file="common_templates/tooltip.tpl" tooltip="Вес одной единицы материала, кг"}</th>
            <th class="cm-non-cb b1_l" width="10px">Вес{include file="common_templates/tooltip.tpl" tooltip="Общий вес всего количества, кг"}</th>
        </tr>
    </tbody>

    {if is__array($d.items)}
        {assign var="t_q" value=0}
        {assign var="t_w" value=0}
        {foreach from=$d.items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.di_id}
            {assign var="e_n" value="data[document_items][`$num`]"}
            {if is__more_0($id)}
                {if !in_array($i.items[$i.item_id].mcat_id,array(78,79,80))}
                {assign var="t_q" value=$t_q+$i.quantity}
                {assign var="t_w" value=$t_w+$i.quantity*$i.weight}
                {/if}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center">
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>
                        <td class="cm-non-cb b1_l">
                            {$i.items[$i.item_id].material_name} {if strlen($i.items[$i.item_id].material_no)}[{$i.items[$i.item_id].material_no}]{/if}
                        </td>
                        {*<td class="cm-non-cb" align="center">*}
                            {*{if $typesize_disabled}-{else}*}
                                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                    {*f_type="typesize"*}
                                    {*f_name="`$e_n`[typesize]"*}
                                    {*f_target='M'*}
                                    {*f_simple_text=true*}
                                {*}*}
                            {*{/if}*}
                        {*</td>*}
                        <td class="cm-non-cb b1_l" align="right">
                            <nobr>{include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_value=$i.quantity|fn_fvalue
                                f_simple_text=true
                            }&nbsp;{$i.units[$i.u_id].u_name}</nobr>
                        </td>
                        {*<td class="cm-non-cb" align="left">*}
                            {*{$i.units[$i.u_id].u_name}*}
                        {*</td>*}
                        <td class="cm-non-cb b1_l" align="right">
                            {$i.weight|fn_fvalue}
                        </td>
                        <td class="cm-non-cb b1_l" align="right">
                            {assign var="total_weight" value=$i.quantity*$i.weight}
                            {$total_weight|fn_fvalue}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
        <tbody>
        <tr>
            <td colspan="5" class="left"><b>Итого только на производство насосов:</b><br>Кол-во: {$t_q} шт;<br>Общий вес: {$t_w|number_format:1:".":" "} кг;<br>Средний вес отливки: {$t_w/$t_q|number_format:1:".":" "} кг;</td>
        </tr>
        </tbody>

    {/if}
</table>