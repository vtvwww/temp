{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
    {assign var="document_id"       value=$d.document_id}
    {assign var="document_items"    value=$d.items}
    {assign var="disabled"          value=true}
    {assign var="date_cast_hide"    value=true}
    {if $d.type == 1}
        {assign var="date_cast_hide" value=false}
    {/if}

    <table class="info">
        <tr>
            <td align="right"><span class="h">Дата проведения:</span></td>
            <td><span class="t">{$d.date|date_format:"%a %d/%m/%Y"}</span></td>
        </tr>
        <tr>
            <td align="right"><span class="h">Дата плавки:</span></td>
            <td><span class="t">{$d.date_cast|date_format:"%a %d/%m/%Y"}</span></td>
        </tr>
        <tr>
            <td align="right"><span class="h">Движение:</span></td>
            <td><span class="t">{$objects_plain[$d.object_from].path} -> {$objects_plain[$d.object_to].path}</span></td>
        </tr>
        <tr>
            <td align="right"><span class="h">Комментарий:</span></td>
            <td><span class="t">{if $d.comment}{$d.comment}{else}-{/if}</span></td>
        </tr>
    </table>

