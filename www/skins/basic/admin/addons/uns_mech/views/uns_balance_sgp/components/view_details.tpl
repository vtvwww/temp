{include file="common_templates/subheader.tpl" title="Детали"}
<div class="subheader_block">

    {***************************************************************************}
    {*ТАБЛИЦА С УЧЕТОМ ЗАКАЗОВ*}
    {***************************************************************************}
    {if $orders|is__array}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr style="background-color: #EDEDED">
                    <th rowspan="2" style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>
                    <th rowspan="2" style="border-left: 1px solid #808080;">КЛМ</th>
                    <th rowspan="2" style="border-left: 1px solid #808080; border-left: 3px solid #000000; background-color: #D3D3D3;">Тек.<br>ост.</th>
                    <th colspan="{$orders|count}" style="text-align: center; border-left: 3px solid #000000; border-bottom: 1px solid #808080;">Заказы</th>
                    <th rowspan="2" style="border-left: 1px solid #808080;">==</th>
                    <th rowspan="2" style="border-left: 1px solid #808080; border-left: 3px solid #000000">Принадлежность к насосам</th>
                </tr>
                <tr style="background-color: #EDEDED">
                    {foreach from=$orders item="o" name="o"}
                        <th style="text-align: center; {if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px solid #000000;{/if}">{$regions[$o.region_id].name_short}</th>
                    {/foreach}
                </tr>
            </thead>
            {if is__array($balances)}
                {assign var="gr" value=$balances}
                {foreach from=$gr key="group_id" item="item"}
                        {assign var="id" value=$group_id}
                        <tbody>
                        {foreach from=$item.items  item=m key=k_m}
                            {if $m.konech >0 or $m.konech<0}
                            <tr>
                                <td colspan="{math equation="5+3*x" x=$orders|count}" style="background-color: #d3d3d3; ">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                                    &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group} [{$item.group_id}] {if $item.group_comment} <span class="info_warning">({$item.group_comment})</span>{/if}</span>
                                </td>
                            </tr>
                            {/if}
                        {/foreach}
                        {foreach from=$item.items  item=m key=k_m}
                            {assign var="q_D"  value=$m.konech}
                            {assign var="q_D_orders"  value=0}
                            {foreach from=$orders item="o"}
                                {if $o.data_for_tmp.D[$k_m].quantity > 0}
                                    {assign var="q_D_orders"  value=$q_D_orders+$o.data_for_tmp.D[$k_m].quantity}
                                {/if}
                            {/foreach}

                            {if ($q_D > 0 or $q_D < 0) or ($q_D_orders < 0 or $q_D_orders > 0)}
                            <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                                <td {if $m.checked == "N"}style="border-right: 2px solid red;"{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {assign var="n" value=$m.name}
                                    {if $m.no != ""}
                                        {assign var="n" value="`$n` [`$m.no`]"}
                                    {/if}
                                    {assign var="href" value="uns_balance_mc_sk_su.motions?item_type=D&item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                                    {assign var="href" value="uns_balance_mc_sk_su.motions?item_type=D&item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                                    <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>
                                    <div id="content_item_{$m.id}" class="hidden" title="Движение по {$n}"></div>
                                </td>
                                <td align="center" style="border-left: 1px dotted #808080;"><span class="info_warning">{$m.material_no|replace:' ':'&nbsp;'}</span></td>
                                <td align="center" style="background-color: #D3D3D3; border-left: 3px solid  #000000;">
                                    <span class="{if $q_D<0}info_warning_block{elseif $q_D==0}zero{/if}">
                                        <b>{$q_D|fn_fvalue:2}</b>
                                    </span>
                                </td>

                                {* ЗАКАЗЫ *}
                                {foreach from=$orders item="o" name="o"}
                                    {if $o.data_for_tmp.D[$k_m].quantity > 0}
                                        <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                            <span class="{if $o.data_for_tmp.D[$k_m].quantity<0}info_warning_block{elseif $o.data_for_tmp.D[$k_m].quantity==0}zero{/if}">
                                                {if $o.data_for_tmp.D[$k_m].comment|strlen}
                                                    {assign var="q" value=$o.data_for_tmp.D[$k_m].quantity|fn_fvalue:'2'}
                                                    {include file="common_templates/tooltip.tpl" tooltip=$o.data_for_tmp.D[$k_m].comment tooltip_mark="<b>`$q`</b>"}
                                                {else}
                                                    {$o.data_for_tmp.D[$k_m].quantity|fn_fvalue:2}
                                                {/if}
                                            </span>
                                        </td>
                                    {else}
                                        <td align="center" style="{if $smarty.foreach.o.first}border-left: 3px solid #000000;{else}border-left: 1px dashed #808080;{/if}">
                                            <span class="zero">0</span>
                                        </td>
                                    {/if}
                                {/foreach}
                                <td align="center" style="border-left: 1px solid #808080;">
                                    {assign var="diff" value=$q_D-$q_D_orders}
                                    <span class="{if $diff<0}info_warning_block{elseif $diff==0}zero{/if}">
                                        {$diff|fn_fvalue:2}
                                    </span>
                                </td>

                                <td align="left" style="border-left: 3px solid #000000;">
                                    &nbsp;
                                    {if $m.accessory_view == "M"}
                                        {$m.accessory_pump_manual} <span class="info_warning">({$m.accessory_view})</span>
                                    {elseif $m.accessory_view == "P"}
                                        {$m.accessory_pumps} <span class="info_warning">({$m.accessory_view})</span>
                                    {else}
                                        {$m.accessory_pump_series}
                                    {/if}
                                    {if strlen($m.comment)}
                                        <br><span class="info_warning">{$m.comment}</span>
                                    {/if}
                                </td>
                            </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                {/foreach}
            {else}
                <tbody>
                    <tr class="no-items">
                        <td colspan="7"><p>{$lang.no_data}</p></td>
                    </tr>
                </tbody>
            {/if}
        </table>

    {***************************************************************************}
    {*ТАБЛИЦА БЕЗ ЗАКАЗОВ*}
    {***************************************************************************}
    {else}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>
                    <th style="border-left: 1px solid #808080;">КЛМ</th>
                    <th style="border-left: 1px solid #808080;">СГП</th>
                    <th style="border-left: 1px solid #808080;">Принадлежность к насосам</th>
                </tr>
            </thead>
            {if is__array($balances)}
                {assign var="gr" value=$balances}
                {foreach from=$gr key="group_id" item="item"}
                        {assign var="id" value=$group_id}
                        <tbody>
                        {foreach from=$item.items  item=m key=k_m}
                            {if $m.konech >0 or $m.konech<0}
                            <tr>
                                <td colspan="4" style="background-color: #d3d3d3; ">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                                    <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                                    &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group} [{$item.group_id}] {if $item.group_comment} <span class="info_warning">({$item.group_comment})</span>{/if}</span>
                                </td>
                            </tr>
                            {/if}
                        {/foreach}
                        {foreach from=$item.items  item=m key=k_m}
                            {if $m.konech >0 or $m.konech<0}
                            <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                                <td {if $m.checked == "N"}style="border-right: 2px solid red;"{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {assign var="n" value=$m.name}
                                    {if $m.no != ""}
                                        {assign var="n" value="`$n` [`$m.no`]"}
                                    {/if}
                                    {assign var="href" value="uns_balance_mc_sk_su.motions?item_type=D&item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                                    {assign var="href" value="uns_balance_mc_sk_su.motions?item_type=D&item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                                    <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>
                                    <div id="content_item_{$m.id}" class="hidden" title="Движение по {$n}"></div>
                                </td>
                                <td align="center"><span class="info_warning">{$m.material_no|replace:' ':'&nbsp;'}</span></td>
                                {assign var="q_obj_19" value=$m.konech}
                                <td align="center" style="border-left: 1px solid  black;"><span class="{if $q_obj_19<0}info_warning_block{elseif $q_obj_19==0}zero{/if}"><b>{$q_obj_19|fn_fvalue:2}</b></span></td>
                                <td align="left" style="border-left: 1px solid #808080;">
                                    &nbsp;
                                    {if $m.accessory_view == "M"}
                                        {$m.accessory_pump_manual} <span class="info_warning">({$m.accessory_view})</span>
                                    {elseif $m.accessory_view == "P"}
                                        {$m.accessory_pumps} <span class="info_warning">({$m.accessory_view})</span>
                                    {else}
                                        {$m.accessory_pump_series}
                                    {/if}
                                    {if strlen($m.comment)}
                                        <br><span class="info_warning">{$m.comment}</span>
                                    {/if}
                                </td>
                            </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                {/foreach}
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
