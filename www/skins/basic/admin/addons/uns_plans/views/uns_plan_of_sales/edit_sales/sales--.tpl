<h3 style="background-color: #ABABAB;margin: 0;padding: 0 10px 2px;">Редактирование плана продаж</h3>
<form action="{""|fn_url}" method="post" name="update_uns_units_form_{$id}" class="cm-form-highlight">
    <input type="hidden" name="data[pi_id]"     value="{$ps_plan.pi_id}" />
    <input type="hidden" name="data[item_id]"   value="{$item_id}" />
    <input type="hidden" name="data[item_type]" value="{$item_type}" />
    <input type="hidden" name="month"           value="{$month}" />
    <input type="hidden" name="year"            value="{$year}" />

    <table cellspacing="0" cellpadding="0" class="table">
        <tbody>
            <tr style="background-color: #F3F3F3;" class="first-sibling">
                <th width="160px" class="cm-non-cb center" colspan="2">{$tpl_curr_month}</th>
                <th width="160px" class="cm-non-cb center b3_l" colspan="2">{$tpl_next_month}</th>
            </tr>
            <tr style="background-color: #F3F3F3;" class="first-sibling">
                <th width="80px" class="cm-non-cb center b1_t">Украина</th>
                <th width="80px" class="cm-non-cb center b1_l b1_t">Экспорт</th>
                <th width="80px" class="cm-non-cb center b3_l b1_t">Украина</th>
                <th width="80px" class="cm-non-cb center b1_l b1_t">Экспорт</th>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td class="b2_t">
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
                <td class="b1_l b2_t">
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
                <td class="b3_l b2_t">
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
                <td class="b1_l b2_t">
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
            <tr style="background-color: #F3F3F3;" class="first-sibling">
                <th colspan="2" class="bold center b2_t">{$ps_plan.ukr_curr+$ps_plan.exp_curr}</th>
                <th colspan="2" class="bold center b2_t b3_l">{$ps_plan.ukr_next+$ps_plan.exp_next}</th>
            </tr>
        </tbody>

    </table>

    <div class="buttons-container">
        {include file="buttons/save_cancel.tpl" but_name="dispatch[uns_plan_of_sales.update_ps_plan]" cancel_action="close"}
    </div>
</form>
<br/>
<br/>