{*<pre>{$p_items|print_r}</pre>*}
{*<pre>{$p_type|print_r}</pre>*}
{*<pre>{$pl__item_type|print_r}</pre>*}
{strip}
<table cellpadding="0" cellspacing="0" class="table">
<tbody>
<tr class="first-sibling">
    <th width="10px" class="cm-non-cb">№</th>
    <th class="cm-non-cb" width="10px">Тип</th>
    <th class="cm-non-cb" width="10px">Класс мат.</th>
    <th class="cm-non-cb" width="90px">Категория</th>
    <th class="cm-non-cb" width="140px">Наименование</th>
    <th class="cm-non-cb" width="10px">Кол-во</th>
    <th class="cm-non-cb" width="10px">Ед. изм.</th>
    <th class="cm-non-cb" width="10px">Исп.</th>
    <th class="cm-non-cb">&nbsp;</th>
</tr>
</tbody>
{if is__array($p_items)}
    {foreach from=$p_items item="i" name="f_i"}
        {assign var="num" value=$smarty.foreach.f_i.iteration}
        {assign var="id" value=$i.ppl_id}
        {assign var="element_name" value="data[packing_list][`$p_part`][`$num`]"}
        {if is__more_0($id)}
            <tbody class="hover cm-row-item {if $i.ppl_status == "D"}hide-row{/if}" id="{$p_part}_{$id}_{$num}" >
                <tr>
                    {if (($p_type == $smarty.const.UNS_PACKING_TYPE__ITEM) and ($i.ppl_item_type == $smarty.const.UNS_PACKING_TYPE__SERIES))}
                        {assign var="is_series_item" value=true}
                        {assign var="series_item_class" value="series_item"}
                        {if is_array($i.replacement)}
                            {assign var="pplr_type" value=$i.replacement.pplr_type}
                            {assign var="series_item_class" value="`$series_item_class`_`$pplr_type`"}
                        {else}
                            {assign var="pplr_type" value=false}
                        {/if}
                    {else}
                        {assign var="is_series_item" value=false}
                        {assign var="pplr_type" value=false}
                        {assign var="series_item_class" value=""}
                    {/if}
                    <td class="cm-non-cb" align="center" {if $is_series_item}rowspan="2"{/if} style="border-right: 1px solid #808080;" >
                        <b>{$smarty.foreach.f_i.iteration}</b>
                    </td>
                    <td class="cm-non-cb">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="hidden"
                            f_name="`$element_name`[ppl_item_type]"
                            f_value=$i.ppl_item_type
                        }

                        {if $copy != "Y"}
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="hidden"
                            f_name="`$element_name`[ppl_id]"
                            f_value=$id
                        }
                        {/if}
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="select"
                            f_required=true f_integer=false
                            f_class=$series_item_class
                            f_name="`$element_name`[item_type]"
                            f_options=$pl__item_type
                            f_option_id="id"
                            f_option_value="name"
                            f_option_target_id=$i.item_type
                            f_simple_text=$is_series_item
                            f_simple=true
                        }
                    </td>

                    <td class="cm-non-cb">
                        {assign var="material_class" value=""}
                        {if $i.item_type == "D"}
                            {assign var="material" value=$i.items[$i.item_id].accounting_data.materials|array_shift}
                        {else}
                            {assign var="material" value=$i.items[$i.item_id]}
                        {/if}
                        {assign var="material_class" value=$material.mclass_name}
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=true f_integer=false
                            f_class=$series_item_class
                            f_name="`$element_name`[material_class]"
                            f_value=$material_class
                            f_simple_text=true
                        }
                    </td>

                    <td class="cm-non-cb">
                        {if $i.item_type == "D"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="dcategories_plain"
                                f_required=true f_integer=false
                                f_class=$series_item_class
                                f_name="`$element_name`[item_cat_id]"
                                f_options=$pl__dcategories_plain
                                f_option_id="dcat_id"
                                f_option_value="dcat_name"
                                f_option_target_id=$i.item_info.dcat_id
                                f_simple_text=$is_series_item
                                f_simple=true
                            }
                        {elseif $i.item_type == "M"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="mcategories_plain"
                                f_required=true f_integer=false
                                f_class=$series_item_class
                                f_name="`$element_name`[item_cat_id]"
                                f_options=$pl__mcategories_plain
                                f_option_id="mcat_id"
                                f_option_value="mcat_name"
                                f_option_target_id=$i.item_info.mcat_id
                                f_simple_text=$is_series_item
                                f_simple=true
                            }
                        {else}
                            {$smarty.const.UNS_ERROR}
                        {/if}
                    </td>
                    <td class="cm-non-cb">
                        {if $i.item_type == "D"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="select"
                                f_required=true f_integer=false
                                f_class=$series_item_class
                                f_name="`$element_name`[item_id]"
                                f_options=$i.items
                                f_option_id="detail_id"
                                f_option_value="format_name"
                                f_option_target_id=$i.item_id
                                f_simple_text=$is_series_item
                                f_simple=true
                            }
                        {elseif $i.item_type == "M"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="select"
                                f_required=true f_integer=false
                                f_class=$series_item_class
                                f_name="`$element_name`[item_id]"
                                f_options=$i.items
                                f_option_id="material_id"
                                f_option_value="format_name"
                                f_option_target_id=$i.item_id
                                f_simple_text=$is_series_item
                                f_simple=true
                            }
                        {else}
                            {$smarty.const.UNS_ERROR}
                        {/if}
                    </td>
                    <td class="cm-non-cb" align="right">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=true f_integer=false
                            f_class=$series_item_class
                            f_name="`$element_name`[quantity]"
                            f_value=$i.quantity|fn_fvalue
                            f_simple_text=$is_series_item
                            f_simple=true
                        }
                    </td>
                    <td class="cm-non-cb">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="select"
                            f_required=true f_integer=false
                            f_class=$series_item_class
                            f_name="`$element_name`[u_id]"
                            f_options=$i.units
                            f_option_id="u_id"
                            f_option_value="u_name"
                            f_option_target_id=$i.u_id
                            f_simple_text=$is_series_item
                            f_simple=true
                        }
                    </td>

                    <td class="cm-non-cb">
                        {assign var="f_empty" value=false}
                        {if $i.item_type == "M"}
                            {assign var="f_empty" value=true}
                        {/if}
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="typesize"
                            f_class=$series_item_class
                            f_name="`$element_name`[typesize]"
                            f_a=$i.item_info.size_a
                            f_b=$i.item_info.size_b
                            f_target=$i.typesize
                            f_simple_text=$is_series_item
                            f_empty=$f_empty
                        }
                    </td>

                    <td class="right cm-non-cb" {if $is_series_item}rowspan="2"{/if}>
                        {if $is_series_item}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][pplr_type]"
                                f_options=$pl__pplr_type
                                f_option_id="id"
                                f_option_value="name"
                                f_option_target_id=$i.replacement.pplr_type
                                f_simple=true
                            }
                        {else}
                            {include file="buttons/multiple_buttons.tpl" item_id="`$p_part`_`$id`_`$num`" tag_level="3" only_delete="Y"}
                        {/if}
                    </td>
                </tr>

                {if $is_series_item}
                <tr class="replacement_item {if $pplr_type != $smarty.const.UNS_PACKING_REPLACEMENT__REPLACE}hidden{else}{/if}">
                    <td>
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="hidden"
                            f_name="`$element_name`[replacement][pplr_id]"
                            f_value=$i.replacement.pplr_id|default:"0"
                        }

                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="hidden"
                            f_name="`$element_name`[replacement][ppl_id]"
                            f_value=$i.replacement.ppl_id|default:"0"
                        }

                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="select"
                            f_required=true f_integer=false
                            f_name="`$element_name`[replacement][item_type]"
                            f_options=$pl__item_type
                            f_option_id="id"
                            f_option_value="name"
                            f_option_target_id=$i.replacement.item_type
                            f_simple=true
                        }
                    </td>
                    <td>{* Класс материалов *}</td>
                    <td>
                        {if $i.replacement.item_type == "D"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="dcategories_plain"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][item_cat_id]"
                                f_options=$pl__dcategories_plain
                                f_option_id="dcat_id"
                                f_option_value="dcat_name"
                                f_option_target_id=$i.replacement.item_info.dcat_id|default:"0"
                                f_simple=true
                            }
                        {elseif $i.replacement.item_type == "M"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="mcategories_plain"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][item_cat_id]"
                                f_options=$pl__mcategories_plain
                                f_option_id="mcat_id"
                                f_option_value="mcat_name"
                                f_option_target_id=$i.replacement.item_info.mcat_id|default:"0"
                                f_simple=true
                            }
                        {else}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][item_cat_id]"
                                f_options=""
                                f_option_id="id"
                                f_option_value="name"
                                f_option_target_id=0
                                f_simple=true
                            }
                        {/if}
                    </td>
                    <td>
                        {if $i.replacement.item_type == "D"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="select"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][item_id]"
                                f_options=$i.replacement.items
                                f_option_id="detail_id"
                                f_option_value="format_name"
                                f_option_target_id=$i.replacement.item_id
                                f_simple=true
                            }
                        {elseif $i.replacement.item_type == "M"}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id=$id
                                f_type="select"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][item_id]"
                                f_options=$i.replacement.items
                                f_option_id="material_id"
                                f_option_value="format_name"
                                f_option_target_id=$i.replacement.item_id
                                f_simple=true
                            }
                        {else}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select"
                                f_required=true f_integer=false
                                f_name="`$element_name`[replacement][item_id]"
                                f_options=""
                                f_option_id="id"
                                f_option_value="name"
                                f_option_target_id=0
                                f_simple=true
                            }
                        {/if}
                    </td>
                    <td>
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=true f_integer=false
                            f_name="`$element_name`[replacement][quantity]"
                            f_value=$i.replacement.quantity|fn_fvalue|default:"1"
                            f_simple=true
                        }
                    </td>
                    <td>
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="select"
                            f_required=true f_integer=false
                            f_class=$series_item_class
                            f_name="`$element_name`[replacement][u_id]"
                            f_options=$i.replacement.units
                            f_option_id="u_id"
                            f_option_value="u_name"
                            f_option_target_id=$i.replacement.u_id
                            f_simple=true
                        }
                    </td>
                    <td>
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="typesize"
                            f_class=$series_item_class
                            f_name="`$element_name`[replacement][typesize]"
                            f_a=$i.replacement.item_info.size_a
                            f_b=$i.replacement.item_info.size_b
                            f_target=$i.replacement.typesize
                        }
                    </td>
                </tr>
                {/if}
            </tbody>
        {/if}
    {/foreach}
{/if}

