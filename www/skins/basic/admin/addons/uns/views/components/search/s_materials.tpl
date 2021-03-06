{strip}
    {literal}
        <script type="text/javascript">
            $(function () {
                $('select#material_classes').live('change', function (e) {
                    if ($(this).val() == '1'){ // Литье
                        $('select#type_casting').parent().parent().removeClass('hidden') ;
                    }else{
                        $('select#type_casting').parent().parent().addClass('hidden');
                    }
                });
            });
        </script>
    {/literal}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            {*<td class="nowrap search-field">*}
                {*<label for="item_type_M">{$lang.uns_materials}:</label>*}
                {*<div class="break">*}
                    {*<input type="radio" name="item_type" id="item_type_M" value="M" {if $search.item_type=="M" or ($search.item_type!="M" and $search.item_type!="D")} checked="checked" {/if}>*}
                {*</div>*}
            {*</td>*}
            {*<td class="nowrap search-field">*}
                {*{if $material_classes_as_input}*}
                    {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                        {*f_type="hidden"*}
                        {*f_name="mclass_id"*}
                        {*f_value=1*}
                    {*}*}
                {*{else}*}
                    {*<label>{$lang.uns_material_classes}:</label>*}
                    {*<div class="break">*}
                        {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                            {*f_id="material_classes"*}
                            {*f_type="select"*}
                            {*f_name="mclass_id"*}
                            {*f_options=$mclasses*}
                            {*f_option_id="mclass_id"*}
                            {*f_option_value="mclass_name"*}
                            {*f_option_target_id=$search.mclass_id*}
                            {*f_simple=true*}
                            {*f_blank=true*}
                        {*}*}
                    {*</div>*}
                {*{/if}*}
            {*</td>*}
            <td class="nowrap search-field">
                <input type="hidden" name="mclass_id" value="1">
                <input type="hidden" name="item_type" value="M">
                <label>{$lang.uns_material_categories}:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="mcategories_plain"
                        f_required=true f_integer=false
                        f_name="mcat_id"
                        f_options=$mcategories_plain
                        f_option_id="mcat_id"
                        f_option_value="mcat_name"
                        f_option_target_id=$search.mcat_id
                        f_simple=true
                        f_blank=true
                        f_with_q_ty=false
                        f_blank_name="---"
                    }
                </div>
            </td>
            <td class="nowrap search-field {if $search.mclass_id !=1} hidden {/if} b1_l">
                <label>Тип литья:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id="type_casting"
                        f_type="type_casting"
                        f_name="type_casting"
                        f_value=$search.type_casting
                        f_simple=true
                    }
                </div>
            </td>
            <td class="nowrap search-field  b1_l">
                <label>Наименование:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="material_name"
                        f_value=$search.material_name
                        f_simple=true
                        f_style="width:100px;"
                    }
                </div>
            </td>
            <td class="nowrap search-field  b1_l">
                <label>{$smarty.const.L_material_no}:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="material_no"
                        f_value=$search.material_no
                        f_simple=true
                    }
                </div>
            </td>
            <td class="nowrap search-field  b1_l">
                <label class="hand" for="all_materials">Отобразить все заготовки:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id="all_materials"
                        f_name="all_materials"
                        f_value=$search.all_materials
                        f_simple=true
                    }
                </div>
            </td>
            <td class="nowrap search-field  b1_l">
                <label class="hand" for="only_for_pumps">Отливки только<br>для насосов: {include file="common_templates/tooltip.tpl" tooltip='Из отчета будут исключены отливки:<br><b>На собственные нужды</b><br><b>На продажу</b><br><b>Старое литье</b>'}</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id="only_for_pumps"
                        f_name="only_for_pumps"
                        f_value=$search.only_for_pumps
                        f_simple=true
                    }
                </div>
            </td>
        </tr>
    </table>
{/strip}