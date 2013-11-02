{strip}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="select_by_group"
    f_name=$s_name
    f_options="units"
    f_option_id="u_id"
    f_option_value="u_name"
    f_option_target_id=$s_target_u_id
    f_optgroups=$s_data
    f_optgroup_label="uc_name"
    f_disabled=$s_disabled
    f_simple=true
    f_blank=true
}
{/strip}