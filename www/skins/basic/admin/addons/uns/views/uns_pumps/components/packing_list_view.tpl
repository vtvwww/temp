{capture name="title"}
    <h1>{$lang.uns_pump}: {$pump.p_name}</h1>
{/capture}

{capture name="info"}
    <h4><b>Дата:</b> {$smarty.now|date_format:"%Y/%m/%d  %H:%M:%S"}</h4>
    <h2><b>Тип насоса:</b> {$pump.pt_name}</h2>
    <h2><b>Серия насоса:</b> {$pump.ps_name}</h2>
    <h2><b>Учет:</b>  {$accounting.u_name} (1 {$accounting.u_name} = {$accounting.weights.M[0].value|fn_fvalue} кг)</h2>

    {if is__array($features.existing)}
        <h2><b>{$lang.uns_features}:</b></h2>
        <ul style="margin-left: 40px;">
            {foreach from=$features.existing item='f'}
                <li>{$f.feature_name}: {$f.feature_value|fn_fvalue} {$f.u_name}</li>
            {/foreach}
        </ul>
    {/if}

    {if is__array($options.existing)}
        <h2><b> {$lang.uns_options}:</b></h2>
        <ul style="margin-left: 40px;">
            {foreach from=$options.existing item='o'}
                <li>{$o.option_name}: {$o.variants[$o.ov_id].ov_value}</li>
            {/foreach}
        </ul>
    {/if}
{/capture}

{capture name="packing_list"}
    <h2><b>Комплектация:</b></h2>
    {foreach from=$packing_list_data.packing_list item='v_pl' key='k_pl'}
        {if $k_pl == $smarty.const.UNS_PACKING_PART__PUMP}<h3><b>Насос</b></h3>{/if}
        {if $k_pl == $smarty.const.UNS_PACKING_PART__FRAME}<h3><b>Рама</b></h3>{/if}
        {if $k_pl == $smarty.const.UNS_PACKING_PART__MOTOR}<h3><b>Двигатель</b></h3>{/if}
        {if !is__array($v_pl)}
            <span style="color:red">Нет данных!</span>
        {else}
            <table class="packing_list">
                <thead>
                    <tr>
                        <th rowspan="2" class="b_t b_l b_r b_b">№</th>
                        <th colspan="3" class="b_t b_r">НАИМЕНОВАНИЕ</th>
                        <th colspan="4" class="b_t b_r">ИСХ. МАТЕРИАЛ</th>
                        <th rowspan="2" class="b_t b_l b_r b_b">Отходы,<br>кг</th>
                    </tr>
                    <tr>
                        <th rowspan="1" class="b_b">Обозначение</th>
                        <th rowspan="1" class="b_b">Кол-во</th>
                        <th rowspan="1" class="b_b b_r">Вес,<br>кг<sup>(1)</sup></th>
                        <th rowspan="1" class="b_b">Класс</th>
                        <th rowspan="1" class="b_b">Обозначение</th>
                        <th rowspan="1" class="b_b">Кол-во<sup>(2)</sup></th>
                        <th rowspan="1" class="b_b b_r">Вес,<br>кг<sup>(3)</sup></th>
                    </tr>
                </thead>
                <tbody>
                    {assign var="n" value="0"}
                    {foreach from=$v_pl item='i'}
                        {assign var="pplr_type" value=""}
                        {if is__array($i.replacement) and ($i.replacement.pplr_type=="R" and $i.replacement.pplr_type=="D")}
                            {assign var="pplr_type" value=$i.replacement.pplr_type}
                        {/if}

                        {if $i.replacement.pplr_type != "D"}
                            {assign var="sup" value=""}
                            {if $i.replacement.pplr_type == "R"}
                                {assign var="i" value=$i.replacement}
                                {assign var="sup" value="<sup>(4)</sup>"}
                            {/if}

                            {assign var="n" value=$n+1}
                            {assign var="item_type" value=$i.item_type}
                            {assign var="waste" value=0}
                            {assign var="ts" value="M"}
                            {assign var="typesize" value=""}

                            {if $item_type == "D"}
                                {assign var="ts" value=$i.typesize}
                                {if $ts == "A"}
                                    {assign var="typesize" value="<sup>(А)</sup>"}
                                {/if}
                                {if $ts == "B"}
                                    {assign var="typesize" value="<sup>(Б)</sup>"}
                                {/if}

                                {assign var="item" value=$i.items[$i.item_id]}
                                {assign var="material" value=$item.accounting_data.materials|array_shift}

                                {assign var="item_name"   value=$i.item_info.detail_name}
                                {assign var="item_no"     value=$i.item_info.detail_no}
                                {assign var="item_q"      value=$i.quantity}
                                {assign var="item_q_unit" value=$item.accounting_data.u_name}
                                {assign var="item_w"      value=$item.accounting_data.weight.$ts}
                                {assign var="item_w_t"    value=$item_q*$item_w}

                                {assign var="material_class" value=$material.mclass_name}
                                {assign var="material_name" value=$material.material_name}
                                {if strlen($material.material_no)}
                                    {assign var="material_name" value="`$material.material_name` [`$material.material_no`]"}
                                {/if}
                                {assign var="material_add_quantity_state"  value=$material.add_quantity_state}
                                {assign var="material_add_q"  value=$material.add_quantity}
                                {assign var="material_q"      value=$material.quantity}
                                {if $material_add_quantity_state == "A"}
                                    {assign var="material_q"      value=$material_q*$material.add_quantity}
                                {/if}
                                {assign var="material_q_unit" value=$material.accounting_data.u_name}
                                {assign var="material_w_t"    value=$item_q*$material_q*$material.accounting_data.weight}
                                {assign var="waste" value=$material_w_t-$item_w_t}
                            {else}
                                {assign var="material" value=$i.items[$i.item_id]}
                                {assign var="item_no"   value=$material.material_no}
                                {assign var="item_name" value=$material.material_name}
                                {if strlen($material.material_no)}
                                    {assign var="item_name" value="`$material.material_name` [`$material.material_no`]"}
                                {/if}

                                {assign var="item_q"      value=$i.quantity}
                                {assign var="item_q_unit" value=$material.accounting_data.u_name}
                                {assign var="item_w"      value=$material.accounting_data.weight.M}
                                {assign var="item_w_t"    value=$item_q*$item_w}

                                {assign var="material_class" value=$material.mclass_name}
                                {assign var="material_q" value=""}
                                {assign var="material_q_unit" value=""}
                                {assign var="material_w_t"    value=""}
                            {/if}
                            <tr>
                                <td align="right" class="b_r b_l">{$n}</td>
                                <td><span style="font-weight: bold;">{$item_name} ({$i.item_id})</span>{$sup}{if $item_no}{if $typesize}{$typesize}{/if}<span style="font-weight: normal; font-size: 10px;"><br>{$item_no}</span>{/if}</td>
                                <td align="right">{$item_q|fn_fvalue} {$item_q_unit}</td>
                                <td align="right" class="b_r">{if $item_w_t}{$item_w_t|fn_fvalue:"2":false}{else}<span style="text-decoration: line-through;">0.00</span><sup>(6)</sup>{/if}</td>

                                <td>{$material_class}</td>
                                <td>{if $item_type == "D"}{$material_name} ({$material.material_id}){/if}</td>
                                <td align="right">{if $item_type == "D"}{$material_q|fn_fvalue}{if $material_a>0}+{$material_a|fn_fvalue}<sup>(7)</sup>{/if} {$material_q_unit}{/if}</td>
                                <td align="right" class="b_r">{if $item_type == "D"}{$material_w_t|fn_fvalue:"2":false}{/if}</td>
                                <td align="right" class="b_r b_l ">{if $item_type == "D"}<span {if $waste<0}style="color: red; font-weight: bold;"{/if}>{$waste|fn_fvalue:"2":false}{if $waste<0}<sup>(5)</sup>{/if}</span>{else}{$waste|fn_fvalue:"2":false}{/if}</td>
                            </tr>
                        {/if}
                    {/foreach}
                </tbody>
            </table>
        {/if}
    {/foreach}
{/capture}


