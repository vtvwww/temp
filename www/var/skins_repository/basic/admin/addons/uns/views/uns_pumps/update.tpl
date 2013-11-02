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