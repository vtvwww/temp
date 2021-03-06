{capture name="mainbox"}
    {assign var="i" value=$material}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.material_id}
            {assign var="name" value=$i.material_name}
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
                f_name="material_id"
                f_value=$id
            }

            {capture name="tabsbox"}
                <div id="content_general">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="material_name"
                        f_value=$i.material_name
                        f_description="Наименование"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=false f_integer=false
                        f_name="material_no"
                        f_value=$i.material_no
                        f_description=$smarty.const.L_material_no
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="select"
                        f_required=true f_integer=false f_integer_more_0=true
                        f_name="mclass_id"
                        f_options=$mclasses
                        f_option_id="mclass_id"
                        f_option_value="mclass_name"
                        f_option_target_id=$i.mclass_id
                        f_description=$lang.uns_material_classes
                        f_blank=true
                    }


                    {* НАЧАЛО -> Блок выбора типа литья*}
                    {literal}
                        <script type="text/javascript">
                            $(function () {
                                $('select[name="data[mclass_id]"]').live('change', function (e) {
                                    if ($(this).val() == '1'){ // Литье
                                        $('div.type_casting').removeClass('hidden') ;
                                    }else{
                                        $('div.type_casting').addClass('hidden');
                                    }
                                });
                            });
                        </script>
                    {/literal}

                    {if $i.mclass_id == 1}
                        {assign var="type_casting_hidden" value=false}
                    {else}
                        {assign var="type_casting_hidden" value=true}
                    {/if}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id="type_casting"
                        f_type="type_casting"
                        f_name="type_casting"
                        f_hidden=$type_casting_hidden
                        f_value=$i.type_casting
                    }
                    {* КОНЕЦ  -> Блок выбора типа литья*}


                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="mcategories_plain"
                        f_required=true f_integer=false  f_integer_more_0=true
                        f_name="mcat_id"
                        f_options=$mcategories_plain
                        f_option_id="mcat_id"
                        f_option_value="mcat_name"
                        f_option_target_id=$i.mcat_id
                        f_description=$lang.uns_material_categories
                        f_blank=true
                        f_blank_name="---"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="status"
                        f_required=true f_integer=false
                        f_name="material_status"
                        f_value=$i.material_status
                        f_description="Статус"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="material_position"
                        f_value=$i.material_position
                        f_default="0"
                        f_description="Позиция"
                    }
                    <hr/>
                    {*Минимально необходимый остаток отливки на складе литья - используется для расчета плана производства литейного цеха*}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="min_necessary_rest"
                        f_value=$i.min_necessary_rest
                        f_default="0"
                        f_description="Минимально необходимый остаток"
                        f_tooltip="Минимально необходимый остаток отливки на складе литья - используется для расчета плана производства литейного цеха"
                    }
                    <hr/>
                    {*Режим отображения принадлежности*}
                    <div class="form-field">
                        <label for="accessory_view_{$id}" class="cm-required">Режим отображения применяемости{include file="common_templates/tooltip.tpl" tooltip="<b>По сериям насосов</b><br>Во многих случаях стоит выбирать именно этот режим. Так как он является информативным и компактным.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_s.png'><br><br><b>По насосам</b> Может быть слишком избыточно.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_p.png'><br><br><b>Указать вручную</b><br>Бывают случаи, когда невозможно рассчитать применяемость детали, тогда следует установить ее вручную в следующем поле для ввода.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_m.png'>"}:</label>
                        <select id="accessory_view_{$id}" name="data[accessory_view]">
                            <option {if $i.accessory_view == "S"} selected="selected" {/if} value="S">по сериям насосов</option>
                            <option {if $i.accessory_view == "P"} selected="selected" {/if} value="P">по насосам</option>
                            <option {if $i.accessory_view == "M"} selected="selected" {/if} value="M">указать вручную</option>
                        </select>
                    </div>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="accessory_manual"
                        f_value=$i.accessory_manual
                        f_description="Применяемость в насосах вручную"
                        f_tooltip="Текст, который будет отображен как применяемость в насосах, если в качестве <Режима отображения принадлежности> был выбран <указать вручную>.<br><img src='skins/basic/admin/images/tooltips/details_accessory_view_m_1.png'><br><br><img src='skins/basic/admin/images/tooltips/details_accessory_view_m.png'>"
                    }

                    <hr>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="textarea"
                        f_required=false f_integer=false
                        f_name="material_comment_1"
                        f_value=$i.material_comment_1
                        f_description="Комментарий"
                    }
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
            {/capture}

            {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
            </div>
        </form>
    </div>
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$name` [`$i.material_no`]"}
{else}
    {assign var="title" value="Добавить"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}