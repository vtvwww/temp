{strip}
    {foreach from=$balances.P key="group_id" item="item"}
            {assign var="id" value=$group_id}
            <tbody>
                <tr  m_id={$m.id}>
                    <td style="background-color: #d3d3d3; ">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="/skins/basic/admin/images/plus.gif">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="/skins/basic/admin/images/minus.gif">
                        &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span>
                    </td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                </tr>
            {foreach from=$item.items  item=m key=k_m}
                {assign var="q_P"   value=$balances.P[$group_id].items[$k_m].konech}
                {assign var="q_PF"  value=$balances.PF[$group_id].items[$k_m].konech}
                {assign var="q_PA"  value=$balances.PA[$group_id].items[$k_m].konech}
                {if $q_P > 0 or $q_PF > 0 or $q_PA > 0}
                    <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            {assign var="n" value=$m.name}
                            {assign var="href" value="foundry_get_balance.motion?item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&nach=`$m.nach`&current__in=`$m.current__in`&current__out=`$m.current__out`&konech=`$m.konech`"}
                            <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>
                            <div id="content_item_{$m.id}" class="hidden" title="Движение по {$n}"></div>
                        </td>

                        <td align="center" style="border-left: 1px dashed #808080;">
                            <span class="{if $q_P<0}info_warning_block{elseif $q_P==0}zero{/if}">
                                {$q_P|fn_fvalue:2}
                            </span>
                        </td>
                        <td align="center" style="border-left: 1px dashed #808080;">
                            <span class="{if $q_PF<0}info_warning_block{elseif $q_PF==0}zero{/if}">
                                {$q_PF|fn_fvalue:2}
                            </span>
                        </td>
                        <td align="center" style="border-left: 1px dashed #808080;">
                            <span class="{if $q_PA<0}info_warning_block{elseif $q_PA==0}zero{/if}">
                                {$q_PA|fn_fvalue:2}
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
{*
        {foreach from=$item.items item=m}
            {assign var="t_nach"            value=$t_nach+$m.nach}
            {assign var="t_current__in"     value=$t_current__in+$m.current__in}
            {assign var="t_current__out"    value=$t_current__out+$m.current__out}
            {assign var="t_konech"          value=$t_konech+$m.konech}

            <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {assign var="n" value=$m.name}
                    {if $m.no != ""}
                        {assign var="n" value="`$n` [`$m.no`]"}
                    {/if}
                    {assign var="href" value="foundry_get_balance.motion?item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&nach=`$m.nach`&current__in=`$m.current__in`&current__out=`$m.current__out`&konech=`$m.konech`"}
                    <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>
                    <div id="content_item_{$m.id}" class="hidden" title="Движение по {$n}"></div>
                </td>
                <td align="center">{if $mode_report == "P"}<b>{$pump_materials[$m.id].quantity|fn_fvalue:2}</b>{/if}</td>
                <td align="right">{$m.weight}</td>
                <td align="center"><span class="{if $m.nach<0}info_warning_block{elseif $m.nach==0}zero{/if}">{$m.nach|fn_fvalue:2}</span></td>
                <td align="center">{if $search.o_id!=999}<span class="{if $m.current__in<0}info_warning_block{elseif $m.current__in==0}zero{/if}">{$m.current__in|fn_fvalue:2}</span>{else}<span class="zero">&mdash;</span>{/if}</td>
                <td align="center">{if $search.o_id!=999}<span class="{if $m.current__out<0}info_warning_block{elseif $m.current__out==0}zero{/if}">{$m.current__out|fn_fvalue:2}</span>{else}<span class="zero">&mdash;</span>{/if}</td>
                <td align="center"><span class="{if $m.konech<0}info_warning_block{elseif $m.konech==0}zero{/if}">{$m.konech|fn_fvalue:2}</span></td>
                {if $search.accessory_pumps == "Y"}
                <td align="left">
                    {if $m.accessory_pump_series}
                        {$m.accessory_pump_series}
                    {else}
                        {if $m.accessory_pumps}[{$m.accessory_pumps}]{/if}
                    {/if}
                </td>
                {/if}
            </tr>
        {foreachelse}
            <tr class="no-items">
                <td colspan="7"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}

        {if $mode_report != "P"}
        <tr class="category_items {$id}{if $expand_all} hidden {/if}">
            <td colspan="7" >&nbsp;</td>
        </tr>
        {/if}
    {/capture}

    <tbody>
        {if $mode_report != "P"}
        <tr  m_id={$m.id}>
            <td style="background-color: #d3d3d3;">
                <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="/skins/basic/admin/images/plus.gif">
                <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="/skins/basic/admin/images/minus.gif">
                &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span></td>
            <td style="background-color: #d3d3d3;" align="center">&nbsp;</td>
            <td style="background-color: #d3d3d3;" align="right">&nbsp;</td>
            <td style="background-color: #d3d3d3;" align="center"></td>
            <td style="background-color: #d3d3d3;" align="center"></td>
            <td style="background-color: #d3d3d3;" align="center"></td>
            <td style="background-color: #d3d3d3;" align="center"></td>
            {if $search.accessory_pumps == "Y"}
                <td style="background-color: #d3d3d3;" align="center"></td>
            {/if}
        </tr>
        {/if}
        {$smarty.capture.category_items}
    </tbody>
    *}
{/strip}