{include file="common_templates/subheader.tpl" title="Позиции плана"}
<div class="subheader_block">

{* Тип позиции *}
{assign var="item_type_detail"  value=true}
{assign var="item_type_p"       value=true}
{assign var="item_type_pf"      value=true}
{assign var="item_type_pa"      value=true}


<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th width="10px" class="cm-non-cb">№</th>
            <th class="cm-non-cb" width="10px">Тип</th>
            <th class="cm-non-cb" width="140px">Наименование</th>
            <th class="cm-non-cb" width="10px">Кол</th>
        </tr>
    </tbody>

    {if is__array($plan.items)}
        {foreach from=$plan.items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.pi_id}
            {assign var="e_n" value="data[plan_items][`$num`]"}
            {if is__more_0($id)}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center">
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>

                        {*ITEM_TYPE*}
                        <td class="cm-non-cb">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[pi_id]"
                                f_value=$id
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="item_type"
                                f_required=true f_integer=false
                                f_pump_series=true
                                f_detail=false
                                f_name="`$e_n`[item_type]"
                                f_value=$i.item_type
                                f_simple=true
                            }
                        </td>

                        {*PUMP SERIES*}
                        <td class="cm-non-cb">
                            {if $i.item_type == "S"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="select_by_group"
                                    f_required=true f_integer=false
                                    f_name="`$e_n`[item_id]"
                                    f_options="pump_series"
                                    f_option_id="ps_id"
                                    f_option_value="ps_name"
                                    f_optgroups=$pump_series
                                    f_optgroup_label="pt_name_short"
                                    f_with_q_ty=false
                                    f_option_target_id=$i.item_id|default:"0"
                                    f_simple=true
                                    f_blank=true
                                }
                            {/if}
                        </td>

                        {*ITEM_NAME*}
                        {*<td class="cm-non-cb">*}
                            {*{if $i.item_type == "D"}*}
                                {*<select name="{$e_n}[item_id]">*}
                                    {*<option value="{$i.detail_id}">{$i.detail_name}{if $i.detail_no|strlen} [{$i.detail_no}]{/if}</option>*}
                                {*</select>*}
                            {*{elseif $i.item_type == "P" or $i.item_type == "PF" or $i.item_type == "PA"}*}
                                {*<select name="{$e_n}[item_id]">*}
                                    {*<option value="{$i.p_id}">{$i.p_name}{if $i.p_no|strlen} [{$i.p_no}]{/if}</option>*}
                                {*</select>*}
                            {*{/if}*}
                        {*</td>*}

                        {assign var="q" value=$i.quantity}
                        <td class="cm-non-cb" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[quantity]"
                                f_value=$q|fn_fvalue
                                f_autocomplete="off"
                                f_number=true
                                f_style="width: 30px;"
                                f_simple=true
                            }
                        </td>
                        {*<td class="cm-non-cb" align="right">*}
                            {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                {*f_type="textarea"*}
                                {*f_row=1*}
                                {*f_col=40*}
                                {*f_full_name="`$e_n`[comment]"*}
                                {*f_value=$i.comment*}
                                {*f_style="width: 100%;"*}
                                {*f_simple=true*}
                            {*}*}
                        {*</td>*}

                        <td class="right cm-non-cb">
                            {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}

    {math assign="num" equation="x + 1" x=$num|default:0}
    {assign var="e_n" value="data[plan_items][`$num`]"}
    <tbody class="hover cm-row-item" id="box_add_{$num}">
        <tr>
            <td class="cm-non-cb" align="center">&nbsp;</td>
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[pi_id]"
                    f_value=0
                }
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="item_type"
                    f_required=true f_integer=false
                    f_pump_series=$item_type_p
                    f_name="`$e_n`[item_type]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="`$e_n`[item_id]"
                    f_simple=true
                }
            </td>
            {*<td class="cm-non-cb">*}
                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                    {*f_type="select"*}
                    {*f_name="`$e_n`[item_id]"*}
                    {*f_simple=true*}
                {*}*}
            {*</td>*}
            <td class="cm-non-cb" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[quantity]"
                    f_value=""
                    f_style="width: 30px;"
                    f_autocomplete="off"
                    f_number=true
                    f_simple=true
                }
            </td>
            {*<td class="cm-non-cb" align="right">*}
                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                    {*f_type="textarea"*}
                    {*f_row=1*}
                    {*f_col=40*}
                    {*f_full_name="`$e_n`[comment]"*}
                    {*f_value=""*}
                    {*f_style="width: 100%;"*}
                    {*f_simple=true*}
                {*}*}
            {*</td>*}
            <td class="right cm-non-cb">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2"}
            </td>
        </tr>
    </tbody>

</table>
</div>
