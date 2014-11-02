<h3 style="border-bottom: 3px double gray;margin-bottom: 6px;padding-bottom: 2px;">Позиции заказа</h3>
<table>
    <tr>
        <td width="25%" class="center"><img src="skins/basic/admin/addons/uns_orders/images/shipment_white.png"> - нет отгрузок</td>
        <td class="b1_l"></td>
        <td width="25%" class="center"><img src="skins/basic/admin/addons/uns_orders/images/shipment_yellow.png"> - частичная отгрузка</td>
        <td class="b1_l"></td>
        <td width="25%" class="center"><img src="skins/basic/admin/addons/uns_orders/images/shipment_green.png"> - полная отгрузка</td>
        <td class="b1_l"></td>
        <td width="25%" class="center"><img src="skins/basic/admin/addons/uns_orders/images/shipment_red.png"> - отгрузка превышает заказ</td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" class="table order_items">
    <tfoot>
        <tr>
            <td style="background-color: rgb(238,238,238);" colspan="4" class="bold" align="right">ИТОГО:</td>
            <td style="background-color: rgb(238,238,238);" colspan="1" class="bold center b_l b1_b"><span class="total">{$o.total_quantity}</span></td>
            <td style="background-color: rgb(238,238,238);" colspan="1" class="bold center b1_l b1_b"><span class="total"><nobr>{$o.total_weight|number_format:1:".":" "}</nobr></span></td>
            <td style="background-color: rgb(238,238,238);" colspan="3" class="b_l">&nbsp;</td>
            <td style="background-color: rgb(238,238,238);" colspan="1" class="b1_l center">
                <span class="submit-button-big" style="margin: 0;">
                    <input class="shipment_add" style="padding: 3px;height: 26px;" type="submit" value="Отгрузить" name="dispatch[uns_orders.shipment.update]" disabled>
                </span>
                <br/>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id="_shipment_add_`$num`"
                    f_type="date"
                    f_required=true
                    f_name="shipment_add_date"
                    f_value=$i.date
                    f_icon=false
                    date_disabled=true
                    date_meta="shipment_add_date"
                    f_style="width:65px;"
                    f_simple=true
                }
            </td>
            <td style="background-color: rgb(238,238,238);" colspan="1" class="b1_l">&nbsp;</td>
        </tr>
    </tfoot>
    <thead>
        <tr class="first-sibling" style="background-color: #eeeeee">
            <th rowspan="2" width="10px" class="cm-non-cb center">№</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="10px">ДАТА</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="10px">ТИП</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="140px">НАИМЕНОВАНИЕ</th>
            <th colspan="2" class="cm-non-cb b_l center" style="text-transform: none;" width="140px">ЗАКАЗ</th>
            <th             class="cm-non-cb b_l center" style="text-transform: none;">В РЕЗЕРВЕ</th>
            <th colspan="3" class="cm-non-cb b_l center">ОТГРУЗКА</th>
            <th rowspan="2" class="cm-non-cb b1_l center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>
        <tr class="first-sibling" style="background-color: #eeeeee">
            <th class="cm-non-cb b_l b1_t center"  style="text-transform: none;">кол-во, шт</th>
            <th class="cm-non-cb b1_l b1_t center" style="text-transform: none;">вес, кг</th>
            <th class="cm-non-cb b_l b1_t center"  style="text-transform: none;">кол-во, шт</th>
            <th class="cm-non-cb b_l b1_t center"  style="text-transform: none; padding: 0 6px;">факт.</th>
            <th class="cm-non-cb b1_l b1_t center" style="text-transform: none;">когда</th>
            <th class="cm-non-cb b1_l b1_t center" style="text-transform: none;">следующая</th>
        </tr>
    </thead>

    {if is__array($o.items)}
        {foreach from=$o.items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.oi_id}
            {assign var="e_n" value="order_data[document_items][`$num`]"}
            {if is__more_0($id)}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center">
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>

                        {*DATE*}
                        <td class="cm-non-cb b1_l">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id="_oi_`$num`"
                                f_type="date"
                                f_required=true
                                f_name="`$e_n`[date]"
                                f_value=$i.date
                                f_icon=false
                                f_style="width:65px;"
                                f_simple=true
                            }
                        </td>

                        {*ITEM_TYPE*}
                        <td class="cm-non-cb b1_l">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[oi_id]"
                                f_value=$id
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="item_type"
                                f_required=true f_integer=false
                                f_detail  = true
                                f_p   =     true
                                f_pf  =     true
                                f_pa  =     true
                                f_name="`$e_n`[item_type]"
                                f_value=$i.item_type
                                f_simple=true
                                f_short=true
                            }
                        </td>

                        {*ITEM_NAME*}
                        <td class="cm-non-cb b1_l">
                            {if $i.item_type == "D"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_by_group"
                                    f_name="`$e_n`[item_id]"
                                    f_simple=true
                                    f_options="details"
                                    f_option_id="detail_id"
                                    f_option_value="detail_name"
                                    f_optgroups=$details_by_categories
                                    f_optgroup_label="dcat_name"
                                    f_option_target_id=$i.item_id
                                }

                            {elseif $i.item_type == "P" or $i.item_type == "PF" or $i.item_type == "PA"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_by_group"
                                    f_name="`$e_n`[item_id]"
                                    f_simple=true
                                    f_options="pumps"
                                    f_option_id="p_id"
                                    f_option_value="p_name"
                                    f_optgroups=$pumps_by_series
                                    f_optgroup_label="ps_name"
                                    f_option_target_id=$i.item_id
                                }
                            {/if}
                        </td>

                        {*КОЛ-ВО*}
                        <td class="cm-non-cb b_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="q_`$num`"
                                f_name="`$e_n`[quantity]"
                                f_from=$f_min
                                f_to=100
                                f_value=$i.quantity|intval
                                f_simple=true
                                f_plus_minus=true
                                f_track=true
                                f_default=$q
                                f_style="width:50px;"
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[weight]"
                                f_value=$i.weight
                            }
                        </td>

                        {*ОБЩИЙ ВЕС*}
                        <td class="cm-non-cb b1_l bold" align="right">
                            <span class="total_weight"><nobr>{$i.quantity*$i.weight|number_format:1:".":" "}</nobr></span>
                        </td>

                        {* ---------------------------------------------------*}
                        {*АНАЛИЗ ОТГРУЗКИ*}
                        {assign var="pos" value="-150"}
                        {assign var="RO" value=false}
                        {assign var="RO_q" value=0}
                        {assign var="RO_q_disabled" value=false}
                        {if $i.info_RO.items|is__array}
                            {assign var="RO" value=true}
                            {assign var="RO_q" value=$i.info_RO.total_q}
                            {if $i.info_RO.total_q == $i.quantity}
                                {assign var="RO_q_disabled" value=true}
                                {assign var="pos" value="-50"}
                            {elseif $i.info_RO.total_q > $i.quantity}
                                {assign var="RO_q_disabled" value=true}
                                {assign var="pos" value="-200"}
                            {else}
                                {math equation="-350+50*x/y" x=$i.info_RO.total_q y=$i.quantity assign="pos"}
                            {/if}
                        {/if}

                        {*В РЕЗЕРВЕ*}
                        <td class="cm-non-cb b_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="q_`$num`"
                                f_name="`$e_n`[quantity_in_reserve]"
                                f_from=0
                                f_to=$i.quantity-$RO_q
                                f_value=$i.quantity_in_reserve
                                f_simple=true
                                f_plus_minus=true
                                f_track=true
                                f_default=$i.quantity_in_reserve
                                f_style="width:50px;"
                            }
                        </td>

                        {*ОТГРУЗКА*}
                        <td class="cm-non-cb b_l {if $i.info_RO.total_q}b{else}zero{/if}" align="center" style="width:44px; background: url('skins/basic/admin/addons/uns_orders/images/bar.png') {$pos}px center;">
                            {$i.info_RO.total_q|default:0}
                        </td>

                        <td class="cm-non-cb b1_l" align="right">
                            {capture name="ro"}
                                {if $i.info_RO.items|is__array}
                                    {foreach from=$i.info_RO.items item="d"}
                                        <li>
                                            {assign var="date" value=$d.date|fn_parse_date|date_format:"%d/%m/%y"}
                                            <a name="{$d.document_id}" onclick="ms({$d.document_id});" href="u.php?dispatch=uns_orders.update&order_id=40#doc_{$d.document_id}">{$date}&nbsp;&nbsp;&nbsp;{$d.quantity}</a>
                                            {*<a rev="content_{$d.document_id}" id="opener_{$d.document_id}" href="u.php?dispatch=uns_orders.shipment.add&amp;order_id=40" class="cm-dialog-opener text-button-edit cm-ajax-update cm-dialog-auto-size">{$date}&nbsp;&nbsp;&nbsp;{$d.quantity}</a>*}
                                            {*<div class="hidden" id="content_{$d.document_id}" title="Отгрузка за {$date}"></div>*}
                                        </li>
                                    {/foreach}
                                {/if}
                            {/capture}
                            {include    file="common_templates/table_tools_list.tpl"
                                        id="oi_id_`$id`"
                                        text="sa"
                                        act="edit"
                                        prefix=$id
                                        tools_list=$smarty.capture.ro}
                        </td>

                        <td class="cm-non-cb b1_l bold" align="right" width="25px">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="RO_q_`$num`"
                                f_name="`$e_n`[RO_q]"
                                f_from=0
                                f_to=$i.quantity-$i.info_RO.total_q
                                f_value=0
                                f_simple=true
                                f_plus_minus=true
                                f_disabled=$RO_q_disabled
                                f_track=true
                                f_default=0
                                f_style="width:50px;"
                            }
                        </td>

                        {*<td class="cm-non-cb b_l" align="left">*}
                            {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                {*f_type="textarea"*}
                                {*f_row=1*}
                                {*f_col=20*}
                                {*f_full_name="`$e_n`[comment]"*}
                                {*f_value=$i.comment*}
                                {*f_simple=true*}
                                {*f_style="width:60px; height:20px;"*}
                            {*}*}
                        {*</td>*}

                        <td class="center cm-non-cb b1_l">
                            {if $RO_q>0 or $i.quantity_in_reserve>0 }
                                <img width="24" height="24" border="0" title="Нельзя удалить позицию, так как она зарезервирована или по ней была отгрузка" src="/temp/www/skins/basic/admin/images/icons/icon_delete_disabled.png">
                            {else}
                                {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
                            {/if}
                        </td>
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}

    {math assign="num" equation="x + 1" x=$num|default:0}
    {assign var="e_n" value="order_data[document_items][`$num`]"}
    <tbody class="hover cm-row-item" id="box_add_{$num}">
        <tr>
            <td class="cm-non-cb" align="center">&nbsp;</td>

            {*DATE*}
            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id="_oi_`$num`"
                    f_type="date"
                    f_required=true
                    f_name="`$e_n`[date]"
                    f_icon=false
                    f_style="width:65px;"
                    f_simple=true
                }
            </td>

            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[oi_id]"
                    f_value=0
                }
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="item_type"
                    f_required=true f_integer=false
                    f_detail  = true
                    f_p   =     true
                    f_pf  =     true
                    f_pa  =     true
                    f_name="`$e_n`[item_type]"
                    f_simple=true
                    f_short=true
                }
            </td>

            <td class="cm-non-cb b1_l">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select"
                    f_name="`$e_n`[item_id]"
                    f_simple=true
                }
            </td>

            <td class="cm-non-cb b_l" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_id="q_`$num`"
                    f_name="`$e_n`[quantity]"
                    f_from=0
                    f_to=100
                    f_value=0
                    f_simple=true
                    f_plus_minus=true
                    f_style="width:50px;"
                }
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[weight]"
                    f_value=0
                }
            </td>

            <td class="cm-non-cb b1_l bold" align="right">
                <span class="total_weight">&nbsp;</span>
            </td>

            <td class="cm-non-cb b_l" align="center">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_name="`$e_n`[quantity_in_reserve]"
                    f_from=0
                    f_to=100
                    f_value=0
                    f_simple=true
                    f_plus_minus=true
                    f_style="width:50px;"
                }
            </td>

            {*<td class="cm-non-cb b_l" align="left">*}
                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                    {*f_type="textarea"*}
                    {*f_row=1*}
                    {*f_col=30*}
                    {*f_full_name="`$e_n`[comment]"*}
                    {*f_value=""*}
                    {*f_simple=true*}
                    {*f_style="width:60px; height:20px;"*}
                {*}*}
            {*</td>*}
            <td colspan="4" class="right cm-non-cb b_l">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2" hide_add=true}
            </td>
        </tr>
    </tbody>
</table>
{*</div>*}
