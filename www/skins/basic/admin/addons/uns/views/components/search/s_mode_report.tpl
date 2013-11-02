{strip}
    {literal}
        <script type="text/javascript">
            $(function () {
                $('select#mode_report').live('change', function (e) {
                    if ($(this).val() == 'P'){
                        $('div.mode_report_pumps').removeClass('hidden') ;
                    }else{
                        $('div.mode_report_pumps').addClass('hidden');
                    }
                });
            });
        </script>
    {/literal}
    {assign var="mode_report" value="I"}
    {if $search.mode_report == "I" or $search.mode_report == "P"}
        {assign var="mode_report" value=$search.mode_report}
    {/if}
    <table class="search-header" cellpadding="0" cellspacing="0" width=""	border="0">
        <tr>
            <td class="nowrap search-field">
                <label for="mode_report">Способ представления отчета:</label>
                <div class="break">
                    <select name="mode_report" id="mode_report" class="input-text">
                        <option {if $mode_report == "I"} selected="selected" {/if} value="I">В разрезе деталей</option>
                        <option	{if $mode_report == "P"} selected="selected" {/if} value="P">В разрезе насоса</option>
                    </select>
                </div>
            </td>
            <td class="nowrap search-field">
                <div class="mode_report_pumps {if $mode_report == "I"}hidden{/if}">
                    <label for="pump_id">Выбор насоса:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id="pump_id"
                            f_type="select_by_group"
                            f_name="pump_id"
                            f_required=true f_integer_more_0=true
                            f_options="pumps"
                            f_option_id="p_id"
                            f_option_value="p_name"
                            f_option_target_id=$search.pump_id|default:"0"
                            f_optgroups=$pumps
                            f_optgroup_label="ps_name"
                            f_description=$lang.uns_accounting_main_unit
                            f_blank=true
                            f_simple=true
                        }
                    </div>

                </div>
            </td>
        </tr>
    </table>
{/strip}
