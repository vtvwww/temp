{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="document_id"       value=0}
    {assign var="document_type"     value=$add_document_type}
    {assign var="document_items"    value=""}

{elseif $mode == "update"}
    {assign var="document_id"       value=$d.document_id}
    {assign var="document_type"     value=$d.document_type}
    {assign var="document_items"    value=$d.items}

{/if}


{******************************************************************************}
{* АКТИВНОСТЬ СКЛАДА "ОТКУДА" и КУДА *}
{******************************************************************************}
{assign var="object_from__active" value=false}
{assign var="object_to__active" value=false}

{if $document_type == $smarty.const.UNS_DOCUMENT__PRIH_ORD}
    {assign var="object_from__active"   value=false}
    {assign var="object_to__active"     value=true}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__RASH_ORD}
    {assign var="object_from__active"   value=true}
    {assign var="object_to__active"     value=false}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__NOPM}
    {assign var="object_from__active"   value=true}
    {assign var="object_to__active"     value=true}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__SDAT_N}
    {assign var="object_from__active"   value=true}
    {assign var="object_to__active"     value=true}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__INPM}
    {assign var="object_from__active"   value=true}
    {assign var="object_to__active"     value=true}

{/if}


{******************************************************************************}
{* ОТОБРАЖЕНИЕ "МАТЕРИАЛОВ" и "ДЕТАЛЕЙ" *}
{******************************************************************************}
{assign var="material__active" value=false}
{assign var="detail__active" value=false}

{if $document_type == $smarty.const.UNS_DOCUMENT__PRIH_ORD}
    {assign var="material__active"   value=true}
    {assign var="detail__active"     value=false}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__RASH_ORD}
    {assign var="material__active"   value=true}
    {assign var="detail__active"     value=true}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__NOPM}
    {assign var="material__active"   value=true}
    {assign var="detail__active"     value=true}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__INPM}
    {assign var="material__active"   value=true}
    {assign var="detail__active"     value=true}

{elseif $document_type == $smarty.const.UNS_DOCUMENT__SDAT_N}
    {assign var="material__active"   value=true}
    {assign var="detail__active"     value=true}

{/if}




{capture name="document_info"}
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="hidden"
        f_name="document_id"
        f_value=$document_id}


    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id=$document_id
        f_type="document_type"
        f_required=true f_integer=false f_integer_more_0=false
        f_name="document_type"
        f_option_target=$document_type
        f_description="Тип документа"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_document_date_`$document_id`"
        f_type="date"
        f_required=true
        f_name="document_date"
        f_value=$d.document_date
        f_description="Дата создание документа"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="objects_plain"
        f_name="object_id_from"

        f_required=$object_from__active
        f_integer_more_0=$object_from__active

        f_options_enabled=$enabled_objects.$document_type.from
        f_options=$objects_plain
        f_option_id="o_id"
        f_option_value="o_name"
        f_target=$d.object_id_from
        f_blank=true
        f_view_id=true
        f_active=$object_from__active
        f_description="Склад Откуда"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="objects_plain"
        f_name="object_id_to"

        f_required=$object_to__active
        f_integer_more_0=$object_to__active

        f_options_enabled=$enabled_objects.$document_type.to
        f_options=$objects_plain
        f_option_id="o_id"
        f_option_value="o_name"
        f_target=$d.object_id_to
        f_blank=true
        f_view_id=true
        f_active=$object_to__active
        f_description="Склад Куда"
    }

    {*<hr>*}

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id=$document_id
        f_type="document_status"
        f_name="document_status"
        f_document_status=$d.document_status
        f_description="Состояние"
    }

    {*<hr>*}

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id=$document_id
        f_type="textarea"
        f_row=1
        f_required=false f_integer=false
        f_name="document_comment"
        f_value=$d.document_comment
        f_description="Комментарий"
    }

{*

    <hr>

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_date_of_create_`$document_id`"
        f_type="date"
        f_required=true
        f_name="date_of_create"
        f_value=$d.date_of_create
        f_description="Дата создания"
        f_disabled=true
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_date_of_update_`$document_id`"
        f_type="date"
        f_required=true
        f_name="date_of_update"
        f_value=$d.date_of_update
        f_description="Дата обновления"
        f_disabled=true
    }

    <hr>
*}

{/capture}

