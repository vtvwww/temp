{strip}
    {assign var="id" value=$key}

    {assign var="t_nach"            value=0}
    {assign var="t_current__in"     value=0}
    {assign var="t_current__out"    value=0}
    {assign var="t_konech"          value=0}

    {capture name="category_items"}
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
                    <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n}</a>
                    <div id="content_item_{$m.id}" class="hidden" title="Движение {$n|upper} по Складу литья"></div>
                </td>
                <td align="center">{if $mode_report == "P"}<b>{$pump_materials[$m.id].quantity|fn_fvalue:2}</b>{/if}</td>
                <td align="center" class="b1_r b1_l"><span style="font-size: 11px; color: #808080;">{$m.weight}</span></td>
                <td align="center" class="b1_r b1_l"><span class="{if $m.nach<0}info_warning_block{elseif $m.nach==0}zero{/if}">{$m.nach|fn_fvalue:2}</span></td>
                <td align="center" class="b1_r b1_l"><span class="{if $m.current__in<0}info_warning_block{elseif $m.current__in==0}zero{/if}">{$m.current__in|fn_fvalue:2}</span></td>
                <td align="center" class="b1_l"><span class="{if $m.current__out<0}info_warning_block{elseif $m.current__out==0}zero{/if}">{$m.current__out|fn_fvalue:2}</span></td>
                <td align="center" class="b_l" style="background-color: #d3d3d3;" ><span class="{if $m.konech<0}info_warning_block{elseif $m.konech==0}zero{/if} bold">{$m.konech|fn_fvalue:2}</span></td>
                {if $search.accessory_pumps == "Y"}
                <td align="left" class="b_l">
                    {if $m.accessory_view == "M"}
                        {assign var="accessory_view" value="`$m.accessory_pump_manual` <span class='info_warning'>(`$m.accessory_view`)</span>"}
                    {elseif $m.accessory_view == "P"}
                        {assign var="accessory_view" value="`$m.accessory_pumps` <span class='info_warning'>(`$m.accessory_view`)</span>"}
                    {else}
                        {assign var="accessory_view" value="`$m.accessory_pump_series`"}
                    {/if}
                    {$accessory_view}
                    {if strlen($m.material_comment_1)}
                        {if strlen($accessory_view)}<br/>{/if}
                        <span class="info_warning">{$m.material_comment_1}</span>
                    {/if}
                </td>
                {/if}
            </tr>
        {foreachelse}
            <tr class="no-items">
                <td colspan="8"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}
        {if $mode_report != "P"}
        {*<tr class="category_items {$id}{if $expand_all} hidden {/if}">*}
            {*<td colspan="8" >&nbsp;</td>*}
        {*</tr>*}
        {/if}
    {/capture}

    <tbody>
        {if $mode_report != "P"} {* список всех материалов *}
        <tr  m_id={$m.id}>
            <td colspan="8" style="background-color: #d3d3d3;">
                <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span>{if $item.group_comment} <span class="info_warning">({$item.group_comment})</span>{/if}</td>
            {*{if $search.accessory_pumps == "Y"}*}
                {*<td style="background-color: #d3d3d3;" align="center"></td>*}
            {*{/if}*}
        </tr>
        {/if}
        {$smarty.capture.category_items}
    </tbody>
{/strip}