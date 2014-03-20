{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        {strip}
            <hr>
            {*СЕРИИ НАСОСОВ*}
            <table cellpadding="10" cellspacing="0" border="0" class="search-header materials" id="search_form_materials">
                <tr>
                    <td class="nowrap search-field">
                        <label>{$lang.uns_pump_series}:</label>
                        <div class="break">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_by_group"
                                f_required=true f_integer=false
                                f_name="ps_id"
                                f_options="pump_series"
                                f_option_id="ps_id"
                                f_option_value="ps_name"
                                f_optgroups=$pump_series
                                f_optgroup_label="pt_name_short"
                                f_option_target_id=$search.ps_id
                                f_simple=true
                                f_blank=true
                            }
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
        {/strip}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}


   <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
       {include file="common_templates/pagination.tpl"}
       <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
           <tr>
               <th width="1%">
                   <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
               </th>
               <th width="1px">&nbsp;</th>
               <th width="10px">№</th>
               <th width="10px">Тип</th>
               <th width="10px">(?)</th>
               {*<th width="150px">Сроки выполнения</th>*}
               <th width="20px">Статус</th>
               <th>Описание</th>
               <th>&nbsp;</th>
           </tr>
           {foreach from=$kits item=i}
               <tr {cycle values="class=\"table-row\", "}>
                   {assign var="id" value=$i.kit_id}
                   {assign var="value" value="kit_id"}
                   <td style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{/if}">
                       <input type="checkbox" name="document_ids[]" value="{$id}" class="checkbox cm-item" />
                   </td>
                   <td>
                       {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                   </td>
                   <td> {*№ партии*}
                       {$id}
                   </td>
                   <td> {* Тип *}
                       {if $i.kit_type == "D"}
                           Дет.
                       {elseif $i.kit_type == "P"}
                           Нас.
                       {/if}
                   </td>
                   {*<td> Сроки выполнения *}
                       {*<b>{$i.date_begin|date_format:"%d/%m/%y"} - {$i.date_end|date_format:"%d/%m/%y"}</b>*}
                   {*</td>*}
                   <td class="nowrap">
                       {include file="common_templates/tooltip.tpl" tooltip="`$i.description`"}
                   </td>
                   <td> {* Статус *}
                       <b>
                       {if      $i.status == "O"}
                           В ожидании
                       {elseif  $i.status == "K"}
                           <img class="hand" border="0" title="Комплектуется" src="skins/basic/admin/addons/uns_acc/images/circle_yellow.png">
                       {elseif  $i.status == "U"}
                           <img class="hand" border="0" title="Укомплектована" src="skins/basic/admin/addons/uns_acc/images/circle_green.png">
                       {elseif  $i.status == "Z"}
                           <img class="hand" border="0" title="Закрыт" src="skins/basic/admin/addons/uns_acc/images/done.png">
                       {elseif  $i.status == "A"}
                           Аннулирована
                       {/if}
                       </b>
                   </td>
                   <td>
                       {if $i.kit_type == "P"}
                           {$pumps[$i.p_id].p_name} - {$i.p_quantity|fn_fvalue} шт.
                       {elseif $i.kit_type == "D"}
                           {foreach from=$i.details item="d" name="d"}
                               {$smarty.foreach.d.iteration}. {$d.detail_name}{if $d.detail_no} {$d.detail_no}{/if} - {$d.quantity|fn_fvalue} шт.
                               {if $smarty.foreach.d.last}{else}<br>{/if}
                           {/foreach}
                       {/if}
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
                   <td colspan="20"><p>{$lang.no_items}</p></td>
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
       {include file="common_templates/tools.tpl" tool_href="`$controller`.add.pump"    prefix="top" link_text="Добавить Партию насоса"  hide_tools=true}
       {*{include file="common_templates/tools.tpl" tool_href="`$controller`.add.details" prefix="top" link_text="Добавить Партию деталей"  hide_tools=true}*}
   {/capture}


{/capture}
{include file="common_templates/mainbox.tpl" title="Партии деталей" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
