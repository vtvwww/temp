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
                    f_type="input"
                    f_required=true f_integer=false
                    f_name="name"
                    f_value=$i.name
                    f_description="Полное имя"
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