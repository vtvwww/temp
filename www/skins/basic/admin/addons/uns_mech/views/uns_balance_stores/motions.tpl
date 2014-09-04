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
        <tbody>
            <tr>
                <td align="center">&nbsp;</td>
                <td align="center"><b>{$params.nach}</b></td>
                <td align="center"><b>{$params.current__in}</b></td>
                <td align="center"><b>{$params.current__out}</b></td>
                <td align="center"><b>{$params.konech}</b></td>
            </tr>
        </tbody>
        {if is__array($motions)}
        {foreach from=$motions item=m}
            {assign var="document_href" value="acc_documents.update&document_id=`$m.document_id`"|fn_url}
            {assign var="doc_name" value="<a target='_blank' href='`$document_href`' title='`$document_types[$m.dt_id].name`' ><b>№`$m.document_id`</b> - `$document_types[$m.dt_id].name_short`</a>" }
            <tr>
                <td>{$m.date|fn_parse_date|date_format:"%a %d/%m/%Y"}&nbsp;&nbsp;{$doc_name}</td>
                <td align="center">&nbsp;</td>
                {if $m.type == 'VLC'}
                    <td align="center">{$m.quantity|fn_fvalue}</td>
                    <td align="center">&nbsp;</td>
                {elseif $m.type == 'MCP' or $m.type == 'RO' or $m.type == 'AS_VLC'}
                    <td align="center">&nbsp;</td>
                    <td align="center">{$m.quantity|fn_fvalue}</td>
                {elseif $m.type == 'AIO'}
                    {if $m.change_type == 'POZ' and $m.motion_type == 'I'}
                        <td align="center">{$m.quantity|fn_fvalue}</td>
                        <td align="center">&nbsp;</td>
                    {elseif $m.change_type == 'NEG' and $m.motion_type == 'O'}
                        <td align="center">&nbsp;</td>
                        <td align="center">{$m.quantity|fn_fvalue}</td>
                    {/if}
                {/if}
                <td align="center">&nbsp;</td>
            </tr>
        {/foreach}
        {/if}
    </table>
    <br>
    <br>
    <pre>{$balances|print_r}</pre>
{/strip}