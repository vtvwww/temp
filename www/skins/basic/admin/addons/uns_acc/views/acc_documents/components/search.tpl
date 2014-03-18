{strip}
{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {* Дата проведение документа *}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>Дата проведения документа:</label>
                    <div class="break">
                        {include file="common_templates/period_selector.tpl" period=$search.period prefix=""}
                    </div>
                </td>
            </tr>
        </table>

        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>Тип документа:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="document_type"
                            f_name="type"
                            f_options=$document_types
                            f_with_id=true
                            f_target=$search.type
                            f_enabled_items=$document_types_enabled
                            f_simple=true
                        }
                    </div>
                </td>
            </tr>
        </table>

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="П о и с к" but_name="dispatch[`$dispatch`]" but_role="big" but_input_css="width:888px;font-weight:bold;"}
                    {*{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}*}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{/strip}