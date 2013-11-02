{assign var="ai" value=$ai__accounting}
{if is__array($ai)}
    {assign var="id" value=$ai.ai_id}
{else}
    {assign var="id" value=0}
{/if}
<table cellpadding="0" cellspacing="0" class="table">
    <tbody class="hover cm-row-item" id="ai_{$id}">
        <tr>
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="data[accounting][ai_id]"
                    f_value=$id
                }
                <b>{$lang.uns_accounting_main_unit}:</b>
                {include file="addons/uns/views/common_templates/select_units_by_group.tpl"
                    s_name="data[accounting][main_u_id]"
                    s_data=$ai__units
                    s_target_u_id=$ai.main_u_id
                }
                <br><span class="info_warning">Если создается деталь, то ОСНОВНОЙ ЕДИНИЦЕЙ должны быть ШТУКИ</span>
            </td>
        </tr>
        <tr>
            <td class="cm-non-cb">
                <b>{$lang.uns_accounting_extra_unit}:</b>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_simple=true
                    f_name="data[accounting][main_value]"
                    f_style="width:65px;"
                    f_value=$ai.main_value
                }

                {include file="addons/uns/views/common_templates/select_units_by_group.tpl"
                    s_disabled=true
                    s_name="data[accounting][main_u_id_duplication]"
                    s_data=$ai__units
                    s_target_u_id=$ai.main_u_id}

                <span> = </span>

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="input"
                    f_simple=true
                    f_name="data[accounting][extra_value]"
                    f_style="width:65px;"
                    f_value=$ai.extra_value
                }

                {include file="addons/uns/views/common_templates/select_units_by_group.tpl"
                    s_name="data[accounting][extra_u_id]"
                    s_data=$ai__units
                    s_target_u_id=$ai.extra_u_id
                }
            </td>
        </tr>
    </tbody>
</table>
