{script src="js/tabs.js"}
{capture name="mainbox"}
   {include file="addons/uns_mech/views/uns_moving_mc_sk_su/components/info.tpl"}
   {include file="addons/uns_mech/views/uns_moving_mc_sk_su/components/search.tpl" dispatch="`$controller`.manage"}
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
                   <td align="center">
                       {if $i.package_type == "SL" and $i.package_id|is__more_0}
                           <a href="{"uns_sheets.update&sheet_id=`$i.package_id`"|fn_url}"><b>SL<br>{$i.package_id}</b></a>
                       {elseif $i.package_type == "PN" and $i.package_id|is__more_0}
                           <a href="{"uns_kits.update&kit_id=`$i.package_id`"|fn_url}"><b>PN<br>{$i.package_id}</b></a>
                       {else}
                           {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                       {/if}
                   </td>
                   <td>
                       {if $i.type == $smarty.const.DOC_TYPE__RO and $i.order_id|is__more_0}
                           <a class="black" href="{"uns_orders.update?order_id=`$i.order_id`"|fn_url}"><b title="{$document_types[$i.type].name} по заказу {$i.order_id}">Заказ {$i.order_id} - {$document_types[$i.type].name_short}</b></a>
                       {else}
                           <a class="black" href="{"`$controller`.update?`$value`=`$id`"|fn_url}"><b title="{$document_types[$i.type].name}">{$id} - {$document_types[$i.type].name_short}</b></a>
                           {if $i.type == $smarty.const.DOC_TYPE__VLC}
                               <br><span style="font-size: 12px; font-weight: bold;" class="date">Дата плавки: {$i.date_cast|date_format:"%a %d/%m/%Y"}</span>
                           {/if}
                       {/if}
                   </td>
                   <td>
                       {include file="common_templates/tooltip.tpl" tooltip=$i.comment}
                   </td>
                   <td>
                       {if $i.customer_id|is__more_0}
                           <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_to].path}&nbsp;</span>
                           <hr style="margin: 3px 0">
                           <span style="font-size: 11px; font-weight:bold;background-repeat: no-repeat; background-position: left center;padding-left: 20px;
                           {if $i.country_id == 1}
                               background-image: url('skins/basic/admin/addons/uns_orders/images/ua-16x16.png');
                           {elseif $i.country_id == 2}
                               background-image: url('skins/basic/admin/addons/uns_orders/images/ru-16x16.png');
                           {elseif $i.country_id == 3}
                               background-image: url('skins/basic/admin/addons/uns_orders/images/by-16x16.png');
                           {elseif $i.country_id == 4}
                               background-image: url('skins/basic/admin/addons/uns_orders/images/ml-16x16.png');
                           {/if}
                           ">{$customers[$i.customer_id].name}&nbsp;</span>
                       {else}
                           <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_from].path}&nbsp;</span>
                           <hr style="margin: 3px 0">
                           <span style="font-size: 11px; font-weight:bold;">{$objects_plain[$i.object_to].path}&nbsp;</span>
                       {/if}
                   </td>
                   <td>
                       {if ($i.package_type == "SL" and $i.package_id|is__more_0) or ($i.package_type == "PN" and $i.package_id|is__more_0)}
                           <b>{if $i.status == "A"}Учитывать{else}Не учитывать{/if}</b>
                       {else}
                           {include file="common_templates/select_popup.tpl" update_controller=$controller id=$id status=$i.status hidden=false active_name="Учитывать" disabled_name="Не учитывать"}
                       {/if}
                   </td>
                   <td>
                       <span class="date">{$i.date|date_format:"%a %d/%m/%Y"}</span>
                   </td>
                   <td align="center">
                       {$i.count|default:0}
                   </td>
                   <td class="nowrap right">
                       {capture name="tools_items"}
                           <li>{if  $i.package_type == "N"}<a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a>{/if}</li>
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
       {*{if $documents}*}
           {*<div class="buttons-container buttons-bg">*}
               {*<div class="float-left">*}
               {*{include file="buttons/button.tpl" but_name="dispatch[`$controller`.m_delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}*}
               {*</div>*}
           {*</div>*}
       {*{/if}*}

   </form>

   {capture name="tools"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add"                              prefix="top" link_text="Добавить документ" hide_tools=true tool_title="Добавить новый документ"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=8&object_to=17" prefix="top" link_text="АИО Скл. КМП"  hide_tools=true tool_title="Добавить Акт изменения остатка по Складу Комплектующих"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=8&object_to=14" prefix="top" link_text="АИО МЦ2"        hide_tools=true tool_title="Добавить Акт изменения остатка по МЦ2"}
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add&document_type=8&object_to=10" prefix="top" link_text="АИО МЦ1"        hide_tools=true tool_title="Добавить Акт изменения остатка по МЦ1"}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Движения по МЦ, Скл. КМП и СГП" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