{math assign="num" equation="x + 1" x=$num|default:0}
{assign var="element_name" value="data[packing_list][`$p_part`][`$num`]"}
<tbody class="hover cm-row-item" id="box_add_{$p_part}_{$id}_{$num}">
<tr>
    <td class="cm-non-cb"></td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="`$element_name`[ppl_id]"
            f_value=0
        }
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_required=true f_integer=false
            f_name="`$element_name`[item_type]"
            f_options=$pl__item_type
            f_option_id="id"
            f_option_value="name"
            f_option_target_id=""
            f_simple=true
        }
    </td>
    <td class="cm-non-cb">
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_required=true f_integer=false
            f_name="`$element_name`[item_cat_id]"
            f_options=""
            f_option_id="id"
            f_option_value="name"
            f_option_target_id=0
            f_simple=true
        }
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_required=true f_integer=false
            f_name="`$element_name`[item_id]"
            f_options=""
            f_option_id=""
            f_option_value=""
            f_option_target_id=0
            f_simple=true
        }
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="input"
            f_required=true f_integer=false
            f_name="`$element_name`[quantity]"
            f_value=""
            f_simple=true
        }
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_required=true f_integer=false
            f_name="`$element_name`[u_id]"
            f_options=""
            f_option_id=""
            f_option_value=""
            f_option_target_id=0
            f_simple=true
        }
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="typesize"
            f_class=$series_item_class
            f_name="`$element_name`[typesize]"
            f_empty=true
        }
    </td>
    <td class="right cm-non-cb">
        {include file="buttons/multiple_buttons.tpl" item_id="add_`$p_part`_`$id`_`$num`" tag_level="2"}
    </td>
</tr>
</tbody>

</table>
{/strip}
