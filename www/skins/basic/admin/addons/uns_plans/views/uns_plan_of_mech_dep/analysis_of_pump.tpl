{* СВОДНАЯ ИНФОРМАЦИЯ *}
<span style="font-size: 14px; font-weight: bold;">План производства насоса {$data.ps_name}</span>
<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr style="background-color: #EDEDED;">
            <th style="text-transform: none; text-align: center;" rowspan="3">СГП на<br>01/{$data.month|string_format:"%02d"}/{$data.year|substr:"-2":"2"}</th>
            <th style="text-transform: none; text-align: center;" class="b_l b1_b" colspan="4">ВЫПОЛНЕНИЕ ПЛАНА НА {$data.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
            <th style="text-transform: none; text-align: center;" class="b_l" rowspan="3">СГП на<br>{$data.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
        </tr>
        <tr style="background-color: #EDEDED;">
            <th class="b_l" rowspan="2" style="background-color: #FFEF8C; text-transform: none; text-align: center;">Задел<br>ожид.<br>сборку</th>
            <th class="b1_l" rowspan="2" style="background-color: #C0FF9A; text-transform: none; text-align: center;">Выпол-<br>нено</th>
            <th class="b_l b1_b" colspan="2" style="text-transform: none; text-align: center;">Осталось</th>
        </tr>
        <tr style="background-color: #EDEDED;">
            <th class="b_l" style="background-color: #B8C1FF; text-transform: none; text-align: center;">{$data.tpl_curr_month}</th>
            <th class="b1_l" style="background-color: #B8C1FF; text-transform: none; text-align: center;">{$data.tpl_next_month}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="" align="center" style=""><b>{$data.sgp}</b></td>
            <td class="b_l" align="center" style="background-color: #FFEF8C;"><b>{$data.zadel}</b></td>
            <td class="b1_l" align="center" style="background-color: #C0FF9A;"><b>{$data.done}</b></td>
            <td class="b_l" align="center" style="background-color: #B8C1FF;"><b>{$data.rpp_curr_month}</b></td>
            <td class="b1_l" align="center" style="background-color: #B8C1FF;"><b>{$data.rpp_next_month}</b></td>
            <td class="b_l" align="center" style=""><b>{$data.sgp_current_day}</b></td>
        </tr>
    </tbody>
</table>


{* КОМПЛЕКТАЦИЯ *}
<br/>
<br/>
{foreach from=$pumps item="p"}
    <span style="font-size: 14px; font-weight: bold;">Насос {$p.p_name}</span>
    <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr style="background-color: #EDEDED;">
                <th style="text-align: center;" rowspan="2">№</th>
                <th style="text-align: center;" class="b1_l" rowspan="2">Наименование</th>
                <th style="text-align: center;" class="b1_l" rowspan="2" style="text-transform: none;">Кол.<br>на<br>1 ед.</th>
                <th style="text-align: center;" class="b_l b1_b" colspan="2">МЦ №1</th>
                <th style="text-align: center;" class="b2_l b1_b" colspan="2">МЦ №2</th>
                <th style="text-align: center;" class="b_l" rowspan="2">Скл.<br>КМП</th>
                <th style="text-align: center;" class="b_l" rowspan="2"><span style="font-size: 30px;">&Sigma;</span></th>
                <th style="text-align: center;" class="b_l b1_b" colspan="2">ЧУГУННЫЕ ОТЛИВКИ</th>
            </tr>
            <tr style="background-color: #EDEDED;">
                <th style="text-align: center;" class="b_l">Обр.</th>
                <th style="text-align: center;" class="b1_l">Зав.</th>
                <th style="text-align: center;" class="b2_l">Обр.</th>
                <th style="text-align: center;" class="b1_l">Зав.</th>
                <th style="text-align: center;" class="b_l">Наименование</th>
                <th style="text-align: center;" class="b1_l">Склад<br>ЛИТЬЯ</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$p.details item="d" name="d"}
                <tr>
                    <td align="center">{$smarty.foreach.d.iteration}</td>
                    <td class="b1_l"><b>{$d.detail_name}</b>{if strlen($d.detail_no)}<br><span style="font-size: 11px;">[{$d.detail_no}]</span>{/if}</td>
                    <td class="b1_l"    align="center">{$d.quantity|fn_fvalue}</td>


                    {assign var="q" value=$d.balance[10].processing_konech}
                    <td class="b_l"     align="center"><span class="bold {if !$q}zero{/if}">{$q}</span></td>

                    {assign var="q" value=$d.balance[10].complete_konech}
                    <td class="b1_l"    align="center"><span class="bold {if !$q}zero{/if}">{$q}</span></td>

                    {assign var="q" value=$d.balance[14].processing_konech}
                    <td class="b2_l"    align="center"><span class="bold {if !$q}zero{/if}">{$q}</span></td>

                    {assign var="q" value=$d.balance[14].complete_konech}
                    <td class="b1_l"    align="center"><span class="bold {if !$q}zero{/if}">{$q}</span></td>

                    {assign var="q" value=$d.balance[17].konech}
                    <td class="b_l"     align="center"><span class="bold {if !$q}zero{/if}">{$q}</span></td>

                    {assign var="q" value=$d.balance[10].processing_konech+$d.balance[10].complete_konech+$d.balance[14].processing_konech+$d.balance[14].complete_konech+$d.balance[17].konech}
                    <td class="b_l" style="background-color: #EDEDED; " align="center"><span style="font-size: 17px;" class="bold {if !$q}zero{/if}">{$q}</span></td>

                    {assign var="q" value=$d.balance_material}
                    <td class="b_l"     align="left">{if $d.mclass_id == 1}{if strlen($d.material_no)}[{$d.material_no}] {/if}{$d.material_name}{else}&nbsp;{/if}</td>
                    <td class="b1_l"    align="center">{if $d.mclass_id == 1}<span class="bold {if !$q}zero{/if}">{$q}</span>{else}&nbsp;{/if}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <br/>
{/foreach}
{**}
{*<pre>{$pumps|print_r}</pre>*}
{*<pre>{$data|print_r}</pre>*}