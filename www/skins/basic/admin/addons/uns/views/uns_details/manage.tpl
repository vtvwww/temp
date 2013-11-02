{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns/views/uns_details/components/search_form.tpl" dispatch="`$controller`.manage"}
    <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
        {include file="common_templates/pagination.tpl"}
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
            <tr>
                <th width="1%">
                    <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
                </th>
                <th width="1%">&nbsp;</th>
                <th width="20%">Наименование / Обозначение</th>
                <th width="35%">Учет</th>
                <th width="15%">Прим.</th>
                <th width="1%">Поз.</th>
                <th>&nbsp;</th>
            </tr>

            {foreach from=$details item=i}
                <tr {cycle values="class=\"table-row\", "}>
                    {assign var="id"    value=$i.detail_id}
                    {assign var="name"  value=$i.detail_name}
                    {assign var="value" value="detail_id"}
                    <td style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{/if}">
                        <input type="checkbox" name="detail_ids[]" value="{$id}" class="checkbox cm-item" />
                    </td>
                    <td>
                        {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                    </td>
                    <td>
                        <span class="uns_title_1">{$i.detail_name}</span>
                        {if $i.detail_no}
                            <br><span class="uns_info">{$i.detail_no}</span>
                        {else}
                            <br><span class="info_warning">Нет обозначения!</span>
                        {/if}
                        {*<br><span class="uns_info">{$i.category_path}</span>*}
                    </td>
                    <td>
                        {if is__array($i.accounting_data)}
                            {assign var="ad" value=$i.accounting_data}

                            {assign var="w" value=$ad.weight.M|fn_fvalue}
                            {if !$w}
                                {assign var="w" value="<span class='info_warning'>Ошибка веса детали!</span>"}
                            {/if}
                            {assign var="accounting" value="<b>`$ad.u_name` (`$w` кг)</b> "}

                            {if isset($ad.weight.A)}
                                {assign var="w_a" value=$ad.weight.A|fn_fvalue}
                                {if !$w_a}
                                    {assign var="w_a" value="<span class='info_warning'>Ошибка веса детали!</span>"}
                                {/if}
                                {assign var="accounting" value="`$accounting` / А = $w_a кг"}
                            {/if}

                            {if isset($ad.weight.B)}
                                {assign var="w_b" value=$ad.weight.B|fn_fvalue}
                                {if !$w_b}
                                    {assign var="w_b" value="<span class='info_warning'>Ошибка веса детали!</span>"}
                                {/if}
                                {assign var="accounting" value="`$accounting` / Б = $w_b кг"}
                            {/if}
                        {else}
                            {assign var="accounting" value="<span class='info_warning'>Нет данных!</span>"}
                        {/if}
                        {capture name="detail_expense"}
                            {if is__array($i.accounting_data.materials)}
                                {foreach from=$i.accounting_data.materials item="d"}
                                    {math   assign="total_weight"
                                            equation="(q + a) * w"
                                            q=$d.quantity
                                            a=$d.allowance
                                            w=$d.accounting_data.weight
                                    }
                                    {*{if $d.allowance>0}({$d.quantity|fn_fvalue} + {$d.allowance|fn_fvalue}){else}{$d.quantity|fn_fvalue}{/if} {$d.units[$d.u_id].u_name}&nbsp;*&nbsp;{$d.format_name}*}{*&nbsp;=&nbsp;{$total_weight|fn_fvalue}&nbsp;кг*}
                                    {$d.quantity|fn_fvalue}{if $d.add_quantity_state == "A"} x {$d.add_quantity|fn_fvalue}{/if} {$d.units[$d.u_id].u_name}&nbsp;*&nbsp;{$d.format_name}{*&nbsp;=&nbsp;{$total_weight|fn_fvalue}&nbsp;кг*}
                                {/foreach}
                            {/if}
                        {/capture}

                        {$accounting}
                        <br>
                        {if $smarty.capture.detail_expense|trim}{$smarty.capture.detail_expense}{else}{$smarty.const.UNS_NO_DATA_FORMAT}{/if}
                    </td>
                    <td>
                        {if $i.detail_comment}<span class="uns_comment">{$i.detail_comment}</span>{/if}
                    </td>
                    <td>
                        {$i.detail_position}
                    </td>

                    <td class="nowrap right">
                        {capture name="tools_items"}
                            <li><a class="" href="{"`$controller`.update?`$value`=`$id`&copy=Y"|fn_url}">{$lang.copy}</a></li>
                            <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a></li>
                        {/capture}
                        {include    file="common_templates/table_tools_list.tpl"
                                    id="`$controller``$id`"
                                    text="`$lang.uns_detail`: `$name`</b>"
                                    act="edit"
                                    prefix=$id
                                    tools_list=$smarty.capture.tools_items}
                    </td>
                </tr>
                {foreachelse}
                <tr class="no-items">
                    <td colspan="5"><p>{$lang.no_items}</p></td>
                </tr>
            {/foreach}
        </table>
        {include file="common_templates/pagination.tpl"}

        {if $details}
            <div class="buttons-container buttons-bg">
                <div class="float-left">
                {include file="buttons/button.tpl" but_name="dispatch[`$controller`.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
                </div>
            </div>
        {/if}

    </form>

    {capture name="tools"}
        {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text=$lang.uns_new_detail hide_tools=true}
    {/capture}
{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.uns_details content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
