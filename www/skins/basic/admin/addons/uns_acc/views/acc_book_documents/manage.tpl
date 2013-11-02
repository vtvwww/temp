{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns/views/uns_pumps/components/search_form.tpl" dispatch="`$controller`.manage"}
   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="1%">
                   <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
               </th>
               <th width="1%">&nbsp;</th>
               <th width="300px">Тип</th>
               <th width="500px">Откуда/Куда</th>
               <th width="100px">Статус</th>
               <th width="70px">Дата</th>
               <th width="10px">Кол-во</th>
               <th width="10px">Созд./Обн.</th>
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
                       {if $i.document_type == $smarty.const.UNS_DOCUMENT__PRIH_ORD}
                           <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__PRIH_ORD_NAME}
                       {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__SDAT_N}
                           <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__SDAT_N_NAME}
                       {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__NOPM}
                           <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__NOPM_NAME}
                       {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__INPM}
                           <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__INPM_NAME}
                       {else}
                            Неизвестный тип Документа!
                       {/if}
                   </td>
                   <td>
                       <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_id_from].path}</span>&nbsp;
                       <hr style="margin: 3px 0">
                       <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_id_to].path}</span>
                   </td>
                   <td>
                       {assign var="document_status" value="D"}
                       {if $i.document_status == "Y"}
                           {assign var="document_status" value="A"}
                       {/if}
                       {include file="common_templates/select_popup.tpl" update_controller=$controller id=$id status=$document_status}
                   </td>
                   <td>
                       {$i.document_date|fn_parse_date|date_format:"%d/%m/%Y"}
                   </td>
                   <td align="center">
                       {$i.count|default:0}
                   </td>
                   <td>
                       <span style="font-size: 10px;">
                           {$i.who_created|fn_uns__get_user_name}&nbsp;{$i.date_of_create|fn_parse_date|date_format:"%d/%m/%Y"}
                           <br>
                           {$i.who_updated|fn_uns__get_user_name}&nbsp;{$i.date_of_update|fn_parse_date|date_format:"%d/%m/%Y"}
                       </span>
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
               {include file="buttons/button.tpl" but_name="dispatch[`$controller`.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
               </div>
           </div>
       {/if}

   </form>

   {capture name="tools"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=`$smarty.const.UNS_DOCUMENT__PRIH_ORD`" prefix="top" link_text=$smarty.const.UNS_DOCUMENT__PRIH_ORD_NAME  hide_tools=true}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=`$smarty.const.UNS_DOCUMENT__INPM`" prefix="top" link_text=$smarty.const.UNS_DOCUMENT__INPM_NAME  hide_tools=true}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=`$smarty.const.UNS_DOCUMENT__SDAT_N`" prefix="top" link_text=$smarty.const.UNS_DOCUMENT__SDAT_N_NAME  hide_tools=true}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=`$smarty.const.UNS_DOCUMENT__NOPM`" prefix="top" link_text=$smarty.const.UNS_DOCUMENT__NOPM_NAME  hide_tools=true}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=`$smarty.const.UNS_DOCUMENT__RASH_ORD`" prefix="top" link_text=$smarty.const.UNS_DOCUMENT__RASH_ORD_NAME  hide_tools=true}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Журнал документов" content=$smarty.capture.mainbox tools=$smarty.capture.tools}