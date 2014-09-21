{strip}
    <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th style="text-align: center;" width="300px">Движение</th>
                <th style="text-align: center;" width="5px">&nbsp;</th>
                <th class="b1_l" style="text-align: center;" width="40px">{include file="common_templates/tooltip.tpl" tooltip='Начальный остаток'  tooltip_mark="<b>НО</b>"}</th>
                <th class="b1_l" style="text-align: center;" width="40px">{include file="common_templates/tooltip.tpl" tooltip='Приход'  tooltip_mark="<b>П</b>"}</th>
                <th class="b1_l" style="text-align: center;" width="40px">{include file="common_templates/tooltip.tpl" tooltip='Расход'  tooltip_mark="<b>Р</b>"}</th>
                <th class="b2_l" style="text-align: center;" width="40px">{include file="common_templates/tooltip.tpl" tooltip='Конечный остаток'  tooltip_mark="<b>КО</b>"}</th>
            </tr>
        </thead>
        {if is__array($motions)}
            {assign var="no"    value=0}
            {assign var="p"     value=0}
            {assign var="r"     value=0}
            {assign var="ko"    value=$params.nach}

            {foreach from=$motions item=m}
                {assign var="document_href" value="uns_moving_stores.update&document_id=`$m.document_id`"|fn_url}

                {* РАСЧЕТ ДВИЖЕНИЯ *}
                {assign var="no"    value=$ko}

                {if $m.type == 'VLC' or $m.type == 'PO' }
                    {assign var="p"     value=$m.quantity}
                    {assign var="r"     value=0}
                {elseif $m.type == 'MCP' or $m.type == 'RO' or $m.type == 'AS_VLC' or $m.type == 'PVP'}
                    {assign var="p"     value=0}
                    {assign var="r"     value=$m.quantity}
                {elseif $m.type == 'AIO'}
                    {if $m.change_type == 'POZ' and $m.motion_type == 'I'}
                        {assign var="p"     value=$m.quantity}
                        {assign var="r"     value=0}
                    {elseif $m.change_type == 'NEG' and $m.motion_type == 'O'}
                        {assign var="p"     value=0}
                        {assign var="r"     value=$m.quantity}
                    {/if}
                {/if}
                {assign var="ko"    value=$no+$p-$r}
            <tbody>
                <tr>
                    <td>
                        {$m.date|fn_parse_date|date_format:"%a %d/%m/%y"}&nbsp;&nbsp;
                        {if $m.type == "PVP" and $m.package_type == "SL"}
                            <a target='_blank' href="{"uns_sheets.update&sheet_id=`$m.package_id`"|fn_url}"><b>СЛ №{$m.sheet_no}</b></a>
                        {else}
                            <a target='_blank' href='{$document_href}' title='{$document_types[$m.dt_id].name}' ><b>{$m.document_id}</b> - {$document_types[$m.dt_id].name_short}{if $m.type == "VLC"} за {$m.date_cast|fn_parse_date|date_format:"%a %d/%m/%y"}{/if}</a>
                        {/if}
                    </td>
                    <td class="center">{if strlen($m.comment)}{include file="common_templates/tooltip.tpl" tooltip=$m.comment}{else}&nbsp;{/if}</td>
                    <td class="center b1_l"><span class="{if $no<0}info_warning_block{elseif $no==0}zero{/if}">{$no|fn_fvalue}</span></td>
                    <td class="center b1_l"><span class="{if $p<0} info_warning_block{elseif $p==0} zero{/if}">{if $p!=0}+{/if}{$p|fn_fvalue}</span></td>
                    <td class="center b1_l"><span class="{if $r<0} info_warning_block{elseif $r==0} zero{/if}">{if $r!=0}-{/if}{$r|fn_fvalue}</span></td>
                    <td class="center b2_l" style="background-color: #D3D3D3;"><span class="{if $ko<0}info_warning_block{elseif $ko==0}zero{/if}">{$ko|fn_fvalue}</span></td>
                </tr>
            </tbody>
            {/foreach}
        {/if}
        <tbody>
            <tr>
                <td class="       b2_b b2_t" align="right" colspan="2" style="background-color: #D3D3D3;"><b style="font-size: 15px;">Итого:</b></td>
                <td class="center b2_b b2_t b1_l" style="background-color: #D3D3D3;"><b style="font-size: 14px;"><span class="{if $params.nach<0}         info_warning_block{elseif $params.nach==0}          zero{/if}">{$params.nach|fn_fvalue}</span></b></td>
                <td class="center b2_b b2_t b1_l" style="background-color: #D3D3D3;"><b style="font-size: 14px;"><span class="{if $params.current__in<0}  info_warning_block{elseif $params.current__in==0}   zero{/if}">{if $params.current__in!=0}+{/if}{$params.current__in|fn_fvalue}</span></b></td>
                <td class="center b2_b b2_t b1_l" style="background-color: #D3D3D3;"><b style="font-size: 14px;"><span class="{if $params.current__out<0} info_warning_block{elseif $params.current__out==0}  zero{/if}">{if $params.current__out!=0}-{/if}{$params.current__out|fn_fvalue}</span></b></td>
                <td class="center b2_b b2_t b2_l" style="background-color: #D3D3D3;"><b style="font-size: 14px;"><span class="{if $params.konech<0}       info_warning_block{elseif $params.konech==0}        zero{/if}">{$params.konech|fn_fvalue}</span></b></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
{/strip}