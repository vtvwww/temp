{include file="common_templates/subheader.tpl" title="Комплектность"}
{*Добавить деталь*}
&nbsp;
<span class="action-add">
        {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="detail_add"
                    text="Добавить деталь"
                    act="edit"
                    link_text="Добавить деталь"
                    href="`$controller`.detail_update?mode=add&kit_id=`$kit.kit_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}
</span>
{if $kit.kit_type == "P"}{*Насос*}
&nbsp;&nbsp;&nbsp;
<span class="action-add">
        {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="details_add"
                    text="Добавить комплектацию насоса"
                    act="edit"
                    link_text="Добавить комплектацию насоса"
                    href="`$controller`.m_detail_update?kit_id=`$kit.kit_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}
</span>
{/if}

<div style="margin: 10px;">
    {if $i.details|is__array}
    <table class="simple">
        <thead>
            <tr>
                <th rowspan="3">&nbsp;</th>
                <th rowspan="3">Наименование</th>
                <th rowspan="2" colspan="2">Требуемое<br>количество</th>
                <th rowspan="3" width="70px" style="padding: 0px; margin: 0px;">Отложено<br>на Сб. уч.</th>
                <th colspan="4" style="border-left: 2px solid  black; border-right: 2px solid  black;">Мех. цех</th>
                <th rowspan="3" style="border-left: 2px solid  black;">Скл.<br>КМП.</th>
                <th rowspan="3" style="border-left: 2px solid  black;">&nbsp;</th>
            </tr>
            <tr>
                <th colspan="2" style="border-left: 2px solid  black;">№1</th>
                <th colspan="2" style="border-left: 2px solid  black;">№2</th>
            </tr>
            <tr>
                <th>на 1 ед.</th>
                <th>на партию</th>
                <th style="min-width: 20px; border-left: 2px solid  black;">О</th>
                <th style="min-width: 20px;">З</th>
                <th style="min-width: 20px; border-left: 2px solid  black;">О</th>
                <th style="min-width: 20px;">З</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$i.details item="d" name="d"}
            <tr>
                {assign var="id" value=$d.pd_id}
                {assign var="value" value="pd_id"}
                {assign var="d_name" value="`$d.detail_name`"}
                {assign var="d_q" value=$d.quantity|fn_fvalue}
                {if $d.detail_no}
                    {assign var="d_name" value="`$d_name` [`$d.detail_no`]"}
                {/if}
                <td>{$smarty.foreach.d.iteration}</td>
                <td>
                    {include    file="common_templates/table_tools_list.tpl"
                                popup=true
                                id="`$d.pd_id`"
                                text=$d_name
                                act="edit"
                                link_text=$d_name
                                href="`$controller`.detail_update?kit_id=`$kit.kit_id`&`$value`=`$id`"
                                prefix=$id
                                link_class="cm-dialog-auto-size black"
                                tools_list=$smarty.capture.tools_items}

                </td>

                {*Кол-во на 1 ед.*}
                <td align="center">{$d_q}</td>

                {*Кол-во на партию*}
                <td align="center">
                    {assign var="k_q" value=$d.quantity}
                    {if $kit.kit_type == "D"}
                        {*Детали*}
                        {$d.quantity|fn_fvalue}
                    {elseif $kit.kit_type == "P"}
                        {*Насос*}
                        {assign var="k_q" value=$k_q*$kit.p_quantity}
                        {$k_q|fn_fvalue}
                    {/if}
                </td>

                {*-------------------------------------------------------------*}
                {* Отложено на Сб. Уч.*}
                {foreach from=$balances[18] item="b_group"}
                    {foreach from=$b_group.items item="b_item"}
                        {if $b_item.detail_id == $d.detail_id}
                            {math equation="-140+70*x/y" x=$b_item.konech y=$k_q assign="pos"}
                            <td align="center" style="font-weight: bold; background-image: url('images/uns/bar.png'); background-position: {$pos}px center;">
                                {$b_item.konech}
                            </td>
                        {/if}
                    {/foreach}
                {/foreach}

                {*-------------------------------------------------------------*}
                {foreach from=$balances[10] item="b_group"}
                    {foreach from=$b_group.items item="b_item"}
                        {if $b_item.detail_id == $d.detail_id}
                            {*МЕХ ЦЕХ - №1*}
                            <td align="center" style="border-left: 2px solid  black;">
                                {assign var="v" value=$b_item.processing}
                                <span class="{if $v<0}info_warning_block{elseif $v==0}zero{/if}">{$v|fn_fvalue:2}</span>
                            </td>
                            <td  align="center" style="border-left: 2px dashed #808080;">
                                {*{if $b_item.complete|fn_fvalue:0:0 > 0}*}
                                    {*<form action="{""|fn_url}" method="post">*}
                                        {*<input type="hidden" name="kit_id"      value="{$kit.kit_id}"/>*}
                                        {*<input type="hidden" name="detail_id"   value="{$d.detail_id}"/>*}
                                        {*<input type="hidden" name="object_from" value="10"/>*}
                                        {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                            {*f_type="select_range"*}
                                            {*f_name="quantity"*}
                                            {*f_id=""*}
                                            {*f_from=0*}
                                            {*f_to=$b_item.complete|fn_fvalue:0:0*}
                                            {*f_value=$b_item.complete|fn_fvalue:0:0*}
                                            {*f_style="min-width: 55px;"*}
                                            {*f_simple=true*}
                                        {*}*}
                                        {*<input type="image" src="images/uns/add-green.png" name="dispatch[uns_kits.detail_mcp]" />*}
                                    {*</form>*}
                                {*{else}*}
                                    {assign var="v" value=$b_item.complete}
                                    <span class="{if $v<0}info_warning_block{elseif $v==0}zero{/if}">{$v|fn_fvalue:2}</span>
                                {*{/if}*}
                            </td>
                        {/if}
                    {/foreach}
                {/foreach}

                {foreach from=$balances[14] item="b_group"}
                    {foreach from=$b_group.items item="b_item"}
                        {if $b_item.detail_id == $d.detail_id}
                            {*МЕХ ЦЕХ - №2*}
                            <td align="center" style="border-left: 2px solid  black;">
                                {assign var="v" value=$b_item.processing}
                                <span class="{if $v<0}info_warning_block{elseif $v==0}zero{/if}">{$v|fn_fvalue:2}</span>
                            </td>
                            <td align="center" style="border-left: 2px dashed #808080;">
                                {*{if $b_item.complete|fn_fvalue:0:0 > 0}*}
                                    {*<form action="{""|fn_url}" method="post">*}
                                        {*<input type="hidden" name="kit_id"      value="{$kit.kit_id}"/>*}
                                        {*<input type="hidden" name="detail_id"   value="{$d.detail_id}"/>*}
                                        {*<input type="hidden" name="object_from" value="14"/>*}
                                        {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                            {*f_type="select_range"*}
                                            {*f_name="quantity"*}
                                            {*f_id=""*}
                                            {*f_from=0*}
                                            {*f_to=$b_item.complete|fn_fvalue:0:0*}
                                            {*f_value=$b_item.complete|fn_fvalue:0:0*}
                                            {*f_style="min-width: 55px;"*}
                                            {*f_simple=true*}
                                        {*}*}
                                        {*<input type="image" src="images/uns/add-green.png" name="dispatch[uns_kits.detail_mcp]" />*}
                                    {*</form>*}
                                {*{else}*}
                                    {assign var="v" value=$b_item.complete}
                                    <span class="{if $v<0}info_warning_block{elseif $v==0}zero{/if}">{$v|fn_fvalue:2}</span>
                                {*{/if}*}
                            </td>
                        {/if}
                    {/foreach}
                {/foreach}

                {foreach from=$balances[17] item="b_group"}
                    {foreach from=$b_group.items item="b_item"}
                        {if $b_item.detail_id == $d.detail_id}
                            {*Склад КМП*}
                            <td align="center" style="border-left: 2px solid  black; border-right: 2px solid  black;">
                                {*{if $b_item.konech|fn_fvalue:0:0 > 0}*}
                                    {*<form action="{""|fn_url}" method="post">*}
                                        {*<input type="hidden" name="kit_id"      value="{$kit.kit_id}"/>*}
                                        {*<input type="hidden" name="detail_id"   value="{$d.detail_id}"/>*}
                                        {*<input type="hidden" name="object_from" value="17"/>*}
                                        {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                            {*f_type="select_range"*}
                                            {*f_name="quantity"*}
                                            {*f_id=""*}
                                            {*f_from=0*}
                                            {*f_to=$b_item.konech|fn_fvalue:0:0*}
                                            {*f_value=$b_item.konech|fn_fvalue:0:0*}
                                            {*f_style="min-width: 55px;"*}
                                            {*f_simple=true*}
                                        {*}*}
                                        {*<input type="image" src="images/uns/add-green.png" name="dispatch[uns_kits.detail_mcp]" />*}
                                    {*</form>*}
                                {*{else}*}
                                    {assign var="v" value=$b_item.konech}
                                    <span class="{if $v<0}info_warning_block{elseif $v==0}zero{/if}">{$v|fn_fvalue:2}</span>
                                {*{/if}*}
                            </td>
                        {/if}
                    {/foreach}
                {/foreach}
                {*-------------------------------------------------------------*}
                <td>{include file="uns/buttons/delete.tpl" href="`$controller`.detail_delete?kit_id=`$kit.kit_id`&detail_id=`$d.detail_id`" confirm_message="Удалить `$d_name` - `$d_q` шт."}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
</div>
{*<pre>{$balances|print_r}</pre>*}