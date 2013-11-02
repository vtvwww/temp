{strip}
    {assign var="id" value=$key}

    {assign var="t_nach"            value=0}
    {assign var="t_current__in"     value=0}
    {assign var="t_current__out"    value=0}
    {assign var="t_konech"          value=0}

    {capture name="category_items"}
        {foreach from=$item.items item=m}
            {assign var="t_nach"            value=$t_nach+$m.nach}
            {assign var="t_current__in"     value=$t_current__in+$m.current__in}
            {assign var="t_current__out"    value=$t_current__out+$m.current__out}
            {assign var="t_konech"          value=$t_konech+$m.konech}

            <tr class="category_items {$id} {if $expand_all} hidden {/if}">
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$m.name}{if $m.no != ""} [{$m.no}]{/if}{* <span style="font-size: 9px; font-weight: bold;">({$m.id} - {$m.weight})</span>*}</td>
                <td align="center">****</td>
                <td align="center"><span class="{if $m.nach<0}info_warning_block{/if}">{$m.nach|fn_fvalue:2}</span></td>
                <td align="center"><span class="{if $m.current__in<0}info_warning_block{/if}">{$m.current__in|fn_fvalue:2}</span></td>
                <td align="center"><span class="{if $m.current__out<0}info_warning_block{/if}">{$m.current__out|fn_fvalue:2}</span></td>
                <td align="center"><span class="{if $m.konech<0}info_warning_block{/if}">{$m.konech|fn_fvalue:2}</span></td>
            </tr>
        {foreachelse}
            <tr class="no-items">
                <td colspan="6"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}
        <tr class="category_items {$id}{if $expand_all} hidden {/if}">
            <td colspan="6" >&nbsp;</td>
        </tr>
    {/capture}

    <tbody>
        <tr>
            <td>
                <img width="14" category_items="{$id}" height="9" border="0" title="Расширить список" class="hand {$id} plus {if !$expand_all} hidden {/if}" alt="Расширить список" src="/skins/basic/admin/images/plus.gif">
                <img width="14" category_items="{$id}" height="9" border="0" title="Свернуть список" class="hand {$id} minus {if $expand_all} hidden {/if}" alt="Свернуть список" src="/skins/basic/admin/images/minus.gif">
                &nbsp;<b>{$item.group}</b></td>
            <td align="center">&nbsp;</td>
            <td align="center"><b><span class="{if $t_nach<0}info_warning_block{/if}">{$t_nach|fn_fvalue:2}</span></b></td>
            <td align="center"><b><span class="{if $t_current__in<0}info_warning_block{/if}">{$t_current__in|fn_fvalue:2}</span></b></td>
            <td align="center"><b><span class="{if $t_current__out<0}info_warning_block{/if}">{$t_current__out|fn_fvalue:2}</span></b></td>
            <td align="center"><b><span class="{if $t_konech<0}info_warning_block{/if}">{$t_konech|fn_fvalue:2}</span></b></td>
        </tr>
        {$smarty.capture.category_items}
    </tbody>
{/strip}