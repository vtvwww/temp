{script src="js/tabs.js"}
{literal}
    <style type="text/css">
        a.anchor{
            padding-top: 20px;
            position: relative;
        }
    </style>
{/literal}
{capture name="mainbox"}
    {include file="addons/uns/views/uns_materials/components/search_form.tpl" dispatch="`$controller`.manage"}
    <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
        {include file="common_templates/pagination.tpl"}
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
            <tr>
                <th width="5%">
                    <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
                </th>
                <th width="1%">&nbsp;</th>
                <th width="40%">Наименование/Обозначение</th>
                <th width="20%">Учет</th>
                <th width="15%">Класс</th>
                <th width="10%">Прим.</th>
                <th>&nbsp;</th>
            </tr>

            {if is__array($materials)}
            {foreach from=$materials item=i}
                <tr {cycle values="class=\"table-row\", "}>
                    {assign var="id"    value=$i.material_id}
                    {assign var="name"  value=$i.material_name}
                    {assign var="value" value="material_id"}

                    <td style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{/if}">
                        <input type="checkbox" name="material_ids[]" value="{$id}" class="checkbox cm-item" />
                        <a class="anchor" name="{$id}"></a>
                    </td>

                    <td>
                        {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                    </td>

                    {if is__array($i.accounting_data)}
                        {assign var="ad" value=$i.accounting_data}
                        {assign var="w" value=$ad.weight|fn_fvalue}
                        {if !$w}
                            {assign var="w" value="<span class='info_warning'>Ошибка веса детали!</span>"}
                        {/if}
                        {assign var="accounting" value="<b>`$ad.u_name` (`$w` кг)</b> "}
                    {else}
                        {assign var="accounting" value="<span class='info_warning'>Нет данных!</span>"}
                    {/if}

                    <td>
                        <span class="uns_title_1{if $i.material_status == "D"} item_disabled{/if}">{$i.material_name}&nbsp;{if strlen($i.material_no)}[{$i.material_no}]{/if}</span>
                        <br>{if strlen($i.options_as_str)}<span style="cursor: pointer;" title="{$i.options_as_str_names}" class="info_warning">{$i.options_as_str}</span>{/if}&nbsp;<span class="uns_info">{$i.category_path}</span>
                    </td>
                    <td>
                        {$accounting}
                    </td>
                    <td>
                        {strip}
                            {$i.mclass_name} {if $i.mclass_id==1}<br>
                                (<b>{if $i.type_casting=="C"}Чугун
                                {elseif $i.type_casting=="S"}Сталь
                                {elseif $i.type_casting=="A"}Алюминий
                                {elseif $i.type_casting=="W"}Чугун белый
                                {/if}</b>)
                            {/if}
                        {/strip}
                    </td>
                    <td>
                        {if $i.material_comment}<span class="uns_comment">{$i.material_comment}</span>{/if}
                    </td>

                    <td class="nowrap right">
                        {capture name="tools_items"}
                            <li><a class="" href="{"`$controller`.update?`$value`=`$id`&copy=Y"|fn_url}">{$lang.copy}</a></li>
                            <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a></li>
                        {/capture}
                        {include    file="common_templates/table_tools_list.tpl"
                                    id="`$controller``$id`"
                                    text="`$lang.uns_material`: `$name`</b>"
                                    act="edit"
                                    prefix=$id
                                    tools_list=$smarty.capture.tools_items}
                    </td>
                </tr>
            {/foreach}
            {else}
                <tr class="no-items">
                    <td colspan="5"><p>{$lang.no_items}</p></td>
                </tr>
            {/if}
        </table>
        {include file="common_templates/pagination.tpl"}

        {if $materials}
            <div class="buttons-container buttons-bg">
                <div class="float-left">
                {include file="buttons/button.tpl" but_name="dispatch[`$controller`.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
                </div>
            </div>
        {/if}
    </form>

    {capture name="tools"}
        {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text=$lang.uns_new_material hide_tools=true}
    {/capture}
{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.uns_materials content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}