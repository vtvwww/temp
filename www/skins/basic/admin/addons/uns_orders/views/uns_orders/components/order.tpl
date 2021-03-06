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

{assign var="e_n" value="order_data[order]"}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="hidden"
    f_name="order_id"
    f_value=$order_id}

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
    f_option_target_id=$o.country_id
    f_description="Страна"
}

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
    f_option_target_id=$o.region_id
    f_description="Регион/Область"
}

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
    f_option_target_id=$o.city_id
    f_description="Город"
}

{*КЛИЕНТ*}
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
    f_option_target_id=$o.customer_id
    f_description="Клиент"
}

{* ДАТА ОБНОВЛЕНИЯ ЗАКАЗА*}
<div class="form-field">
    <label class="cm-required" for="order_dates">Дата обновления заказа:</label>
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_order_date_updated_`$order_id`"
        f_type="date"
        f_required=true
        f_name="`$e_n`[date_updated]"
        f_value=$o.date_updated
        button_today=true
        f_simple=true
    }
</div>

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

{* Номер счета в 1С *}
<div class="form-field">
    <label class="" for="no_1s">Номер счета в 1С:</label>
    {include file="addons/uns/views/components/get_form_field.tpl"
        f_id="_order_no_1s_`$order_id`"
        f_type="input"
        f_required=false
        f_name="`$e_n`[no_1s]"
        f_value=$o.no_1s
        f_simple=true
    }
</div>

{* СТАТУС *}
<div class="form-field">
    <label class="cm-required" for="order_status">Состояние заказа:</label>
    <select class="order_status" name="{$e_n}[status]" id="order_status" onchange="$('#img_order_status').removeClass().addClass($(this).val())">
        <option value="">---</option>
        {*<option value="Hide" {if $o.status == "Hide"}selected="selected"{/if} {if $mode == "add"}selected="selected"{/if}>Скрыт - предварительный заказ</option>*}
        <option value="Open"    class="open"    {if $o.status == "Open"}selected="selected"{/if}>Открыт</option>
        {*<option value="Close"   class="close"   {if $o.status == "Close"}selected="selected"{/if}>Выполнен</option>*}
        <option value="Paid"    class="paid"    {if $o.status == "Paid"}selected="selected"{/if}>Оплачен</option>
        <option value="Shipped" class="shipped" {if $o.status == "Shipped"}selected="selected"{/if}>Отгружен</option>
    </select>
    <span id="img_order_status" class="{$o.status}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
</div>

{*КОММЕНТАРИЙ*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_id="comment"
    f_type="textarea"
    f_row=1
    f_required=false f_integer=false
    f_full_name="`$e_n`[comment]"
    f_value=$o.comment
    f_style="height:17px;"
    f_description="Комментарий"
}
