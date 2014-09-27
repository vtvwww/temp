{script src="js/tabs.js"}
{capture name="mainbox"}
   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="10px">№</th>
               <th width="1px">&nbsp;</th>
               <th width="500px">Полное имя</th>
               <th width="30px">Аббревиатура</th>
               <th width="20px">Статус</th>
               <th width="10px">Поз.</th>
               <th>&nbsp;</th>
           </tr>
           {foreach from=$customers item="i"}
               <tr class="{if  $i.status == "Close"}CL{else}OP{/if}">
                   {assign var="id" value=$i.customer_id}
                   {assign var="value" value="customer_id"}
                   <td>{$id}</td>
                   <td>
                       {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                   </td>
                   <td> {*Полное имя*}
                       {$i.name}&nbsp;
                   </td>
                   <td> {*Аббревиатура*}
                       <b>{$i.name_short}</b>&nbsp;
                   </td>
                   <td>{*Статус*}
                       {$i.status}
                   </td>
                   <td>{*Позиция*}
                       {$i.position}
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
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add"    prefix="top" link_text="Добавить КЛИЕНТА"  hide_tools=true}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.uns_customers`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
