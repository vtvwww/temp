{capture name="section"}
    <form action="{""|fn_url}" method="post" name="search_form">
        {include file="common_templates/period_selector.tpl" period=$period form_name="orders_search_form" display="form" but_name="dispatch[`$dispatch`]"}
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}