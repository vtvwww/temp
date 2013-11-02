{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        {*{include file="addons/uns/views/components/search/s_materials.tpl" material_classes_as_input=true}*}
        {*{include file="addons/uns/views/components/search/s_mode_report.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_view_all_position.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_accessory_pumps.tpl"}*}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}


   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="1%">
                   <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
               </th>
               <th width="130px">Номер - Дата</th>
               <th width="10px">Статус</th>
               <th width="300px">Исходный материал</th>
               <th width="300px">Детали</th>
               <th>&nbsp;</th>
           </tr>
           {foreach from=$sheets item=i}
               <tr {cycle values="class=\"table-row\", "}>
                   {assign var="id" value=$i.sheet_id}
                   {assign var="value" value="sheet_id"}
                   <td style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{/if}">
                       <input type="checkbox" name="document_ids[]" value="{$id}" class="checkbox cm-item" />
                   </td>
                   <td> {*Идентификатор листа*}
                        <b><a style="color: #000000;" href="{"`$controller`.update?`$value`=`$id`"|fn_url}">{$i.no} - {$i.date_open|date_format:"%d/%m/%y"}</a></b>
                   </td>
                   <td> {*Статус*}
                       {if $i.status == "OP"}
                           <img border="0" title="Открыт" src="/skins/basic/admin/addons/uns_acc/images/circle_yellow.png">
                       {elseif $i.status == "CL"}
                           <img border="0" title="Закрыт" src="/skins/basic/admin/addons/uns_acc/images/done.png">
                       {elseif $i.status == "CN"}
                           <img border="0" title="Отменен" src="/skins/basic/admin/addons/uns_acc/images/circle_red.png">
                       {/if}
                   </td>
                   <td> {*Исходный материал*}
                        {if strlen($i.material_no)}[{$i.material_no}] {/if}{$i.material_name}
                   </td>
                   <td> {*Детали*}
                       {if is__array($i.details)}
                           {foreach from=$i.details item="d" name="d"}
                               {$smarty.foreach.d.iteration}. {$d.detail_name} [{$d.detail_no}]{* - {$d.quantity} шт.*}{if !$smarty.foreach.d.last}<br>{/if}
                           {/foreach}
                       {else}
                           <span class="info_warning">Ошибка!</span>
                       {/if}
                   </td>
                   <td class="nowrap right">
                       {capture name="tools_items"}
                           <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}" title="Удалить сопроводительный лист: {$i.no} - {$i.date_open|date_format:"%d/%m/%y"}">
                                   <img border="0" src="/skins/basic/admin/addons/uns_acc/images/delete.png">
                               </a>
                           </li>
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
       {if $documents}
           <div class="buttons-container buttons-bg">
               <div class="float-left">
               {include file="buttons/button.tpl" but_name="dispatch[`$controller`.m_delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
               </div>
           </div>
       {/if}

   </form>

   {capture name="tools"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text="Добавить новый Сопроводительный лист"  hide_tools=true}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Журнал сопроводительных листов" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
