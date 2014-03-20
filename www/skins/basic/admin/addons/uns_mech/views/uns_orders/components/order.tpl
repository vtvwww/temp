{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="order_id"  value=0}
    {assign var="details"   value=""}
    {assign var="disabled"  value=false}

{elseif $mode == "update"}
    {assign var="order_id"    value=$order.order_id}
    {assign var="items"     value=$order.items}
    {assign var="disabled"  value=true}
{/if}

{assign var="e_n" value="data[order]"}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="hidden"
    f_name="order_id"
    f_value=$order_id}

{*РЕГИОН*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_id="region_id"
    f_type="select"
    f_required=true f_integer=true f_integer_more_0=true
    f_name="`$e_n`[region_id]"
    f_blank=true
    f_full=true
    f_options=$regions
    f_option_id="region_id"
    f_option_value="name"
    f_option_target_id=$o.region_id
    f_description="Регион"
}

{* ДАТА ОТГРУЗКИ*}
<div class="form-field">
    <label class="cm-required" for="order_dates">Дата отгрузки:</label>
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_order_date_finished_`$order_id`"
        f_type="date"
        f_required=true
        f_name="`$e_n`[date_finished]"
        f_value=$o.date_finished
        f_simple=true
    }
</div>

{*КОММЕНТАРИЙ*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_id="comment"
    f_type="textarea"
    f_row=1
    f_required=false f_integer=false
    f_full_name="`$e_n`[comment]"
    f_value=$o.comment
    f_description="Комментарий"
}

{* СТАТУС *}
<div class="form-field">
    <label class="cm-required" for="order_status">Состояние заказа:</label>
    <select name="{$e_n}[status]" id="order_status">
        <option value="">---</option>
        <option value="Open" {if $o.status == "Open"}selected="selected"{/if} {if $mode == "add"}selected="selected"{/if}>Открыт</option>
        <option value="Close" {if $o.status == "Close"}selected="selected"{/if}>Выполнен</option>
    </select>
</div>