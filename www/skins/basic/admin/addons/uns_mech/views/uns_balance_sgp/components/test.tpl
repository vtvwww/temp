{strip}


    <table border="1" style="border-collapse: collapse;">
        <tr>
            <td align="center" colspan="3">МЦ1</td>
            <td align="center" colspan="3">МЦ2</td>
        </tr>
        <tr>
            <td width="40px" align="center" >PRCSS</td>
            <td width="40px" align="center" >CMPL</td>
            <td width="40px" align="center" >TTL</td>
            <td width="40px" align="center" >PRCSS</td>
            <td width="40px" align="center" >CMPL</td>
            <td width="40px" align="center" >TTL</td>

        </tr>
        <tr>
            <td>{$balances[10].PROCESSING[7].items[5].konech|fn_fvalue:2}</td>
            <td>{$balances[10].COMPLETE[7].items[5].konech|fn_fvalue:2}</td>
            <td align="center">
                <b>{$balances[10].PROCESSING[7].items[5].konech|fn_fvalue:2}/{$balances[10].COMPLETE[7].items[5].konech|fn_fvalue:2}</b>
            </td>
            <td>{$balances[14].PROCESSING[7].items[5].konech|fn_fvalue:2}</td>
            <td>{$balances[14].COMPLETE[7].items[5].konech|fn_fvalue:2}</td>
            <td align="center">
                <b>{$balances[14].PROCESSING[7].items[5].konech|fn_fvalue:2}/{$balances[14].COMPLETE[7].items[5].konech|fn_fvalue:2}</b>
            </td>
        </tr>
    </table>



    <table class="table">
        {foreach from=$gr key="group_id" item="item"}
            {assign var="id" value=$group_id}
            <tbody>
                <tr  m_id={$m.id}>
                    <td style="background-color: #d3d3d3; ">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="/skins/basic/admin/images/plus.gif">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="/skins/basic/admin/images/minus.gif">
                        &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span>
                    </td>
                    <td style="background-color: #d3d3d3;" align="right">&nbsp;</td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                    <td style="background-color: #d3d3d3;" align="center"></td>
                    {if $search.accessory_pumps == "Y"}
                        <td style="background-color: #d3d3d3;" align="center"></td>
                    {/if}
                </tr>
            {foreach from=$item.items  item=m key=k_m}
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
                    {assign var="q_obj_10" value=$balances[10][$group_id].items[$k_m].konech}
                    {assign var="q_obj_14" value=$balances[14][$group_id].items[$k_m].konech}
                    {assign var="q_obj_17" value=$balances[17][$group_id].items[$k_m].konech}
                    {assign var="q_obj_total" value=$q_obj_10+$q_obj_14+$q_obj_17}

                    <td align="center" style="border-left: 1px solid #808080;"><span class="{if $q_obj_10<0}info_warning_block{elseif $q_obj_10==0}zero{/if}">{$q_obj_10|fn_fvalue:2}</span></td>
                    <td align="center" style="border-left: 1px solid #808080;"><span class="{if $q_obj_14<0}info_warning_block{elseif $q_obj_14==0}zero{/if}">{$q_obj_14|fn_fvalue:2}</span></td>
                    <td align="center" style="border-left: 1px solid #808080;"><span class="{if $q_obj_17<0}info_warning_block{elseif $q_obj_17==0}zero{/if}">{$q_obj_17|fn_fvalue:2}</span></td>
                    <td align="center" style="border-left: 1px solid #808080;"><span class="{if $q_obj_total<0}info_warning_block{elseif $q_obj_total==0}zero{/if}"><b>{$q_obj_total|fn_fvalue:2}</b></span></td>
                    {if $search.accessory_pumps == "Y"}
                    <td align="left" style="border-left: 1px solid #808080;">
                        {if $m.accessory_pump_series}
                            &nbsp;&nbsp;{$m.accessory_pump_series}
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
            </tbody>
        {/foreach}
    </table>
<hr>
{/strip}