{capture name="mainbox"}
    {if $analisys_of_pumps|is__array}
        {foreach from=$analisys_of_pumps item="ps" key="ps_id" name="ps"}
            {* Сводная информация *}
            <table cellpadding="0" cellspacing="0" border="1" class="table" style="margin: 20px 0 0 10px;">
                <thead>
                    <tr style="background-color: #EDEDED;">
                        <th class="center" rowspan="3" width="170px">{$smarty.foreach.ps.iteration}). {$pump_series.$ps_id.ps_name}</th>
                        <th class="center" rowspan="2">Задел</th>
                        <th class="center" rowspan="2" style="text-transform: none;">Выпол-<br>нено</th>
                        <th class="center" >ОСТАЛОСЬ</th>
                        <th class="center" rowspan="2" width="60px">СГП<br>{$data.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                    </tr>
                    <tr style="background-color: #EDEDED;">
                        <th class="center">{$data.tpl_curr_month}/{$data.tpl_next_month}</th>
                    </tr>
                    <tr style="background-color: #EDEDED;">
                        {assign var="q" value=$data.zadel.$ps_id|default:0}
                        <th class="center {if !$q}zero{/if}" width="60px">{$q}</th>

                        {assign var="q" value=$data.done.$ps_id|default:0}
                        <th class="center {if !$q}zero{/if}" width="60px">{$q}</th>

                        {assign var="q" value=$ps.remaining|default:0}
                        <th class="center {if !$q}zero{/if}" width="60px">{$q}</th>

                        {assign var="q" value=$data.sgp_current_day.$ps_id|default:0}
                        <th class="center {if !$q}zero{/if}" width="60px">{$q}</th>
                    </tr>
                </thead>
            </table>

            {* Информация по деталям*}
            <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 0 0 0 10px;">
                <thead>
                    <tr style="background-color: #EDEDED;">
                        <th class="center" rowspan="3">№</th>
                        <th class="center b1_l" rowspan="3">Наименование</th>
                        <th class="center b1_l" rowspan="3" style="text-transform: none;">Кол.<br>на<br>1 ед.</th>
                        <th class="center b_l b_b" colspan="6" style="text-transform: none;">ОСТАТКИ ДЕТАЛЕЙ</th>
                        <th class="center b_l" rowspan="3" style="text-transform: none;">Треб.<br>кол-во<br>дет.<br> на план</th>
                        <th class="center b_l" rowspan="3" style="text-transform: none;">Недо-<br>стача<br>дет.</th>
                        <th class="center b_l b1_b" colspan="3">ЧУГУННЫЕ ОТЛИВКИ</th>
                    </tr>
                    <tr style="background-color: #EDEDED;">
                        <th style="text-align: center;" class="b_l b1_b" colspan="2">МЦ №1</th>
                        <th style="text-align: center;" class="b2_l b1_b" colspan="2">МЦ №2</th>
                        <th style="text-align: center;" class="b_l" rowspan="2">Скл.<br>КМП</th>
                        <th style="text-align: center;" class="b_l" rowspan="2"><span style="font-size: 30px;">&Sigma;</span></th>
                        <th style="text-align: center;" class="b_l" rowspan="2">Наименование</th>
                        <th style="text-align: center;" class="b1_l" rowspan="2">Склад<br>ЛИТЬЯ</th>
                        <th style="text-align: center; text-transform: none;" class="b1_l" rowspan="2">Недо-<br>стача<br>загот.</th>
                    </tr>
                    <tr style="background-color: #EDEDED;">
                        <th style="text-align: center;" class="b_l">Обр.</th>
                        <th style="text-align: center;" class="b1_l">Зав.</th>
                        <th style="text-align: center;" class="b2_l">Обр.</th>
                        <th style="text-align: center;" class="b1_l">Зав.</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$ps.details item="d" name="d"}
                    <tr>
                        <td align="center">{$smarty.foreach.d.iteration}</td>
                        <td class="b1_l"><b>{$d.detail_name}</b>{if strlen($d.detail_no) and $d.detail_no!="-"}<br><span style="font-size: 11px;">[{$d.detail_no}]</span>{/if}</td>
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
                        <td class="b_l" style="background-color: #EDEDED; " align="center"><span style="font-size: 15px;" class="bold {if !$q}zero{/if}">{$q}</span></td>

                        {* Требуемое количество на план *}
                        {assign var="required_q" value=$ps.remaining*$d.quantity|fn_fvalue}
                        <td class="b_l" style="" align="center"><span style="font-size: 17px;" class=" {if !$required_q}zero{/if}">{$required_q}</span></td>

                        {* Фактическая недостача деталей *}
                        {assign var="diff_q" value=$q-$required_q}
                        <td class="b_l" style="background-color: #EDEDED; " align="center"><span style="font-size: 17px;" class="{if !$diff_q}zero bold{elseif $diff_q<0}info_warning_block bold{else}zero{/if}">{$diff_q}</span></td>

                        {assign var="q" value=$d.balance_material}
                        {if $d.mclass_id == 1}
                            <td class="b_l"     align="left">{if strlen($d.material_no)}[{$d.material_no}] {/if}{$d.material_name}</td>
                            <td class="b1_l"    align="center"><span class="bold {if !$q}zero{/if}">{$q}</span></td>
                            {if $diff_q<0}
                                {assign var="q" value=$diff_q*$d.material_quantity+$d.balance_material}
                                <td class="b1_l" align="center"><span style="font-size: 15px;" class="{if !$q}zero bold{elseif $q<0}info_warning_block bold{else}zero{/if}">{$q}</span></td>
                            {else}
                                <td class="b1_l"    align="center">&nbsp;</td>
                            {/if}
                        {else}
                            <td class="b_l"     align="left">&nbsp;</td>
                            <td class="b1_l"    align="center">&nbsp;</td>
                            <td class="b1_l"    align="center">&nbsp;</td>
                        {/if}
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <br/>
            <hr/>
            <br/>
        {/foreach}
    {/if}
{/capture}
{include file="common_templates/mainbox.tpl" title="Анализ плана производства по РАЗРЕШЕННЫМ насосам на `$data.current_day`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
