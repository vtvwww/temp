{literal}
    <style type="text/css">
        tr.status td{
            font-weight: bold;
        }

        tr.status.U td{
            background-color: rgba(1, 128, 0, 0.49);
        }

        tr.status.Z td{
            background-color: rgba(0, 81, 255, 0.2);
            color: #808080;
            font-weight: normal;
        }

        tr.status.K td{
            background-color: rgba(255, 218, 72, 0.68);
        }
    </style>
{/literal}

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
               <th width="1px">&nbsp;</th>
               <th class="b1_l" width="10px">№</th>
               <th class="b1_l" width="80px">{include file="common_templates/tooltip.tpl" tooltip="Дата открытия партии" tooltip_mark="<b>Дата открытия</b>"}</th>
               <th width="10px">(?)</th>
               <th class="b1_l" width="20px">Ст.</th>
               <th class="" width="100px">Наменование насоса, кол-во</th>
               <th class="b1_l center">Собрано насосов из партии</th>
               <th class="b1_l" width="10px">&nbsp;</th>
           </tr>
           {foreach from=$kits item=i}
               <tr class="status {$i.status}">
                   {assign var="id" value=$i.kit_id}
                   {assign var="value" value="kit_id"}
                   <td align="right" style="{if $id == "`$smarty.session.mark_item.$controller`"}border-left: 10px solid #FF9D1F;{else}border-left: 10px solid transparent;{/if}">
                       {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                   </td>
                   <td class="b1_l center"> {*№ партии*}
                       {$id}
                   </td>
                   <td class="b1_l">
                       {$i.date_open|date_format:"%a %d/%m/%y"}
                   </td>
                   <td class="nowrap">
                       {include file="common_templates/tooltip.tpl" tooltip="`$i.description`"}
                   </td>
                   <td class="b1_l"> {* Статус *}
                       <b>
                       {if      $i.status == "O"}
                           В ожидании
                       {elseif  $i.status == "K"}
                           <img class="hand" border="0" title="Комплектуется" src="skins/basic/admin/addons/uns_acc/images/circle_yellow.png">
                       {elseif  $i.status == "U"}
                           <img class="hand" border="0" title="Укомплектована" src="skins/basic/admin/addons/uns_acc/images/circle_green.png">
                       {elseif  $i.status == "Z"}
                           <img style="opacity: 0.4;" class="hand" border="0" title="Закрыт" src="skins/basic/admin/addons/uns_acc/images/done.png">
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

                   <td class="b1_l">{* Собрано *}
                       {if $i.VN|is__array}
                           <ol style="color: #606060; margin: 0;">
                               {foreach from=$i.VN item="vn" name="vn"}
                               <li {if !$smarty.foreach.vn.last}style="border-bottom:1px dashed #A3A3A3;"{/if}>{$vn.date|date_format:"%a %d/%m/%y"}&nbsp;&nbsp;&nbsp;&nbsp;<b>{$vn.name}{if $vn.item_type == "PF"} на раме{/if}</b>, {$vn.quantity|fn_fvalue} шт</li>
                               {/foreach}
                           </ol>
                       {/if}
                   </td>

                   <td class="nowrap right b1_l center">
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
