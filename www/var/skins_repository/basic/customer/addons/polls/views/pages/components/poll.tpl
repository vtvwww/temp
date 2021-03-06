<div class="polls">
{if $poll.completed}
	{if $poll.show_results == "N"}
		<p>{$lang.text_you_have_already_filled_this_poll}</p>
		{if $poll.results}<p>{$poll.results|unescape}</p>{/if}
	{else}
		{if $poll.results}<p>{$poll.results|unescape}</p>{/if}
		{if $poll.questions}
		<div class="polls-results">
			{foreach from=$poll.questions item="question"}
				<h2 class="poll-header">{$question.description}</h2>
				{if $question.type == "T"}
					<p>{$lang.polls_answers_with_comments}</p>
					{include file="addons/polls/views/pages/components/graph_bar.tpl" value_width=$question.results.ratio color=$_color count=$question.results.count ratio=$question.results.ratio}
				{else}
					{foreach from=$question.answers item=answer}
					{if $answer.results.max_ratio}
						{assign var="_color" value="1"}
					{else}
						{assign var="_color" value=""}
					{/if}		
						{include file="addons/polls/views/pages/components/graph_bar.tpl" value_width=$answer.results.ratio color=$_color count=$answer.results.count ratio=$answer.results.ratio answer_description=$answer.description}
					{/foreach}
				{/if}
			{/foreach}
			<p class="polls-total">{$lang.polls_total_votes}: {$poll.summary.total}</p>
		</div>
		{/if}
	{/if}
	<div class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.continue_shopping but_href=$index_script}
	</div>

{else}
	<form name="{$form_name|default:"main_login_form"}" action="{""|fn_url}" method="post">
	<input type="hidden" name="page_id" value="{$poll.page_id}" />
	<input type="hidden" name="obj_prefix" value="{$obj_prefix}" />
	<input type="hidden" name="redirect_url" value="{$config.current_url}" />
	
	{if $poll.has_required_questions}{$lang.text_mandatory_fields}{/if}
	
	{if $page.description}<p>{$page.description|unescape}</p>{/if}
	
	{if $poll.header}<p>{$poll.header|unescape}</p>{/if}

	{if $poll.questions}
	{foreach from=$poll.questions item="question"}
		<h2 class="poll-header">{$question.description}{if $question.required == "Y"}<span class="required-question">*</span>{/if}</h2>
		
		{if $question.type == "T"}
			<textarea name="answer_text[{$question.item_id}]" class="input-textarea poll-other-answer" cols="81" rows="10"></textarea>
		{else}
	
		<ul class="poll">
		{foreach from=$question.answers item="answer"}
			<li>
			{if $question.type == "Q"}
				<input type="radio" name="answer[{$question.item_id}]" value="{$answer.item_id}" id="var_{$obj_prefix}{$answer.item_id}" class="radio" />
			{else}
				<input type="checkbox" name="answer[{$question.item_id}][{$answer.item_id}]" value="Y" id="var_{$obj_prefix}{$answer.item_id}" />
			{/if}
			<label for="var_{$obj_prefix}{$answer.item_id}">{$answer.description}</label>
			{if $answer.type == "O"}<p><input type="text" name="answer_more[{$question.item_id}][{$answer.item_id}]" class="input-text" /></p>{/if}
			</li>
		{/foreach}
		</ul>
		{/if}
	{/foreach}
	{/if}
	
	{if $poll.footer}<p>{$poll.footer|unescape}</p>{/if}
	
	{if $settings.Image_verification.use_for_polls == "Y"}
		{include file="common_templates/image_verification.tpl" id="poll_`$obj_prefix``$poll.page_id`"}
	{/if}
	
	<div class="buttons-container">
		{include file="buttons/button.tpl" but_text=$lang.submit but_name="dispatch[pages.poll_submit]" }
		{include file="buttons/button.tpl" but_text=$lang.view_results but_href="pages.view?page_id=`$poll.page_id`" but_role="text" but_rev="polls_block_`$poll.page_id`" but_meta="cm-dialog-opener cm-dialog-auto-size"}
		<div  id="polls_block_{$poll.page_id}" class="hidden poll-popup" title="{$poll.page|escape:quotes}">
		</div>
	</div>
	
	</form>
{/if}
</div>