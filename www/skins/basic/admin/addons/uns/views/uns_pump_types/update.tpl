{capture name="mainbox"}
    {assign var="i" value=$pump_type}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.pt_id}
            {assign var="name" value=$i.pt_name}
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
                f_name="pt_id"
                f_value=$id}

            {capture name="tabsbox"}
                <div id="content_general_{$id}">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="pt_name"
                        f_value=$i.pt_name
                        f_description="Наименование"}

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="pt_name_short"
                        f_value=$i.pt_name_short
                        f_description="Краткое наименование"
                        f_tooltip="Используется для удобства выбора серий насосов<br><img src='skins/basic/admin/images/tooltips/pump_types_pt_name_short.png'>"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="pt_status"
                        f_value=$i.pt_status
                        f_description="Статус"}

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="pt_position"
                        f_value=$i.pt_position
                        f_default="0"
                        f_description="Позиция"}
                    <hr/>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id=$id
                        f_value=$i.view_in_plans
                        f_name="view_in_plans"
                        f_description="Отображать в планах"
                        f_tooltip="Разрешить отображение насосов этого типа в расчетах планирования.<br><b>План производства насосов (птичка установлена)</b><br><img src='skins/basic/admin/images/tooltips/pump_types_view_in_plans.png'><br><br><b>План производства насосов (птичка сброшена)</b><br><img src='skins/basic/admin/images/tooltips/pump_types_view_in_plans_1.png'>"
                    }
                    <hr/>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="pt_comment"
                        f_value=$i.pt_comment
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
