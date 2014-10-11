{script src="js/tabs.js"}
{strip}
    {literal}
        <script>
            function calc_projected_release (days){
                var weight_C = parseFloat($("#weight_C").val());
                var weight_S = parseFloat($("#weight_S").val());
                var weight_A = parseFloat($("#weight_A").val());
                var weight_W = parseFloat($("#weight_W").val());

//                console.log(number_format(days*weight_C, 1, '.', ' '));
//                console.log(number_format(days*weight_S, 1, '.', ' '));
//                console.log(number_format(days*weight_A, 1, '.', ' '));
//                console.log(number_format(days*weight_W, 1, '.', ' '));
                $("td.tw_C, td.tw_S, td.tw_A, td.tw_W, td.tw_T").empty();
                if (weight_C>0){
                    $("td.tw_C").append(number_format(days*weight_C, 1, '.', ' '));
                }

                if (weight_S>0){
                    $("td.tw_S").append(number_format(days*weight_S, 1, '.', ' '));
                }

                if (weight_A>0){
                    $("td.tw_A").append(number_format(days*weight_A, 1, '.', ' '));
                }

                if (weight_W>0){
                    $("td.tw_W").append(number_format(days*weight_W, 1, '.', ' '));
                }
            }
        </script>
    {/literal}
{capture name="mainbox"}
    {include file="addons/uns/views/components/search/form.tpl" dispatch="`$controller`.manage" s_time=true}
    <br>
    <p><b>ИТОГ выпуска продукции Литейным цехом: {$search.period|fn_get_period_name:$search.time_from:$search.time_to}</b></p>

    <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
        <tr style="background-color: #D4D0C8;">
            <th style="width: 150px; text-transform: none;" class="center">&nbsp;</th>
            <th style="width: 100px; text-transform: none;" class="center b_l">Чугун, кг</th>
            <th style="width: 100px; text-transform: none;" class="center b1_l">Сталь, кг</th>
            <th style="width: 100px; text-transform: none;" class="center b1_l">Алюминий, кг</th>
            <th style="width: 100px; text-transform: none;" class="center b1_l">Чугун белый, кг</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="center"><b>Фактический выпуск</b></td>
            <td class="center b b_l">{if $total_weight.C}{$total_weight.C|number_format:1:".":" "}{else}&nbsp;{/if}</td>
            <td class="center b b1_l">{if $total_weight.S}{$total_weight.S|number_format:1:".":" "}{else}&nbsp;{/if}</td>
            <td class="center b b1_l">{if $total_weight.A}{$total_weight.A|number_format:1:".":" "}{else}&nbsp;{/if}</td>
            <td class="center b b1_l">{if $total_weight.W}{$total_weight.W|number_format:1:".":" "}{else}&nbsp;{/if}</td>
        </tr>
        </tbody>
        {if $search.period == "M"}
            <tr>
                <td class="center">Прогнозируемый выпуск<br>за
                        <input type="hidden" id="weight_C" value="{$avr_weights.C}"/>
                        <input type="hidden" id="weight_S" value="{$avr_weights.S}"/>
                        <input type="hidden" id="weight_A" value="{$avr_weights.A}"/>
                        <input type="hidden" id="weight_W" value="{$avr_weights.W}"/>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select_range"
                        f_name=""
                        f_id=""
                        f_from=1
                        f_to=30
                        f_value=20
                        f_simple=true
                        f_plus_minus=true
                        f_onchange="calc_projected_release(\$(this).val());"
                    }
                    раб. дн.
                </td>
                <td class="tw_C center b_l"></td>
                <td class="tw_S center b1_l"></td>
                <td class="tw_A center b1_l"></td>
                <td class="tw_W center b1_l"></td>
            </tr>
        {/if}
    </table>

    {include file="common_templates/pagination.tpl"}

    <table cellpadding="0" cellspacing="0" border="0" width="" class="table">
        <tr>
            <th width="1px" class="b1_r" style="text-align: center;">№</th>
            <th colspan="2" width="180px" class="b1_r" style="text-align: center;">Наименование</th>
            <th width="120px" class="b1_r" style="text-align: center;">Дата плавки</th>
            <th width="10px" class="" style="text-align: center;">Чугун{include file="common_templates/tooltip.tpl" tooltip="Чугун, кг"}</th>
            <th width="10px" class="b1_l" style="text-align: center;">Сталь{include file="common_templates/tooltip.tpl" tooltip="Сталь, кг"}</th>
            <th width="10px" class="b1_l" style="text-align: center;">Алюм.{include file="common_templates/tooltip.tpl" tooltip="Алюминий, кг"}</th>
            <th width="10px" class="b1_l" style="text-align: center;">Ч/Б{include file="common_templates/tooltip.tpl" tooltip="Чугун белый, кг"}</th>
            {*<th>&nbsp;</th>*}
        </tr>
        {foreach from=$documents item="i" name="doc"}
          <tr {cycle values="class=\"table-row\", "}>
              {assign var="id" value=$i.document_id}
              {assign var="value" value="document_id"}
              {assign var="name" value=$i.document_type}
              {assign var="doc_name" value="<b>№`$id`</b> - `$document_types[$i.type].name_short`" }
              <td align="center" class="b b1_r">{$smarty.foreach.doc.total-$smarty.foreach.doc.index}</td>
              <td class="">
                  {include    file="common_templates/table_tools_list.tpl"
                              popup=true
                              id="`$controller`_`$id`"
                              text="Просмотр документа"
                              act="edit"
                              link_text=$doc_name
                              href="acc_documents.view?`$value`=`$id`"
                              prefix=$id
                              link_class="cm-dialog-auto-size black"
                              tools_list=$smarty.capture.tools_items}
              </td>
              <td class="b1_r" align="right">
                {if strlen($i.comment)}{include file="common_templates/tooltip.tpl" tooltip=$i.comment}{else}&nbsp;{/if}
              </td>
              <td class="b1_r" align="center">
                  <span class="date">{$i.date_cast|fn_parse_date|date_format:"%a %d/%m/%Y"}</span>
              </td>
              <td class="" align="right">
                  {if $i.weight.C}{$i.weight.C|number_format:1:".":" "}{else}&nbsp;{/if}
              </td>
              <td class="b1_l" align="right">
                  {if $i.weight.S}{$i.weight.S|number_format:1:".":" "}{else}&nbsp;{/if}
              </td>
              <td class="b1_l" align="right">
                  {if $i.weight.A}{$i.weight.A|number_format:1:".":" "}{else}&nbsp;{/if}
              </td>
              <td class="b1_l" align="right">
                  {if $i.weight.W}{$i.weight.W|number_format:1:".":" "}{else}&nbsp;{/if}
              </td>
          </tr>
          {foreachelse}
          <tr class="no-items">
              <td colspan="5"><p>{$lang.no_items}</p></td>
          </tr>
        {/foreach}
    </table>

    {include file="common_templates/pagination.tpl"}

{/capture}
{include file="common_templates/mainbox.tpl" title="Выпуск Литейного цеха" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
{/strip}
