{script src="js/tabs.js"}
{capture name="mainbox"}
   {include file="addons/uns_acc/views/acc_documents/components/info.tpl"}
   {include file="addons/uns_acc/views/acc_documents/components/search.tpl" dispatch="`$controller`.manage"}
   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="1%">
                   <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
               </th>
               <th width="1%">&nbsp;</th>
               <th width="300px">Тип</th>
               <th width="1px">?</th>
               <th width="500px">Откуда/Куда</th>
               <th width="100px">Статус</th>
               <th width="120px">Дата</th>
               <th width="10px">Позиций</th>
               <th>&nbsp;</th>
           </tr>
           {foreach from=$documents item=i}
               <tr {cycle values="class=\"table-row\", "}>
                   {assign var="id" value=$i.document_id}
                   {assign var="value" value="document_id"}
                   {assign var="name" value=$i.document_type}
                   <td style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{/if}">
                       <input type="checkbox" name="document_ids[]" value="{$id}" class="checkbox cm-item" />
                   </td>
                   <td>
                       {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                   </td>
                   <td>
                       <b>№{$id}</b> - {$document_types[$i.type].name}
                       {if $i.type == $smarty.const.DOC_TYPE__VLC}
                           <br><span style="font-size: 12px; font-weight: bold;" class="date">Дата плавки: {$i.date_cast|date_format:"%a %d/%m/%Y"}</span>
                       {/if}
                   </td>
                   <td>
                       {include file="common_templates/tooltip.tpl" tooltip=$i.comment}
                   </td>
                   <td>
                       <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_from].path}&nbsp;</span>&nbsp;
                       <hr style="margin: 3px 0">
                       <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_to].path}&nbsp;</span>
                   </td>
                   <td>
                       {include file="common_templates/select_popup.tpl" update_controller=$controller id=$id status=$i.status hidden=false active_name="Учитывать" disabled_name="Не учитывать"}
                   </td>
                   <td>
                       <span class="date">{$i.date|date_format:"%a %d/%m/%Y"}</span>
                   </td>
                   <td align="center">
                       {$i.count|default:0}
                   </td>
                   <td class="nowrap right">
                       {capture name="tools_items"}
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
       {if $documents}
           <div class="buttons-container buttons-bg">
               <div class="float-left">
               {include file="buttons/button.tpl" but_name="dispatch[`$controller`.m_delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
               </div>
           </div>
       {/if}

   </form>

   {capture name="tools"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text="Добавить документ"  hide_tools=true}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Журнал документов" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
