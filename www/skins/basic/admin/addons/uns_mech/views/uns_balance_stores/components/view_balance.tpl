{strip}
    {foreach from=$balances key="group_id" item="item"}
            {assign var="id" value=$group_id}
            <tbody>
                <tr  m_id={$m.id}>
                    <td style="background-color: #d3d3d3; " colspan="9">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                        <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                        &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group} {*[{$item.group_id}] *}{if $item.group_comment} <span class="info_warning">({$item.group_comment})</span>{/if}</span>
                    </td>
                </tr>
            {foreach from=$item.items  item=m key=k_m}
                <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                    <td {if $m.checked == "N"}style="border-right: 2px solid red;"{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        {assign var="n" value=$m.name}
                        {if $m.no != ""}
                            {assign var="n" value="`$n` [`$m.no`]"}
                        {/if}
                        {assign var="href" value="uns_balance_stores.motion?item_type=M&item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&o_id=`$search.o_id`&nach=`$m.nach`&current__in=`$m.current__in`&current__out=`$m.current__out`&konech=`$m.konech`"}
                        <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" onclick="mark_item($(this));">{$n}</a>
                        <div id="content_item_{$m.id}" class="hidden" title="Движение: {$n}"></div>
                        {*{$n}*}
                    </td>
                    <td class="center b1_l">
                        {$m.u_name}
                    </td>
                    {assign var="q" value=$m.nach}
                    <td class="center b1_l">
                        <span class="{if $q<0}info_warning_block{elseif $q==0}zero{/if}">{$q|fn_fvalue:1}</span>
                    </td>

                    {assign var="q" value=$m.current__in}
                    <td class="center b1_l">
                        <span class="{if $q<0}info_warning_block{elseif $q==0}zero{/if}">{$q|fn_fvalue:1}</span>
                    </td>

                    {assign var="q" value=$m.current__out}
                    <td class="center b1_l">
                        <span class="{if $q<0}info_warning_block{elseif $q==0}zero{/if}">{$q|fn_fvalue:1}</span>
                    </td>

                    {assign var="q" value=$m.konech}
                    <td class="center b2_l bold" style="background-color: #d3d3d3;">
                        <span class="{if $q<0}info_warning_block{elseif $q==0}zero{/if}">{$q|fn_fvalue:1}</span>
                    </td>

                    <td class="b2_l">
                        {if strlen($m.material_comment_1)}{$m.material_comment_1}{else}&nbsp;{/if}
                    </td>
                </tr>
            {foreachelse}
                <tr class="no-items">
                    <td colspan="7"><p>{$lang.no_data}</p></td>
                </tr>
            {/foreach}
            </tbody>
    {/foreach}
{/strip}