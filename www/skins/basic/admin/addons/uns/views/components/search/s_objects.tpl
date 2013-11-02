{strip}
    {assign var="all_objects" value=0|fn_uns__get_objects|array_shift}
    {assign var="objects_size" value=8}
    {*<pre>{$all_objects|print_r}</pre>*}
    <table class="search-header" cellpadding="0" cellspacing="0" width="100%"	border="0">
        <tr>
            <td width="48%" class="nowrap search-field">
                <label for="destination_object" class="cm-all">Выбранные объекты</label>
                <div class="break">
                    <select name="o_id[]" id="destination_object" size="{$objects_size}" value="" multiple="multiple" class="input-text expanded">
                        {foreach from=$all_objects item=o}
                            {if $o.o_id|in_array:$search.o_id}
                                <option	value="{$o.o_id}">{$o.o_name} [{$o.o_id}]</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </td>
            <td class="center valign" width="4%">
                <p><img src="{$images_dir}/icons/to_left_icon.gif" width="11" height="11"   onclick="{if !$lock_change}$('#all_objects').moveOptions('#destination_object'); {else} alert('Заблокировано!'); {/if}" class="hand" /></p>
                <p><img src="{$images_dir}/icons/to_right_icon.gif" width="11" height="11"  onclick="{if !$lock_change}$('#destination_object').moveOptions('#all_objects'); {else} alert('Заблокировано!'); {/if}" class="hand" /></p>
            </td>
            <td width="48%" class="nowrap search-field">
                <label for="all_objects" class="cm-all">Доступные объекты</label>
                <div class="break">
                    <select name="all_objects" id="all_objects" size="{$objects_size}" value="" multiple="multiple" class="input-text expanded">
                        {foreach from=$all_objects item=o}
                            {if !$o.o_id|in_array:$search.o_id}
                            <option	value="{$o.o_id}">{$o.o_name} [{$o.o_id}]</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </td>
        </tr>
    </table>
{/strip}
