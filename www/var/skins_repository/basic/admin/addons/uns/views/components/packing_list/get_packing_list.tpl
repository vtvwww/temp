{*<pre>{$pl__packing_list_series|print_r}</pre>*}
{*<pre>{$smarty.const.UNS_PACKING_PART__PUMP|print_r}</pre>*}

{assign var="ppl_part" value=$smarty.const.UNS_PACKING_PART__PUMP}
{include file="common_templates/subheader.tpl" title=$lang.uns_packing_part__pump}
{include file="addons/uns/views/components/packing_list/get_part.tpl"
         p_type=$pl__packing_list_type
         p_part=$ppl_part
         p_items=$pl__packing_list.$ppl_part
         copy=$copy
         }


{assign var="ppl_part" value=$smarty.const.UNS_PACKING_PART__FRAME}
{include file="common_templates/subheader.tpl" title=$lang.uns_packing_part__frame}
{include file="addons/uns/views/components/packing_list/get_part.tpl"
         p_type=$pl__packing_list_type
         p_part=$ppl_part
         p_items=$pl__packing_list.$ppl_part
         copy=$copy
         }

{assign var="ppl_part" value=$smarty.const.UNS_PACKING_PART__MOTOR}
{include file="common_templates/subheader.tpl" title=$lang.uns_packing_part__motor}
{include file="addons/uns/views/components/packing_list/get_part.tpl"
         p_type=$pl__packing_list_type
         p_part=$ppl_part
         p_items=$pl__packing_list.$ppl_part
         copy=$copy
         }
