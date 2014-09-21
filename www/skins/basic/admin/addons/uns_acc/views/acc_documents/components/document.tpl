{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="document_id"       value=0}
    {assign var="document_items"    value=""}
    {assign var="disabled"          value=false}
    {assign var="date_cast_hide"    value=true}

    {if $smarty.request.fast_link == "Y"}
        {if $smarty.request.type == 1} {*Выпуск Литейного цеха*}
            {assign var="date_cast_hide"    value=false}
            {assign var="options_enabled"   value=','|explode:"7,8"}

        {elseif $smarty.request.type == 7 or $smarty.request.type == 8 or $smarty.request.type == 9} {*Расходный ордер или Акт изм. остатка или Акт списания на Литейный цех*}
            {assign var="options_enabled"   value=','|explode:"8"}
            {assign var="hidden"            value=true}
        {/if}
    {/if}


{elseif $mode == "update"}
    {assign var="document_id"       value=$d.document_id}
    {assign var="document_items"    value=$d.items}
    {assign var="disabled"          value=true}
    {assign var="date_cast_hide"    value=true}
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
        f_with_id=true
        f_target=$d.type|default:$smarty.request.type
        f_disabled=$disabled
        f_enabled_items=$document_types_enabled
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

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="object_from"
        f_type="objects_plain"
        f_full_name="`$e_n`[object_from]"
        f_required=true f_integer=true f_integer_more_0=true
        f_target=$d.object_from|default:$smarty.request.object_from
        f_options_enabled=$options_enabled
        f_options=$objects_plain
        f_option_id="o_id"
        f_option_value="o_name"
        f_view_id=true
        f_disabled=$disabled
        f_hidden=$hidden
        f_description="Склад Откуда"
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="object_to"
        f_type="objects_plain"
        f_full_name="`$e_n`[object_to]"
        f_required=true f_integer=true f_integer_more_0=true
        f_target=$d.object_to|default:$smarty.request.object_to
        f_options_enabled=$options_enabled
        f_options=$objects_plain
        f_option_id="o_id"
        f_option_value="o_name"
        f_view_id=true
        f_disabled=$disabled
        f_description="Склад Куда"
    }

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
{/capture}

{* ФОРМИРОВАНИЕ ДОКУМЕНТА *}
<fieldset {*disabled="disabled"*}>{$smarty.capture.document_info}</fieldset>
{*<fieldset disabled="disabled">{$smarty.capture.document_items}</fieldset>*}
