{include file="common_templates/subheader.tpl" title="Насосная продукция `$target_town`"}
<div class="subheader_block">
<table cellpadding="0" cellspacing="0" border="0" class="table sgp">
    <thead>
        <tr class="gr_l">
            <th rowspan="{if count($orders)}3{else}2{/if}" colspan="2" class="c" width="230px">
                <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                &nbsp;Наименование<br>(исполнение)
            </th>

            {*Задел*}
            {if $search.view_backlog == "Y"}
                <th rowspan="{if count($orders)}3{else}2{/if}"  class="c b1_l zadel"><img src="skins/basic/admin/addons/uns_mech/images/zadel.png"/></th>
            {/if}

            {*Насос*}
            <th colspan="{math equation="1+2*x" x=$orders|count}" class="c gr_d b_l">НАСОС</th>

            {*На раме*}
            <th colspan="{math equation="1+2*x" x=$orders|count}" class="c gr_d b_l">НА РАМЕ</th>

            {*Агрегат*}
            <th colspan="{math equation="1+2*x" x=$orders|count}" class="c gr_d b_l">АГРЕГАТ</th>
        </tr>
        {if count($orders)}
        <tr class="gr_l">
            {*Насос*}
            <th rowspan="2" class="c b_l gr_d">Тек.<br>ост.</th>
            <th colspan="{math equation="2*x" x=$orders|count}" class="c b_l b_t b1_b_b">Заказы</th>

            {*На раме*}
            <th rowspan="2" class="c b_l gr_d">Тек.<br>ост.</th>
            <th colspan="{math equation="2*x" x=$orders|count}" class="c b_l b_t b1_b_b">Заказы</th>

            {*Агрегат*}
            <th rowspan="2" class="c b_l gr_d">Тек.<br>ост.</th>
            <th colspan="{math equation="2*x" x=$orders|count}" class="c b_l b_t b1_b_b">Заказы</th>
        </tr>
        <tr class="gr_l">
            {*Насос*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <th colspan="2" class="c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</th>
            {/foreach}

            {*На раме*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <th colspan="2" class="c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</th>
            {/foreach}

            {*Агрегат*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <th colspan="2" class="c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</th>
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
                    <td class="gr_d" colspan="{math equation="1+5+3*2*x" x=$orders|count}">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                        &nbsp;<span style="color: #000000; font-weight: bold; font-size: 12px;">{$pt.pt_name}</span>
                    </td>
                </tr>
            {foreach from=$pt.pump_series key="ps_id" item="ps" name="ps"}
                {foreach from=$ps.pumps key="p_id" item="p" name="p"}
                <tr>
                    {assign var="curr_q" value=0}
                    {if $smarty.foreach.p.first}
                    <td class="{if !$smarty.foreach.ps.first}b2_t{/if} mw100" {if $ps.pumps|count > 1}rowspan="{$ps.pumps|count}"{/if}>&nbsp;
                        {assign var="n" value=$ps.ps_name}
                        {assign var="href" value="uns_balance_sgp.motion?item_id=`$p.p_id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&o_id=`$search.o_id`"}
                        <a  rev="content_item_{$p.p_id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n}</a>
                        <div id="content_item_{$p.p_id}" class="hidden" title="Движения <i><u>{$p.p_name}</u></i> по Складу готовой продукции {$target_town}"></div>
                    </td>
                    {/if}

                    <td class="b1_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} mw50">{*&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*}
                        {assign var="n" value=$p.p_name|replace:"`$ps.ps_name`":""}
                        {assign var="href" value="uns_balance_sgp.motion?item_id=`$p.p_id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&o_id=`$search.o_id`"}
                        <a  rev="content_item_{$p.p_id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="block cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n|trim}</a>
                        <div id="content_item_{$p.p_id}" class="hidden" title="Движения <i><u>{$p.p_name}</u></i> по Складу готовой продукции {$target_town}"></div>
                    </td>

                    {*Задел*}
                    {if $search.view_backlog == "Y"}
                        {if $zadel.$p_id > 0}
                            <td class="zadel c b1_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">{$zadel.$p_id}</td>
                        {else}
                            <td class="{if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}"></td>
                        {/if}
                    {/if}

                    {*************************************************************************************}
                    {*НАСОС*}
                    {*************************************************************************************}
                    {assign var="curr_q" value=$p.balances.P-$p.P.total_number_of_reserved}
                    {assign var="total_q_P" value=$total_q_P+$curr_q}
                    <td class="c b_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} {if is__array($orders)}gr_d{/if}">
                        <span class="{if $curr_q<0}i_w_b{elseif $curr_q==0}z{elseif $curr_q>0}b{/if}">{$curr_q}</span>
                    </td>
                    {foreach from=$orders item="o" name="o"}
                        {assign var="order_q"               value=$p.orders[$o.order_id].P-$p.orders_total_shipped[$o.order_id].P}
                        {assign var="order_q_in_reserve"    value=$p.orders_in_reserve[$o.order_id].P|default:0}
                        {assign var="curr_q"                value=$curr_q-$order_q+$order_q_in_reserve} {*Для того, чтобы последовательно вычитать из СГП имеющиеся заказы*}
                        <td class="mw16 c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if} {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">
                            {if $order_q!=0}
                                <span class="{if $order_q<0}i_w_b{elseif $order_q==0}z b{/if}">
                                    {if $o.data_for_tmp.P[$p.p_id].comment|strlen}
                                        {include file="common_templates/tooltip.tpl" tooltip="<b>Примечание</b><br>`$o.data_for_tmp.P[$p.p_id].comment`" params="black" tooltip_mark="<u>`$order_q`</u>"}
                                    {else}
                                        {$order_q}
                                    {/if}
                                </span>
                            {/if}
                        </td>

                        <td class="mw16 c bd_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">
                            {if $order_q>0}
                                {if $order_q_in_reserve > 0}
                                    <span title="Зарезервировано {$order_q_in_reserve} шт. из {$order_q} шт." class="h {if $order_q_in_reserve>=$order_q}i_g_b{else}i_y_b{/if} b">
                                        {$order_q_in_reserve}
                                    </span>
                                {else}
                                    {if $curr_q>=0}
                                        <span class="done"></span>
                                    {else}
                                        <span class="{if $curr_q<0}i_w_b b{/if}">{$curr_q}</span>
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                    {/foreach}

                    {*************************************************************************************}
                    {*НАСОС НА РАМЕ*}
                    {*************************************************************************************}
                    {assign var="curr_q" value=$p.balances.PF-$p.PF.total_number_of_reserved}
                    {assign var="total_q_PF" value=$total_q_PF+$curr_q}
                    <td class="c b_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} {if is__array($orders)}gr_d{/if}">
                        <span class="{if $curr_q<0}i_w_b{elseif $curr_q==0}z{elseif $curr_q>0}b{/if}">{$curr_q}</span>
                    </td>
                    {foreach from=$orders item="o" name="o"}
                        {assign var="order_q"               value=$p.orders[$o.order_id].PF-$p.orders_total_shipped[$o.order_id].PF}
                        {assign var="order_q_in_reserve"    value=$p.orders_in_reserve[$o.order_id].PF|default:0}
                        {assign var="curr_q"                value=$curr_q-$order_q+$order_q_in_reserve} {*Для того, чтобы последовательно вычитать из СГП имеющиеся заказы*}
                        <td class="mw16 c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if} {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">
                            {if $order_q!=0}
                                <span class="{if $order_q<0}i_w_b{elseif $order_q==0}z b{/if}">
                                    {if $o.data_for_tmp.PF[$p.p_id].comment|strlen}
                                        {include file="common_templates/tooltip.tpl" tooltip="<b>Примечание</b><br>`$o.data_for_tmp.PF[$p.p_id].comment`" params="black" tooltip_mark="<u>`$order_q`</u>"}
                                    {else}
                                        {$order_q}
                                    {/if}
                                </span>
                            {/if}
                        </td>

                        <td class="mw16 c bd_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">
                            {if $order_q>0}
                                {if $order_q_in_reserve > 0}
                                    <span title="Зарезервировано {$order_q_in_reserve} шт. из {$order_q} шт." class="h {if $order_q_in_reserve>=$order_q}i_g_b{else}i_y_b{/if} b">
                                        {$order_q_in_reserve}
                                    </span>
                                {else}
                                    {if $curr_q>=0}
                                        <span class="done"></span>
                                    {else}
                                        <span class="{if $curr_q<0}i_w_b b{/if}">{$curr_q}</span>
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                    {/foreach}

                    {*************************************************************************************}
                    {*НАСОС АГРЕГАТ*}
                    {*************************************************************************************}
                    {assign var="curr_q" value=$p.balances.PA-$p.PA.total_number_of_reserved}
                    {assign var="total_q_PA" value=$total_q_PA+$curr_q}
                    <td class="c b_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if} {if is__array($orders)}gr_d{/if}">
                        <span class="{if $curr_q<0}i_w_b{elseif $curr_q==0}z{elseif $curr_q>0}b{/if}">{$curr_q}</span>
                    </td>
                    {foreach from=$orders item="o" name="o"}
                        {assign var="order_q"               value=$p.orders[$o.order_id].PA-$p.orders_total_shipped[$o.order_id].PA}
                        {assign var="order_q_in_reserve"    value=$p.orders_in_reserve[$o.order_id].PA|default:0}
                        {assign var="curr_q"                value=$curr_q-$order_q+$order_q_in_reserve} {*Для того, чтобы последовательно вычитать из СГП имеющиеся заказы*}
                        <td class="mw16 c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if} {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">
                            {if $order_q!=0}
                                <span class="{if $order_q<0}i_w_b{elseif $order_q==0}z b{/if}">
                                    {if $o.data_for_tmp.PA[$p.p_id].comment|strlen}
                                        {include file="common_templates/tooltip.tpl" tooltip="<b>Примечание</b><br>`$o.data_for_tmp.PA[$p.p_id].comment`" params="black" tooltip_mark="<u>`$order_q`</u>"}
                                    {else}
                                        {$order_q}
                                    {/if}
                                </span>
                            {/if}
                        </td>

                        <td class="mw16 c bd_l {if !$smarty.foreach.ps.first and $smarty.foreach.p.first }b2_t{/if}">
                            {if $order_q>0}
                                {if $order_q_in_reserve > 0}
                                    <span title="Зарезервировано {$order_q_in_reserve} шт. из {$order_q} шт." class="h {if $order_q_in_reserve>=$order_q}i_g_b{else}i_y_b{/if} b">
                                        {$order_q_in_reserve}
                                    </span>
                                {else}
                                    {if $curr_q>=0}
                                        <span class="done"></span>
                                    {else}
                                        <span class="{if $curr_q<0}i_w_b b{/if}">{$curr_q}</span>
                                    {/if}
                                {/if}
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
            <td rowspan="4" colspan="{if $search.view_backlog == "Y"}3{else}2{/if}" class="right b b_t b_b">
                ИТОГО:
            </td>

            {*************************************************************************************}
            {* ИТОГО НАСОС*}
            {*************************************************************************************}
            <td rowspan="2" class="b_l b_t c gr_d" style="border-bottom-width: 0px;">
                <span class="b {if $total_q_P<0}i_w_b{elseif $total_q_P==0}z{/if}">{$total_q_P}</span>
            </td>
            {foreach from=$orders item="o" name="o"}
                {assign var="total_q_P_order" value=0}
                {foreach from=$o.data_for_tmp.P item="i"}
                    {if $i.quantity > 0}
                        {assign var="total_q_P_order" value=$total_q_P_order+$i.quantity}
                    {/if}
                {/foreach}

                {if $total_q_P_order > 0}
                    <td colspan="2" class="c b_t {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">
                        <span class="{if $total_q_P_order<0}i_w_b{elseif $total_q_P_order==0}z{/if}">{$total_q_P_order}</span>
                    </td>
                {else}
                    <td colspan="2" class="c b_t {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">
                        &nbsp;
                    </td>
                {/if}
            {/foreach}

            {*************************************************************************************}
            {* ИТОГО НАСОС НА РАМЕ*}
            {*************************************************************************************}
            <td rowspan="2" class="b_l b_t c gr_d" style="border-bottom-width: 0px;">
                <span class="b {if $total_q_PF<0}i_w_b{elseif $total_q_PF==0}z{/if}">{$total_q_PF}</span>
            </td>
            {foreach from=$orders item="o" name="o"}
                {assign var="total_q_PF_order" value=0}
                {foreach from=$o.data_for_tmp.PF item="i"}
                    {if $i.quantity > 0}
                        {assign var="total_q_PF_order" value=$total_q_PF_order+$i.quantity}
                    {/if}
                {/foreach}

                {if $total_q_PF_order > 0}
                    <td colspan="2" class="c b_t {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">
                        <span class="{if $total_q_PF_order<0}i_w_b{elseif $total_q_PF_order==0}z{/if}">{$total_q_PF_order}</span>
                    </td>
                {else}
                    <td colspan="2" class="c b_t {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">
                        &nbsp;
                    </td>
                {/if}
            {/foreach}

            {*************************************************************************************}
            {* ИТОГО НАСОС АГРЕГАТ*}
            {*************************************************************************************}
            <td rowspan="2" class="b_l b_t c gr_d" style="border-bottom-width: 0px;">
                <span class="b {if $total_q_PA<0}i_w_b{elseif $total_q_PA==0}z{/if}">{$total_q_PA}</span>
            </td>
            {foreach from=$orders item="o" name="o"}
                {assign var="total_q_PA_order" value=0}
                {foreach from=$o.data_for_tmp.PA item="i"}
                    {if $i.quantity > 0}
                        {assign var="total_q_PA_order" value=$total_q_PA_order+$i.quantity}
                    {/if}
                {/foreach}

                {if $total_q_PA_order > 0}
                    <td colspan="2" class="c b_t {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">
                        <span class="{if $total_q_PA_order<0}i_w_b{elseif $total_q_PA_order==0}z{/if}">{$total_q_PA_order}</span>
                    </td>
                {else}
                    <td colspan="2" class="c b_t {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">
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
                <td colspan="2" class="b1_t_b b_b c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</td>
            {/foreach}

            {*На раме*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <td colspan="2" class="b1_t_b b_b c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</td>
            {/foreach}

            {*Агрегат*}
            {foreach from=$orders item="o" name="o"}
                {assign var="date_finish" value=$o.date_finished|date_format:'%a %d/%m/%y'}
                {assign var="date_finish" value="`$date_finish` (осталось `$o.remaining_time` дней)"}
                <td colspan="2" class="b1_t_b b_b c {if $smarty.foreach.o.first}b_l{else}b1_l_b{/if}">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$o.customer_id].name`<br>Отгрузка: `$date_finish`" params="black" tooltip_mark="<b>`$customers[$o.customer_id].name_short`</b>"}</td>
            {/foreach}
        </tr>

        <tr>
            <td colspan="{math equation="1+2*x" x=$orders|count}" style="border-top-width: 0px;" class="c b_b b_l gr_d"><b>НАСОС</b></td>
            <td colspan="{math equation="1+2*x" x=$orders|count}" style="border-top-width: 0px;" class="c b_b b_l gr_d"><b>НА РАМЕ</b></td>
            <td colspan="{math equation="1+2*x" x=$orders|count}" style="border-top-width: 0px;" class="c b_b b_l gr_d b_r"><b>АГРЕГАТ</b></td>
        </tr>

        {* ОБЩИЙ ИТОГО *}
        <tr>
            <td class="c b_l b_b b_r gr_d" colspan="{math equation="3*(1+2*x)" x=$orders|count}" style="border-top-width: 0px;">
                {assign var="total_q" value=$total_q_P+$total_q_PF+$total_q_PA}
                <span class="b {if $total_q<0}i_w_b{elseif $total_q==0}z{/if}">
                    <span style="font-size: 15px;">{$total_q}</span>
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
