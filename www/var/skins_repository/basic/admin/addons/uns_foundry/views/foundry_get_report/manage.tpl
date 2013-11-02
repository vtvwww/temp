{script src="js/tabs.js"}

{assign var="cast_weight"   value=$total_weight.C}
{assign var="still_weight"  value=$total_weight.S}

{capture name="mainbox"}
    {include file="addons/uns/views/components/search/form.tpl" dispatch="`$controller`.manage" s_time=true}
    <br>
    <p><b>ИТОГ выпуска продукции Литейным цехом: {$search.period|fn_get_period_name:$search.time_from:$search.time_to}</b></p>

    <table border="1" style="border-collapse: collapse;">
        <tr>
            <td>Чугунное литье</td>
            <td align="right"><b>{$cast_weight|fn_fvalue:"4"} кг</b></td>
        </tr>
        <tr>
            <td>Стальное литье</td>
            <td align="right"><b>{$still_weight|fn_fvalue:"4"} кг</b></td>
        </tr>
    </table>

    {include file="common_templates/pagination.tpl"}

    <table cellpadding="0" cellspacing="0" border="0" width="" class="table">
        <tr>
            <th width="200px">Наименование</th>
            <th width="70px">Дата</th>
            <th width="10px">Чугун, кг</th>
            <th width="10px">Сталь, кг</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$documents item=i}
          <tr {cycle values="class=\"table-row\", "}>
              {assign var="id" value=$i.document_id}
              {assign var="value" value="document_id"}
              {assign var="name" value=$i.document_type}
              <td>
                  {if $i.document_type == $smarty.const.UNS_DOCUMENT__PRIH_ORD}
                      <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__PRIH_ORD_NAME}
                  {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__SDAT_N}
                      <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__SDAT_N_NAME}
                  {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__NOPM}
                      <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__NOPM_NAME}
                  {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__INPM}
                      <b>№{$id}</b> {$smarty.const.UNS_DOCUMENT__INPM_NAME}
                  {else}
                       Неизвестный тип Документа!
                  {/if}
              </td>
              <td>
                  {$i.document_date|fn_parse_date|date_format:"%d/%m/%Y"}
              </td>
              <td align="right">
                  {$i.weight.C|fn_fvalue:2}
              </td>
              <td align="right">
                  {$i.weight.S|fn_fvalue:2}
              </td>
              <td class="nowrap right">
                  {include    file="common_templates/table_tools_list.tpl"
                              popup=true
                              id="`$controller`_`$id`"
                              text="Позиции накладной"
                              act="edit"
                              link_text="Просмотр"
                              href="acc_book_documents.update?`$value`=`$id`&lock=Y"
                              prefix=$id
                              tools_list=$smarty.capture.tools_items}
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
{include file="common_templates/mainbox.tpl" title="Сдаточные накладные по Литейному цеху" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
