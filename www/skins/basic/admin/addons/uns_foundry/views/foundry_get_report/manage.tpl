{script src="js/tabs.js"}

{capture name="mainbox"}
    {include file="addons/uns/views/components/search/form.tpl" dispatch="`$controller`.manage" s_time=true}
    <br>
    <p><b>ИТОГ выпуска продукции Литейным цехом: {$search.period|fn_get_period_name:$search.time_from:$search.time_to}</b></p>

    <table border="1" style="border-collapse: collapse;" cellpadding="5" >
        <tr>
            <th>Чугун, кг</th>
            <th>Сталь, кг</th>
            <th>Алюминий, кг</th>
            <th>Чугун белый, кг</th>
        </tr>
        <tr>
            <td align="center"><b>{$total_weight.C}</b></td>
            <td align="center"><b>{$total_weight.S}</b></td>
            <td align="center"><b>{$total_weight.A}</b></td>
            <td align="center"><b>{$total_weight.W}</b></td>
        </tr>
    </table>

    {include file="common_templates/pagination.tpl"}

    <table cellpadding="0" cellspacing="0" border="0" width="" class="table">
        <tr>
            <th width="1px" class="b1_r" style="text-align: center;">№</th>
            <th width="120px" class="b1_r" style="text-align: center;">Наименование</th>
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
              <td align="left" class="b1_r">{$smarty.foreach.doc.total-$smarty.foreach.doc.index}</td>
              <td class="b1_r">
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
              <td class="b1_r" align="center">
                  <span class="date">{$i.date_cast|fn_parse_date|date_format:"%a %d/%m/%Y"}</span>
              </td>
              <td class="" align="right">
                  {if $i.weight.C}{$i.weight.C}{else}&nbsp;{/if}
              </td>
              <td class="b1_l" align="right">
                  {if $i.weight.S}{$i.weight.S}{else}&nbsp;{/if}
              </td>
              <td class="b1_l" align="right">
                  {if $i.weight.A}{$i.weight.A}{else}&nbsp;{/if}
              </td>
              <td class="b1_l" align="right">
                  {if $i.weight.W}{$i.weight.W}{else}&nbsp;{/if}
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
