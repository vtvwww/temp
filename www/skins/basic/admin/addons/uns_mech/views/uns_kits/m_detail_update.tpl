<div id="content_group">
    <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
        <div id="content_general">
            <span style="display: block; font-size: 15px; font-weight: bold; margin: 0 0 10px; padding: 0; ">Насос {$pump.p_name}</span>
            <input type="hidden" name="kit_id" value="{$kit.kit_id}"/>
            <table class="simple">
                <thead>
                    <tr>
                        <th>
                            <input checked type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" />
                        </th>
                        <th>№</th>
                        <th>Наименование</th>
                        <th>Кол-во</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$details item="d" name="d"}
                        {assign var="e_n" value="kit_details[`$d.detail_id`]"}
                        <tr>
                            <td>
                                <input type="hidden"    name="{$e_n}[detail_id]" value="{$d.detail_id}"/>
                                <input type="hidden"    name="{$e_n}[state]" value="N"/>
                                <input type="checkbox" id="detail_id_{$d.detail_id}" name="{$e_n}[state]" value="Y" checked class="checkbox cm-item" />
                            </td>
                            <td align="center"><b>{$smarty.foreach.d.iteration}</b></td>
                            <td><label for="detail_id_{$d.detail_id}">{$d.detail_name}{if $d.detail_no} [{$d.detail_no}]{/if}</label></td>
                            <td>
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_range"
                                    f_name="`$e_n`[quantity]"
                                    f_id="quantity_`$add_index`"
                                    f_from=0
                                    f_to=200
                                    f_value=$d.quantity
                                    f_simple=true
                                }
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>


        </div>

        <div class="buttons-container cm-toggle-button buttons-bg">
            {if $mode == "detail_update"}
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.`$mode`]" hide_second_button=true}
            {else}
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.`$mode`]" hide_second_button=true}
            {/if}
        </div>
    </form>
</div>
<br>
{*<hr>*}
{*<pre>{$details|print_r}</pre>*}
{*<pre>{$set|print_r}</pre>*}
{*<pre>{$kit|print_r}</pre>*}
{*<pre>{$pump|print_r}</pre>*}