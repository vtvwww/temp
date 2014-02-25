{strip}
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
{/strip}