{capture name="main"}
    {$smarty.capture.title}
    {$smarty.capture.info}
    {$smarty.capture.packing_list}
    <br>_________________________________________________________________________
    <p><b>(1)</b> - вес позиции, с учетом его количества, т.е. итоговый вес позиции.</p>
    <p><b>(2)</b> - кол-во исх. материала на 1 деталь.</p>
    <p><b>(3)</b> - общий вес исх. материала, требуемого на заданное кол-во в позиции.</p>
    <p><b>(4)</b> - позиция была заменена, относительно базовой комплектации Серии насосов.</p>
    <p><b>(5)</b> - деталь не может быть больше по весу от исх. материала, необходимо исправить значения веса.</p>
    <p><b>(6)</b> - деталь не может весить 0 кг, необходимо исправить значения веса.</p>
    <p><b>(A)</b> - исполнение детали с подрезкой А.</p>
    <p><b>(Б)</b> - исполнение детали с подрезкой Б.</p>
{/capture}

{strip}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Комплектация насоса</title>
    {include file="meta.tpl"}
    {include file="common_templates/styles.tpl"}
    {literal}
    <style type="text/css">
        h1{
            font-size: 21px;
        }
        h2{
            font-size: 17px;
            font-weight: normal;
            margin-top: 5px;
            margin-bottom: 1px;
        }
        h3{
            font-size: 14px;
            font-weight: normal;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        table{
            border-collapse: collapse;
        }

        table th, table.packing_list td {
            padding: 0 4px;

        }
        table th{
            border: 1px solid #000000;
        }

        table td{
            border: 1px solid #000000;
        }

        .b_t{
            border-top: 2px solid #000000;
        }

        .b_r{
            border-right: 2px solid #000000;
        }

        .b_b{
            border-bottom: 2px solid #000000;
        }

        .b_l{
            border-left: 2px solid #000000;
        }
        sup{
            font-weight: normal;
        }

        thead {
            background-color: #d7d7d7;
        }
        tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        p{
            font-size: 10px;
        }
    </style>
    {/literal}
</head>
<body class="nobackground">
<div id="content" style="margin: 20px;">{$smarty.capture.main|unescape}</div>
</body>

</html>
{/strip}