{strip}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            <td class="nowrap search-field">
                <input type="hidden" name="item_type" value="D">
                <label>{$lang.uns_detail_categories}:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="dcategories_plain"
                        f_required=true f_integer=false
                        f_name="dcat_id"
                        f_options=$dcategories_plain
                        f_option_id="dcat_id"
                        f_option_value="dcat_name"
                        f_option_target_id=$search.dcat_id
                        f_simple=true
                        f_blank=true
                        f_with_q_ty=false
                        f_blank_name="---"
                    }
                </div>
            </td>
            <td class="nowrap search-field b1_l">
                <label>Наименование детали:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="detail_name"
                        f_value=$search.detail_name
                        f_style="width:150px;"
                        f_simple=true
                    }
                </div>
            </td>
            <td class="nowrap search-field b1_l">
                <label>Номер чертежа:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="detail_no"
                        f_value=$search.detail_no
                        f_style="width:150px;"
                        f_simple=true
                    }
                </div>
            </td>
            <td class="nowrap search-field b1_l">
                <label for="all_details">Отобразить все детали:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id="all_details"
                        f_name="all_details"
                        f_value=$search.all_details
                        f_simple=true
                    }
                </div>
            </td>
        </tr>
    </table>
{/strip}