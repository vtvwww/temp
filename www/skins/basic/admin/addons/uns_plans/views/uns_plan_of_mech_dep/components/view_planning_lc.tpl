{strip}
    {assign var="id" value=$key}

    <tbody>
        <tr>
            <td colspan="16" class="dg l">
                {*<img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="skins/basic/admin/images/plus.gif">*}
                {*<img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="skins/basic/admin/images/minus.gif">*}
                {*&nbsp;*}<span class="category_name">{$item.group}</span>{if $item.group_comment} <span class="info_warning">({$item.group_comment})</span>{/if}</td>
        </tr>
        {foreach from=$item.items item=m key=k}
            {assign var="mark_prohibition"  value=""}
            {assign var="mark_priority"     value="p_n"} {*priority none*}
            {*Запрет*}
            {if $prohibition_of_casts[$m.id] == "Y"}
                {assign var="mark_prohibition"  value="prh"}
                {assign var="mark_priority"     value=""}

            {*Приоритет*}
            {elseif $priority_materials.R[$m.id] == "Y"}
                {assign var="mark_priority" value="p_r"}
            {elseif $priority_materials.R2[$m.id] == "Y"}
                {assign var="mark_priority" value="p_r2"}
            {elseif $priority_materials.Y[$m.id] == "Y"}
                {assign var="mark_priority" value="p_y"}
            {/if}

            <tr class="category_items {$id} {if $expand_all or ($mark_priority != "p_r" and $mark_priority != "p_r2" and $mark_priority != "p_y" )} hidden {/if} {if $mark_prohibition == "prh"}{$mark_prohibition}{/if} {$mark_priority}">
                {assign var="mark" value=""}
                {assign var="mark_star" value=""}
                {assign var="q" value=$remaining_of_casts.curr_month[$m.id]|fn_fvalue:0}
                {if $q>0}
                    {if (isset($requirement_of_casts_for_min_rest[$m.id]) and $requirement_of_casts_for_min_rest[$m.id] == "Y") and ((isset($priority_materials__details.R[$m.id]) and $priority_materials__details.R[$m.id] == "Y") or (isset($priority_materials__details.Y[$m.id]) and $priority_materials__details.Y[$m.id] == "Y"))}
                        {assign var="mark" value="<sup title='Минимальный остаток + На продажу'>МО+НП</sup>"}
                        {assign var="mark_star" value="<span class='info_warning'>*</span>"}
                    {elseif (isset($requirement_of_casts_for_min_rest[$m.id]) and $requirement_of_casts_for_min_rest[$m.id] == "Y")}
                        {assign var="mark" value="<sup title='Минимальный остаток'>МО</sup>"}
                        {assign var="mark_star" value="<span class='info_warning'>*</span>"}
                    {elseif ((isset($priority_materials__details.R[$m.id]) and $priority_materials__details.R[$m.id] == "Y") or (isset($priority_materials__details.Y[$m.id]) and $priority_materials__details.Y[$m.id] == "Y"))}
                        {assign var="mark" value="<sup title='На продажу'>НП</sup>"}
                        {assign var="mark_star" value="<span class='info_warning'>*</span>"}
                    {/if}
                {/if}

                {*НАИМЕНОВАНИЕ*}
                <td class="l">&nbsp;&nbsp;
                    {assign var="n" value=$m.name}
                    {if $m.no != ""}
                        {assign var="n" value="`$n` [`$m.no`]"}
                    {/if}
                    {assign var="href" value="uns_plan_of_mech_dep.planning.balance_of_details?m_id=`$m.id`"}
                    <a  rev="ci_{$m.id}" href="{$href|fn_url}" class="cm-dialog-opener cm-dialog-auto-size black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$n}{$mark}</a>
                    <div id="ci_{$m.id}" class="hidden" title="Остаток деталей {$n|upper}"></div>
                </td>

                {*ВЕС*}
                <td class="b1_l w">{$m.weight}</td>

                {*ПЛАН ПОТРЕБНОСТЬ*}
                {assign var="q" value=$requirement_of_casts.curr_month[$m.id]|fn_fvalue:1}
                <td class="b3_l {if $q < 0}info_warning_block{elseif $q==0}{/if}">{if !$q}{else}{$q}{/if}</td>
                {assign var="q" value=$requirement_of_casts.next_month[$m.id]|fn_fvalue:1}
                <td class="b1_l {if $q < 0}info_warning_block{elseif $q==0}{/if}">{if !$q}{else}{$q}{/if}</td>
                {assign var="q" value=$requirement_of_casts.next2_month[$m.id]|fn_fvalue:1}
                <td class="b1_l {if $q < 0}info_warning_block{elseif $q==0}{/if}">{if !$q}{else}{$q}{/if}</td>

                {*СКЛАДА ЛИТЬЯ*}
                {assign var="q" value=$m.nach|fn_fvalue:1}
                <td class="b3_l g {if $q < 0}info_warning_block{elseif $q==0}{/if}">{if !$q}{else}{$q}{/if}</td>
                {assign var="q" value=$m.current__in|fn_fvalue:1}
                <td class="b2_l g {if $q < 0}info_warning_block{elseif $q==0}{/if}">{if !$q}{else}{$q}{/if}</td>
                {assign var="q" value=$m.current__out|fn_fvalue:1}
                <td class="b1_l g {if $q < 0}info_warning_block{elseif $q==0}{/if}">{if !$q}{else}{$q}{/if}</td>
                {assign var="q" value=$m.konech|fn_fvalue:1}
                <td class="b2_l dg {if $q < 0}info_warning_block{elseif $q==0}{else}b{/if}">{if !$q}{else}{$q}{/if}</td>

                {*ОСТАЛОСЬ ОТЛИТЬ ПО ПЛАНУ ПОТРЕБНОСТИ В ЗАГОТОВКАХ*}
                {assign var="q" value=$remaining_of_casts.curr_month[$m.id]|fn_fvalue:0}
                <td class="b3_l r {if $q > 0}b{else}{/if}">{if !$q}{else}{$q}{/if}{$mark_star}</td>
                {assign var="q" value=$remaining_of_casts.next_month[$m.id]|fn_fvalue:0}
                <td class="b1_l r {if $q > 0}b{else}{/if}">{if !$q}{else}{$q}{/if}</td>
                {assign var="q" value=$remaining_of_casts.next2_month[$m.id]|fn_fvalue:0}
                <td class="b1_l r {if $q > 0}b{else}{/if}">{if !$q}{else}{$q}{/if}</td>

                {*ЗАПРЕТ*}
                <td class="b3_l {$mark_prohibition} {$mark_priority}">
                    &nbsp;
                </td>

                {*ПРИМЕНЯЕМОСТЬ В НАСОСАХ*}
                <td class="l {$mark_priority}">
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