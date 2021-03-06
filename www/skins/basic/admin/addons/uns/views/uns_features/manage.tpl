{script src="js/tabs.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
    {include file="common_templates/pagination.tpl"}
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
        <tr>
            <th width="5%">
                <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
            </th>
            <th width="20%">Наименование</th>
            <th width="20%">Символ</th>
            <th width="20%">Статус</th>
            <th width="20%">Позиция</th>
            <th width="20%">Группа единиц измерения</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$features item=i}
            <tr {cycle values="class=\"table-row\", "}>
                {assign var="id" value=$i.feature_id}
                {assign var="value" value="feature_id"}
                {assign var="name" value=$i.feature_name}

                <td width="1%">
                    <input type="checkbox" name="feature_ids[]" value="{$i.feature_id}" class="checkbox cm-item" />
                </td>
                <td>{$i.feature_name}</td>
                <td>{$i.feature_no}</td>
                <td>{$i.feature_status}</td>
                <td>{$i.feature_position}</td>
                <td>{$i.uc_name}</td>

                <td class="nowrap right">
                    {capture name="tools_items"}
                        <li><a class="" href="{"`$controller`.update?`$value`=`$id`&copy=Y"|fn_url}">{$lang.copy}</a></li>
                        <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a></li>
                    {/capture}
                    {include    file="common_templates/table_tools_list.tpl"
                                id="`$controller``$id`"
                                text="`$lang.uns_feature`: `$name`</b>"
                                act="edit"
                                href="`$controller`.update?`$value`=`$id`"
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

    {if $features}
        <div class="buttons-container buttons-bg">
            <div class="float-left">
            {include file="buttons/button.tpl" but_name="dispatch[`$controller`.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
            </div>
        </div>
    {/if}

</form>

{capture name="tools"}
    {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text=$lang.uns_new_feature hide_tools=true}
{/capture}

{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.uns_features content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
