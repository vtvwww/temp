{strip}
    {assign var="id" value=$key}

    <tbody>
        <tr  m_id={$m.id}>
            <td colspan="16" style="background-color: #d3d3d3;">
                <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">
                <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">
                &nbsp;<span style="color: #000000; font-weight: bold; font-size: 14px;">{$item.group}</span>{if $item.group_comment} <span class="info_warning">({$item.group_comment})</span>{/if}</td>
        </tr>
        {foreach from=$item.items item=m key=k}
            <tr class="category_items {$id} {if $expand_all} hidden {/if}" m_id={$m.id}>
                {*Наименование*}
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {assign var="n" value=$m.name}
                    {if $m.no != ""}
                        {assign var="n" value="`$n` [`$m.no`]"}
                    {/if}
                    {assign var="href" value="foundry_get_balance.motion?item_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`&nach=`$m.nach`&current__in=`$m.current__in`&current__out=`$m.current__out`&konech=`$m.konech`"}
                    <a  rev="content_item_{$m.id}" id="opener_item_{$m_id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n}</a>
                    <div id="content_item_{$m.id}" class="hidden" title="Движение {$n|upper} по Складу литья"></div>
                </td>

                {*Запрет*}
                <td>
                    {if $prohibition_of_casts[$m.id] == "Y"}<img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/>{/if}
                </td>


                {*Вес*}
                <td align="center" class="b1_r b1_l"><span style="font-size: 11px; color: #808080;">{$m.weight}</span></td>

                {*ПЛАН НЕПОТРЕБНОСТЬ*}
                {*{assign var="q" value=$unrequirement_of_casts.curr_month[$m.id]|fn_fvalue:1}*}
                {*<td align="center" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>*}
                {*{assign var="q" value=$unrequirement_of_casts.next_month[$m.id]|fn_fvalue:1}*}
                {*<td align="center" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>*}

                {*ПЛАН ПОТРЕБНОСТЬ*}
                {assign var="q" value=$requirement_of_casts.curr_month[$m.id]|fn_fvalue:1}
                <td align="center" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$requirement_of_casts.next_month[$m.id]|fn_fvalue:1}
                <td align="center" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$requirement_of_casts.next2_month[$m.id]|fn_fvalue:1}
                <td align="center" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$requirement_of_casts.next3_month[$m.id]|fn_fvalue:1}
                <td align="center" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>

                {*СКЛАДА ЛИТЬЯ*}
                {assign var="q" value=$m.nach|fn_fvalue:1}
                <td align="center" style="background-color: #f1f1f1;" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$m.current__in|fn_fvalue:1}
                <td align="center" style="background-color: #f1f1f1;" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$m.current__out|fn_fvalue:1}
                <td align="center" style="background-color: #f1f1f1;" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$m.konech|fn_fvalue:1}
                <td align="center" style="background-color: #D3D3D3;" class="b1_r b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{else}bold{/if}">{$q}</td>

                {*ОСТАЛОСЬ отлить по плану потребности в заготовках*}
                {assign var="q" value=$remaining_of_casts.curr_month[$m.id]|fn_fvalue:1}
                <td align="center" style="background-color: #B8C1FF;" class="b1_r b1_l {if $q > 0}bold{else}zero{/if}">{$q}</td>
                {assign var="q" value=$remaining_of_casts.next_month[$m.id]|fn_fvalue:1}
                <td align="center" style="background-color: #B8C1FF;" class="b1_r b1_l {if $q > 0}bold{else}zero{/if}">{$q}</td>
                {assign var="q" value=$remaining_of_casts.next2_month[$m.id]|fn_fvalue:1}
                <td align="center" style="background-color: #B8C1FF;" class="b1_r b1_l {if $q > 0}bold{else}zero{/if}">{$q}</td>
                {assign var="q" value=$remaining_of_casts.next3_month[$m.id]|fn_fvalue:1}
                <td align="center" style="background-color: #B8C1FF;" class="b1_r b1_l {if $q > 0}bold{else}zero{/if}">{$q}</td>

                {*Принадлежность к насосам*}
                <td align="left" class="b_l">
                    {if $m.material_comment_1|strlen}
                        <span class="info_warning">{$m.material_comment_1}</span>
                    {else}
                        {if $m.accessory_view == "M"}
                            {$m.accessory_pump_manual} <span class="info_warning">({$m.accessory_view})</span>
                        {elseif $m.accessory_view == "P"}
                            {$m.accessory_pumps} <span class="info_warning">({$m.accessory_view})</span>
                        {else}
                            {$m.accessory_pump_series}
                        {/if}
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr class="no-items">
                <td colspan="8"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}
    </tbody>
{/strip}