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
            <td style="background-color: rgb(238,238,238);" colspan="2" class="bold center b1_l b1_b"><span class="total"><nobr>{$o.total_weight|number_format:1:".":" "}</nobr></span></td>
            <td style="background-color: rgb(238,238,238);" colspan="4" class="b_l">&nbsp;</td>
        </tr>
    </tfoot>
    <thead>
        <tr class="first-sibling" style="background-color: #eeeeee">
            <th rowspan="2" width="10px" class="cm-non-cb center">№</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="10px">Дата</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="10px">Тип</th>
            <th rowspan="2" class="cm-non-cb b1_l center" width="140px">Наименование</th>
            <th colspan="3" class="cm-non-cb b_l center" style="text-transform: none;" width="140px">Предварительный заказ</th>
            <th rowspan="2" class="cm-non-cb b_l center" style="text-transform: none;" width="100px">Кол-во<br>в произ.<br><span style="padding: 0; font-size: 11px; font-style: normal; color: red; font-weight: normal;">по факту<br>оплаты</span><br><span style="background-color: #ffffff;border: 1px solid gray;font-size: 12px;" class="hand all_in_production">Уст. все</span></th>
            <th rowspan="2" class="cm-non-cb b_l center" style="text-transform: none;" width="100px">Кол-во<br>в резерв<br><span style="padding: 0; font-size: 11px; font-style: normal; color: red; font-weight: normal;">предпродажная<br>подготовка</span></th>
            <th rowspan="2" class="cm-non-cb b_l center" width="">Примеч.</th>
            <th rowspan="2" class="cm-non-cb b1_l center">&nbsp;</th>
        </tr>
        <tr class="first-sibling" style="background-color: #eeeeee">
            <th class="cm-non-cb b_l b1_t center" width="40px" style="text-transform: none;">кол-во, шт</th>
            <th class="cm-non-cb b1_l b1_t center" colspan="2" style="text-transform: none;" width="40px">вес, кг</th>
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
                                f_material= $item_type_material
                                f_detail  = $item_type_detail
                                f_p   =     $item_type_p
                                f_pf  =     $item_type_pf
                                f_pa  =     $item_type_pa
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
                                    f_option_target_id=$i.detail_id
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
                                    f_option_target_id=$i.p_id
                                }
                            {/if}
                        </td>

                        {assign var="q" value=$i.quantity}
                        <td class="cm-non-cb b_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="q_`$num`"
                                f_name="`$e_n`[quantity]"
                                f_from=0
                                f_to=200
                                f_value=$q|fn_fvalue
                                f_simple=true
                                f_plus_minus=true
                                f_style="width:50px;"
                            }
                        </td>

                        <td class="cm-non-cb b1_l" align="center">
                            <span class="weight">{$i.weight|number_format:1:".":" "}</span>
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[weight]"
                                f_value=$i.weight
                            }
                        </td>

                        <td class="cm-non-cb b1_l bold" align="center">
                            <span class="total_weight"><nobr>{$q*$i.weight|number_format:1:".":" "}</nobr></span>
                        </td>

                        <td class="cm-non-cb b_l" align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_id="q_`$num`_in_production"
                                f_name="`$e_n`[quantity_in_production]"
                                f_from=0
                                f_to=$i.quantity
                                f_value=$i.quantity_in_production
                                f_simple=true
                                f_plus_minus=true
                                f_style="width:50px;"
                            }
                        </td>

                        <td class="cm-non-cb b_l" align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_name="`$e_n`[quantity_in_reserve]"
                                f_from=0
                                f_to=$i.quantity
                                f_value=$i.quantity_in_reserve
                                f_simple=true
                                f_style="width:50px;"
                            }
                        </td>

                        <td class="cm-non-cb b_l" align="left">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="textarea"
                                f_row=1
                                f_col=20
                                f_full_name="`$e_n`[comment]"
                                f_value=$i.comment
                                f_simple=true
                                f_style="width:60px; height:20px;"
                            }
                        </td>

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
                    f_material= $item_type_material
                    f_detail  = $item_type_detail
                    f_p   =     $item_type_p
                    f_pf  =     $item_type_pf
                    f_pa  =     $item_type_pa
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
                    f_to=200
                    f_value=0
                    f_simple=true
                    f_plus_minus=true
                    f_style="width:50px;"
                }
            </td>

            <td class="cm-non-cb b1_l" align="center">
                <span class="weight">&nbsp;</span>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="`$e_n`[weight]"
                    f_value=0
                }
            </td>
            <td class="cm-non-cb b1_l bold" align="center">
                <span class="total_weight">&nbsp;</span>
            </td>

            <td class="cm-non-cb b_l" align="center">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_id="q_`$num`_in_production"
                    f_name="`$e_n`[quantity_in_production]"
                    f_from=0
                    f_to=200
                    f_value=0
                    f_simple=true
                    f_plus_minus=true
                    f_style="width:50px;"
                }
            </td>

            <td class="cm-non-cb b_l" align="center">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_name="`$e_n`[quantity_in_reserve]"
                    f_from=0
                    f_to=200
                    f_value=0
                    f_simple=true
                    f_style="width:50px;"
                }
            </td>

            <td class="cm-non-cb b_l" align="left">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="textarea"
                    f_row=1
                    f_col=30
                    f_full_name="`$e_n`[comment]"
                    f_value=""
                    f_simple=true
                    f_style="width:60px; height:20px;"
                }
            </td>
            <td class="right cm-non-cb b1_l">
                {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2" hide_add=true}
            </td>
        </tr>
    </tbody>

</table>
</div>
