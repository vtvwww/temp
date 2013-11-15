{strip}
    <table class="table">
        <thead>
            <tr>
                <th style="text-align: center;" width="300px">Движение</th>
                <th style="text-align: center;" width="40px">Нач. Ост.</th>
                <th style="text-align: center;" width="40px">Приход</th>
                <th style="text-align: center;" width="40px">Расход</th>
                <th style="text-align: center;" width="40px">Кон. Ост.</th>
            </tr>
        <thead>
        {if is__array($motions)}
            {assign var="no"    value=0}
            {assign var="p"     value=0}
            {assign var="r"     value=0}
            {assign var="ko"    value=$params.nach}

            {foreach from=$motions item=m}
                {assign var="document_href" value="acc_documents.update&document_id=`$m.document_id`"|fn_url}

                {* РАСЧЕТ ДВИЖЕНИЯ *}
                {assign var="no"    value=$ko}

                {if $m.type == 'VLC'}
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
                <tr>
                    <td>
                        {$m.date|fn_parse_date|date_format:"%a %d/%m/%Y"}&nbsp;&nbsp;
                        {if $m.type == "PVP" and $m.package_type == "SL"}
                            <a target='_blank' href="{"uns_sheets.update&sheet_id=`$m.package_id`"|fn_url}"><b>СЛ №{$m.sheet_no}</b></a>
                        {else}
                            <a target='_blank' href='{$document_href}' title='{$document_types[$m.dt_id].name}' ><b>№{$m.document_id}</b> - {$document_types[$m.dt_id].name_short}</a>
                        {/if}
                    </td>
                    <td align="center"><span class="{if $no<0}info_warning_block{elseif $no==0}zero{/if}">{$no|fn_fvalue}</span></td>
                    <td align="center"><span class="{if $p<0} info_warning_block{elseif $p==0} zero{/if}">{if $p!=0}+{/if}{$p|fn_fvalue}</span></td>
                    <td align="center"><span class="{if $r<0} info_warning_block{elseif $r==0} zero{/if}">{if $r!=0}-{/if}{$r|fn_fvalue}</span></td>
                    <td align="center"><span class="{if $ko<0}info_warning_block{elseif $ko==0}zero{/if}">{$ko|fn_fvalue}</span></td>
                </tr>
            {/foreach}
        {/if}
        <tbody>
            <tr>
                <td align="right"><b style="font-size: 15px;">Итого:</b></td>
                <td align="center"><b style="font-size: 14px;"><span class="{if $params.nach<0}         info_warning_block{elseif $params.nach==0}          zero{/if}">{$params.nach|fn_fvalue}</span></b></td>
                <td align="center"><b style="font-size: 14px;"><span class="{if $params.current__in<0}  info_warning_block{elseif $params.current__in==0}   zero{/if}">{if $params.current__in!=0}+{/if}{$params.current__in|fn_fvalue}</span></b></td>
                <td align="center"><b style="font-size: 14px;"><span class="{if $params.current__out<0} info_warning_block{elseif $params.current__out==0}  zero{/if}">{if $params.current__out!=0}-{/if}{$params.current__out|fn_fvalue}</span></b></td>
                <td align="center"><b style="font-size: 14px;"><span class="{if $params.konech<0}       info_warning_block{elseif $params.konech==0}        zero{/if}">{$params.konech|fn_fvalue}</span></b></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
{/strip}