{** block-description:polls_side **}

<div class="polls">
{foreach from=$items item=poll}
{if $smarty.request.page_id != $poll.page_id}

{if $poll.completed}
	<h2 class="poll-header">{$lang.polls_have_completed}</h2>
	{if $poll.show_results == "Y"}
	<div class="polls-buttons">
		{include file="buttons/button.tpl" but_text=$lang.view_results but_href="pages.view?page_id=`$poll.page_id`" but_role="text" but_rev="polls_block_`$poll.page_id`" but_meta="cm-dialog-opener cm-dialog-auto-size"}
		<div  id="polls_block_{$poll.page_id}" class="hidden poll-popup" title="{$poll.page|escape:quotes}">
		</div>
	</div>
	{/if}
{else}
	<form name="{$form_name|default:"main_login_form"}" action="{""|fn_url}" method="post">
	<input type="hidden" name="page_id" value="{$poll.page_id}" />
	<input type="hidden" name="redirect_url" value="{$config.current_url}" />
	<input type="hidden" name="obj_prefix" value="{$block.block_id}" />
	{if $poll.header}<p>{$poll.header|unescape}</p>{/if}
	{if $poll.questions}
	{foreach from=$poll.questions item="question"}
	<h2 class="poll-header">{$question.description}{if $question.required == "Y"}&nbsp;<span class="required-question">*</span>{/if}</h2>
	{if $question.type == "T"}
	<textarea name="answer_text[{$question.item_id}]" class="poll-text-answer input-textarea"></textarea>
	{else}
	<ul class="poll">
		{foreach from=$question.answers item="answer"}
			<li>
				{if $question.type == "Q"}
					<input type="radio" class="radio" id="var_{$block.block_id}_{$answer.item_id}" name="answer[{$question.item_id}]" value="{$answer.item_id}" />
				{else}
					<input type="checkbox" id="var_{$block.block_id}_{$answer.item_id}" name="answer[{$question.item_id}][{$answer.item_id}]" value="Y" />
				{/if}
				<label for="var_{$block.block_id}_{$answer.item_id}">{$answer.description}</label>
				{if $answer.type == "O"}
					<p><input type="text" name="answer_more[{$question.item_id}][{$answer.item_id}]" class="input-text" value="" /></p>
				{/if}
			</li>
		{/foreach}
	</ul>
	{/if}
	{/foreach}
	{/if}
	{if $poll.footer}<p>{$poll.footer|unescape}</p>{/if}
	<div class="image-verification">
		{if $settings.Image_verification.use_for_polls == "Y"}
			{include file="common_templates/image_verification.tpl" id="poll_`$block.block_id``$poll.page_id`" sidebox=true}
		{/if}
		<div class="polls-buttons">
			{include file="buttons/button.tpl" but_text=$lang.submit but_name="dispatch[pages.poll_submit]"}
		</div>
	</div>
	</form>
{/if}
{/if}
{/foreach}
</div>