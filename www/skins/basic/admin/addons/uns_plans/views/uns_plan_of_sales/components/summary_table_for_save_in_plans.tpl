{if is__array($pump_series)}
<span style="page-break-before:always;"></span>
<span style="font-size: 14px; font-weight: bold;">Сводная таблица <u>Плана продаж</u> на {$months[$search.month]} {$search.year} г. для сохранения в планах</span>
    <form method="post" action="{""|fn_url}">
        {assign var="e_n" value="data[plan]"}

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="`$e_n`[type]"
            f_value="sales"}

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="`$e_n`[override]"
            f_value="Y"}

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="`$e_n`[month]"
            f_value=$search.month}

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="`$e_n`[year]"
            f_value=$search.year}

        <table cellpadding="0" cellspacing="0" border="0" class="simple" style="margin: 10px; 0">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Наименование</th>
                    <th>
                        Украина<br>
                        на {$months[$search.month]} {$search.year} г.
                    </th>
                    <th>
                        Украина<br>
                        на след. мес.
                    </th>
                    <th>
                        Экспорт<br>
                        на {$months[$search.month]} {$search.year} г.
                    </th>
                    <th>
                        Экспорт<br>
                        на след. мес.
                    </th>
                </tr>
            </thead>
            <tbody>
                {*{assign var="total_plan_prodazh_calc" value=0}*}
                {*{assign var="total_potrebnost" value=0}*}
                {foreach from=$pump_series item="ps" key="id" name="ps"}
                    {*{assign var="total_plan_prodazh_calc" value=$total_plan_prodazh_calc+$plan[$ps.ps_id].plan_prodazh_calc}*}
                    {*{assign var="total_potrebnost" value=$total_potrebnost+$plan[$ps.ps_id].potrebnost}*}
                    {assign var="e_n" value="data[plan_items][`$smarty.foreach.ps.index`]"}
                    <tr>
                        <td align="center">
                            {$smarty.foreach.ps.iteration}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[pi_id]"
                                f_value=0}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[item_type]"
                                f_value="S"}
                        </td>
                        <td>
                            {$ps.ps_name}
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="hidden"
                                f_name="`$e_n`[item_id]"
                                f_value=$ps.ps_id}
                        </td>
                        <td align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[ukr_curr]"
                                f_value=""
                                f_style="width: 50px;text-align: center; font-weight: bold;"
                                f_autocomplete="off"
                                f_simple=true
                            }
                        </td>
                        <td align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[ukr_next]"
                                f_value=""
                                f_style="width: 50px;text-align: center; font-weight: bold;"
                                f_autocomplete="off"
                                f_simple=true
                            }
                        </td>
                        <td align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[exp_curr]"
                                f_value=""
                                f_style="width: 50px;text-align: center; font-weight: bold;"
                                f_autocomplete="off"
                                f_simple=true
                            }
                        </td>
                        <td align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="input"
                                f_required=true f_integer=false
                                f_name="`$e_n`[exp_next]"
                                f_value=""
                                f_style="width: 50px;text-align: center; font-weight: bold;"
                                f_autocomplete="off"
                                f_simple=true
                            }
                        </td>
                    </tr>
                {/foreach}
                <tr>
                    <td align="right" colspan="6">
                        <span class="submit-button cm-button-main ">
                            <input type="submit" value="Сохранить" name="dispatch[uns_plan_of_sales.update]">
                        </span>
                    </td>
                </tr>
            </tbody>

        </table>

    </form>
{/if}
