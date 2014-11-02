{literal}
    <script>
        $("span.all_in_production").live("click", function(){
            console.log(123);
            var q = $('select[name^="order_data[document_items]"][name$="[quantity]"]');
            q.each (function(){
                var id = $(this).prop("id");
                var v  = $(this).val();
                $('select#'+id+'_in_production').val(v);
            });
        });
    </script>
{/literal}

{include file="common_templates/subheader.tpl" title="Позиции заказа"}
<div class="subheader_block">

{* Тип позиции *}
{assign var="item_type_detail"  value=true}
{assign var="item_type_p"       value=true}
{assign var="item_type_pf"      value=true}
{assign var="item_type_pa"      value=true}


<table cellpadding="0" cellspacing="0" class="table order_items">
    <tfoot>
        <tr>
            <td style="background-color: rgb(238,238,238);" colspan="4" class="bold" align="right">ИТОГО:</td>
            <td style="background-color: rgb(238,238,238);" colspan="1" class="bold center b_l b1_b"><span class="total">{$o.total_quantity}</span></td>
            <td style="background-color: rgb(238,238,238);" colspan="1" class="bold center b1_l b1_b"><span class="total"><nobr>{$o.total_weight|number_format:1:".":" "}</nobr></span></td>
            <td style="background-color: rgb(238,238,238);" colspan="5" class="b_l">&nbsp;</td>
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
            <th rowspan="2" class="cm-non-cb b1_l center">&nbsp;</th>
        </tr>
        <tr class="first-sibling" style="background-color: #eeeeee">
            <th class="cm-non-cb b_l b1_t center"  style="text-transform: none;">кол-во, шт</th>
            <th class="cm-non-cb b1_l b1_t center" style="text-transform: none;">вес, кг</th>
            <th class="cm-non-cb b_l b1_t center"  style="text-transform: none;">кол-во, шт</th>
            <th class="cm-non-cb b_l b1_t center"  style="text-transform: none;">факт.</th>
            <th class="cm-non-cb b1_l b1_t center" style="text-transform: none;">когда</th>
            <th class="cm-non-cb b1_l b1_t center" style="text-transform: none;">след.</th>
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
                        {assign var="f_min" value=0}
                        {if $i.RO_document_id>0}
                            {assign var="f_min" value=$i.RO_q|intval}
                        {/if}
                        {assign var="q" value=$i.quantity|fn_fvalue}
                        <td class="cm-non-cb b_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="q_`$num`"
                                f_name="`$e_n`[quantity]"
                                f_from=$f_min
                                f_to=100
                                f_value=$q
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
                            <span class="total_weight"><nobr>{$q*$i.weight|number_format:1:".":" "}</nobr></span>
                        </td>

                        {*В РЕЗЕРВЕ*}
                        <td class="cm-non-cb b_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="q_`$num`"
                                f_name="`$e_n`[quantity_in_reserve]"
                                f_from=0
                                f_to=$i.quantity_in_reserve
                                f_value=$i.quantity_in_reserve
                                f_simple=true
                                f_plus_minus=true
                                f_track=true
                                f_default=$i.quantity_in_reserve
                                f_style="width:50px;"
                            }
                        </td>

                        {*ОТГРУЗКА*}
                        {assign var="pos" value="-250"}
                        {assign var="RO" value=false}
                        {assign var="RO_q_disabled" value=false}
                        {if $i.info_RO.items|is__array}
                            {assign var="RO" value=true}
                            {if $i.info_RO.total_q >= $i.quantity}
                                {assign var="RO_q_disabled" value=true}
                                {assign var="pos" value="-150"}
                            {else}
                                {math equation="-250+50*x/y" x=$i.info_RO.total_q y=$i.quantity assign="pos"}
                            {/if}
                        {/if}

                        <td class="cm-non-cb b_l {if $i.info_RO.total_q}b{else}zero{/if}" align="center" style="width:44px; background: url('skins/basic/admin/addons/uns_orders/images/bar.png') {$pos}px center;">
                            {$i.info_RO.total_q|default:0}
                        </td>

                        <td class="cm-non-cb b1_l" align="right">
                            {capture name="ro"}
                                {if $i.info_RO.items|is__array}
                                    {foreach from=$i.info_RO.items item="d"}
                                        <li><a title="{$d.date|fn_parse_date|date_format:"%d/%m/%y"} было отгружено {$d.quantity} шт." style="text-transform: none;" class="" href="#">{$d.date|fn_parse_date|date_format:"%d/%m/%y"}&nbsp;&nbsp;&nbsp;{$d.quantity}</a></li>
                                    {/foreach}
                                {/if}
                                {*<li><a class="" href="{"`$controller`.update?`$value`=`$id`&copy=Y"|fn_url}">{$lang.copy}</a></li>*}
                                {*<li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a></li>*}
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

                        <td class="right cm-non-cb b1_l">
                            {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
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
            <td colspan="1" class="right cm-non-cb b1_l">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2" hide_add=true}
            </td>
        </tr>
    </tbody>
</table>
</div>
