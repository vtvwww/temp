{* РЕАЛИЗАЦИЯ управления ДЕТАЛЯМИ *}
{assign var="label" value="_id_"}
{literal}
    <script type="text/javascript">
        var add_index = 0;
        $('table.sheet_details span.add_detail').live('click', function(e){
            var replace;
            var row;
            var search;

            add_index++;
            row = $('table.sheet_details tbody.template').html();
            replace = 'add_' + add_index;
            row = row.replace(/{/literal}{$label}{literal}/g, replace);
            $('table.sheet_details tbody.data').append(row);
        });

        $('select[name^="details"][name$="[detail_id]"]').live('change', function (e) {
            calc_sheet_details();
        });

        $('table.sheet_details span.delete_detail').live('click', function(e){
            $(this).parent().parent().remove();     // удалить текущую строку
            calc_sheet_details();                   // выполнить перерасчет таблицы с деталями
        });


        // Смена КАТЕГОРИИ детали
        $('select[name^="details"][name$="[dcat_id]"]').live('change', function (e) {
            var dcat_id     = $(this);
            var detail_id   = $(this).parent().parent().find('select[name^="details"][name$="[detail_id]"]');
            detail_id.empty();

            if (dcat_id.val() > 0){
                $.ajaxRequest(
                    fn_url('uns_sheets.get_details'),
                    {
                        hidde: false,
                        method: 'post',
                        data: {
                            event       : "change__dcat_id",
                            dcat_id     : dcat_id.val()
                        },
                        callback: function(data){
                            detail_id.append(data.options);
                            calc_sheet_details();
                        }
                    }
                );
            }
        });

        function calc_sheet_details (){
            var res = "";
            $('table.sheet_details tbody:not(".template") tr').each(function() {
                var d_id        = $(this).find("input[name='detail_id']").val();
                var detail_id   = $(this).find("select[name='details[" + d_id + "][detail_id]']").val();
                if (detail_id>0){
                    res += detail_id+"|";
                }
            });
            res = res.substr(0,res.length-1);
            $("input#details").val(res);
        }

    </script>
    <style type="text/css">
        table.sheet_details span.add_detail{
            background-color: #518000;
            border: 1px solid #518000;
            color: #FFFFFF;
            cursor: pointer;
            display: block;
            font-size: 18px;
            font-weight: bold;
            height: 16px;
            line-height: 14px;
            margin: 0;
            padding: 0;
        }

        table.sheet_details span.delete_detail{
            background-color: red;
            border: 1px solid red;
            color: #FFFFFF;
            cursor: pointer;
            display: block;
            font-size: 18px;
            font-weight: bold;
            height: 16px;
            line-height: 14px;
            margin: 0;
            padding: 0;
        }
    </style>

{/literal}

<div class="form-field" id="material">
    <label for="details" class="cm-required">Детали:</label>
    <table class="table sheet_details">
        <thead>
        <tr>
            <th>{if !$disabled_details}<span class="add_detail">+</span>{/if}</th>
            <th>Категория</th>
            <th>Наименование</th>
            <th>&nbsp;</th>
        </tr>
        </thead>

        {*ЗАГОТОВКА ДЛЯ ВСТАВКИ*}
        <tbody class="hidden template">
            <tr>
                <td>
                    <input type="hidden" name="detail_id" value="{$label}"/>
                </td>
                <td>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="dcategories_plain"
                        f_name="details[`$label`][dcat_id]"
                        f_options=$dcategories_plain
                        f_option_id="dcat_id"
                        f_option_value="dcat_name"
                        f_option_target_id=0
                        f_with_q_ty=false
                        f_blank=true
                        f_blank_name="---"
                        f_simple=true
                    }
                </td>
                <td>
                    <select name="details[{$label}][detail_id]">
                        <option value="0">---</option>
                    </select>
                </td>
                <td>
                    <span class="delete_detail">x</span>
                </td>
            </tr>
        </tbody>

        <tbody class="data">
        {if is__array($details)}
            {assign var="input_details" value=""}
            {foreach from=$details item="d" name="d"}
                {if $smarty.foreach.d.last}
                    {*{assign var="input_details" value="`$input_details``$d.detail_id`:`$d.quantity`"}*}
                    {assign var="input_details" value="`$input_details``$d.detail_id`"}
                {else}
                    {*{assign var="input_details" value="`$input_details``$d.detail_id`:`$d.quantity`|"}*}
                    {assign var="input_details" value="`$input_details``$d.detail_id`|"}
                {/if}

                <tr>
                    <td>
                        {$smarty.foreach.d.iteration}
                        <input type="hidden" name="detail_id" value="{$d.detail_id}" {if $disabled_details}disabled="disabled"{/if} />
                    </td>
                    <td>
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="dcategories_plain"
                            f_name="details[`$d.detail_id`][dcat_id]"
                            f_options=$dcategories_plain
                            f_option_id="dcat_id"
                            f_option_value="dcat_name"
                            f_option_target_id=$d.dcat_id
                            f_disabled=$disabled_details
                            f_with_q_ty=false
                            f_blank=true
                            f_blank_name="---"
                            f_simple=true
                        }
                    </td>
                    <td>
                        <select name="details[{$d.detail_id}][detail_id]" {if $disabled_details}disabled="disabled"{/if}>
                            <option value="0">---</option>
                            <option selected="selected" value="{$d.detail_id}">{if strlen($d.detail_no)}[{$d.detail_no}] {/if}{$d.detail_name}</option>
                        </select>
                    </td>
                    <td>{if !$disabled_details}<span class="delete_detail">x</span>{/if}</td>
                </tr>
            {/foreach}
        {/if}
        </tbody>
    </table>
    <input type="hidden" readonly id="details" name="{$e_n}[details]" value="{$input_details}" {if $disabled_details}disabled="disabled"{/if} style="font-size: 10px; font-weight: bold; border: none;"/>
</div>
{*<pre>{$details|print_r}</pre>*}