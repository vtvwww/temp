{capture name="mainbox"}
{assign var="i" value=$pump}
{if is__array($i)}
    {if $smarty.request.copy != "Y"}
        {assign var="id" value=$i.p_id}
        {assign var="name" value=$i.p_name}
    {else}
        {assign var="id" value=0}
        {assign var="copy" value=true}
    {/if}
{else}
    {assign var="id" value=0}
{/if}

<div id="content_group">
    <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
        <input type="hidden" value="detailed" name="selected_section">
        {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="hidden"
            f_name="p_id"
            f_value=$id
        }

        {capture name="tabsbox"}
            <div id="content_general">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="p_name"
                    f_value=$i.p_name
                    f_description=$lang.name
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="select_by_group"
                    f_name="data[ps_id]"
                    f_required=true f_integer=false f_integer_more_0=true
                    f_options="pump_series"
                    f_option_id="ps_id"
                    f_option_value="ps_name"
                    f_option_target_id=$i.ps_id
                    f_optgroups=$pump_series
                    f_optgroup_label="pt_name"
                    f_description=$lang.uns_pump_series
                    f_blank=true
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="status"
                    f_required=true f_integer=false
                    f_name="p_status"
                    f_value=$i.p_status
                    f_description="Статус"
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="p_position"
                    f_value=$i.p_position
                    f_default="0"
                    f_description="Позиция"
                }
                <hr/>
                {*ВЕС НАСОСА*}
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="weight_p"
                    f_value=$i.weight_p|fn_fvalue
                    f_description="Вес насоса, кг"
                }

                {*ВЕС НАСОСА НА РАМЕ*}
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="weight_pf"
                    f_value=$i.weight_pf|fn_fvalue
                    f_description="Вес насоса на раме, кг"
                }

                {*ВЕС НАСОСНОГО АГРЕГАТА*}
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="weight_pa"
                    f_value=$i.weight_pa|fn_fvalue
                    f_description="Вес насосного агрегата, кг"
                }
                <hr/>
                {*Включить название этого насоса в список насосов, к которым принадлежит деталь или отливка*}
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="checkbox"
                    f_id=$id
                    f_value=$i.include_to_accessory
                    f_name="include_to_accessory"
                    f_description="Включить в список принадлежность к насосам"
                    f_tooltip="Если в настройках детали(заготовки) выбран режим отображения принадлежности: <По насосоам>, тогда при установленой птичке, название этого насоса будет отображено в списке принадлежности к насосам.<br><br>Птичка установлена.<br><img src='skins/basic/admin/images/tooltips/pumps_include_to_accessory.png'><br><br>Птичка сброшена.<br><img src='skins/basic/admin/images/tooltips/pumps_include_to_accessory_1.png'>"
                }
                <hr/>
                {*Считать эту насосную единицу как набор деталей*}
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="checkbox"
                    f_id=$id
                    f_value=$i.as_set_of_details
                    f_name="as_set_of_details"
                    f_description="Считать эту насосную единицу как набор деталей"
                    f_tooltip="Считать эту насосную единицу как набор деталей. Это необходимо для расчета планирования производства насосов.<br>Например, <Д320-50 ротор в сб.> - это комплект деталей, а не насос, поэтому он должен быть исключен из расчетов плана производства, но он будет отображен на балансе Склада готовой продукции.<br><b>ПЛАН ПРОИЗВОДСТВА НАСОСОВ<br>Птичка установлена.</b><br>На 01/07/2014 на СГП есть 2 насоса серии 1Д315/71.<br><img src='skins/basic/admin/images/tooltips/pumps_as_set_of_details.png'><br><br>Хотя на Балансе СГП мы видим еще и три ротора в сборе.<br><img src='skins/basic/admin/images/tooltips/pumps_as_set_of_details_1.png'>. <br><br><b>Птичка сброшена.</b><br>Тогда эти ротора в сборе будут считаться как насосы, и мы увидим не 2 насоса этой серии, а уже 5. А это неправильно. Что будет сбивать с толку мех. цеха, литейный цех и коммерческий отдел.<br><img src='skins/basic/admin/images/tooltips/pumps_as_set_of_details_2.png'>"
                }
                <hr/>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="textarea"
                    f_required=false f_integer=false
                    f_name="p_comment"
                    f_value=$i.p_comment
                    f_description="Комментарий"
                }


            </div>
            {if $mode != "add"}
            <div id="content_packing_list" class="hidden">
                {include file="addons/uns/views/components/packing_list/get_packing_list.tpl" copy=$copy}
            </div>

            <div id="content_accounting" class="hidden">
                {include file="addons/uns/views/components/get_form_accounting.tpl"}
            </div>
            <div id="content_features" class="hidden">
                {include file="addons/uns/views/components/get_form_features.tpl"}
            </div>
            <div id="content_options" class="hidden">
                {include file="addons/uns/views/components/get_form_options.tpl"}
            </div>
            {/if}
        {/capture}

        {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

        <div class="buttons-container cm-toggle-button buttons-bg">
            {assign var="hide_second_button" value=false}
            {if $mode == "add"}
                {assign var="hide_second_button" value=true}
            {/if}
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]" hide_second_button=$hide_second_button}
        </div>
    </form>
</div>
{/capture}

{capture name="tools"}
    <div class="tools-container">
   		<span class="action-btn">
   			<a target="_blank" href="{"`$controller`.packing_list_view&p_id=`$id`"|fn_url}">Полное описание насоса</a>
   		</span>
   	</div>
{/capture}


{if $id > 0}
    {assign var="title" value="Редактирование насоса: `$name`"}
{else}
    {assign var="title" value="Добавить новый насос"}
{/if}


{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.tools}