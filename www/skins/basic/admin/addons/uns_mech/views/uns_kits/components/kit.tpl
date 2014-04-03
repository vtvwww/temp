{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="kit_id"    value=0}
    {assign var="details"   value=""}
    {assign var="disabled"  value=false}

{elseif $mode == "update"}
    {assign var="kit_id"    value=$kit.kit_id}
    {assign var="details"   value=$kit.details}
    {assign var="disabled"  value=true}
{/if}

{assign var="e_n" value="data[kit]"}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="hidden"
    f_name="kit_id"
    f_value=$kit_id}

{* Тип KIT *}
<div class="form-field">
    <label class="{if !$disabled}cm-required{/if}" for="kit_type">Тип партии:</label>
    {if $mode == "add"}
        {if $action == "pump"}
            <select name="{$e_n}[kit_type]" id="kit_type" {if $disabled}disabled="disabled"{/if}>
                <option value="P" {if $kit.kit_type == "P"}selected="selected"{/if}>Партия насоса</option>
            </select>
        {elseif $action == "details"}
            <select name="{$e_n}[kit_type]" id="kit_type" {if $disabled}disabled="disabled"{/if}>
                <option value="D" {if $kit.kit_type == "D"}selected="selected"{/if}>Партия деталей</option>
            </select>
        {/if}
    {else}
        <select name="{$e_n}[kit_type]" id="kit_type" {if $disabled}disabled="disabled"{/if}>
            <option value="">---</option>
            <option value="D" {if $kit.kit_type == "D"}selected="selected"{/if}>Партия деталей</option>
            <option value="P" {if $kit.kit_type == "P"}selected="selected"{/if}>Партия насоса</option>
        </select>
    {/if}
</div>

{if $action == "pump" or $kit.kit_type == "P"}
    {* Насос *}
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="kit_pump"
        f_type="select_by_group"
        f_name="`$e_n`[p_id]"
        f_required=true f_integer_more_0=true
        f_options="pumps"
        f_option_id="p_id"
        f_option_value="p_name"
        f_option_target_id=$kit.p_id|default:"0"
        f_optgroups=$pumps
        f_optgroup_label="ps_name"
        f_description="Насос"
        f_disabled=$disabled
        f_blank=true
    }

    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="select_range"
        f_id="kit_pump_quantity"
        f_name="`$e_n`[p_quantity]"
        f_required=true f_integer_more_0=true
        f_description="Количество"
        f_from=0
        f_to=200
        f_value=$kit.p_quantity|default:"0"
        f_disabled=$disabled
        f_plus_minus=true
    }
{/if}


{* Сроки выполнения KIT*}
<div class="form-field">
    <label class="cm-required" for="kit_dates">Дата открытия партии:</label>
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_kit_date_open_`$kit_id`"
        f_type="date"
        f_required=true
        f_name="`$e_n`[date_open]"
        f_value=$kit.date_open
        f_simple=true
        f_disabled=$disabled
    }
</div>

{*Описание KIT*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="textarea"
    f_row=1
    f_required=false f_integer=false
    f_full_name="`$e_n`[description]"
    f_value=$kit.description
    f_description="Описание"
}


<div class="form-field">
    <label for="kit_status" class="cm-required">Статус:</label>
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_type="radio_button"
        f_id="kit_status"
        f_name="`$e_n`[status]"
        f_simple=true
        f_value=$kit.status

        f1_value="K"
        f1_default=true
        f1_title="Партия комплектуется"

        f2_value="U"
        f2_title="Партия укоплектована"

        f3_value="Z"
        f3_title="Партия закрыта"
    }
</div>

