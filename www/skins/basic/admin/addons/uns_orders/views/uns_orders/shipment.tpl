{assign var="m_mode" value=$action}
<form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
    <div class="data-block top">
        <input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
        {if $m_mode == 'add'}
            <input type="hidden" name="action"                           value="{$m_mode}" />
            <input type="hidden" name="document_type"                    value="{$document_type}" />
            <input type="hidden" name="document_id"                      value="0" />
            <input type="hidden" name="shipment[document][type]"         value="{$document_type}" />
            <input type="hidden" name="shipment[document][package_id]"   value="{$kit.kit_id}" />
            <input type="hidden" name="shipment[document][package_type]" value="PN" />
        {else}
            <input type="hidden" name="document_id"                      value="{$shipment.document_id}" />
            <input type="hidden" name="shipment[document][order_id]"     value="{$order.order_id}" />
            <input type="hidden" name="shipment[document][customer_id]"  value="{$order.customer_id}" />
            <input type="hidden" name="shipment[document][document_id]"  value="{$shipment.document_id}" />
            <input type="hidden" name="shipment[document][package_id]"   value="{$shipment.package_id}" />
            <input type="hidden" name="shipment[document][package_type]" value="{$shipment.package_type}" />
        {/if}

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="shipment_comment"
            f_type="textarea"
            f_required=false f_integer=false
            f_full_name="shipment[document][comment]"
            f_value=$shipment.comment
            f_row=1
            f_style="height:17px; width:400px;"
            f_description="Комментарий"
        }

        {* ДАТА *}
        <div class="form-field">
            {if $m_mode == 'add'}
                {assign var="shipment_date" value=$smarty.const.TIME}
            {else}
                {assign var="shipment_date" value=$shipment.date}
            {/if}
            <label for="shipment_date" class="cm-required cm-integer-more-0">Дата:</label>
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id="_shipment_date_`$shipment.document_id`"
                f_type="date"
                f_required=true
                f_name="shipment[document][date]"
                f_value=$shipment.date
                f_simple=true
            }
        </div>

        {* СТАТУС *}
        <div class="form-field hidden">
            <label for="shipment_status" class="cm-required">Статус:</label>
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id="shipment_status"
                f_type="status"
                f_required=true f_integer=false
                f_name="shipment[document][status]"
                f_value=$shipment.status
                f_simple=true
            }
        </div>

        {* ОТКУДА *}
        <div class="form-field hidden">
            <label for="object_from" class="">Откуда:</label>
            <select name="shipment[document][object_from]" id="object_from">
                <option selected="selected" value="0">0</option>
            </select>
        </div>

        {* КУДА *}
        <div class="form-field hidden">
            <label for="object_to" class="cm-required cm-integer-more-0">Откуда:</label>
            <select name="shipment[document][object_to]" id="object_to">
                <option {if $shipment.object_to == 19}selected="selected"{/if} value="19">Склад Гот. прод.</option>
                {*<option {if $shipment.object_to == 25}selected="selected"{/if} value="25">Склад Гот. прод. (Днепр.)</option>*}
            </select>
        </div>

        {* ДЕТАЛЬ *}
        <div class="form-field">
            <label for="detail_id" class="cm-required cm-integer-more-0">Деталь:</label>
            <table class="table" border="0" cellspacing="0" cellpadding="0">
            {*<table class="simple">*}
                <thead>
                    <tr style="background-color: #EDEDED;">
                        <th class="center">№</th>
                        <th class="center b1_l">Наименование</th>
                        <th class="center b1_l">Отгружено, шт</th>
                        {*<th class="center b1_l">{include file="common_templates/tooltip.tpl" tooltip="Количество отгруженных насосов и/или деталей" tooltip_mark="<b>Кол-во, шт</b>"}</th>*}
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$shipment.items item="i" name="i"}
                        {assign var="num" value=$smarty.foreach.i.iteration}
                        {assign var="e_n" value="order_data[document_items][`$num`]"}
                        {assign var="id" value=$i.di_id}
                        <tr>
                            <td align="center" class="">
                                <input type="hidden"    name="{$e_n}[di_id]"        value="{$i.di_id}"/>
                                <input type="hidden"    name="{$e_n}[item_id]"      value="{$i.item_id}"/>
                                <b>{$smarty.foreach.i.iteration}</b>
                            </td>
                            <td class="left b1_l">
                                {if $i.item_type == "D"}
                                    {$i.item_info.detail_name} {if $i.item_info.detail_no|strlen}[{$i.item_info.detail_no}]{/if}
                                {elseif $i.item_type == "P" or $i.item_type == "PF" or $i.item_type == "PA"}
                                    {$i.item_info.p_name} {if $i.item_type == "PF"}на раме{elseif $i.item_type == "PA"}агрегат{/if}
                                {/if}
                            </td>
                            <td class="center b1_l">
                                {$i.quantity|intval}
                                {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                    {*f_type="select_range"*}
                                    {*f_name="`$e_n`[quantity]"*}
                                    {*f_id=""*}
                                    {*f_from=0*}
                                    {*f_to=200*}
                                    {*f_value=$i.quantity*}
                                    {*f_simple=true*}
                                    {*f_plus_minus=true*}
                                {*}*}
                            </td>
                            {*<td class="right cm-non-cb b1_l">*}
                                {*{include file="buttons/multiple_buttons.tpl" item_id="`$id`_`$num`" tag_level="3" only_delete="Y"}*}
                            {*</td>*}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    <br>
    {*<div class="buttons-container cm-toggle-button buttons-bg">*}
        {*{if $m_mode == "add"}*}
            {*{include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.shipment.update]" hide_second_button=true mode=$m_mode}*}
        {*{else}*}
            {*{include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.shipment.update]" hide_second_button=true mode=$m_mode}*}
        {*{/if}*}
    {*</div>*}
</form>