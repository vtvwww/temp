{if $smarty.request.copy == "Y"}
    {assign var="ai_id" value=0}
{else}
    {assign var="ai_id" value=$id}
{/if}

<table cellpadding="0" cellspacing="0" class="table">
    <tbody class="hover cm-row-item" id="ai_{$id_main}">
        <tr>
            <td class="cm-non-cb">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="hidden"
                    f_name="data[accounting][ai_id]"
                    f_value=$ai_id
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id="accounting_u_id_`$ai_id`"
                    f_type="select_by_group"
                    f_name="data[accounting][u_id]"
                    f_required=true f_integer_more_0=true
                    f_options="units"
                    f_option_id="u_id"
                    f_option_value="u_name"
                    f_option_target_id=$ai_main.u_id
                    f_optgroups=$ai__units
                    f_optgroup_label="uc_name"
                    f_description=$lang.uns_accounting_main_unit
                    f_blank=true
                }

                <br><br><span class="info_warning">Если создается <b>ДЕТАЛЬ</b> или <b>ЧУГУННАЯ ОТЛИВКА</b>, то основной единицей должны быть <b>шт</b></span>
            </td>
        </tr>
    </tbody>
</table>