{*{include file="addons/uns_plans/views/uns_plan_of_sales/edit_sales/statistica.tpl"}*}
<form action="{""|fn_url}" method="post" name="update_uns_units_form_{$id}" class="cm-form-highlight">
    <input type="hidden" name="data[pi_id]"     value="{$ps_plan.pi_id}" />
    <input type="hidden" name="data[item_id]"   value="{$item_id}" />
    <input type="hidden" name="data[item_type]" value="{$item_type}" />
    <input type="hidden" name="month"           value="{$month}" />
    <input type="hidden" name="year"            value="{$year}" />

    <table border="1">
        <tr>
            <td colspan="2" class="bold center">по УКРАИНЕ</td>
            <td colspan="2" class="bold center">на ЭКСПОРТ</td>
        </tr>

        {* ------ СТАТИСТИКА ПРОДАЖ ------ *}
        <tr style="background-color: #D1D1D1;">
            <td colspan="4" class="bold center">Статистика продаж за последние 2 года (<span style="font-size: 20px;">&diams;</span> - прогнозируемое значение продаж на {$tpl_curr_month})</td>
        </tr>
        <tr>
            <td colspan="2" width="460" height="300">
                <img src="skins/basic/admin/images/uns_charts/{$graphs_key}_ps_{$ps_id}_ukr.png" alt=""/>
            </td>
            <td colspan="2" width="460" height="300">
                <img src="skins/basic/admin/images/uns_charts/{$graphs_key}_ps_{$ps_id}_exp.png" alt=""/>
            </td>
        </tr>


        {* ------ ПРЕДВАРИТЕЛЬНЫЕ ЗАКАЗЫ ------ *}
        <tr style="background-color: #D1D1D1;">
            <td colspan="4" class="bold center">Предварительные заказы</td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0 10px 10px 10px;">
                {if $orders.UKR|is__array}
                    {include file="addons/uns_plans/views/uns_plan_of_sales/edit_sales/pre-orders.tpl" pre_orders=$orders.UKR}
                {/if}
            </td>
            <td colspan="2" style="padding: 0 10px 10px 10px;">
                {if $orders.EXP|is__array}
                    {include file="addons/uns_plans/views/uns_plan_of_sales/edit_sales/pre-orders.tpl" pre_orders=$orders.EXP}
                {/if}
            </td>
        </tr>


        {* ------ ФАКТИЧЕСКИЕ ПРОДАЖИ ------ *}
        <tr style="background-color: #D1D1D1;">
            <td colspan="4" class="bold center">Фактические продажи со Склада готовой продукции (Александрия)</td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0 10px 10px 10px;">
                {if $sales.UKR|is__array}
                    {include file="addons/uns_plans/views/uns_plan_of_sales/edit_sales/sales.tpl" actual_sales=$sales.UKR}
                {/if}
            </td>
            <td colspan="2" style="padding: 0 10px 10px 10px;">
                {if $sales.EXP|is__array}
                    {include file="addons/uns_plans/views/uns_plan_of_sales/edit_sales/sales.tpl" actual_sales=$sales.EXP}
                {/if}
            </td>
        </tr>

        {* ------ ПЛАН ПРОДАЖ ------ *}
        <tr style="background-color: #D1D1D1;">
            <td colspan="4" class="bold center">План продаж</td>
        </tr>
        <tr>
            <td class="bold center" style="padding: 5px;">{$tpl_curr_month}</td>
            <td class="bold center" style="padding: 5px;">{$tpl_next_month}</td>
            <td class="bold center" style="padding: 5px;">{$tpl_curr_month}</td>
            <td class="bold center" style="padding: 5px;">{$tpl_next_month}</td>
        </tr>
        <tr>
            <td class="center" style="padding: 5px;">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_required=true f_integer=false
                    f_name="data[ukr_curr]"
                    f_value=$ps_plan.ukr_curr|fn_fvalue
                    f_from=0
                    f_to=200
                    f_simple=true
                    f_track=true
                    f_plus_minus=true
                }
            </td>
            <td class="center" style="padding: 5px;">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_required=true f_integer=false
                    f_name="data[ukr_next]"
                    f_value=$ps_plan.ukr_next|fn_fvalue
                    f_from=0
                    f_to=200
                    f_simple=true
                    f_track=true
                    f_plus_minus=true
                }
            </td>
            <td class="center" style="padding: 5px;">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_required=true f_integer=false
                    f_name="data[exp_curr]"
                    f_value=$ps_plan.exp_curr|fn_fvalue
                    f_from=0
                    f_to=200
                    f_simple=true
                    f_track=true
                    f_plus_minus=true
                }
            </td>
            <td class="center" style="padding: 5px;">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_required=true f_integer=false
                    f_name="data[exp_next]"
                    f_value=$ps_plan.exp_next|fn_fvalue
                    f_from=0
                    f_to=200
                    f_simple=true
                    f_track=true
                    f_plus_minus=true
                }
            </td>
        </tr>
        <tr>
            <td class="center" colspan="4">
                Принудительная установка приоритета:
                {*FORCED_STATUS*}
                <select name="data[forced_status]">
                    <option value="N">---</option>
                    <option value="Y" style="background-color: #E3AD32;" {if $ps_plan.forced_status == "Y"}selected="selected"{/if}>Желтый</option>
                    <option value="R" style="background-color: #BB474E;" {if $ps_plan.forced_status == "R"}selected="selected"{/if}>Красный</option>
                </select>
            </td>
        </tr>
    </table>
    <div class="buttons-container">
        {include file="buttons/save_cancel.tpl" but_name="dispatch[uns_plan_of_sales.update_ps_plan]" cancel_action="close"}
    </div>
</form>
{*<br/>*}
{*<br/>*}
{*{include file="addons/uns_plans/views/uns_plan_of_sales/edit_sales/sales.tpl"}*}
