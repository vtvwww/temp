{script src="js/tabs.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="uns_units_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
        <tr>
            <th width="5%">
                <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
            </th>
            <th width="20%">Категория</th>
            <th width="20%">Имя</th>
            <th width="20%">Тип</th>
            <th width="20%">Коэф.</th>
            <th width="20%">Статус</th>
            <th width="20%">Позиция</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$uns_units item=uns_unit}
            <tr {cycle values="class=\"table-row\", "}>
                <td width="1%">
                    <input type="checkbox" name="unit_ids[]" value="{$uns_unit.u_id}" class="checkbox cm-item" />
                </td>
                <td>{$uns_unit.uc_name}</td>
                <td>{$uns_unit.u_name}</td>
                <td>{if $uns_unit.u_type == "M"}Основная{else}Дополнительная{/if}</td>
                <td>{$uns_unit.u_coefficient}</td>
                <td>{$uns_unit.u_status}</td>
                <td>{$uns_unit.u_position}</td>

                <td class="nowrap right">
                    {capture name="tools_items"}
                        <li><a class="cm-confirm" href="{"uns_units.delete?u_id=`$uns_unit.u_id`"|fn_url}">{$lang.delete}</a></li>
                    {/capture}
                    {include    file="common_templates/table_tools_list.tpl"
                                popup=true
                                id="uns_unit`$uns_unit.u_id`"
                                text="`$uns_unit.uc_name`: <b>`$uns_unit.u_name`</b>"
                                act="edit"
                                href="uns_units.update?u_id=`$uns_unit.u_id`"
                                prefix=$uns_unit.u_id
                                tools_list=$smarty.capture.tools_items}
                </td>
            </tr>
            {foreachelse}
            <tr class="no-items">
                <td colspan="5"><p>{$lang.no_items}</p></td>
            </tr>
        {/foreach}
    </table>

    {if $uns_units}
        <div class="buttons-container buttons-bg">
            <div class="float-left">
            {include file="buttons/button.tpl" but_name="dispatch[uns_units.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
            </div>
        </div>
    {/if}

</form>

    {capture name="tools"}
        {capture name="add_new_picker"}
            {include file="addons/uns/views/uns_units/update.tpl" uns_unit=null }
        {/capture}
        {include    file="common_templates/popupbox.tpl"
                    id="add_new_uns_units"
                    text=$lang.uns_new_unit
                    content=$smarty.capture.add_new_picker
                    link_text=$lang.uns_add_unit
                    act="general"}
    {/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.uns_units content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}