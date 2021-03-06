{script src="js/tabs.js"}
{literal}
    <style type="text/css">
        span.material_type {
            border-radius: 4px;
            display: block;
            font-weight: bold;
            padding: 0;
            width: 15px;
        }
        span.O{
            background-color: #C4BC96;
            border: 2px solid #938B63;
        }
        span.M{
            background-color: #CCC0D9;
            border: 2px solid #B89AD9;
        }

        span.target_object {
            border-radius: 4px;
            display: block;
            padding: 0;
            width: 35px;
            font-weight: bold;
        }
        span.mc1{
            background-color: #8DB3E2;
            border: 2px solid #3F88E2;
        }
        span.mc2{
            background-color: #C2D69B;
            border: 2px solid #9DD62C;
        }
        span.kmp{
            background-color: #E5B8B7;
            border: 2px solid #E56563;
        }

        table tr td a, table tr td a:visited{
            color: #000000;
        }


        tr.sheet_CL:not(:hover) td{
            background-color: rgba(0, 81, 255, 0.20);
            color: #808080;
        }

        tr.sheet_CL:not(:hover) td a{
            color: #808080;
        }

        tr.sheet_OP:not(:hover) td{
            background-color: rgba(255, 246, 0, 0.40);
            /*color: #808080;*/
        }

        /*tr.sheet_OP:not(:hover) td a{*/
            /*color: #808080;*/
        /*}*/

    </style>
{/literal}
{capture name="mainbox"}
    {include file="addons/uns_mech/views/uns_sheets/components/search_form.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}
    <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <thead>
           <tr>
               {*<th width="1%">*}
                   {*<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />*}
               {*</th>*}
               <th width="130px" style="text-align: center;">Номер - Дата</th>
               <th width="10px"  style="text-align: center;">{include file="common_templates/tooltip.tpl" tooltip="<b>Местоположение СЛ:</b><br>1 - МЦ1; 2 - МЦ2; КМП - Скл. КМП" tooltip_mark="<b>Цех</b>"}</th>
               <th width="10px"  style="text-align: center;">{include file="common_templates/tooltip.tpl" tooltip="<b>Статус СЛ:</b><br>Открыт/Закрыт" tooltip_mark="<b>СТ</b>"}</th>
               <th width="10px"  style="text-align: center;">{include file="common_templates/tooltip.tpl" tooltip="<b>Тип материала:</b><br>О - отливка; М - металлопрокат;" tooltip_mark="<b>Тип</b>"}</th>
               <th width="300px" style="text-align: center;">Исходный материал</th>
               <th width="10px" style="text-align: center;">{include file="common_templates/tooltip.tpl" tooltip="<b>Кол-во выданного литья по СЛ.<br>Учитываются все доборы.<br><span style='color:red;'>* - брак.</span></b>" tooltip_mark="<b>Кол.</b>"}</th>
               <th width="300px" style="text-align: center;">Детали</th>
               <th>&nbsp;</th>
           </tr>
           </thead>
           {foreach from=$sheets item=i}
               {*<tr {cycle values="class=\"table-row\", "}>*}
               <tr class="sheet_{$i.status}">
                   {assign var="id" value=$i.sheet_id}
                   {assign var="value" value="sheet_id"}
                   <td {if $id == "`$smarty.session.mark_item.$controller`"} class="mark_item" {else} class="mark_item_clear" {/if} align="right" >
                   {*Идентификатор листа*}
                       <b><a name="{$id}" href="{"`$controller`.update?`$value`=`$id`"|fn_url}">{$i.no} - {$i.date_open|date_format:"%d/%m/%y"}</a></b>
                   </td>
                   <td align="center"  style="border-left: 1px solid #808080;"> {*target_object*}
                       {if $i.target_object == 10}
                           <span class="target_object mc1">1</span>
                       {elseif $i.target_object == 14}
                           <span class="target_object mc2">2</span>
                       {elseif $i.target_object == 17}
                           <span class="target_object kmp" title="Склад Комплектующих">КМП</span>
                       {elseif $i.target_object == 18}
                           <span class="target_object su" title="Сборочный участок">Сб.Уч</span>
                       {elseif $i.target_object == 19}
                           <span class="target_object sgp" title="Склад Готовой Продукции">СГП</span>
                       {else}
                           <span title="Местонахождение деталей неизвестно! Необходимо исправить!" class="info_warning hand">Н/Д</span>
                       {/if}
                   </td>
                   <td style="border-left: 1px solid #808080;"> {*Статус*}
                       {if $i.status == "OP"}
                           <img border="0" class="hand" title="Открыт" src="skins/basic/admin/addons/uns_acc/images/sl_open.png">
                       {elseif $i.status == "PARTIALLYCL"}
                           <img border="0" class="hand" title="Частично закрыт" src="skins/basic/admin/addons/uns_acc/images/sl_partiallyclose.png">
                       {elseif $i.status == "CL"}
                           <img border="0" class="hand" title="Закрыт {if $i.date_close}{$i.date_close|date_format:"%a %d/%m/%y"}{else}Н/Д{/if}" src="skins/basic/admin/addons/uns_acc/images/sl_close.png">
                       {elseif $i.status == "CN"}
                           <img border="0" title="Отменен" src="skins/basic/admin/addons/uns_acc/images/circle_red.png">
                       {/if}
                   </td>
                   <td align="center" style="border-left: 1px solid #808080;"> {*Тип материала*}
                       <span class="material_type {$i.material_type}">{$i.material_type}</span>
                   </td>
                   <td style="border-left: 1px solid #808080;"> {*Исходный материал*}
                        {if strlen($i.material_no)}[{if strlen($smarty.request.material_no)}{$i.material_no|replace:$smarty.request.material_no:"<span class='filtered_text'>`$smarty.request.material_no`</span>"}{else}{$i.material_no}{/if}] {/if}{$i.material_name}
                   </td>
                   <td align="center" style="border-left: 1px solid #808080;"> {*Исходный материал*}
                       {$i.material_quantity_by_PVP|fn_fvalue}{if $i.material_quantity_by_BRAK|is__more_0}<span class="info_warning">-{$i.material_quantity_by_BRAK|fn_fvalue}</span>{/if}
                   </td>
                   <td style="border-left: 1px solid #808080;"> {*Детали*}
                       {if is__array($i.details)}
                           {foreach from=$i.details item="d" name="d"}
                               {*{$smarty.foreach.d.iteration}. *}{$d.detail_name} [{if strlen($pump_name)}{$d.detail_no|replace:$pump_name:"<span class='filtered_text'>`$pump_name`</span>"}{else}{$d.detail_no}{/if}]{* - {$d.quantity} шт.*}{if !$smarty.foreach.d.last}<br>{/if}
                           {/foreach}
                       {else}
                           <span class="info_warning">Ошибка!</span>
                       {/if}
                   </td>
                   <td class="nowrap right" style="border-left: 1px solid #808080;">
                       {capture name="tools_items"}
                           <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}" title="Удалить сопроводительный лист: {$i.no} - {$i.date_open|date_format:"%d/%m/%y"}">
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
