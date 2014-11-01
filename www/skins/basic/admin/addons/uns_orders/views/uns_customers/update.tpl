{capture name="mainbox"}
    {assign var="i" value=$customer}
    {if is__array($i)}
        {assign var="id"    value=$i.customer_id}
        {assign var="name"  value=$i.name}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="" name="selected_section">
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="hidden"
                f_name="customer_id"
                f_value=$id
            }

            <div id="content_general">
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="select"
                    f_required=true f_integer=true
                    f_name="country_id"
                    f_options=$countries
                    f_option_id="id"
                    f_option_value="name"
                    f_description="Страна"
                    f_blank=true
                    f_option_target_id=$i.country_id
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="select"
                    f_required=true f_integer=true
                    f_name="region_id"
                    f_options=$regions
                    f_option_id="id"
                    f_option_value="name"
                    f_description="Регион/Область"
                    f_option_target_id=$i.region_id
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="select"
                    f_required=true f_integer=true
                    f_name="city_id"
                    f_options=$cities
                    f_option_id="id"
                    f_option_value="name"
                    f_description="Город"
                    f_option_target_id=$i.city_id
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="name"
                    f_value=$i.name
                    f_description="Полное имя"
                    f_tooltip=""
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="name_short"
                    f_value=$i.name_short
                    f_description="Аббревиатура"
                }
                <span style="font-size: 11px;font-weight: bold;margin-left: 190px;color: #000000;">Аббревиатура должна состоять только из трех символов, которые будут однозначно определять клиента.</span>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="status"
                    f_required=true f_integer=false
                    f_name="status"
                    f_value=$i.status
                    f_description="Статус"
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="position"
                    f_value=$i.position
                    f_default="0"
                    f_description="Позиция"
                }

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="input"
                    f_required=false f_integer=false
                    f_name="tin"
                    f_value=$i.tin
                    f_description="ИИН"
                    f_tooltip="Идентификационный номер налогоплательщика"
                }

                <div class="form-field">
                    <label class="cm-required " for="to_export">Куда отгрузка:</label>
                    <select id="to_export" name="data[to_export]">
                        <option value="N">по Украине</option>
                        <option value="Y" {if $i.to_export == "Y"}selected="selected"{/if}>за пределами Украины (экспорт)</option>
                    </select>
                </div>

                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_id=$id
                    f_type="textarea"
                    f_required=true f_integer=false
                    f_name="comment"
                    f_row=1
                    f_value=$i.comment
                    f_description="Комментарий"
                }
            </div>

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