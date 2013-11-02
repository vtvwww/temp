{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns_acc/views/acc_ostatki/components/search_form.tpl" dispatch="`$controller`.manage"}


    <p style="font-size: 12px;">
        Период: <b>{$search.date_from|fn_parse_date|date_format:"%d/%m/%Y"} - {$search.date_to|fn_parse_date|date_format:"%d/%m/%Y"}</b>
        <br>
        Объект: <b>{$objects_plain[$search.o_id].path}</b>
    </p>


<table cellpadding="0" cellspacing="0" border="0" class="table">
    <tr>
        <th style="text-align: center;" width="300px">Наименование</th>
        <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="100px">
            Нач.&nbsp;остаток<br>
            <span style="font-size: 10px;">({$search.date_from|fn_parse_date|date_format:"%d/%m/%Y"})</span>
        </th>
        <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="100px">Приход</th>
        <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="100px">Расход</th>
        <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="100px">
            Кон.&nbsp;остаток<br>
            <span style="font-size: 10px;">({$search.date_to|fn_parse_date|date_format:"%d/%m/%Y"})</span>
        </th>
    </tr>
    {foreach from=$balance item=i}
        <tr>
            <td align="left">{$i.item_name} {if strlen($i.item_no)}[{$i.item_no}]{/if}</td>
            <td align="center" style="border-left: 1px solid #EBEBEB; "><span class="{if $i.nach<0}info_warning_block{/if}">{$i.nach|fn_fvalue:2}</span></td>
            <td align="center" style="border-left: 1px solid #EBEBEB; "><span class="{if $i.current__in<0}info_warning_block{/if}">{$i.current__in|fn_fvalue:2}</span></td>
            <td align="center" style="border-left: 1px solid #EBEBEB; "><span class="{if $i.current__out<0}info_warning_block{/if}">{$i.current__out|fn_fvalue:2}</span></td>
            <td align="center" style="border-left: 1px solid #EBEBEB; "><span class="{if $i.konech<0}info_warning_block{/if}">{$i.konech|fn_fvalue:2}</span></td>
        </tr>
    {foreachelse}
        <tr class="no-items">
            <td colspan="5"><p>{$lang.no_data}</p></td>
        </tr>
    {/foreach}
</table>



    <div class="ostatki">
        {*<pre>{$balance|print_r}</pre>*}
        {*<pre>{$smarty.request|print_r}</pre>*}
    </div>
{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.uns_ostatki content=$smarty.capture.mainbox tools=$smarty.capture.tools}
