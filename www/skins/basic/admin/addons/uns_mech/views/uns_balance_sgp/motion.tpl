{strip}
    <h4>Период: {$smarty.request.time_from|fn_parse_date|date_format:"%a %d/%m/%Y"} - {$smarty.request.time_to|fn_parse_date|date_format:"%a %d/%m/%Y"}</h4>
    <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr style="background-color: #EDEDED;">
                <th rowspan="2" style="text-align: center;" width="270px">Движение</th>
                <th rowspan="2" style="text-align: center;" width="1px">&nbsp;</th>
                <th colspan="4" style="text-align: center; border-left: 2px solid #000000; border-bottom: 1px solid #000000;" width="40px">Насос</th>
                <th colspan="4" style="text-align: center; border-left: 2px solid #000000; border-bottom: 1px solid #000000;" width="40px">На раме</th>
                <th colspan="4" style="text-align: center; border-left: 2px solid #000000; border-bottom: 1px solid #000000;" width="40px">Агрегат</th>
            </tr>
            <tr style="background-color: #EDEDED;">
                <th width="20px" style="text-align: center; border-left: 2px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Начальный остаток" tooltip_mark="<b>НО</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Приход" tooltip_mark="<b>П</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Расход" tooltip_mark="<b>Р</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Конечный остаток" tooltip_mark="<b>КО</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 2px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Начальный остаток" tooltip_mark="<b>НО</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Приход" tooltip_mark="<b>П</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Расход" tooltip_mark="<b>Р</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Конечный остаток" tooltip_mark="<b>КО</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 2px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Начальный остаток" tooltip_mark="<b>НО</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Приход" tooltip_mark="<b>П</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Расход" tooltip_mark="<b>Р</b>"}</th>
                <th width="20px" style="text-align: center; border-left: 1px solid #000000;">{include file="common_templates/tooltip.tpl" tooltip="Конечный остаток" tooltip_mark="<b>КО</b>"}</th>
            </tr>
        <thead>
        {if is__array($motions)}
            {assign var="P_p_total"     value=0}
            {assign var="P_r_total"     value=0}
            {assign var="P_no"          value=0}
            {assign var="P_ko"          value=$balances.P[$item_id].no}

            {assign var="PF_p_total"    value=0}
            {assign var="PF_r_total"    value=0}
            {assign var="PF_no"         value=0}
            {assign var="PF_ko"         value=$balances.PF[$item_id].no}

            {assign var="PA_p_total"    value=0}
            {assign var="PA_r_total"    value=0}
            {assign var="PA_no"         value=0}
            {assign var="PA_ko"         value=$balances.PA[$item_id].no}


        {foreach from=$motions item=m}
            {if $m.type == "VN"}
                {assign var="document_href" value="uns_kits.update&kit_id=`$m.package_id`"|fn_url}
            {else}
                {assign var="document_href" value="uns_moving_mc_sk_su.update&document_id=`$m.document_id`"|fn_url}
            {/if}
            {assign var="doc_name" value="<a target='_blank' href='`$document_href`' title='`$document_types[$m.dt_id].name`' ><b>№`$m.document_id`</b> - `$document_types[$m.dt_id].name_short`</a>" }
            <tr>
                <td>{$m.date|fn_parse_date|date_format:"%a %d/%m/%Y"}&nbsp;&nbsp;{$doc_name}</td>
                <td>{if strlen($m.comment)}{include file="common_templates/tooltip.tpl" tooltip=$m.comment}{/if}</td>
                {assign var="P_no"  value=$P_ko}
                {assign var="P_p"   value=0}
                {assign var="P_r"   value=0}

                {assign var="PF_no"  value=$PF_ko}
                {assign var="PF_p"   value=0}
                {assign var="PF_r"   value=0}

                {assign var="PA_no"  value=$PA_ko}
                {assign var="PA_p"   value=0}
                {assign var="PA_r"   value=0}

                {if $m.item_type == "P"}
                    {if $m.motion_type == "I"}
                        {assign var="P_p" value=$m.quantity}
                    {elseif $m.motion_type == "O"}
                        {assign var="P_r" value=$m.quantity}
                    {/if}
                    {assign var="P_ko"  value=$P_no+$P_p-$P_r}
                    {assign var="P_p_total"  value=$P_p_total+$P_p}
                    {assign var="P_r_total"  value=$P_r_total+$P_r}
                {/if}

                {if $m.item_type == "PF"}
                    {if $m.motion_type == "I"}
                        {assign var="PF_p" value=$m.quantity}
                    {elseif $m.motion_type == "O"}
                        {assign var="PF_r" value=$m.quantity}
                    {/if}
                    {assign var="PF_ko"         value=$PF_no+$PF_p-$PF_r}
                    {assign var="PF_p_total"    value=$PF_p_total+$PF_p}
                    {assign var="PF_r_total"    value=$PF_r_total+$PF_r}
                {/if}

                {if $m.item_type == "PA"}
                    {if $m.motion_type == "I"}
                        {assign var="PA_p" value=$m.quantity}
                    {elseif $m.motion_type == "O"}
                        {assign var="PA_r" value=$m.quantity}
                    {/if}
                    {assign var="PA_ko"         value=$PA_no+$PA_p-$PA_r}
                    {assign var="PA_p_total"    value=$PA_p_total+$PA_p}
                    {assign var="PA_r_total"    value=$PA_r_total+$PA_r}
                {/if}

                <td style="text-align: center; border-left: 2px solid #000000; "><span class="{if $P_no<0}info_warning_block{elseif $P_no==0}zero{/if}">{$P_no|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $P_p<0} info_warning_block{elseif $P_p==0} zero{/if}">{if $P_p>0}+{/if}{$P_p|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $P_r<0} info_warning_block{elseif $P_r==0} zero{/if}">{if $P_r>0}-{/if}{$P_r|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $P_ko<0}info_warning_block{elseif $P_ko==0}zero{/if}">{$P_ko|fn_fvalue}</span></td>

                <td style="text-align: center; border-left: 2px solid #000000; "><span class="{if $PF_no<0}info_warning_block{elseif $PF_no==0}zero{/if}">{$PF_no|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $PF_p<0} info_warning_block{elseif $PF_p==0} zero{/if}">{if $PF_p>0}+{/if}{$PF_p|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $PF_r<0} info_warning_block{elseif $PF_r==0} zero{/if}">{if $PF_r>0}-{/if}{$PF_r|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $PF_ko<0}info_warning_block{elseif $PF_ko==0}zero{/if}">{$PF_ko|fn_fvalue}</span></td>

                <td style="text-align: center; border-left: 2px solid #000000; "><span class="{if $PA_no<0}info_warning_block{elseif $PA_no==0}zero{/if}">{$PA_no|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $PA_p<0} info_warning_block{elseif $PA_p==0} zero{/if}">{if $PA_p>0}+{/if}{$PA_p|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $PA_r<0} info_warning_block{elseif $PA_r==0} zero{/if}">{if $PA_r>0}-{/if}{$PA_r|fn_fvalue}</span></td>
                <td style="text-align: center; border-left: 1px solid #000000; "><span class="{if $PA_ko<0}info_warning_block{elseif $PA_ko==0}zero{/if}">{$PA_ko|fn_fvalue}</span></td>
            </tr>
        {/foreach}
            <tr>
                <td colspan="2" style="text-align: right;" ><span style="font-size: 13px; font-weight: bold;">Итого:</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 2px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.P[$item_id].no<0}info_warning_block{elseif $balances.P[$item_id].no==0}zero{/if}">{$balances.P[$item_id].no|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $P_p_total<0} info_warning_block{elseif $P_p_total==0} zero{/if}">{if $P_p_total>0}+{/if}{$P_p_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $P_r_total<0} info_warning_block{elseif $P_r_total==0} zero{/if}">{if $P_r_total>0}-{/if}{$P_r_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $P_ko<0}info_warning_block{elseif $P_ko==0}zero{/if}">{$P_ko|fn_fvalue}</span></td>

                <td style="background-color: #EDEDED; text-align: center; border-left: 2px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.PF[$item_id].no<0}info_warning_block{elseif $balances.PF[$item_id].no==0}zero{/if}">{$balances.PF[$item_id].no|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PF_p_total<0} info_warning_block{elseif $PF_p_total==0} zero{/if}">{if $PF_p_total>0}+{/if}{$PF_p_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PF_r_total<0} info_warning_block{elseif $PF_r_total==0} zero{/if}">{if $PF_r_total>0}-{/if}{$PF_r_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PF_ko<0}info_warning_block{elseif $PF_ko==0}zero{/if}">{$PF_ko|fn_fvalue}</span></td>

                <td style="background-color: #EDEDED; text-align: center; border-left: 2px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.PA[$item_id].no<0}info_warning_block{elseif $balances.PA[$item_id].no==0}zero{/if}">{$balances.PA[$item_id].no|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PA_p_total<0} info_warning_block{elseif $PA_p_total==0} zero{/if}">{if $PA_p_total>0}+{/if}{$PA_p_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PA_r_total<0} info_warning_block{elseif $PA_r_total==0} zero{/if}">{if $PA_r_total>0}-{/if}{$PA_r_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PA_ko<0}info_warning_block{elseif $PA_ko==0}zero{/if}">{$PA_ko|fn_fvalue}</span></td>
            </tr>
        {else}
            <tr>
                <td colspan="2" style="text-align: right;" ><span style="font-size: 13px; font-weight: bold;">Итого:</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 2px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.P[$item_id].no<0}info_warning_block{elseif $balances.P[$item_id].no==0}zero{/if}">{$balances.P[$item_id].no|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $P_p_total<0} info_warning_block{elseif $P_p_total==0} zero{/if}">{if $P_p_total>0}+{/if}{$P_p_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $P_r_total<0} info_warning_block{elseif $P_r_total==0} zero{/if}">{if $P_r_total>0}-{/if}{$P_r_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.P[$item_id].no<0}info_warning_block{elseif $balances.P[$item_id].no==0}zero{/if}">{$balances.P[$item_id].no|fn_fvalue}</span></td>

                <td style="background-color: #EDEDED; text-align: center; border-left: 2px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.PF[$item_id].no<0}info_warning_block{elseif $balances.PF[$item_id].no==0}zero{/if}">{$balances.PF[$item_id].no|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PF_p_total<0} info_warning_block{elseif $PF_p_total==0} zero{/if}">{if $PF_p_total>0}+{/if}{$PF_p_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PF_r_total<0} info_warning_block{elseif $PF_r_total==0} zero{/if}">{if $PF_r_total>0}-{/if}{$PF_r_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.PF[$item_id].no<0}info_warning_block{elseif $balances.PF[$item_id].no==0}zero{/if}">{$balances.PF[$item_id].no|fn_fvalue}</span></td>

                <td style="background-color: #EDEDED; text-align: center; border-left: 2px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.PA[$item_id].no<0}info_warning_block{elseif $balances.PA[$item_id].no==0}zero{/if}">{$balances.PA[$item_id].no|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PA_p_total<0} info_warning_block{elseif $PA_p_total==0} zero{/if}">{if $PA_p_total>0}+{/if}{$PA_p_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $PA_r_total<0} info_warning_block{elseif $PA_r_total==0} zero{/if}">{if $PA_r_total>0}-{/if}{$PA_r_total|fn_fvalue}</span></td>
                <td style="background-color: #EDEDED; text-align: center; border-left: 1px solid #000000; border-top: 1px solid #000000;"><span style="font-size: 13px; font-weight: bold;" class="{if $balances.PA[$item_id].no<0}info_warning_block{elseif $balances.PA[$item_id].no==0}zero{/if}">{$balances.PA[$item_id].no|fn_fvalue}</span></td>
            </tr>
        {/if}
    </table>
    <br>
    <br>
{/strip}