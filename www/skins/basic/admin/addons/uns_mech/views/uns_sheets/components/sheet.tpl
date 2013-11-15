{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="sheet_id"      value=0}
    {assign var="details"       value=""}
    {assign var="disabled"      value=false}

{elseif $mode == "update"}
    {assign var="sheet_id"      value=$sheet.sheet_id}
    {assign var="details"       value=$sheet.details}
    {assign var="disabled"      value=true}
{/if}

{assign var="e_n" value="data[sheet]"}

{capture name="document_info"}
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
        f_value=$sheet.no
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
    </div>

    {*Ожидаемые ДЕТАЛИ СЛ*}
    {include file="addons/uns_mech/views/uns_sheets/components/details.tpl" details=$details}

    {*Статус*}
    <div class="form-field">
        <label for="status">Статус:</label>
        <select name="{$e_n}[status]" id="status">
              <option {if $sheet.status == "OP"}selected="selected"{/if} value="OP">&nbsp;Открыт (т.е. детали cейчас обрабатываются)</option>
              <option {if $sheet.status == "CL"}selected="selected"{/if} value="CL">&nbsp;Закрыт (т.е. детали уже обработаны и переданы на Склад комплектующих)</option>
        </select>
    </div>

    {*Тип исходного материала*}
    <div class="form-field">
        <label for="material_type">Тип исх. материал:</label>
        <select name="{$e_n}[material_type]" id="material_type">
              <option {if $sheet.material_type == "O"}selected="selected"{/if} value="O">Отливка</option>
              <option {if $sheet.material_type == "M"}selected="selected"{/if} value="M">Металлопрокат</option>
        </select>
    </div>

    {*Объект назначения*}
    <div class="form-field">
        <label for="target_object">Местоположение:</label>
        <select name="{$e_n}[target_object]" id="target_object">
              <option {if $sheet.target_object == "10"}selected="selected"{/if} value="10">Мех. цех 1</option>
              <option {if $sheet.target_object == "14"}selected="selected"{/if} value="14">Мех. цех 2</option>
              <option {if $sheet.target_object == "17"}selected="selected"{/if} value="17">Скл. КМП</option>
        </select>
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
{/capture}

{* ФОРМИРОВАНИЕ ДОКУМЕНТА *}
<fieldset {*disabled="disabled"*}>{$smarty.capture.document_info}</fieldset>
{*<fieldset disabled="disabled">{$smarty.capture.document_items}</fieldset>*}
