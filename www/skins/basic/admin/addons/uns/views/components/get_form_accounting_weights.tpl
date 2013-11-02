{if (($typesize == $smarty.const.UNS_TYPESIZE__A) or ($typesize == $smarty.const.UNS_TYPESIZE__B))}
    {if $typesize_status == "D" or $typesize_status == "A"}
    {else}
        {assign var="typesize_status" value="D"}
    {/if}

    {assign value=$typesize_status}
    <div style="text-align: center; margin: 10px; height: 28px;">
        {assign var="typesize_id" value="typesize_status_`$typesize`"}
        <label for="{$typesize_id}">Статус:</label>
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="typesize_status_`$typesize`"
            f_type="status"
            f_required=true f_integer=false
            f_name="data[accounting][typesizes][`$typesize`]"
            f_value=$typesize_status
            f_simple=true
        }
    </div>
{/if}

{assign var="field_prefix" value="data[accounting][weights][`$typesize`]"}

<table cellpadding="0" cellspacing="0" class="table {if $typesize_status == "D"} hidden {/if} {$typesize} " >
<tbody>
<tr class="first-sibling">
    <th class="cm-non-cb">{$lang.date}</th>
    <th class="cm-non-cb">{$lang.weight}, кг</th>
    <th class="cm-non-cb">&nbsp;</th>
</tr>
</tbody>
{if is__array($ws.$typesize)}
    {foreach from=$ws.$typesize item="w" name="f_w"}
    {assign var="num" value=$smarty.foreach.f_w.iteration}
    {assign var="id" value=$w.aw_id}
    {if $smarty.request.copy == "Y"}
        {assign var="aw_id" value=0}
    {else}
        {assign var="aw_id" value=$id}
    {/if}
    {if is__more_0($id)}
    <tbody class="hover cm-row-item" id="aw_{$id}_{$num}">
    <tr>
        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="`$field_prefix`[`$num`][aw_id]"
                f_value=$aw_id
            }
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="`$field_prefix`[`$num`][typesize]"
                f_value=$typesize
            }

            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id=$id
                f_type="date"
                f_required=true f_integer=false
                f_name="`$field_prefix`[`$num`][timestamp]"
                f_value=$w.timestamp
                f_simple=true
            }
        </td>

        <td class="cm-non-cb">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id=$id
                f_type="input"
                f_required=true f_integer=false
                f_name="`$field_prefix`[`$num`][value]"
                f_value=$w.value
                f_simple=true
            }

        </td>

         <td class="right cm-non-cb">
            {include file="buttons/multiple_buttons.tpl" item_id="aw_`$id`_`$num`" tag_level="3" only_delete="Y"}
        </td>
    </tr>
        </tbody>
    {/if}
    {/foreach}
{/if}
{math equation="x + 1" assign="num" x=$num|default:0}
<tbody class="hover cm-row-item " id="box_add_aw_{$id}">
<tr>
    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="`$field_prefix`[`$num`][aw_id]"
            f_value=0
        }

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id=$id
            f_type="date"
            f_required=true f_integer=false
            f_name="`$field_prefix`[`$num`][timestamp]"
            f_value=time()
            f_simple=true
        }
    </td>

    <td class="cm-non-cb">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id=$id
            f_type="input"
            f_required=true f_integer=false
            f_name="`$field_prefix`[`$num`][value]"
            f_value=""
            f_simple=true
        }
    <td class="right cm-non-cb">
        {include file="buttons/multiple_buttons.tpl" item_id="add_aw_`$id`" tag_level="2"}
    </td>
</tr>
</tbody>
</table>
