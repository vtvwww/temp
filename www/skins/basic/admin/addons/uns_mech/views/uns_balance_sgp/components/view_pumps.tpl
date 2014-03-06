{include file="common_templates/subheader.tpl" title="Насосная продукция"}
<div class="subheader_block">

    {***************************************************************************}
    {*ТАБЛИЦА С УЧЕТОМ ЗАКАЗОВ*}
    {***************************************************************************}
    {if $orders|is__array}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr style="background-color: #EDEDED">
                    <th rowspan="3" align="center" style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>

                    {*Насос*}
                    <th colspan="{math equation="2+x" x=$orders|count}" align="center" style="text-align: center; border-left: 3px solid #000000; background-color: #D3D3D3;">НАСОС</th>

                    {*На раме*}
                    <th colspan="{math equation="2+x" x=$orders|count}" align="center" style="text-align: center; border-left: 3px solid #000000; background-color: #D3D3D3;">НА РАМЕ</th>

                    {*Агрегат*}
                    <th colspan="{math equation="2+x" x=$orders|count}" align="center" style="text-align: center; border-left: 3px solid #000000; background-color: #D3D3D3;">АГРЕГАТ</th>
                </tr>
                <tr style="background-color: #EDEDED">
                    {*Насос*}
                    <th rowspan="2" style="text-align: center; border-left: 3px solid #000000; border-top : 1px solid #808080; background-color: #D3D3D3;">Тек.<br>ост.</th>
                    <th colspan="{$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #808080; border-top: 3px solid #000000;">Заказы</th>
                    <th rowspan="2" style="text-align: center; border-left: 1px solid #808080; border-top : 3px solid #000000;">==</th>

                    {*На раме*}
                    <th rowspan="2" style="text-align: center; border-left: 3px solid #000000; border-top : 1px solid #808080; background-color: #D3D3D3;">Тек.<br>ост.</th>
                    <th colspan="{$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #808080; border-top: 3px solid #000000;">Заказы</th>
                    <th rowspan="2" style="text-align: center; border-left: 1px solid #808080; border-top : 3px solid #000000;">==</th>

                    {*Агрегат*}
                    <th rowspan="2" style="text-align: center; border-left: 3px solid #000000; border-top : 1px solid #808080; background-color: #D3D3D3;">Тек.<br>ост.</th>
                    <th colspan="{$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #808080; border-top: 3px solid #000000;">Заказы</th>
                    <th rowspan="2" style="text-align: center; border-left: 1px solid #808080; border-top : 3px solid #000000;">==</th>
                </tr>
                <tr style="background-color: #EDEDED">
                    {*Насос*}
                    {foreach from=$orders item="o" name="o"}
                        <th style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{$regions[$o.region_id].name_short}</th>
                    {/foreach}

                    {*На раме*}
                    {foreach from=$orders item="o" name="o"}
                        <th style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{$regions[$o.region_id].name_short}</th>
                    {/foreach}

                    {*Агрегат*}
                    {foreach from=$orders item="o" name="o"}
                        <th style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{$regions[$o.region_id].name_short}</th>
                    {/foreach}
                </tr>
            </thead>
            {if is__array($balances)}
                {assign var="total_q_P"     value=0}
                {assign var="total_q_PF"    value=0}
                {assign var="total_q_PA"    value=0}
                {foreach from=$balances.P key="group_id" item="item"}
                        {assign var="id" value=$group_id}
                        <tbody>
                            <tr  m_id={$m.id}>
                                <td style="background-color: #d3d3d3; " colspan="{math equation="7+3*x" x=$orders|count}">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                                    &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span>
                                </td>
                            </tr>
                        {foreach from=$item.items  item=m key=k_m}
                            {assign var="q_P"   value=$balances.P[$group_id].items[$k_m].konech}
                            {assign var="q_PF"  value=$balances.PF[$group_id].items[$k_m].konech}
                            {assign var="q_PA"  value=$balances.PA[$group_id].items[$k_m].konech}

                            {assign var="q_P_orders"  value=0}
                            {assign var="q_PF_orders" value=0}
                            {assign var="q_PA_orders" value=0}
                            {foreach from=$orders item="o"}
                                {if $o.data_for_tmp.P[$k_m].quantity > 0}
                                    {assign var="q_P_orders"  value=$q_P_orders+$o.data_for_tmp.P[$k_m].quantity}
                                {/if}
                                {if $o.data_for_tmp.PF[$k_m].quantity > 0}
                                    {assign var="q_PF_orders"  value=$q_PF_orders+$o.data_for_tmp.PF[$k_m].quantity}
                                {/if}
                                {if $o.data_for_tmp.PA[$k_m].quantity > 0}
                                    {assign var="q_PA_orders"  value=$q_PA_orders+$o.data_for_tmp.PA[$k_m].quantity}
                                {/if}
                            {/foreach}

                            {if ($q_P > 0 or $q_P < 0) or ($q_PF > 0 or $q_PF < 0) or ($q_PA > 0 or $q_PA < 0) or ($q_P_orders > 0 or $q_PF_orders > 0 or $q_PA_orders > 0)}
                                <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {assign var="n" value=$m.name}
                                        {assign var="href" value="foundry_get_balance.motion?item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&nach=`$m.nach`&current__in=`$m.current__in`&current__out=`$m.current__out`&konech=`$m.konech`"}
                                        <b>{$n}</b>
                                        {*<a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>*}
                                        {*<div id="content_item_{$m.id}" class="hidden" title="Движение по {$n}"></div>*}
                                    </td>

                                    {*************************************************************************************}
                                    {*НАСОС*}
                                    {*************************************************************************************}
                                    <td align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;">
                                        <span class="{if $q_P<0}info_warning_block{elseif $q_P==0}zero{/if}">
                                            <b>{$q_P|fn_fvalue:2}</b>
                                            {if $q_P >= 0}{assign var="total_q_P" value=$total_q_P+$q_P}{/if}
                                        </span>
                                    </td>
                                    {foreach from=$orders item="o" name="o"}
                                        {if $o.data_for_tmp.P[$k_m].quantity > 0}
                                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                                <span class="{if $o.data_for_tmp.P[$k_m].quantity<0}info_warning_block{elseif $o.data_for_tmp.P[$k_m].quantity==0}zero{/if}">
                                                    {assign var="q" value=$o.data_for_tmp.P[$k_m].quantity|fn_fvalue:2}
                                                    {if $o.data_for_tmp.P[$k_m].comment|strlen}
                                                        {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.P[$k_m].comment tooltip_mark="<b>`$q`</b>"}
                                                    {else}
                                                        {$q}
                                                    {/if}
                                                </span>
                                            </td>
                                        {else}
                                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                                &nbsp;
                                            </td>
                                        {/if}
                                    {/foreach}
                                    <td align="center" style="border-left: 1px solid #808080;">
                                        {if $q_P_orders > 0}
                                            {assign var="diff" value=$q_P-$q_P_orders}
                                            <span class="{if $diff<0}info_warning_block{elseif $diff==0}zero{/if}">
                                                {$diff|fn_fvalue:2}
                                            </span>
                                        {else}
                                            &nbsp;
                                        {/if}
                                    </td>

                                    {*************************************************************************************}
                                    {*НАСОС НА РАМЕ*}
                                    {*************************************************************************************}
                                    <td align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;">
                                        <span class="{if $q_PF<0}info_warning_block{elseif $q_PF==0}zero{/if}">
                                            <b>{$q_PF|fn_fvalue:2}</b>
                                            {if $q_PF >= 0}{assign var="total_q_PF" value=$total_q_PF+$q_PF}{/if}
                                        </span>
                                    </td>
                                    {foreach from=$orders item="o" name="o"}
                                        {if $o.data_for_tmp.PF[$k_m].quantity > 0}
                                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                                <span class="{if $o.data_for_tmp.PF[$k_m].quantity<0}info_warning_block{elseif $o.data_for_tmp.PF[$k_m].quantity==0}zero{/if}">
                                                    {assign var="q" value=$o.data_for_tmp.PF[$k_m].quantity|fn_fvalue:2}
                                                    {if $o.data_for_tmp.PF[$k_m].comment|strlen}
                                                        {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.PF[$k_m].comment tooltip_mark="<b>`$q`</b>"}
                                                    {else}
                                                        {$q}
                                                    {/if}
                                                </span>
                                            </td>
                                        {else}
                                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                                &nbsp;
                                            </td>
                                        {/if}
                                    {/foreach}
                                    <td align="center" style="border-left: 1px solid #808080;">
                                        {if $q_PF_orders > 0}
                                            {assign var="diff" value=$q_PF-$q_PF_orders}
                                            <span class="{if $diff<0}info_warning_block{elseif $diff==0}zero{/if}">
                                                {$diff|fn_fvalue:2}
                                            </span>
                                        {else}
                                            &nbsp;
                                        {/if}
                                    </td>

                                    {*************************************************************************************}
                                    {*НАСОС АГРЕГАТ*}
                                    {*************************************************************************************}
                                    <td align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;">
                                        <span class="{if $q_PA<0}info_warning_block{elseif $q_PA==0}zero{/if}">
                                            <b>{$q_PA|fn_fvalue:2}</b>
                                            {if $q_PA >= 0}{assign var="total_q_PA" value=$total_q_PA+$q_PA}{/if}
                                        </span>
                                    </td>
                                    {foreach from=$orders item="o" name="o"}
                                        {if $o.data_for_tmp.PA[$k_m].quantity > 0}
                                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                                <span class="{if $o.data_for_tmp.PA[$k_m].quantity<0}info_warning_block{elseif $o.data_for_tmp.PA[$k_m].quantity==0}zero{/if}">
                                                    {assign var="q" value=$o.data_for_tmp.PA[$k_m].quantity|fn_fvalue:2}
                                                    {if $o.data_for_tmp.PA[$k_m].comment|strlen}
                                                        {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.PA[$k_m].comment tooltip_mark="<b>`$q`</b>"}
                                                    {else}
                                                        {$q}
                                                    {/if}
                                                </span>
                                            </td>
                                        {else}
                                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                                &nbsp;
                                            </td>
                                        {/if}
                                    {/foreach}
                                    <td align="center" style="border-left: 1px solid #808080;">
                                        {if $q_PF_orders > 0}
                                            {assign var="diff" value=$q_PA-$q_PA_orders}
                                            <span class="{if $diff<0}info_warning_block{elseif $diff==0}zero{/if}">
                                                {$diff|fn_fvalue:2}
                                            </span>
                                        {else}
                                            &nbsp;
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                        {foreachelse}
                            <tr class="no-items">
                                <td colspan="7"><p>{$lang.no_data}</p></td>
                            </tr>
                        {/foreach}
                        </tbody>
                {/foreach}

                {***************************************************************}
                {**** ИТОГО: ***************************************************}
                {***************************************************************}
                <tr>
                    <td align="right" rowspan="4"><span style="font-size: 15px; font-weight: bold; text-align: right;">ИТОГО:</span></td>

                    {*************************************************************************************}
                    {* ИТОГО НАСОС*}
                    {*************************************************************************************}
                    <td rowspan="2" align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;">
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
                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                <span class="{if $total_q_P_order<0}info_warning_block{elseif $total_q_P_order==0}zero{/if}">
                                    {$total_q_P_order|fn_fvalue:2}
                                </span>
                            </td>
                        {else}
                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                &nbsp;
                            </td>
                        {/if}
                    {/foreach}
                    <td rowspan="2" align="center" style="border-left: 1px solid #808080; border-bottom: 3px solid #000000;">
                        <span class="zero">&nbsp;</span>
                    </td>

                    {*************************************************************************************}
                    {* ИТОГО НАСОС НА РАМЕ*}
                    {*************************************************************************************}
                    <td rowspan="2" align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;">
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
                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                <span class="{if $total_q_PF_order<0}info_warning_block{elseif $total_q_PF_order==0}zero{/if}">
                                    {$total_q_PF_order|fn_fvalue:2}
                                </span>
                            </td>
                        {else}
                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                &nbsp;
                            </td>
                        {/if}
                    {/foreach}
                    <td rowspan="2" align="center" style="border-left: 1px solid #808080; border-bottom: 3px solid #000000;">
                        <span class="zero">&nbsp;</span>
                    </td>

                    {*************************************************************************************}
                    {* ИТОГО НАСОС АГРЕГАТ*}
                    {*************************************************************************************}
                    <td rowspan="2" align="center" style="border-left: 3px solid #000000; background-color: #D3D3D3;">
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
                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                <span class="{if $total_q_PA_order<0}info_warning_block{elseif $total_q_PA_order==0}zero{/if}">
                                    {$total_q_PA_order|fn_fvalue:2}
                                </span>
                            </td>
                        {else}
                            <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                &nbsp;
                            </td>
                        {/if}
                    {/foreach}
                    <td rowspan="2" align="center" style="border-left: 1px solid #808080; border-bottom: 3px solid #000000;">
                        <span class="zero">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    {*Насос*}
                    {foreach from=$orders item="o" name="o"}
                        <td style=" border-bottom: 3px solid #000000; text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}"><b>{$regions[$o.region_id].name_short}</b></td>
                    {/foreach}

                    {*На раме*}
                    {foreach from=$orders item="o" name="o"}
                        <td style=" border-bottom: 3px solid #000000; text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}"><b>{$regions[$o.region_id].name_short}</b></td>
                    {/foreach}

                    {*Агрегат*}
                    {foreach from=$orders item="o" name="o"}
                        <td style=" border-bottom: 3px solid #000000; text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}"><b>{$regions[$o.region_id].name_short}</b></td>
                    {/foreach}
                </tr>

                <tr>
                    <td colspan="{math equation="2+x" x=$orders|count}" align="center" style="text-align: center; border-bottom: 3px solid #000000; border-left: 3px solid #000000; background-color: #D3D3D3;"><b>НАСОС</b></td>
                    <td colspan="{math equation="2+x" x=$orders|count}" align="center" style="text-align: center; border-bottom: 3px solid #000000; border-left: 3px solid #000000; background-color: #D3D3D3;"><b>НА РАМЕ</b></td>
                    <td colspan="{math equation="2+x" x=$orders|count}" align="center" style="text-align: center; border-bottom: 3px solid #000000; border-left: 3px solid #000000; border-right: 3px solid #000000; background-color: #D3D3D3;"><b>АГРЕГАТ</b></td>
                </tr>

                {* ОБЩИЙ ИТОГО *}
                <tr>
                    <td align="center" style="border-left: 3px solid #000000; border-bottom: 3px solid #000000; border-right: 3px solid #000000; background-color: #D3D3D3;" colspan="{math equation="6+3*x" x=$orders|count}">
                        <span class="{if $total_q_P<0}info_warning_block{elseif $total_q_P==0}zero{/if}">
                            <span style="font-size: 15px; font-weight: bold; text-align: right;">{$total_q_P+$total_q_PF+$total_q_PA|fn_fvalue:2}</span>
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
    {else}
        {***************************************************************************}
        {*ТАБЛИЦА БЕЗ ЗАКАЗОВ*}
        {***************************************************************************}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th rowspan="3" style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>
                    <th style="border-left: 1px solid #808080;">Насос</th>
                    <th style="border-left: 1px solid #808080;">На раме</th>
                    <th style="border-left: 1px solid #808080;">Агрегат</th>
                </tr>
            </thead>
            {if is__array($balances)}
                {assign var="total_q_P"     value=0}
                {assign var="total_q_PF"    value=0}
                {assign var="total_q_PA"    value=0}
                {foreach from=$balances.P key="group_id" item="item"}
                        {assign var="id" value=$group_id}
                        <tbody>
                            <tr  m_id={$m.id}>
                                <td style="background-color: #d3d3d3; " colspan="4">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                                    &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span>
                                </td>
                            </tr>
                        {foreach from=$item.items  item=m key=k_m}
                            {assign var="q_P"   value=$balances.P[$group_id].items[$k_m].konech}
                            {assign var="q_PF"  value=$balances.PF[$group_id].items[$k_m].konech}
                            {assign var="q_PA"  value=$balances.PA[$group_id].items[$k_m].konech}
                            {if ($q_P > 0 or $q_P < 0) or ($q_PF > 0 or $q_PF < 0) or ($q_PA > 0 or $q_PA < 0)}
                                <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {assign var="n" value=$m.name}
                                        {assign var="href" value="foundry_get_balance.motion?item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&nach=`$m.nach`&current__in=`$m.current__in`&current__out=`$m.current__out`&konech=`$m.konech`"}
                                        <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>
                                        <div id="content_item_{$m.id}" class="hidden" title="Движение по {$n}"></div>
                                    </td>

                                    <td align="center" style="border-left: 1px dashed #808080;">
                                        <span class="{if $q_P<0}info_warning_block{elseif $q_P==0}zero{/if}">
                                            <b>{$q_P|fn_fvalue:2}</b>
                                            {if $q_P >= 0}
                                                {assign var="total_q_P" value=$total_q_P+$q_P}
                                            {/if}
                                        </span>
                                    </td>
                                    <td align="center" style="border-left: 1px dashed #808080;">
                                        <span class="{if $q_PF<0}info_warning_block{elseif $q_PF==0}zero{/if}">
                                            <b>{$q_PF|fn_fvalue:2}</b>
                                            {if $q_PF >= 0}
                                                {assign var="total_q_PF" value=$total_q_PF+$q_PF}
                                            {/if}
                                        </span>
                                    </td>
                                    <td align="center" style="border-left: 1px dashed #808080;">
                                        <span class="{if $q_PA<0}info_warning_block{elseif $q_PA==0}zero{/if}">
                                            <b>{$q_PA|fn_fvalue:2}</b>
                                            {if $q_PA >= 0}
                                                {assign var="total_q_PA" value=$total_q_PA+$q_PA}
                                            {/if}
                                        </span>
                                    </td>
                                </tr>
                            {/if}
                        {foreachelse}
                            <tr class="no-items">
                                <td colspan="7"><p>{$lang.no_data}</p></td>
                            </tr>
                        {/foreach}
                        </tbody>
                {/foreach}
                <tr>
                    <td align="right"><span style="font-size: 15px; font-weight: bold; text-align: right;">ИТОГО:</span></td>

                    <td align="center" style="border-left: 1px dashed #808080;">
                        <span class="{if $total_q_P<0}info_warning_block{elseif $total_q_P==0}zero{/if}">
                            <span style="font-size: 15px; font-weight: bold; text-align: right;">{$total_q_P|fn_fvalue:2}</span>
                        </span>
                    </td>
                    <td align="center" style="border-left: 1px dashed #808080;">
                        <span class="{if $total_q_PF<0}info_warning_block{elseif $total_q_PF==0}zero{/if}">
                            <span style="font-size: 15px; font-weight: bold; text-align: right;">{$total_q_PF|fn_fvalue:2}</span>
                        </span>
                    </td>
                    <td align="center" style="border-left: 1px dashed #808080;">
                        <span class="{if $total_q_PA<0}info_warning_block{elseif $total_q_PA==0}zero{/if}">
                            <span style="font-size: 15px; font-weight: bold; text-align: right;">{$total_q_PA|fn_fvalue:2}</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td align="center" style="border-left: 1px dashed #808080;" colspan="3">
                        <span class="{if $total_q_P<0}info_warning_block{elseif $total_q_P==0}zero{/if}">
                            <span style="font-size: 15px; font-weight: bold; text-align: right;">{$total_q_P+$total_q_PF+$total_q_PA|fn_fvalue:2}</span>
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
    {/if}
</div>