{capture name="document_items"}
<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th width="10px" class="cm-non-cb">№</th>
            <th class="cm-non-cb" width="10px">Тип</th>
            <th class="cm-non-cb" width="90px">Категория</th>
            <th class="cm-non-cb" width="140px">Наименование</th>
            <th class="cm-non-cb" width="10px">Кол-во</th>
            <th class="cm-non-cb" width="10px">Ед. изм.</th>
            {if $detail__active}
            <th class="cm-non-cb" width="10px">Исп.</th>
            {/if}
            {if $lock}
                <th class="cm-non-cb">Итого, кг</th>
            {else}
                <th class="cm-non-cb">&nbsp;</th>
            {/if}
        </tr>
    </tbody>

    {if is__array($document_items)}
        {foreach from=$document_items item="i" name="d_i"}
            {assign var="num" value=$smarty.foreach.d_i.iteration}
            {assign var="id" value=$i.di_id}
            {assign var="element_name" value="data[items][`$num`]"}
            {if is__more_0($id)}
                <tbody class="hover cm-row-item" id="{$id}_{$num}" >
                    <tr>
                        <td class="cm-non-cb" align="center" {if $is_series_item}rowspan="2"{/if} style="border-right: 1px solid #808080;" >
                            <b>{$smarty.foreach.d_i.iteration}</b>
                        </td>
                        <td class="cm-non-cb">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$element_name`[di_id]"
                                f_value=$id
                            }
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="item_type"
                                f_required=true f_integer=false
                                f_material=$material__active
                                f_detail=$detail__active
                                f_name="`$element_name`[item_type]"
                                f_value=$i.item_type
                                f_simple=true
                            }
                        </td>
                        <td class="cm-non-cb">
                            {if $i.item_type == "D"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="dcategories_plain"
                                    f_required=true f_integer=false
                                    f_name="`$element_name`[item_cat_id]"
                                    f_options=$dcategories_plain
                                    f_option_id="dcat_id"
                                    f_option_value="dcat_name"
                                    f_option_target_id=$i.item_info.dcat_id|default:"0"
                                    f_simple=true
                                }
                            {elseif $i.item_type == "M"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="mcategories_plain"
                                    f_required=true f_integer=false
                                    f_name="`$element_name`[item_cat_id]"
                                    f_options=$mcategories_plain
                                    f_option_id="mcat_id"
                                    f_option_value="mcat_name"
                                    f_with_q_ty=true
                                    f_option_target_id=$i.item_info.mcat_id|default:"0"
                                    f_simple=true
                                }
                            {/if}
                        </td>
                        <td class="cm-non-cb">
                            {if $i.item_type == "D"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="select"
                                    f_required=true f_integer=false
                                    f_name="`$element_name`[item_id]"
                                    f_options=$i.items
                                    f_option_id="detail_id"
                                    f_option_value="format_name"
                                    f_option_target_id=$i.item_id
                                    f_simple=true
                                }
                            {elseif $i.item_type == "M"}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id=$id
                                    f_type="select"
                                    f_required=true f_integer=false
                                    f_name="`$element_name`[item_id]"
                                    f_options=$i.items
                                    f_option_id="material_id"
                                    f_option_value="format_name"
                                    f_option_target_id=$i.item_id
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
                                f_name="`$element_name`[quantity]"
                                f_value=$i.quantity|fn_fvalue
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

                        {if $detail__active}
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
                        {/if}
                        {if !$lock}
                        <td class="right cm-non-cb">
                            {include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}
                        </td>
                        {else}
                            {if $i.item_type == "M"}
                                <td align="right">
                                    {assign var="t" value=$i.items[$i.item_info.material_id].accounting_data.weight*$i.quantity}
                                    {$t|fn_fvalue:"2"} кг
                                </td>
                            {/if}
                        {/if}
                    </tr>
                </tbody>
            {/if}
        {/foreach}
    {/if}
    {if !$lock}
    {math assign="num" equation="x + 1" x=$num|default:0}
    {assign var="element_name" value="data[items][`$num`]"}
    <tbody class="hover cm-row-item" id="box_add_{$num}">
        <tr>
        <td class="cm-non-cb"></td>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="`$element_name`[di_id]"
                f_value=0
            }
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="item_type"
                f_required=true f_integer=false
                f_material=$material__active
                f_detail=$detail__active
                f_name="`$element_name`[item_type]"
                f_value=""
                f_simple=true
            }
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

        {if $detail__active}
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="typesize"
                f_class=$series_item_class
                f_name="`$element_name`[typesize]"
                f_empty=true
            }
        </td>
        {/if}
        <td class="right cm-non-cb">
            {include file="buttons/multiple_buttons.tpl" item_id="add_`$num`" tag_level="2"}
        </td>
    </tr>
    </tbody>
    {/if}

</table>
    {*<pre>{$document_items|print_r}</pre>*}
{/capture}


{* ФОРМИРОВАНИЕ ДОКУМЕНТА *}
{* 1-2. Информация о документе*}
{if $lock}
    <fieldset disabled="disabled">{$smarty.capture.document_info}</fieldset>
{else}
    {$smarty.capture.document_info}
{/if}


{* 2-2. Информация о позициях в документе*}

{if $lock}
    <fieldset disabled="disabled">{$smarty.capture.document_items}</fieldset>
{else}
    {$smarty.capture.document_items}
{/if}




