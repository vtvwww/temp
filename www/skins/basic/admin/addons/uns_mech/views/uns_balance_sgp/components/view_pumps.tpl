{include file="common_templates/subheader.tpl" title="Насосная продукция"}
<div class="subheader_block">
<table cellpadding="0" cellspacing="0" border="0" class="table">
    <thead>
        <tr style="background-color: #EDEDED">
            <th rowspan="{if count($orders)}3{else}2{/if}" colspan="2" align="center" style="text-align: center; " width="230px">
                <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                &nbsp;Наименование<br>(исполнение)
            </th>

            {*Насос*}
            <th colspan="{math equation="1+2*x" x=$orders|count}" align="center" style="text-align: center; border-left: 3px solid #000000; background-color: #D3D3D3;">НАСОС</th>

            {*На раме*}
            <th colspan="{math equation="1+2*x" x=$orders|count}" align="center" style="text-align: center; border-left: 3px solid #000000; background-color: #D3D3D3;">НА РАМЕ</th>

            {*Агрегат*}
            <th colspan="{math equation="1+2*x" x=$orders|count}" align="center" style="text-align: center; border-left: 3px solid #000000; background-color: #D3D3D3;">АГРЕГАТ</th>
        </tr>
        {if count($orders)}
        <tr style="background-color: #EDEDED">
            {*Насос*}
            <th rowspan="2" style="text-align: center; border-left: 3px solid #000000; border-top : 1px solid #808080; background-color: #D3D3D3;">Тек.<br>ост.</th>
            <th colspan="{math equation="2*x" x=$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #000000; border-top: 3px solid #000000;">Заказы</th>

            {*На раме*}
            <th rowspan="2" style="text-align: center; border-left: 3px solid #000000; border-top : 1px solid #808080; background-color: #D3D3D3;">Тек.<br>ост.</th>
            <th colspan="{math equation="2*x" x=$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #000000; border-top: 3px solid #000000;">Заказы</th>

            {*Агрегат*}
            <th rowspan="2" style="text-align: center; border-left: 3px solid #000000; border-top : 1px solid #808080; background-color: #D3D3D3;">Тек.<br>ост.</th>
            <th colspan="{math equation="2*x" x=$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #000000; border-top: 3px solid #000000;">Заказы</th>
        </tr>
        <tr style="background-color: #EDEDED">
            {*Насос*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <th colspan="2" style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</th>
            {/foreach}

            {*На раме*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <th colspan="2" style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</th>
            {/foreach}

            {*Агрегат*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <th colspan="2" style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</th>
            {/foreach}
        </tr>
        {/if}

    </thead>
    {if is__array($balances)}
        {assign var="total_q_P"     value=0}
        {assign var="total_q_PF"    value=0}
        {assign var="total_q_PA"    value=0}
        {foreach from=$balances key="pt_id" item="pt"}
            <tbody>
                <tr>
                    <td style="background-color: #d3d3d3; " colspan="{math equation="5+3*2*x" x=$orders|count}">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                        &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$pt.pt_name}</span>
                    </td>
                </tr>
            {foreach from=$pt.pump_series key="ps_id" item="ps" name="ps"}
                {foreach from=$ps.pumps key="p_id" item="p" name="p"}
                <tr>
                    {assign var="curr_q" value=0}
                    {if $smarty.foreach.p.first}
                    <td class="{if !$smarty.foreach.ps.first}b2_t{/if}" style="min-width: 100px;" {if $ps.pumps|count > 1} rowspan="{$ps.pumps|count}" {/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        {assign var="n" value=$ps.ps_name}
                        {assign var="href" value="uns_balance_sgp.motion?item_id=`$p.p_id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                        <a  rev="content_item_{$p.p_id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black bold" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n}</a>
                        <div id="content_item_{$p.p_id}" class="hidden" title="Движения <i><u>{$n}</u></i> по Складу готовой продукции"></div>
                    </td>
                    {/if}

                    <td class="b1_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="min-width: 50px;">{*&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*}
                        {assign var="n" value=$p.p_name|replace:"`$ps.ps_name`":""}
                        {assign var="href" value="uns_balance_sgp.motion?item_id=`$p.p_id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                        <a  rev="content_item_{$p.p_id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="block cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black bold" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n|trim}</a>
                        <div id="content_item_{$p.p_id}" class="hidden" title="Движения <i><u>{$p.p_name}</u></i> по Складу готовой продукции"></div>
                    </td>

                    {*************************************************************************************}
                    {*НАСОС*}
                    {*************************************************************************************}
                    <td align="center" class="b3_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="background-color: #D3D3D3;">
                        <span class="{if $p.balances.P<0}info_warning_block{elseif $p.balances.P==0}zero{elseif $p.balances.P>0}bold{/if}">
                            {$p.balances.P|fn_fvalue:2}
                            {assign var="curr_q" value=$p.balances.P}
                            {assign var="total_q_P" value=$total_q_P+$curr_q}
                        </span>
                    </td>
                    {foreach from=$orders item="o" name="o"}
                        {assign var="order_q" value=$p.orders[$o.order_id].P|default:0}
                        {assign var="curr_q" value=$curr_q-$order_q}
                        <td align="center" class="{if $smarty.foreach.o.first}b3_l{else}b1_l_black{/if} {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="min-width: 16px;">
                            {if $order_q!=0}
                                <span class="{if $order_q<0}info_warning_block{elseif $order_q==0}zero bold{/if}">
                                    {assign var="q" value=$order_q|fn_fvalue:2}
                                    {if $o.data_for_tmp.P[$p.p_id].comment|strlen}
                                        {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.P[$p.p_id].comment tooltip_mark="<b>`$q`</b>"}
                                    {else}
                                        {$q}
                                    {/if}
                                </span>
                            {else}
                                &nbsp;
                            {/if}
                        </td>

                        <td align="center" style="border-left: 1px dashed #808080; min-width: 16px;" class="{if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} {if $curr_q>=0 and $order_q>0 }done{/if}">
                            {if $order_q>0}
                                {if $curr_q>=0}
                                    {*{$curr_q}*}&nbsp;
                                {else}
                                    <span class="{if $curr_q<0}info_warning_block{elseif $curr_q==0}zero bold{/if}">
                                        {$curr_q|fn_fvalue:2}
                                    </span>
                                {/if}
                            {else}
                                &nbsp;
                            {/if}
                        </td>
                    {/foreach}

                    {*************************************************************************************}
                    {*НАСОС НА РАМЕ*}
                    {*************************************************************************************}
                    <td align="center" class="b3_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="background-color: #D3D3D3;">
                        <span class="{if $p.balances.PF<0}info_warning_block{elseif $p.balances.PF==0}zero{elseif $p.balances.PF>0}bold{/if}">
                            {$p.balances.PF|fn_fvalue:2}
                            {assign var="curr_q" value=$p.balances.PF}
                            {assign var="total_q_PF" value=$total_q_PF+$curr_q}
                        </span>
                    </td>
                    {foreach from=$orders item="o" name="o"}
                        {assign var="order_q" value=$p.orders[$o.order_id].PF|default:0}
                        {assign var="curr_q" value=$curr_q-$order_q}
                        <td align="center" class="{if $smarty.foreach.o.first}b3_l{else}b1_l_black{/if} {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="min-width: 16px;">
                            {if $order_q!=0}
                                <span class="{if $order_q<0}info_warning_block{elseif $order_q==0}zero bold{/if}">
                                    {assign var="q" value=$order_q|fn_fvalue:2}
                                    {if $o.data_for_tmp.PF[$p.p_id].comment|strlen}
                                        {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.PF[$p.p_id].comment tooltip_mark="<b>`$q`</b>"}
                                    {else}
                                        {$q}
                                    {/if}
                                </span>
                            {else}
                                &nbsp;
                            {/if}
                        </td>

                        <td align="center" style="border-left: 1px dashed #808080; min-width: 16px;" class="{if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} {if $curr_q>=0 and $order_q>0 }done{/if}">
                            {if $order_q>0}
                                {if $curr_q>=0}
                                    {*{$curr_q}*}&nbsp;
                                {else}
                                    <span class="{if $curr_q<0}info_warning_block{elseif $curr_q==0}zero bold{/if}">
                                        {$curr_q|fn_fvalue:2}
                                    </span>
                                {/if}
                            {else}
                                &nbsp;
                            {/if}
                        </td>
                    {/foreach}

                    {*************************************************************************************}
                    {*НАСОС АГРЕГАТ*}
                    {*************************************************************************************}
                    <td align="center" class="b3_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="background-color: #D3D3D3;">
                        <span class="{if $p.balances.PA<0}info_warning_block{elseif $p.balances.PA==0}zero{elseif $p.balances.PA>0}bold{/if}">
                            {$p.balances.PA|fn_fvalue:2}
                            {assign var="curr_q" value=$p.balances.PA}
                            {assign var="total_q_PA" value=$total_q_PA+$curr_q}
                        </span>
                    </td>
                    {foreach from=$orders item="o" name="o"}
                        {assign var="order_q" value=$p.orders[$o.order_id].PA|default:0}
                        {assign var="curr_q" value=$curr_q-$order_q}
                        <td align="center" class="{if $smarty.foreach.o.first}b3_l{else}b1_l_black{/if} {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}" style="min-width: 16px;">
                            {if $order_q!=0}
                                <span class="{if $order_q<0}info_warning_block{elseif $order_q==0}zero bold{/if}">
                                    {assign var="q" value=$order_q|fn_fvalue:2}
                                    {if $o.data_for_tmp.PA[$p.p_id].comment|strlen}
                                        {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.PA[$p.p_id].comment tooltip_mark="<b>`$q`</b>"}
                                    {else}
                                        {$q}
                                    {/if}
                                </span>
                            {else}
                                &nbsp;
                            {/if}
                        </td>

                        <td align="center" style="border-left: 1px dashed #808080; min-width: 16px;" class="{if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} {if $curr_q>=0 and $order_q>0 }done{/if}">
                            {if $order_q>0}
                                {if $curr_q>=0}
                                    {*{$curr_q}*}&nbsp;
                                {else}
                                    <span class="{if $curr_q<0}info_warning_block{elseif $curr_q==0}zero bold{/if}">
                                        {$curr_q|fn_fvalue:2}
                                    </span>
                                {/if}
                            {else}
                                &nbsp;
                            {/if}
                        </td>
                    {/foreach}

                </tr>
                {foreachelse}
                    <tr class="no-items">
                        <td colspan="17"><p>{$lang.no_data}</p></td>
                    </tr>
                {/foreach}
            {/foreach}
            </tbody>
        {/foreach}

        {***************************************************************}
        {**** ИТОГО: ***************************************************}
        {***************************************************************}
        <tr>
            <td align="right" rowspan="4" colspan="2" class="b2_t">
                <span style="font-size: 15px; font-weight: bold; text-align: right;">ИТОГО:</span>
            </td>

            {*************************************************************************************}
            {* ИТОГО НАСОС*}
            {*************************************************************************************}
            <td rowspan="2" align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;" class="b2_t">
                <span class="{if $total_q_P<0}info_warning_block{elseif $total_q_P==0}zero{/if}">
                    <b>{$total_q_P|fn_fvalue:2}</b>
                </span>
            </td>
            {foreach from=$orders item="o" name="o"}
                {assign var="total_q_P_order" value=0}
                {foreach from=$o.data_for_tmp.P item="i"}
                    {if $i.quantity > 0}
                        {assign var="total_q_P_order" value=$total_q_P_order+$i.quantity}
                    {/if}
                {/foreach}

                {if $total_q_P_order > 0}
                    <td colspan="2" class="b2_t" align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">
                        <span class="{if $total_q_P_order<0}info_warning_block{elseif $total_q_P_order==0}zero{/if}">
                            {$total_q_P_order|fn_fvalue:2}
                        </span>
                    </td>
                {else}
                    <td colspan="2" class="b2_t" align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">
                        &nbsp;
                    </td>
                {/if}
            {/foreach}

            {*************************************************************************************}
            {* ИТОГО НАСОС НА РАМЕ*}
            {*************************************************************************************}
            <td rowspan="2" align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;" class="b2_t">
                <span class="{if $total_q_PF<0}info_warning_block{elseif $total_q_PF==0}zero{/if}">
                    <b>{$total_q_PF|fn_fvalue:2}</b>
                </span>
            </td>
            {foreach from=$orders item="o" name="o"}
                {assign var="total_q_PF_order" value=0}
                {foreach from=$o.data_for_tmp.PF item="i"}
                    {if $i.quantity > 0}
                        {assign var="total_q_PF_order" value=$total_q_PF_order+$i.quantity}
                    {/if}
                {/foreach}

                {if $total_q_PF_order > 0}
                    <td colspan="2" class="b2_t" align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">
                        <span class="{if $total_q_PF_order<0}info_warning_block{elseif $total_q_PF_order==0}zero{/if}">
                            {$total_q_PF_order|fn_fvalue:2}
                        </span>
                    </td>
                {else}
                    <td colspan="2" class="b2_t" align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">
                        &nbsp;
                    </td>
                {/if}
            {/foreach}

            {*************************************************************************************}
            {* ИТОГО НАСОС АГРЕГАТ*}
            {*************************************************************************************}
            <td rowspan="2" align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;" class="b2_t">
                <span class="{if $total_q_PA<0}info_warning_block{elseif $total_q_PA==0}zero{/if}">
                    <b>{$total_q_PA|fn_fvalue:2}</b>
                </span>
            </td>
            {foreach from=$orders item="o" name="o"}
                {assign var="total_q_PA_order" value=0}
                {foreach from=$o.data_for_tmp.PA item="i"}
                    {if $i.quantity > 0}
                        {assign var="total_q_PA_order" value=$total_q_PA_order+$i.quantity}
                    {/if}
                {/foreach}

                {if $total_q_PA_order > 0}
                    <td colspan="2" class="b2_t" align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">
                        <span class="{if $total_q_PA_order<0}info_warning_block{elseif $total_q_PA_order==0}zero{/if}">
                            {$total_q_PA_order|fn_fvalue:2}
                        </span>
                    </td>
                {else}
                    <td colspan="2" class="b2_t" align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">
                        &nbsp;
                    </td>
                {/if}
            {/foreach}
        </tr>
        <tr>
            {*Насос*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 3px solid #000000; text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</td>
            {/foreach}

            {*На раме*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 3px solid #000000; text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</td>
            {/foreach}

            {*Агрегат*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 3px solid #000000; text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</td>
            {/foreach}
        </tr>

        <tr>
            <td colspan="{math equation="1+2*x" x=$orders|count}" align="center" style="text-align: center; border-bottom: 3px solid #000000; border-left: 3px solid #000000; background-color: #D3D3D3;"><b>НАСОС</b></td>
            <td colspan="{math equation="1+2*x" x=$orders|count}" align="center" style="text-align: center; border-bottom: 3px solid #000000; border-left: 3px solid #000000; background-color: #D3D3D3;"><b>НА РАМЕ</b></td>
            <td colspan="{math equation="1+2*x" x=$orders|count}" align="center" style="text-align: center; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000; background-color: #D3D3D3;"><b>АГРЕГАТ</b></td>
        </tr>

        {* ОБЩИЙ ИТОГО *}
        <tr>
            <td align="center" style="border-left: 3px solid #000000; border-bottom: 3px solid #000000; border-right: 3px solid #000000; background-color: #D3D3D3;" colspan="{math equation="3*(1+2*x)" x=$orders|count}">
                {assign var="total_q" value=$total_q_P+$total_q_PF+$total_q_PA}
                <span class="{if $total_q<0}info_warning_block{elseif $total_q==0}zero{/if}">
                    <span style="font-size: 15px; font-weight: bold; text-align: right;">{$total_q|fn_fvalue:2}</span>
                </span>
            </td>
        </tr>
    {else}
        <tbody>
            <tr class="no-items">
                <td colspan="7"><p>{$lang.no_data}</p></td>
            </tr>
        </tbody>
    {/if}
</table>
</div>
