{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="document_id"       value=0}
    {assign var="document_items"    value=""}
    {assign var="disabled"          value=false}
    {assign var="date_cast_hide"    value=true}

    {*РЕАЛИЗАЦИЯ БЫСТРОГО ДОБАВЛЕНИЯ ДОКУМЕНТА*}
    {assign var="document_type"     value=$smarty.request.document_type}
    {assign var="object_to"         value=$smarty.request.object_to}
    {if $document_type|is__more_0:$object_to}
        {assign var="fast_add_document" value=true}
        {if $document_type == 8 or $document_type == 7} {*Акт изменения остатка or Расходный ордер*}
            {assign var="object_from_hide" value=true}
        {else}
        {/if}
    {/if}

{elseif $mode == "update"}
    {assign var="document_id"       value=$d.document_id}
    {assign var="document_items"    value=$d.items}
    {assign var="disabled"          value=true}
    {assign var="date_cast_hide"    value=true}
    {assign var="document_type"     value=$d.type}
    {if $d.type == $smarty.const.DOC_TYPE__VLC}
        {assign var="date_cast_hide" value=false}
    {/if}


{/if}

{assign var="e_n" value="data[document]"}

{capture name="document_info"}
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="hidden"
        f_name="document_id"
        f_value=$document_id}


    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="document_type"
        f_type="document_type"
        f_name="`$e_n`[type]"
        f_integer_more_0=true
        f_options=$document_types
        f_enabled_items=$document_types_enabled
        f_with_id=true
        f_target=$document_type
        f_disabled=$disabled
        f_blank=true
        f_description="Тип документа"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_document_date_`$document_id`"
        f_type="date"
        f_required=true
        f_name="`$e_n`[date]"
        f_value=$d.date
        f_full=true
        f_description="Дата проведения"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_date_cast_`$document_id`"
        f_type="date"
        f_required=true
        f_name="`$e_n`[date_cast]"
        f_value=$d.date_cast
        f_full=true
        f_hidden=$date_cast_hide
        f_description="Дата плавки"
    }

    {if $fast_add_document}
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="object_from"
            f_type="objects_plain"
            f_full_name="`$e_n`[object_from]"
            f_target=$d.object_from
            f_options=$objects_plain
            f_option_id="o_id"
            f_option_value="o_name"
            f_disabled=$disabled
            f_hidden=$object_from_hide
            f_description="Склад Откуда"
        }

        <div class="form-field">
            <label class="cm-required cm-integer-more-0" for="object_to">Склад Куда:</label>
            <select name="{$e_n}[object_to]" id="object_to">
                <option value="{$object_to}">{$objects_plain[$object_to].path}</option>
            </select>
        </div>
    {else}
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="object_from"
            f_type="objects_plain"
            f_full_name="`$e_n`[object_from]"
            f_required=true f_integer=true f_integer_more_0=true
            f_target=$d.object_from
            f_options=$objects_plain
            f_option_id="o_id"
            f_option_value="o_name"
            f_disabled=$disabled
            f_hidden=$object_from_hide
            f_description="Склад Откуда"
        }

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="object_to"
            f_type="objects_plain"
            f_full_name="`$e_n`[object_to]"
            f_required=true f_integer=true f_integer_more_0=true
            f_target=$d.object_to
            f_options=$objects_plain
            f_option_id="o_id"
            f_option_value="o_name"
            f_disabled=$disabled
            f_description="Склад Куда"
        }
    {/if}

    {if $mode == 'update' and $d.type == $smarty.const.DOC_TYPE__RO}
    {*Отображать только для расходного ордера*}
    <div class="form-field">
        <label class="cm-required" for="city_id">Страна / Регион / Город:</label>
        {*Страна*}
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="country_id"
            f_type="select"
            f_required=true f_integer=true f_integer_more_0=true
            f_name="`$e_n`[country_id]"
            f_blank=true
            f_full=true
            f_options=$countries
            f_option_id="id"
            f_option_value="name"
            f_option_target_id=$customers[$d.customer_id].country_id
            f_simple=true
        }
        &nbsp;/&nbsp;
        {*Регион*}
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="region_id"
            f_type="select"
            f_required=true f_integer=true f_integer_more_0=true
            f_name="`$e_n`[region_id]"
            f_blank=true
            f_full=true
            f_options=$regions
            f_option_id="id"
            f_option_value="name"
            f_option_target_id=$customers[$d.customer_id].region_id
            f_simple=true
        }
        &nbsp;/&nbsp;
        {*Город*}
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="city_id"
            f_type="select"
            f_required=true f_integer=true f_integer_more_0=true
            f_name="`$e_n`[city_id]"
            f_blank=true
            f_full=true
            f_options=$cities
            f_option_id="id"
            f_option_value="name"
            f_option_target_id=$customers[$d.customer_id].city_id
            f_simple=true
        }
    </div>

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="customer_id"
        f_type="select"
        f_required=true f_integer=true f_integer_more_0=true
        f_name="`$e_n`[customer_id]"
        f_blank=true
        f_full=true
        f_options=$customers
        f_option_id="customer_id"
        f_option_value="name"
        f_option_target_id=$d.customer_id
        f_description="Клиент"
    }
    {/if}

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id=$document_id
        f_type="document_status"
        f_full_name="`$e_n`[status]"
        f_value=$d.status
        f_description="Состояние"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="textarea"
        f_row=1
        f_required=false f_integer=false
        f_full_name="`$e_n`[comment]"
        f_value=$d.comment
        f_description="Комментарий"
    }

    {* Автоматический выбор "Наименования" *}
    {*<div class="form-field">*}
        {*<label class="" for="auto_select_name">Автомат. выбор:{include file="common_templates/tooltip.tpl" tooltip="Автоматический выбор первого элемента НАИМЕНОВАНИЯ при выборе КАТЕГОРИИ/СЕРИИ"}</label>*}
        {*<select id="auto_select_name">*}
            {*<option value="N">Нет</option>*}
            {*<option value="Y">Да</option>*}
        {*</select>*}
    {*</div>*}

{/capture}

{* ФОРМИРОВАНИЕ ДОКУМЕНТА *}
<fieldset {*disabled="disabled"*}>{$smarty.capture.document_info}</fieldset>