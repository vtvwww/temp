<b><p>Указать исходный материал для изготовления детали</p></b>
<table cellpadding="0" cellspacing="0" class="table">
<tbody>
<tr class="first-sibling">
    <th class="cm-non-cb">{$lang.uns_class}</th>
    <th class="cm-non-cb">{$lang.uns_category}</th>
    <th class="cm-non-cb">{$lang.name}</th>
    <th class="cm-non-cb">{$lang.amount}</th>
    <th class="cm-non-cb">&nbsp;</th>
</tr>
</tbody>

{if is__array($ao__existing_options)}
    {foreach from=$ao__existing_options item="eo" name="f_eo"}
    {assign var="num" value=$smarty.foreach.f_eo.iteration}
    {assign var="id" value=$eo.fi_id}
    <tbody class="hover cm-row-item" id="oi_{$id}_{$num}">
    <tr>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="data[options][`$num`][oi_id]"
                f_value=$eo.oi_id
            }

            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="select"
                f_name="data[options][`$num`][option_id]"
                f_options=$ao__all_options
                f_option_id="option_id"
                f_option_value="option_name"
                f_option_target_id=$eo.option_id
                f_simple=true
            }
        </td>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="select"
                f_name="data[options][`$num`][ov_id]"
                f_options=$eo.variants
                f_option_id="ov_id"
                f_option_value="ov_value"
                f_option_target_id=$eo.ov_id
                f_simple=true
            }
        </td>

         <td class="right cm-non-cb">
            {include file="buttons/multiple_buttons.tpl" item_id="oi_`$id`_`$num`" tag_level="3" only_delete="Y"}
        </td>
    </tr>
        </tbody>
    {/foreach}
{/if}
{math equation="x + 1" assign="num" x=$num|default:0}{assign var="vr" value=""}
<tbody class="hover cm-row-item " id="box_add_oi_{$id}">
<tr>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="data[options][`$num`][oi_id]"
            f_value=0
        }

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_name="data[options][`$num`][option_id]"
            f_options=$ao__all_options
            f_option_id="option_id"
            f_option_value="option_name"
            f_option_target_id=0
            f_simple=true
            f_blank=true
        }
    </td>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="select"
            f_name="data[options][`$num`][ov_id]"
            f_options=''
            f_option_id="ov_id"
            f_option_value="ov_value"
            f_option_target_id=0
            f_simple=true
            f_blank=true
        }
    </td>
    <td class="right cm-non-cb">
        {include file="buttons/multiple_buttons.tpl" item_id="add_oi_`$id`" tag_level="2"}
    </td>
</tr>
</tbody>
</table>
