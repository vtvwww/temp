{capture name="mainbox"}

<iframe src="{$webmail_url}" name="webmail" id="webmail_frame" width="98%" height="730" style="border: 1px solid #e2e7e9;"></iframe>

{/capture}
{include file="common_templates/mainbox.tpl" content=$smarty.capture.mainbox title=$lang.webmail}