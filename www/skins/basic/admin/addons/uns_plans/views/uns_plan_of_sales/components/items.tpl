{include file="common_templates/subheader.tpl" title="Позиции плана продаж"}
<div class="subheader_block">

{* Тип позиции *}
{assign var="item_type_detail"  value=true}
{assign var="item_type_p"       value=true}
{assign var="item_type_pf"      value=true}
{assign var="item_type_pa"      value=true}

{literal}
    <style>
        table.table td.ukr{
            background-color: #FFF4B0;
        }
        table.table td.exp{
            background-color: #CEFFC6;
        }
    </style>
{/literal}

<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling" style="background-color: #F3F3F3;">
            <th rowspan="2" width="10px" class="cm-non-cb">№</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="10px">Тип</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="140px">Наименование</th>
            <th colspan="2" class="cm-non-cb b1_l center b3_l" width="160px">УКРАИНА</th>
            <th colspan="2" class="cm-non-cb b1_l center b3_l" width="160px">ЭКСПОРТ</th>
            <th rowspan="2" class="cm-non-cb b1_l center b3_l">&nbsp;</th>
        </tr>
        <tr class="first-sibling" style="background-color: #F3F3F3;">
            <th class="cm-non-cb b1_l center b3_l b1_t" width="80px">тек.мес.</th>
            <th class="cm-non-cb b1_l center b1_l b1_t" width="80px">след.мес.</th>
            <th class="cm-non-cb b1_l center b3_l b1_t" width="80px">тек.мес.</th>
            <th class="cm-non-cb b1_l center b1_l b1_t" width="80px">след.мес.</th>
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
                        <td class="cm-non-cb bold" align="center">
                            {$smarty.foreach.d_i.iteration}
                        </td>

                        {*ITEM_TYPE*}
                        <td class="cm-non-cb b1_l">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[pi_id]"
                                f_value=$id
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="item_type"
                                f_required=true f_integer=false
                                f_pump_series=true
                                f_detail=true
                                f_name="`$e_n`[item_type]"
                                f_value=$i.item_type
                                f_simple=true
                            }
                        </td>

                        {*PUMP SERIES*}
                        <td class="cm-non-cb b1_l">
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
                            {elseif $i.item_type == "D"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="select_by_group"
                                    f_required=true f_integer=false
                                    f_name="`$e_n`[item_id]"
                                    f_options="details"
                                    f_option_id="detail_id"
                                    f_option_value="detail_name"
                                    f_optgroups=$category_details
                                    f_optgroup_label="dcat_name"
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

                        {assign var="q" value=$i.ukr_curr}
                        <td class="cm-non-cb center b3_l ukr">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[ukr_curr]"
                                f_value=$q|fn_fvalue
                                f_autocomplete="off"
                                f_number=true
                                f_style="width: 50px;"
                                f_simple=true
                            }
                        </td>

                        {assign var="q" value=$i.ukr_next}
                        <td class="cm-non-cb center b1_l ukr">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[ukr_next]"
                                f_value=$q|fn_fvalue
                                f_autocomplete="off"
                                f_number=true
                                f_style="width: 50px;"
                                f_simple=true
                            }
                        </td>

                        {assign var="q" value=$i.exp_curr}
                        <td class="cm-non-cb center b3_l exp">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[exp_curr]"
                                f_value=$q|fn_fvalue
                                f_autocomplete="off"
                                f_number=true
                                f_style="width: 50px;"
                                f_simple=true
                            }
                        </td>

                        {assign var="q" value=$i.exp_next}
                        <td class="cm-non-cb center b1_l exp">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[exp_next]"
                                f_value=$q|fn_fvalue
                                f_autocomplete="off"
                                f_number=true
                                f_style="width: 50px;"
                                f_simple=true
                            }
                        </td>

                        <td class="right cm-non-cb b3_l">
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
            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[pi_id]"
                    f_value=0
                }
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="item_type"
                    f_required=true f_integer=false
                    f_pump_series=true
                    f_detail=true
                    f_name="`$e_n`[item_type]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_required=true f_integer=false
                    f_name="`$e_n`[item_id]"
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb center b3_l ukr">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[ukr_curr]"
                    f_value=""
                    f_style="width: 50px;"
                    f_autocomplete="off"
                    f_number=true
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb center b1_l ukr">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[ukr_next]"
                    f_value=""
                    f_style="width: 50px;"
                    f_autocomplete="off"
                    f_number=true
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb center b3_l exp">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[exp_curr]"
                    f_value=""
                    f_style="width: 50px;"
                    f_autocomplete="off"
                    f_number=true
                    f_simple=true
                }
            </td>
            <td class="cm-non-cb center b1_l exp">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="`$e_n`[exp_next]"
                    f_value=""
                    f_style="width: 50px;"
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
            <td class="right cm-non-cb b3_l">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2"}
            </td>
        </tr>
    </tbody>

</table>
</div>
