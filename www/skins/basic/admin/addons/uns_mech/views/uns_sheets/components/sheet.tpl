{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="sheet_id"      value=0}
    {assign var="details"       value=""}
    {assign var="disabled"      value=false}
    {assign var="sheet_no"      value=""}
    {if $smarty.request.sheet_no|is__more_0}
        {assign var="sheet_no"      value=$smarty.request.sheet_no}
    {/if}

{elseif $mode == "update"}
    {assign var="sheet_no"      value=$sheet.no}
    {assign var="sheet_id"      value=$sheet.sheet_id}
    {assign var="details"       value=$sheet.details}
    {assign var="disabled"          value=true}
    {assign var="disabled_details"  value=false}
{/if}

{assign var="e_n" value="data[sheet]"}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="hidden"
    f_name="sheet_id"
    f_value=$sheet_id}

{*Номер СЛ*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_id="sheet_no"
    f_name="`$e_n`[no]"
    f_type="input_2"
    f_number=true
    f_integer_more_0=true
    f_required=true
    f_autocomplete="off"
    f_value=$sheet_no
    f_class="input-text-short"
    f_disabled=$disabled
    f_description="Номер"
}

{*Дата выдачи СЛ*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_id="_sheet_date_`$sheet_id`"
    f_type="date"
    f_required=true
    f_name="`$e_n`[date]"
    f_value=$sheet.date_open
    f_full=true
    f_disabled=$disabled
    f_description="Дата выдачи"
    button_today=true
    button_yesterday=true
}

{*Исходный материал*}
<div class="form-field" id="material">
    <label for="material_id" class="cm-required cm-integer-more-0">Исходный материал:</label>
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="material_id_`$sheet_id`"
        f_type="mcategories_plain"
        f_required=true f_integer=false
        f_name="`$e_n`[mcat_id]"
        f_options=$mcategories_plain
        f_option_id="mcat_id"
        f_option_value="mcat_name"
        f_with_q_ty=false
        f_option_target_id=$sheet.mcat_id
        f_blank=true
        f_blank_name="---"
        f_disabled=$disabled
        f_simple=true
    }
    <select id="material_id" name="{$e_n}[material_id]" {if $disabled}disabled="disabled"{/if}>
    {if $mode == "update"}
        <option value="0">---</option>
        <option selected="selected" value="{$sheet.material_id}">{if strlen($sheet.material_no)}[{$sheet.material_no}] {/if}{$sheet.material_name}</option>
    {/if}
    </select>
    {*{if $mode == "add"}*}
        &nbsp;
        <span class="action-btn">
            <a id="get_list_details">Возможные детали</a>
        </span>
        <div id="list_details" style="margin: 10px 0"></div>
    {*{/if}*}
</div>

{*Ожидаемые ДЕТАЛИ СЛ*}
{include file="addons/uns_mech/views/uns_sheets/components/details.tpl" details=$details}

{*Статус*}
<div class="form-field">
    <label for="status_radio">Статус:</label>
    {*{if $sheet.status == "OP"}*}
        {*<b style="display: block; padding-top: 4px;">Открыт</b>*}
    {*{if $sheet.status == "OP"}*}
        {*<b style="display: block; padding-top: 4px;">Частично закрыт</b>*}
    {*{elseif $sheet.status == "CL"}*}
        {*<b style="background-color: #f4dc43;*}
        {*border: 2px solid #dbc43d;*}
        {*display: block;*}
        {*height: 21px;*}
        {*padding-left: 5px;*}
        {*padding-top: 4px;">Закрыт</b>*}
    {*{else}*}
        {*<b style="display: block; padding-top: 4px;" class="info_warning">Нет данных!</b>*}
    {*{/if}*}
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="radio_button"
        f_id="sheet_status"
        f_name="`$e_n`[status]"
        f_required=true f_integer=false
        f_simple=true
        f_value=$sheet.status

        f1_default=true
        f1_value="OP"
        f1_title="Открыт"

        f2_value="PARTIALLYCL"
        f2_title="Частично закрыт"

        f3_value="CL"
        f3_title="Закрыт"
    }
</div>

{*Тип исходного материала*}
<div class="form-field">
    <label for="{$radio_id}">Тип исх. материал:</label>
    {if $sheet.material_type == "O"}
        <b style="display: block; padding-top: 4px;">Отливка</b>
    {elseif $sheet.material_type == "M"}
        <b style="display: block; padding-top: 4px;">Металлопрокат</b>
    {else}
        <b style="display: block; padding-top: 4px;" class="info_warning">Нет данных!</b>
    {/if}

{*    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="radio_button"
        f_id="sheet_material_type"
        f_name="`$e_n`[material_type]"
        f_required=true f_integer=false
        f_simple=true
        f_value=$sheet.material_type

        f1_value="O"
        f1_title="Отливка"

        f2_value="M"
        f2_title="Металлопрокат"
    }*}
</div>

{*Объект назначения*}
<div class="form-field">
    <label for="target_object">Местоположение:</label>
    {if $sheet.target_object == 10}
        <b style="display: block; padding-top: 4px;">{$objects_plain[10].path}</b>
    {elseif $sheet.target_object == 14}
        <b style="display: block; padding-top: 4px;">{$objects_plain[14].path}</b>
    {elseif $sheet.target_object == 17}
        <b style="display: block; padding-top: 4px;">{$objects_plain[17].path}</b>
    {elseif $sheet.target_object == 18}
        <b style="display: block; padding-top: 4px;">{$objects_plain[18].path}</b>
    {elseif $sheet.target_object == 19}
        <b style="display: block; padding-top: 4px;">{$objects_plain[19].path}</b>
    {else}
        <b style="display: block; padding-top: 4px;" class="info_warning">Нет данных!</b>
    {/if}

{*    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="radio_button"
        f_id="sheet_target_object"
        f_name="`$e_n`[target_object]"
        f_required=true f_integer=false
        f_simple=true
        f_value=$sheet.target_object

        f1_value=10
        f1_title=$objects_plain[10].path

        f2_value=14
        f2_title=$objects_plain[14].path

        f3_value=17
        f3_title=$objects_plain[17].path
    }*}
</div>

{*Комментарий СЛ*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="textarea"
    f_row=1
    f_required=false f_integer=false
    f_full_name="`$e_n`[comment]"
    f_value=$sheet.comment
    f_description="Комментарий"
}