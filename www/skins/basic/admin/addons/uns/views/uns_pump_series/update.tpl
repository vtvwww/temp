{capture name="mainbox"}
    {assign var="i" value=$pump_series_one}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.ps_id}
            {assign var="name" value=$i.ps_name}
        {else}
            {assign var="id" value=0}
            {assign var="copy" value=true}
        {/if}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="" name="selected_section">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="ps_id"
                f_value=$id
            }

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="ps_name"
                        f_value=$i.ps_name
                        f_description=$lang.name
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="select"
                        f_required=true f_integer=false f_integer_more_0=true
                        f_name="pt_id"
                        f_options=$pump_types
                        f_option_id="pt_id"
                        f_option_value="pt_name"
                        f_option_target_id=$i.pt_id
                        f_description=$lang.uns_pump_types
                        f_blank=true
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="ps_status"
                        f_value=$i.ps_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="ps_position"
                        f_value=$i.ps_position
                        f_default="0"
                        f_description="Позиция"
                    }
                    <hr/>
                    {*Отображать в планах*}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id=$id
                        f_value=$i.view_in_plans
                        f_name="view_in_plans"
                        f_description="Отображать в планах"
                        f_tooltip="Разрешить отображение этой серии насосов в расчетах планирования"
                    }
                    <hr/>
                    {*Размеры партий*}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select_range"
                        f_id="party_size_min"
                        f_name="data[party_size_min]"
                        f_required=true f_integer_more_0=true
                        f_description="Размер партии MIN"
                        f_from=1
                        f_to=150
                        f_value=$i.party_size_min|default:1
                        f_plus_minus=true
                        f_tooltip="Минимальное значение партии насосов для расчета плана производства насосов для механического цеха и расчета плана производства отливок для литейного цеха"
                    }
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select_range"
                        f_id="party_size_max"
                        f_name="data[party_size_max]"
                        f_required=true f_integer_more_0=true
                        f_description="Размер партии MAX"
                        f_from=2
                        f_to=150
                        f_value=$i.party_size_max|default:2
                        f_plus_minus=true
                        f_tooltip="Максимальное значение партии насосов для расчета плана производства насосов для механического цеха и расчета плана производства отливок для литейного цеха"
                    }
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select_range"
                        f_id="party_size_step"
                        f_name="data[party_size_step]"
                        f_required=true f_integer_more_0=true
                        f_description="Размер партии STEP"
                        f_from=1
                        f_to=20
                        f_value=$i.party_size_step|default:1
                        f_plus_minus=true
                        f_tooltip="Шаг увеличения значения партии насосов от минимального до максимального.<br>Например, если МИН=10, МАКС=22, ШАГ=3, тогда размер партии может быть равен: 10, 13, 16, 19 и 22"
                    }

                    <hr/>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="ps_comment"
                        f_value=$i.ps_comment
                        f_description="Комментарий"
                    }
                </div>
                <div id="content_packing_list" class="hidden">
                    {include file="addons/uns/views/components/packing_list/get_packing_list.tpl" copy=$copy}
                </div>
            {/capture}

            {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
            </div>
        </form>
    </div>
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$i.ps_name`"}
{else}
    {assign var="title" value="Добавить"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}
