{include file="common_templates/subheader.tpl" title=$lang.customer_information}

{assign var="profile_fields" value=$location|fn_get_profile_fields}
{split data=$profile_fields.C size=2 assign="contact_fields" simple=true}

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orders-info">
<tr valign="top">
	<td width="31%">
		{if $profile_fields.B}
			<h5>{$lang.billing_address}</h5>
			<div class="orders-field">{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.B title=$lang.billing_address}</div>
		{/if}
	</td>
	<td width="31%">
		{if $profile_fields.S}
			<h5>{$lang.shipping_address}</h5>
			<div class="orders-field">{include file="views/profiles/components/profile_fields_info.tpl" fields=$profile_fields.S title=$lang.shipping_address}</div>
		{/if}
	</td>
	<td width="35%">
		{if $contact_fields.0}
			{capture name="contact_information"}
				{include file="views/profiles/components/profile_fields_info.tpl" fields=$contact_fields.0 title=$lang.contact_information}
			{/capture}
			{if $smarty.capture.contact_information|trim != ""}
				<h5>{$lang.contact_information}</h5>
				<div class="orders-field">{$smarty.capture.contact_information}</div>
			{/if}
		{/if}
	</td>
</tr>
</table>