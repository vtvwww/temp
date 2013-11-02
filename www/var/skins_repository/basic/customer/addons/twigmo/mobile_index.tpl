{* $Id: mobile_index.tpl 11823 2011-02-11 15:55:09Z $ *}
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta content="Twigmo" name="description" /> 
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes" />

<meta name="MobileOptimized" content="width" />
<meta name="HandheldFriendly" content="true" />

<link rel="apple-touch-icon" href="{$mobile_scripts_url}/images/apple-touch-icon.png" />
<link rel="shortcut icon" href="{$mobile_scripts_url}/images/icon.png" />

<link rel="stylesheet" type="text/css" href="{$mobile_scripts_url}/jquery-mobile.css" />
<link rel="stylesheet" type="text/css" href="{$mobile_scripts_url}/jquery.easyslider.css">
<link rel="stylesheet" type="text/css" href="{$mobile_scripts_url}/app.css" />
{if $tw_settings.apply_custom_css == "Y"}
	<link rel="stylesheet" type="text/css" href="{$config.skin_path}/addons/twigmo/custom.css" rel="stylesheet"/>
{/if}
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="{$mobile_scripts_url}/ie.css" />
<![endif]-->

<script type="text/javascript" src="{$mobile_scripts_url}/splash.js"></script>
<script type="text/javascript" src="{$mobile_scripts_url}/settings.js"></script>

<script type="text/javascript">
//<![CDATA[
TWGGlobal.currencySettings = {$currency_settings|fn_to_json};
TWGGlobal.currencySettings.symbol = "{$currency_settings.symbol|html_entity_decode}";
TWGGlobal.allow_negative_amount = "{$settings.General.allow_negative_amount}";
//]]>
</script>

<script type="text/javascript" src="{$mobile_scripts_url}/jquery.js"></script>
<script type="text/javascript" src="{$mobile_scripts_url}/twigmo.js"></script>
<script type="text/javascript" src="{$mobile_scripts_url}/jquery-mobile.js"></script>

</head>
<body id= "main-body" class="template-mobilefrontpage portaltype-plone-site icons-on" dir="ltr"></body>
{if $addons.google_analytics.status == "A"}
	{include file="addons/google_analytics/hooks/index/footer.post.tpl"}
{/if}
</html>
