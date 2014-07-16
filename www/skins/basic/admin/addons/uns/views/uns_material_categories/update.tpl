{capture name="mainbox"}
    {assign var="i" value=$mcategory}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.mcat_id}
            {assign var="name" value=$i.mcat_name}
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
                f_name="mcat_id"
                f_value=$id}

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="mcat_name"
                        f_value=$i.mcat_name
                        f_description="Наименование"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="mcategories_plain"
                        f_required=true f_integer=false
                        f_name="mcat_parent_id"
                        f_blank=true
                        f_options=$mcategories_plain
                        f_option_id="mcat_id"
                        f_option_value="mcat_name"
                        f_target=$i
                        f_exclude=$id
                        f_description="Расположение"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="mcat_status"
                        f_value=$i.mcat_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="mcat_position"
                        f_value=$i.mcat_position
                        f_default="0"
                        f_description="Позиция"
                    }
                    <hr>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="mcat_position_accounting"
                        f_value=$i.mcat_position_accounting
                        f_default="0"
                        f_description="Позиция категории в отчетах"
                        f_tooltip="Последовательность категорий в Учете движений отливок по складу литья - так исторически сложилось."
                    }
                    <hr>
                    {*Отображать в планах*}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id=$id
                        f_value=$i.view_in_plans
                        f_name="view_in_plans"
                        f_description="Отображать в планах"
                        f_tooltip="Разрешить отображение этой категории в расчетах планирования<br><b>План производства насосов (птичка установлена)</b><br><img src='skins/basic/admin/images/tooltips/pump_series_view_in_plans.png'><br><br><b>План производства насосов (птичка сброшена)</b><img src='skins/basic/admin/images/tooltips/pump_series_view_in_plans_1.png'>"
                    }
                    <hr>
                    {*Отображать в отчетах*}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id=$id
                        f_value=$i.view_in_reports
                        f_name="view_in_reports"
                        f_description="Отображать в отчетах"
                        f_tooltip="Разрешить отображение этой категории в отчетах/таблицах баланса деталей по предприятию"
                    }
                    <hr>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=true f_integer=false
                        f_name="mcat_comment"
                        f_value=$i.mcat_comment
                        f_description="Комментарий"
                    }
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
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}