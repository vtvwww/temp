{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {$search_content}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}