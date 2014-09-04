{script src="js/tabs.js"}
{capture name="mainbox"}
{include file="addons/uns/views/uns_pumps/components/search_form.tpl" dispatch="`$controller`.manage"}
<form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
    {include file="common_templates/pagination.tpl"}
    <table cellpadding="0" cellspacing="0" border="0" class="table">
        <tr>
            <th width="5px">
                <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
            </th>
            <th width="1px">&nbsp;</th>
            <th width="1px">&nbsp;</th>
            <th width="500px">{$lang.uns_pumps}</th>
            {*<th width="5%">Н/Р/Д</th>*}
            <th width="10px">Ст.</th>
            <th width="60px">Поз.</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$pumps item=i}
            <tr {cycle values="class=\"table-row\", "}>
                {assign var="id" value=$i.p_id}
                {assign var="value" value="p_id"}
                {assign var="name" value=$i.p_name}
                <td style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{/if}">
                    <input type="checkbox" name="p_ids[]" value="{$id}" class="checkbox cm-item" />
                </td>
                <td>
                    {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                </td>
                <td>
                    {include file="addons/uns/views/components/tools.tpl" target="_blank" type="view_list" href="`$controller`.packing_list_view?`$value`=`$id`"}
                </td>
                <td>
                    <b>{$i.p_name}</b>
                    <br><span class="uns_info">{$lang.uns_pump_series}: <b>{$i.ps_name}</b>&nbsp;({$i.pt_name})</span>
                </td>
{*                <td>
                    <a href="{"`$controller`.update?`$value`=`$id`&selected_section=packing_list"|fn_url}">
                    {strip}
                        {if is__more_0($i.number_of_parts.pump.q)}
                            {$i.number_of_parts.pump.q}
                        {else}
                            0
                        {/if}
                        &nbsp;/&nbsp;
                        {if is__more_0($i.number_of_parts.frame.q)}
                            {$i.number_of_parts.frame.q}
                        {else}
                            0
                        {/if}
                        &nbsp;/&nbsp;
                        {if is__more_0($i.number_of_parts.motor.q)}
                            {$i.number_of_parts.motor.q}
                        {else}
                            0
                        {/if}
                    {/strip}
                    </a>
                </td>*}
                <td>{$i.p_status}</td>
                <td>{$i.p_position}</td>
                <td class="nowrap right">
                    {capture name="tools_items"}
                        <li><a class="" href="{"`$controller`.update?`$value`=`$id`&copy=Y"|fn_url}">{$lang.copy}</a></li>
                        <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a></li>
                    {/capture}
                    {include    file="common_templates/table_tools_list.tpl"
                                id="`$controller``$id`"
                                text="`$lang.uns_pumps`: `$name`</b>"
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

    {if $pumps}
        <div class="buttons-container buttons-bg">
            <div class="float-left">
            {include file="buttons/button.tpl" but_name="dispatch[`$controller`.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
            </div>
        </div>
    {/if}

</form>

{capture name="tools"}
    {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text=$lang.uns_new_pump hide_tools=true}
{/capture}

{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.uns_pumps content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}