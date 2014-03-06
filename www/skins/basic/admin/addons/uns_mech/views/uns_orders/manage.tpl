{script src="js/tabs.js"}
{capture name="mainbox"}
    {*{capture name="search_content"}*}
        {*{include file="addons/uns/views/components/search/s_time.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_materials.tpl" material_classes_as_input=true}*}
        {*{include file="addons/uns/views/components/search/s_mode_report.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_view_all_position.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_accessory_pumps.tpl"}*}
    {*{/capture}*}
    {*{include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}*}

   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="10px">№</th>
               <th width="1px">&nbsp;</th>
               <th width="10px">Статус</th>
               <th width="300px">Регион</th>
               <th width="10px">Дата отгрузки</th>
               <th width="10px">Позиций</th>
               <th>&nbsp;</th>
           </tr>
           {foreach from=$orders item=i}
               <tr class="{if  $i.status == "Close"}CL{else}OP{/if}">
                   {assign var="id" value=$i.order_id}
                   {assign var="value" value="order_id"}
                   <td> {*№ партии*}
                       {$id}
                   </td>
                   <td>
                       {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                   </td>
                   <td> {* Статус *}
                       {if      $i.status == "Open"}
                           <img class="hand" border="0" title="Комплектуется" src="skins/basic/admin/addons/uns_acc/images/circle_yellow.png">
                       {elseif  $i.status == "Close"}
                           <img class="hand" border="0" title="Закрыт" src="skins/basic/admin/addons/uns_acc/images/done.png">
                       {/if}
                   </td>
                   <td> {*РЕГИОН*}
                       {if      $i.status == "Open"}
                           <b>{$regions[$i.region_id].name}</b>
                       {elseif  $i.status == "Close"}
                           {$regions[$i.region_id].name}
                       {/if}
                   </td>
                   <td> {*ДАТА ОТГРУЗКИ*}
                       {$i.date_finished|date_format:"%a %d/%m/%y"}
                   </td>
                   <td> {*ПОЗИЦИЙ*}
                       {$i.count}
                   </td>
                   <td class="nowrap right">
                       {capture name="tools_items"}
                           <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">
                                   <img border="0" src="skins/basic/admin/addons/uns_acc/images/delete.png">
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
                   <td colspan="70"><p>{$lang.no_items}</p></td>
               </tr>
           {/foreach}
       </table>
       {include file="common_templates/pagination.tpl"}
   </form>

   {capture name="tools"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add"    prefix="top" link_text="Добавить ЗАКАЗ"  hide_tools=true}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Заказы" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
