{*============================================================================*}
{*ОТОБРАЗИТЬ ТАБЛИЦУ ПЛАНИРОВАНИЯ НАСОСА*}
{*============================================================================*}
{if $action != "pump"}
<hr/>
&nbsp;&nbsp;&nbsp;<span style="font-size: 20px; font-weight: bold;">Насос {$ppump_series[$ps_set_id].ps_name}</span>
{/if}
<table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 10px; 0">
    <thead>
        <tr style="background-color: #D4D0C8;">
            <th style="text-transform: none;"               rowspan="3"             class="center">Наименование</th>
            <th style="text-transform: none;"               rowspan="3"             class="center">&nbsp;</th>
            <th style="text-transform: none;"               rowspan="3"             class="center b_l">СГП<br>на<br>01/{$data.month|string_format:"%02d"}<hr class="roman_dates">{$data.year}</th>
            <th style="text-transform: none;"               rowspan="2" colspan="2" class="center b_l b1_b">План<br>продаж</th>
            <th style="text-transform: none;"          rowspan="2" colspan="3" class="center b3_l b1_b">План<br>производства<br>ФАКТИЧЕСКИЙ</th>
            <th style="text-transform: none;"          rowspan="2" colspan="3" class="center b3_l b1_b">План<br>производства<br>ПО ПАРТИЯМ</th>
            <th style="text-transform: uppercase;background-color:#B8C1FF; "                      colspan="5" class="center b3_l b1_b">Выполнение плана на {$data.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
            <th style="text-transform: none;"               rowspan="3"             class="center b3_l">СГП<br>на<br>{$data.current_day|fn_parse_date|date_format:"%d/%m"}<hr class="roman_dates">{$data.year}</th>
            <th style="text-transform: none;"               rowspan="3"             class="center b3_l">{include file="common_templates/tooltip.tpl" tooltip="<b>Кратность партии насоса.</b><br>ОТ - ДО, ШАГ" tooltip_mark="<b>КП</b>"}</th>
        </tr>
        <tr style="background-color: #D4D0C8;">
            <th style="text-transform: none; width: 50px; background-color: #FFEF8C;"  rowspan="3"             class="center b3_l">Задел<br>ожид.<br>сборку</th>
            <th style="text-transform: none; width: 50px; background-color: #C0FF9A;"  rowspan="3"             class="center b2_l">Выпол-<br>нено</th>
            <th style="text-transform: uppercase; background-color:#B8C1FF;"                      colspan="3" class="center b3_l b1_b">Осталось</th>
        </tr>
        <tr style="background-color: #D4D0C8;">
            <th style="width: 40px;" class="center b_l">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
            <th style="width: 40px;" class="center b1_l">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
            <th style="width: 40px;" class="center b3_l">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
            <th style="width: 40px;" class="center b1_l">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
            <th style="width: 40px;" class="center b1_l">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
            <th style="width: 40px;" class="center b3_l">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
            <th style="width: 40px;" class="center b1_l">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
            <th style="width: 40px;" class="center b1_l">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
            <th style="width: 40px;background-color: #B8C1FF;" class="center b3_l">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
            <th style="width: 40px;background-color: #B8C1FF;" class="center b1_l">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
            <th style="width: 40px;background-color: #B8C1FF;" class="center b1_l">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
        </tr>
    </thead>
    {foreach from=$data.pump_series item="pt" name="pt"}
    <tbody>
        {foreach from=$pt.pump_series item="ps" key="ps_id" name="ps"}
        {if $ps_set_id == $ps_id}
        <tr>
            {*Наименование*}
            <td>
                <b>{$ps.ps_name}</b>
            </td>

            <td>
                {if $data.prohibition.$ps_id == "Y"}<img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/>{/if}
            </td>

            {*Склад Готовой Продукции*}
            {assign var="q" value=$data.sgp.$ps_id|default:0}
            <td class="b_l  center {if !$q}zero{/if}">{$q}</td>

            {*План продаж*}
            {assign var="q" value=$data.requirement.curr_month.$ps_id|default:0}
            <td class="b_l center {if !$q}zero{/if}">{$q}</td>
            {assign var="q" value=$data.requirement.next_month.$ps_id|default:0}
            <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

            {*План производства*}
            {assign var="q" value=$data.initial_production_plan.curr_month.$ps_id|default:0}
            <td class="b3_l  center {if !$q}zero{/if}">{$q}</td>
            {assign var="q" value=$data.initial_production_plan.next_month.$ps_id|default:0}
            <td class="b1_l center {if !$q}zero{/if}">{$q}</td>
            {assign var="q" value=$data.initial_production_plan.next2_month.$ps_id|default:0}
            <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

            {*Плановая сдача партий насосов на СГП*}
            {assign var="q" value=$data.initial_production_plan_parties.curr_month.$ps_id|default:0}
            <td class="b3_l  center {if !$q}zero{/if}">{$q}</td>
            {assign var="q" value=$data.initial_production_plan_parties.next_month.$ps_id|default:0}
            <td class="b1_l center {if !$q}zero{/if}">{$q}</td>
            {assign var="q" value=$data.initial_production_plan_parties.next2_month.$ps_id|default:0}
            <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

            {*Задел*}
            {assign var="q" value=$data.zadel.$ps_id|default:0}
            <td style="background-color: #FFEF8C;" class="b3_l center {if !$q}zero{else}bold{/if}">{$q}</td>

            {*Выполнено*}
            {assign var="q" value=$data.done.$ps_id|default:0}
            <td style="background-color: #C0FF9A;" class="b2_l center {if !$q}zero{else}bold{/if}">{$q}</td>

            {*Осталось*}
            {assign var="q" value=$data.remaining_production_plan_parties.curr_month.$ps_id|default:0}
            <td style="background-color: #B8C1FF;" class="b3_l center {if !$q}zero{else}bold{/if}">{$q}</td>
            {assign var="q" value=$data.remaining_production_plan_parties.next_month.$ps_id|default:0}
            <td style="background-color: #B8C1FF;" class="b1_l center {if !$q}zero{else}bold{/if}">{$q}</td>
            {assign var="q" value=$data.remaining_production_plan_parties.next2_month.$ps_id|default:0}
            <td style="background-color: #B8C1FF;" class="b1_l center {if !$q}zero{else}bold{/if}">{$q}</td>

            {*Склад Готовой Продукции*}
            {assign var="q" value=$data.sgp_current_day.$ps_id|default:0}
            <td class="b3_l  center {if !$q}zero{else}bold{/if}">{$q}</td>

            {*КРАТНОСТЬ ПАРТИИ НАСОСОВ*}
            <td class="b3_l center">
                {$ps.party_size_min}-{$ps.party_size_max},{$ps.party_size_step}
            </td>
        </tr>
        {/if}
        {/foreach}
    </tbody>
    {/foreach}
</table>


{*============================================================================*}
{*ОТОБРАЗИТЬ КОМПЛЕКТАЦИИ НАСОСОВ*}
{*============================================================================*}
{assign var="plan_parties" value=$data.initial_production_plan_parties.curr_month.$ps_set_id|default:0}
{foreach from=$pump_series.pumps key="pump_id" item="p"}
    {if $action == "pump"}
    <br/>
    &nbsp;&nbsp;&nbsp;<span style="font-size: 16px; font-weight: bold;">Насос {$p.p_name}</span>
    {/if}
    <table cellpadding="0" cellspacing="0" border="0" class="table ps_set_id__{$ps_set_id}_{$pump_id}" style="margin: 0 0 0 10px;">
        <thead>
            <tr style="background-color: #EDEDED;">
                <th class="center" rowspan="3">№</th>
                <th class="center b1_l" rowspan="3">Наименование</th>
                <th width="10px" class="center" rowspan="3">&nbsp;</th>
                <th class="center b1_l" rowspan="3" style="text-transform: none;">Кол.<br>на<br>1 ед.</th>
                <th class="center b_l b_b" colspan="6" style="text-transform: none;">ОСТАТКИ ДЕТАЛЕЙ</th>
                <th class="center b_l" rowspan="3" style="text-transform: none;">Кол-во<br>насосов<br>
                    {assign var="details" value=$p.details|array_keys|implode:'-'}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select_range"
                        f_name=""
                        f_id=""
                        f_from=0
                        f_to=200
                        f_value=$plan_parties
                        f_simple=true
                        f_plus_minus=true
                        f_onchange="calc_ps_set(\$(this).val(), `$ps_set_id`, `$pump_id`, '`$details`');"
                    }
                    <hr class="roman_dates"/>
                    Кол-во<br>деталей
                </th>
                <th class="center b_l" rowspan="3" style="text-transform: none;">Недо-<br>стача<br>дет.</th>
                <th class="center b_l b1_b" colspan="4">ОСТАТКИ ОТЛИВОК</th>
            </tr>
            <tr style="background-color: #EDEDED;">
                <th style="text-align: center;" class="b_l b1_b" colspan="2">МЦ №1</th>
                <th style="text-align: center;" class="b2_l b1_b" colspan="2">МЦ №2</th>
                <th style="text-align: center;" class="b_l" rowspan="2">Скл.<br>КМП</th>
                <th style="text-align: center;" class="b_l" rowspan="2"><span style="font-size: 30px;">&Sigma;</span></th>
                <th style="text-align: center;" class="b_l" rowspan="2">Наименование</th>
                <th style="text-align: center;" class=""    rowspan="2" >&nbsp;</th>
                <th style="text-align: center;" class="b1_l" rowspan="2">Остат.<br>Склад<br>ЛИТЬЯ</th>
                <th style="text-align: center; text-transform: none;" class="b1_l" rowspan="2">Недо-<br>стача<br>загот.</th>
            </tr>
            <tr style="background-color: #EDEDED;">
                <th style="text-align: center;" class="b_l">Обр.</th>
                <th style="text-align: center;" class="b1_l">Зав.</th>
                <th style="text-align: center;" class="b2_l">Обр.</th>
                <th style="text-align: center;" class="b1_l">Зав.</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$p.details item="d" name="d"}
                <tr>
                    {assign var="total_of_details"  value=$balance_of_details[$d.detail_id][10].processing_konech+$balance_of_details[$d.detail_id][10].complete_konech+$balance_of_details[$d.detail_id][14].processing_konech+$balance_of_details[$d.detail_id][14].complete_konech+$balance_of_details[$d.detail_id][17].konech}
                    {assign var="total_of_cast"     value=$balance_of_casts[$d.material_id]}

                    <td align="center">{$smarty.foreach.d.iteration}
                        {*ДАННЫЕ ДЛЯ БЫСТРОГО РАСЧЕТА*}
                        {assign var="e_n" value="ps_set_data__`$ps_set_id`_`$pump_id`_`$d.detail_id`"}
                        {*Остатки деталей*}
                        <input type="hidden" name="{$e_n}[detail_id]"    value="{$d.detail_id}"/>
                        {*Остатки деталей*}
                        <input type="hidden" name="{$e_n}[total_of_details]"    value="{$total_of_details}"/>
                        {*Расход детали на один насос*}
                        <input type="hidden" name="{$e_n}[details_per_pump]"    value="{$d.quantity}"/>
                        {*Расход заготовки на одну деталь*}
                        <input type="hidden" name="{$e_n}[cast_per_detail]"     value="{$d.material_quantity}"/>
                        {*Остатки заготовок*}
                        <input type="hidden" name="{$e_n}[total_of_cast]"          value="{$total_of_cast}"/>
                    </td>
                    <td class="b1_l"><b>{$d.detail_name}</b>{if strlen($d.detail_no) and $d.detail_no!="-"}<br><span style="font-size: 11px;">[{$d.detail_no}]</span>{/if}</td>
                    <td align="center">{include file="common_templates/tooltip.tpl" tooltip="<b>Применяемость детали:</b><br>`$accessory_of_details[$d.detail_id]`"}</td>
                    <td class="b1_l"    align="center">{$d.quantity|fn_fvalue}</td>

                    {assign var="q" value=$balance_of_details[$d.detail_id][10].processing_konech}
                    <td class="b_l"     align="center"><span class="{if !$q}zero{else}bold{/if}">{$q}</span></td>

                    {assign var="q" value=$balance_of_details[$d.detail_id][10].complete_konech}
                    <td class="b1_l"    align="center"><span class="{if !$q}zero{else}bold{/if}">{$q}</span></td>

                    {assign var="q" value=$balance_of_details[$d.detail_id][14].processing_konech}
                    <td class="b2_l"    align="center"><span class="{if !$q}zero{else}bold{/if}">{$q}</span></td>

                    {assign var="q" value=$balance_of_details[$d.detail_id][14].complete_konech}
                    <td class="b1_l"    align="center"><span class="{if !$q}zero{else}bold{/if}">{$q}</span></td>

                    {assign var="q" value=$balance_of_details[$d.detail_id][17].konech}
                    <td class="b_l"     align="center"><span class="{if !$q}zero{else}bold{/if}">{$q}</span></td>

                    {assign var="q" value=$total_of_details}
                    <td class="b_l" style="background-color: #EDEDED; " align="center"><span style="font-size: 15px;" class="{if !$q}zero{else}bold{/if}">{$q}</span></td>

                    {* Требуемое количество на план *}
                    {assign var="required_q" value=$plan_parties*$d.quantity|fn_fvalue}
                    <td class="b_l plan_of_pumps_{$ps_set_id}_{$pump_id}_{$d.detail_id}" style="" align="center"><span style="font-size: 17px;" class=" {if !$required_q}zero{/if}">{$required_q}</span></td>

                    {* Фактическая недостача деталей *}
                    {assign var="diff_q" value=$q-$required_q}
                    <td class="b_l deficit_of_details_{$ps_set_id}_{$pump_id}_{$d.detail_id}" style="background-color: #EDEDED; " align="center"><span style="font-size: 16px;" class="{if $diff_q<0}info_warning_block bold{else}zero{/if}">{$diff_q}</span></td>

                    {* Расчет по заготовкам*}
                    {assign var="q" value=$total_of_cast}
                    {if $d.mclass_id == 1}
                        <td class="b_l"     align="left">{if strlen($d.material_no)}[{$d.material_no}] {/if}{$d.material_name}</td>
                        <td align="center">{include file="common_templates/tooltip.tpl" tooltip="<b>Применяемость заготовки:</b><br>`$accessory_of_casts[$d.material_id]`"}</td>
                        <td class="b1_l"    align="center"><span class="{if !$q}zero{else}bold{/if}">{$q}</span></td>
                        {if $diff_q<0}
                            {assign var="q" value=$diff_q*$d.material_quantity+$q}
                            <td class="b1_l deficit_of_casts_{$ps_set_id}_{$pump_id}_{$d.detail_id}" align="center"><span style="font-size: 15px;" class="{if $q<0}info_warning_block bold{else}zero{/if}">{$q}</span></td>
                        {else}
                            <td class="b1_l deficit_of_casts_{$ps_set_id}_{$pump_id}_{$d.detail_id}" align="center">&nbsp;</td>
                        {/if}
                    {else}
                        <td class="b_l"     align="left">&nbsp;</td>
                        <td class=""        align="left">&nbsp;</td>
                        <td class="b1_l"    align="center">&nbsp;</td>
                        <td class="b1_l"    align="center">&nbsp;</td>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>
{/foreach}

<br/>
<br/>
<br/>