<table cellpadding="0" cellspacing="0" class="table">
<tbody>
<tr class="first-sibling">
    <th class="cm-non-cb">{$lang.name}</th>
    <th class="cm-non-cb">{$lang.value}</th>
    <th class="cm-non-cb">{$lang.uns_units_short}</th>
    <th class="cm-non-cb">&nbsp;</th>
</tr>
</tbody>
{if is__array($af__existing_features)}
    {foreach from=$af__existing_features item="ef" name="f_ef"}
    {assign var="num" value=$smarty.foreach.f_ef.iteration}
    {assign var="id" value=$ef.fi_id}
    {if $smarty.request.copy == "Y"}
        {assign var="fi_id" value=0}
    {else}
        {assign var="fi_id" value=$id}
    {/if}
    <tbody class="hover cm-row-item" id="fi_{$id}_{$num}">
    <tr>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="data[features][num]_`$num`"
                f_value=$num
            }

            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="data[features][`$num`][fi_id]"
                f_value=$fi_id
            }

            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="select"
                f_name="data[features][`$num`][feature_id]"
                f_options=$af__all_features
                f_option_id="feature_id"
                f_option_value="feature_name"
                f_option_value_add="feature_no"
                f_option_value_add_prefix=" ("
                f_option_value_add_suffix=") "
                f_option_target_id=$ef.feature_id
                f_simple=true
                f_blank=true
            }
        </td>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id=$id
                f_type="input"
                f_simple=true
                f_name="data[features][`$num`][feature_value]"
                f_value=$ef.feature_value
            }
        </td>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="select"
                f_name="data[features][`$num`][u_id]"
                f_options=$ef.target_units
                f_option_id="u_id"
                f_option_value="u_name"
                f_option_target_id=$ef.u_id
                f_simple=true
            }
        </td>

         <td class="right cm-non-cb">
            {include file="buttons/multiple_buttons.tpl" item_id="fi_`$id`_`$num`" tag_level="3" only_delete="Y"}
        </td>
    </tr>
        </tbody>
    {/foreach}
{/if}

{math equation="x + 1" assign="num" x=$num|default:0}{assign var="vr" value=""}
<tbody class="hover cm-row-item " id="box_add_fi_{$id}">
<tr>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="data[features][num]_`$num`"
            f_value=$num
        }

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="data[features][`$num`][fi_id]"
            f_value=0
        }

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_name="data[features][`$num`][feature_id]"
            f_options=$af__all_features
            f_option_id="feature_id"
            f_option_value="feature_name"
            f_option_value_add="feature_no"
            f_option_value_add_prefix=" ("
            f_option_value_add_suffix=") "
            f_option_target_id=''
            f_simple=true
            f_blank=true
        }
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="input"
            f_simple=true
            f_name="data[features][`$num`][feature_value]"
            f_value=""
        }
    </td>

    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_name="data[features][`$num`][u_id]"
            f_options=''
            f_option_id="u_id"
            f_option_value="u_name"
            f_option_target_id=''
            f_simple=true
            f_blank=true
        }

    </td>
    <td class="right cm-non-cb">
        {include file="buttons/multiple_buttons.tpl" item_id="add_fi_`$id`" tag_level="2"}
    </td>
</tr>
</tbody>
</table>
