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
                {* Минимально необходимый остаток*}

                {*НАИМЕНОВАНИЕ*}
                <td>&nbsp;&nbsp;
                    {assign var="n" value=$m.name}
                    {if $m.no != ""}
                        {assign var="n" value="`$n` [`$m.no`]"}
                    {/if}
                    {assign var="href" value="uns_plan_of_mech_dep.planning.balance_of_details?material_id=`$m.id`&period=`$search.period`&time_from=`$search.time_from`&time_to=`$search.time_to`"}
                    <a  rev="content_item_{$m.id}" id="opener_item_{$m.id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n}</a>
                    <div id="content_item_{$m.id}" class="hidden" title="Остаток деталей по цехам и складу КМП по заготовке {$n|upper}"></div>
                </td>

                {*ВЕС*}
                <td class="center b1_l w">{$m.weight}</td>

                {*ПЛАН ПОТРЕБНОСТЬ*}
                {assign var="q" value=$requirement_of_casts.curr_month[$m.id]|fn_fvalue:1}
                <td class="center b3_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}{if $q>0 and $requirement_of_casts_for_min_rest[$m.id] == "Y"}<span class="info_warning">*</span>{/if}</td>
                {assign var="q" value=$requirement_of_casts.next_month[$m.id]|fn_fvalue:1}
                <td class="center b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$requirement_of_casts.next2_month[$m.id]|fn_fvalue:1}
                <td class="center b1_l {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>

                {*СКЛАДА ЛИТЬЯ*}
                {assign var="q" value=$m.nach|fn_fvalue:1}
                <td class="center b3_l g {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$m.current__in|fn_fvalue:1}
                <td class="center b2_l g {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$m.current__out|fn_fvalue:1}
                <td class="center b1_l g {if $q < 0}info_warning_block{elseif $q==0}zero{/if}">{$q}</td>
                {assign var="q" value=$m.konech|fn_fvalue:1}
                <td class="center b2_l dg {if $q < 0}info_warning_block{elseif $q==0}zero{else}bold{/if}">{$q}</td>

                {*ОСТАЛОСЬ ОТЛИТЬ ПО ПЛАНУ ПОТРЕБНОСТИ В ЗАГОТОВКАХ*}
                {assign var="q" value=$remaining_of_casts.curr_month[$m.id]|fn_fvalue:0}
                <td class="center b3_l r {if $q > 0}bold{else}zero{/if}">{$q}{if $q>0 and $requirement_of_casts_for_min_rest[$m.id] == "Y"}<span class="info_warning">*</span>{/if}</td>
                {assign var="q" value=$remaining_of_casts.next_month[$m.id]|fn_fvalue:0}
                <td class="center b1_l r {if $q > 0}bold{else}zero{/if}">{$q}</td>
                {assign var="q" value=$remaining_of_casts.next2_month[$m.id]|fn_fvalue:0}
                <td class="center b1_l r {if $q > 0}bold{else}zero{/if}">{$q}</td>

                {*ЗАПРЕТ*}
                <td class="b3_l {if $prohibition_of_casts[$m.id] == "Y"}prh{/if}">
                </td>

                {*ПРИНАДЛЕЖНОСТЬ К НАСОСАМ*}
                <td>
                    {if $m.material_comment_1|strlen}
                        <span class="info_warning">{$m.material_comment_1}</span>
                    {else}
                        {if $m.accessory_view == "M"}
                            {$m.accessory_pump_manual} <span class="info_warning">({$m.accessory_view})</span>
                        {elseif $m.accessory_view == "P"}
                            {$m.accessory_pumps} <span class="info_warning">({$m.accessory_view})</span>
                        {else}
                            {$m.accessory_pump_series}&nbsp;
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