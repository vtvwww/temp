{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns_orders/views/uns_orders/components/search_form.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}
    <table>
        <tbody>
            <tr>
                <td class=""><img border="0" title="Открыт" src="skins/basic/admin/addons/uns_acc/images/circle_green.png"></td>
                <td class="" width="230" align="left"> - <b>заказ открыт</b>, предварительный</td>

                <td class="b1_l"><img border="0" title="Открыт" width="24" height="24" src="skins/basic/admin/addons/uns_orders/images/paid_32x32.png"></td>
                <td class="" width="300" align="left"> - <b>заказ оплачен</b>, постановка в производство</td>

                <td class="b1_l"><img border="0" title="Открыт" width="24" height="24" src="skins/basic/admin/addons/uns_orders/images/shipped_32x32.png"></td>
                <td class="" width="300" align="left"> - <b>заказ отгружен</b>, закрыт</td>
            </tr>
        </tbody>
    </table>


   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="30px">№</th>
               <th width="1px" class="b1_l center">&nbsp;</th>
               <th width="10px" class="center">Статус</th>
               <th width="20px" class="b1_l center">Отгрузка</th>
               <th width="10px" class="b1_l center">&nbsp;</th>
               <th width="450px" class="center">Клиент (Регион)</th>
               {*<th width="10px">Позиций</th>*}
               <th width="10px" class="b1_l center" style="text-transform: none;">Кол-во</th>
               <th width="10px" class="b1_l center" style="text-transform: none;">Вес, кг</th>
               <th class="b1_l">&nbsp;</th>
           </tr>
           {foreach from=$orders item="i" name="o"}
               <tr class="{if  $i.status == "Close" or $i.status == "Shipped"}CL{else}OP{/if}">
                   {assign var="id" value=$i.order_id}
                   {assign var="value" value="order_id"}
                   <td {if $id == "`$smarty.session.mark_item.$controller`"} class="mark_item" {else} class="mark_item_clear" {/if} align="right" >
                   {*<td align="left">*}
                       {*{math equation="a-b" a=$orders|count b=$smarty.foreach.o.index}*}
                       {$id}
                   </td>
                   <td class="b1_l">
                       {include file="addons/uns/views/components/tools.tpl" type="edit" name=$id href="`$controller`.update?`$value`=`$id`"}
                   </td>
                   <td align="center"> {* Статус *}
                       {if      $i.status == "Hide"}
                           <img class="hand" border="0" title="Скрыт - предварительный заказ" src="skins/basic/admin/addons/uns_acc/images/circle_yellow.png">
                       {elseif  $i.status == "Open"}
                           <img class="hand" border="0" title="Открыт" src="skins/basic/admin/addons/uns_acc/images/circle_green.png">
                       {elseif  $i.status == "Close"}
                           <img class="hand" border="0" title="Выполнен - заказ отгружен" src="skins/basic/admin/addons/uns_acc/images/done.png">
                       {elseif  $i.status == "Paid"}
                           <img class="hand" border="0" title="Оплачен" src="skins/basic/admin/addons/uns_orders/images/paid_32x32.png">
                       {elseif  $i.status == "Shipped"}
                           <img class="hand" border="0" title="Отгружен" src="skins/basic/admin/addons/uns_orders/images/shipped_32x32.png">
                       {/if}
                   </td>
                   <td class="b1_l"> {*ДАТА ОТГРУЗКИ*}
                       {$i.date_finished|date_format:"%a %d/%m/%y"}
                   </td>
                   <td class="b1_l"> {*КЛИЕНТ*}
                       {if $customers[$i.customer_id].country_id == 1}
                           <img border="0" src="skins/basic/admin/addons/uns_orders/images/ua-24x24.png">
                       {elseif $customers[$i.customer_id].country_id == 2}
                           <img border="0" src="skins/basic/admin/addons/uns_orders/images/ru-24x24.png">
                       {elseif $customers[$i.customer_id].country_id == 3}
                           <img border="0" src="skins/basic/admin/addons/uns_orders/images/by-24x24.png">
                       {elseif $customers[$i.customer_id].country_id == 4}
                           <img border="0" src="skins/basic/admin/addons/uns_orders/images/ml-24x24.png">
                       {/if}
                   </td>
                   <td class=""> {*КЛИЕНТ*}
                       {if      $i.status == "Hide"}
                           <i>{$customers[$i.customer_id].name}</i>
                       {elseif  $i.status == "Open" or $i.status == "Paid"}
                           <b>{$customers[$i.customer_id].name}</b>
                       {elseif  $i.status == "Close" or $i.status == "Shipped"}
                           <b>{$customers[$i.customer_id].name}</b>
                       {/if}
                       <br><span style="font-size: 11px;">({$countries[$i.country_id].name} : {$regions[$i.region_id].name} : г. {$cities[$i.city_id].name})</span>
                   </td>
                   {*<td> *}{*ПОЗИЦИЙ*}
                       {*{$i.count}*}
                   {*</td>*}
                   <td class="b1_l center">
                       {$i.total_quantity}
                   </td>
                   <td class="b1_l right">
                       <nobr>{$i.total_weight|number_format:1:".":" "}</nobr>
                   </td>
                   <td class="nowrap right b1_l">
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
       {include file="common_templates/tools.tpl" tool_href="uns_customers.add"    prefix="top" link_text="Добавить КЛИЕНТА"  hide_tools=true}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Заказы" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
