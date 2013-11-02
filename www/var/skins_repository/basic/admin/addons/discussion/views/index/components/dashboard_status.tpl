<div class="float-right nowrap right" id="post_{$post.post_id}">
	{assign var="lang_approved" value=$lang.approved|escape:dquotes}
	{assign var="lang_disapproved" value=$lang.disapproved|escape:dquotes}

	{include file="common_templates/select_popup.tpl" id=$post.post_id status=$post.status hidden="" object_id_name="post_id" table="discussion_posts" items_status="A: \"`$lang_approved`\", D: \"`$lang_disapproved`\""}
	<span>{$post.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span>&nbsp;-&nbsp;
<!--post_{$post.post_id}--></div>