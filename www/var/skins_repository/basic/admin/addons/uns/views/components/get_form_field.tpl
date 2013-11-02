{strip}


{******************************************************************************}
{* HIDDEN                                                                     *}
{******************************************************************************}
{if $f_type == "hidden"}
    <input type="hidden" name="{$f_name}" value="{$f_value}" {if $f_class} class="{$f_class}" {/if} />
{/if}



{******************************************************************************}
{* INPUT                                                                      *}
{******************************************************************************}
{if $f_type == "input"}
    {if $f_simple_text}
        <span {if $f_class}class="{$f_class}"{/if}>{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}</span>
    {elseif $f_simple}
        <input type="text" {if $f_id}id="{$f_name}_{$f_id}"{/if} {if $f_name}name="{$f_name}"{/if} size="35" value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" {if $f_class}class="{$f_class}"{else}class="input-text-short{*medium*} main-input {$f_add_class} "{/if} {if $f_style} style="{$f_style}" {/if}  {if $f_disabled}disabled="disabled"{/if}  />
    {else}
        <div class="form-field">
             <label class="{if $f_required}cm-required{/if}{if $f_integer} cm-integer{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
             <input type="text" id="{$f_name}_{$f_id}" name="data[{$f_name}]" size="35" value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" class="input-text-large main-input" />
         </div>
    {/if}
{/if}



{******************************************************************************}
{* STATUS                                                                     *}
{******************************************************************************}
{if $f_type == "status"}
    {if $f_simple_text}
        {if $f_value == "A"}<span {if $f_class}class="{$f_class}"{/if}>{$lang.active}</span>{/if}
        {if $f_value == "D"}<span {if $f_class}class="{$f_class}"{/if}>{$lang.disabled}</span>{/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_disabled}disabled="disabled"{/if} {if $f_id}id="{$f_id}"{/if} >
            <option value="A" {if $f_value == "A"}selected="selected"{/if}>{$lang.active}</option>
            <option value="D" {if $f_value == "D"}selected="selected"{/if}>{$lang.disabled}</option>
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                <option value="A" {if $f_value == "A"}selected="selected"{/if}>{$lang.active}</option>
                <option value="D" {if $f_value == "D"}selected="selected"{/if}>{$lang.disabled}</option>
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* TYPESIZE                                                                   *}
{******************************************************************************}
{if $f_type == "typesize"}
    {assign var="size_m_name" value="Ном."}
    {assign var="size_a_name" value="исп.А"}
    {assign var="size_b_name" value="исп.Б"}
    {assign var="size_m" value="M"}
    {assign var="size_a" value="A"}
    {assign var="size_b" value="B"}
    {if $f_simple_text}
        {if $f_target == "M"}<span {if $f_class}class="{$f_class}"{/if}>{$size_m_name}</span>{/if}
        {if $f_target == "A"}<span {if $f_class}class="{$f_class}"{/if}>{$size_a_name}</span>{/if}
        {if $f_target == "B"}<span {if $f_class}class="{$f_class}"{/if}>{$size_b_name}</span>{/if}
    {elseif $f_simple}
        <option value="{$size_m}">{$size_m_name}</option>
        <option value="{$size_a}" {if $f_a == "D"}disabled="disabled"{/if}>{$size_a_name}</option>
        <option value="{$size_b}" {if $f_b == "D"}disabled="disabled"{/if}>{$size_b_name}</option>
    {else}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if}>
            {if !$f_empty}
                <option value="M" {if $f_target == "M"} selected="selected" {/if}>{$size_m_name}</option>
                <option value="A" {if $f_target == "A"} selected="selected" {/if} {if $f_a == "D"} disabled="disabled" {/if}>{$size_a_name}</option>
                <option value="B" {if $f_target == "B"} selected="selected" {/if} {if $f_b == "D"} disabled="disabled" {/if}>{$size_b_name}</option>
            {/if}
        </select>
    {/if}
{/if}



{******************************************************************************}
{* TEXTAREA                                                                   *}
{******************************************************************************}
{if $f_type == "textarea"}
    <div class="form-field">
        <label class="" for="{$f_name}_{$f_id}">{$f_description}:</label>
        <textarea id="{$f_name}_{$f_id}" name="data[{$f_name}]" rows="{$f_row|default:"5"}" class="input-textarea-long">{if $f_value_prefix}{$f_value_prefix}{/if}{$f_value}{if $f_value_suffix}{$f_value_suffix}{/if}</textarea>
    </div>
{/if}



{******************************************************************************}
{* SELECT                                                                     *}
{******************************************************************************}
{if $f_type == "select"}
    {if $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j"}
                {if $j.$f_option_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.$f_option_value}</span>{/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0">---</option>{/if}
            {if is__array($f_options)}
            {foreach from=$f_options item="j"}
                <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add}{$f_option_value_add_prefix}{$j.$f_option_value_add}{$f_option_value_add_suffix}{/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
            {/foreach}
            {/if}
        </select>
    {elseif $f_simple_2}
        {if $f_blank}<option value="0">---</option>{/if}
        {if is__array($f_options)}
        {foreach from=$f_options item="j"}
            <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add} ({$j.$f_option_value_add}) {/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
        {/foreach}
        {/if}
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                {if $f_blank}<option value="0" {if $f_option_target_id == 0} selected="selected" {/if}>---</option>{/if}
                {if is__array($f_options)}
                {foreach from=$f_options item="j"}
                    <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add} ({$j.$f_option_value_add}) {/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* MCATEGORIES_PLAIN                                                          *}
{******************************************************************************}
{if $f_type == "mcategories_plain"}
    {if $f_simple_2}
        {if $f_blank}<option value="0" {if $f_target.mcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                    {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                    <option value="{$j.mcat_id}" {if $j.mcat_status == "D"}disabled="disabled"{/if} {if $j.mcat_id == $f_target.mcat_parent_id}selected="selected"{/if} {if $j.mcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                    {if $j.mcat_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</span>{/if}
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0" {if $f_target.mcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
            {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                    {if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}
                    <option value="{$j.mcat_id}" {if $j.mcat_status == "D"}disabled="disabled"{/if} {if $j.mcat_id == $f_target.mcat_parent_id}selected="selected"{/if} {if $j.mcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                {/if}
            {/foreach}
            {/if}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                {if $f_blank}<option value="0" {if $f_target.mcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
                <option disabled="disabled">&nbsp;</option>
                {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                        {if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}
                        <option value="{$j.mcat_id}" {if ($j.mcat_status == "D") or (is__more_0($f_exclude) and ($f_exclude==$j.mcat_id))}disabled="disabled"{/if} {if $j.mcat_id == $f_target.mcat_parent_id}selected="selected"{/if} {if $j.mcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                    {/if}
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* DCATEGORIES_PLAIN                                                          *}
{******************************************************************************}
{if $f_type == "dcategories_plain"}
    {if $f_simple_2}
        {if $f_blank}<option value="0" {if $f_target.dcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                    {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                    <option value="{$j.dcat_id}" {if $j.dcat_status == "D"}disabled="disabled"{/if} {if $j.dcat_id == $f_target.dcat_parent_id}selected="selected"{/if} {if $j.dcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                    {if $j.dcat_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</span>{/if}
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0" {if $f_target.dcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
            {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                        {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                        <option value="{$j.dcat_id}" {if $j.dcat_status == "D"}disabled="disabled"{/if} {if $j.dcat_id == $f_target.dcat_parent_id}selected="selected"{/if} {if $j.dcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                    {/if}
                {/foreach}
            {/if}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                {if $f_blank}<option value="0" {if $f_target.dcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
                <option disabled="disabled">&nbsp;</option>
                {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                        {if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}
                        <option value="{$j.dcat_id}" {if ($j.dcat_status == "D") or (is__more_0($f_exclude) and ($f_exclude==$j.dcat_id))}disabled="disabled"{/if} {if $j.dcat_id == $f_target.dcat_parent_id}selected="selected"{/if} {if $j.dcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                    {/if}
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* OBJECTS_PLAIN                                                              *}
{******************************************************************************}
{if $f_type == "objects_plain"}
    {if $f_simple_2}
        {if $f_blank}<option value="0" {if $f_target.o_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                    {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                    <option value="{$j.o_id}" {if $j.o_status == "D"}disabled="disabled"{/if} {if $j.o_id == $f_target.o_parent_id}selected="selected"{/if} {if $j.o_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.o_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                    {if $j.o_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.o_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</span>{/if}
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0" {if $f_target.o_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
            {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                        {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                        <option value="{$j.o_id}" {if $j.o_status == "D"}disabled="disabled"{/if} {if $j.o_id == $f_target.o_parent_id}selected="selected"{/if} {if $j.o_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.o_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                    {/if}
                {/foreach}
            {/if}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}"  {if $f_disabled}disabled="disabled"{/if} >
                {if $f_blank}<option value="0" {if $f_target.o_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}---{/if}</option>{/if}
                {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                        <option value="{$j.o_id}" {if (!in_array($j.o_id, $f_options_enabled))}disabled="disabled"{/if} {if $j.o_id == $f_target.o_parent_id}selected="selected"{/if} >{$j.o_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</option>
                    {/if}
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* SELECT_BY_GROUP                                                            *}
{******************************************************************************}
{if $f_type == "select_by_group"}
   {if $f_simple}
       <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled} disabled="disabled" {/if}>
           {if $f_blank}<option value="0">---</option>{/if}
           {if is__array($f_optgroups)}
           {foreach from=$f_optgroups item="optgroup"}
               {if is__array($optgroup.$f_options)}
                <optgroup label="{$optgroup.$f_optgroup_label}">
                   {foreach from=$optgroup.$f_options item="j"}
                       <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add}{$f_option_value_add_prefix}{$j.$f_option_value_add}{$f_option_value_add_suffix}{/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                   {/foreach}
                </optgroup>
               {/if}
           {/foreach}
           {/if}
       </select>
   {else}
       <div class="form-field">
           {*<label class="{if $f_required}cm-required{/if} {if $f_integer}cm_integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}"*}
                  {*for="{$id}">{$f_description}++:</label>*}
           {*<select name="data[{$f_name}]" id="{$f_name}_{$f_id}">*}
           <label class="{if $f_required}cm-required{/if} {if $f_integer}cm-integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}"
                  for="{$f_id}">{$f_description}:</label>
           <select name="{$f_name}" id="{$f_id}">
               {if $f_blank}<option>---</option>{/if}
               {if is__array($f_optgroups)}
               {foreach from=$f_optgroups item="optgroup"}
                   {if is__array($optgroup.$f_options)}
                    <optgroup label="{$optgroup.$f_optgroup_label}">
                       {foreach from=$optgroup.$f_options item="j"}
                           <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add}{$f_option_value_add_prefix}{$j.$f_option_value_add}{$f_option_value_add_suffix}{/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                       {/foreach}
                    </optgroup>
                   {/if}
               {/foreach}
               {/if}
           </select>
       </div>

   {/if}
{/if}



{******************************************************************************}
{* DATE                                                                       *}
{******************************************************************************}
{if $f_type == "date"}
   {if $f_simple}
       {assign var="r" value=10000000|rand:99999999}
       {include file="addons/uns/views/components/calendar.tpl"
                date_id="date_`$r`__`$f_id`"
                date_name=$f_name
                date_val=$f_value
                start_year="2000" end_year="2030"}
   {else}
       <div class="form-field">
           <label class="{if $f_required}cm-required{/if}" for="date_{$f_id}">{$f_description}:</label>
       	   {include file="addons/uns/views/components/calendar.tpl" date_id="date_`$f_id`" date_name="data[`$f_name`]" date_val=$f_value|default:$smarty.now start_year="2000" end_year="2030" date_style="width:120px;"}
       </div>
   {/if}
{/if}



{******************************************************************************}
{* DOCUMENT_TYPE                                                              *}
{******************************************************************************}
{if $f_type == "document_type"}
    {if $f_simple}
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer}cm-integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_id}">{$f_description}:</label>
            <select name="{$f_name}" id="{$f_id}" {if $f_disabled} disabled="disabled" {/if}>
                {if $f_blank}<option>---</option>{/if}
                <option {if $f_option_target == $smarty.const.UNS_DOCUMENT__PRIH_ORD} selected="selected" {/if} value="{$smarty.const.UNS_DOCUMENT__PRIH_ORD}"> {$smarty.const.UNS_DOCUMENT__PRIH_ORD_NAME}</option>
                <option {if $f_option_target == $smarty.const.UNS_DOCUMENT__RASH_ORD} selected="selected" {/if} value="{$smarty.const.UNS_DOCUMENT__RASH_ORD}"> {$smarty.const.UNS_DOCUMENT__RASH_ORD_NAME}</option>
                <option {if $f_option_target == $smarty.const.UNS_DOCUMENT__NOPM}     selected="selected" {/if} value="{$smarty.const.UNS_DOCUMENT__NOPM}">     {$smarty.const.UNS_DOCUMENT__NOPM_NAME}</option>
                <option {if $f_option_target == $smarty.const.UNS_DOCUMENT__SDAT_N}   selected="selected" {/if} value="{$smarty.const.UNS_DOCUMENT__SDAT_N}">   {$smarty.const.UNS_DOCUMENT__SDAT_N_NAME}</option>
            </select>
        </div>


    {/if}
{/if}


{******************************************************************************}
{* TRANSACTION                                                                *}
{******************************************************************************}
{if $f_type == "transaction"}
    {if $f_simple}
    {else}
        <div class="form-field">
            <label class="" for="{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_id}" {if $f_disabled} disabled="disabled" {/if} onchange="fn_toggle_transaction(this)">
                <option {if $f_transaction_status == "N"} selected="selected" {/if} value="N">Нет</option>
                <option {if $f_transaction_status == "Y"} selected="selected" {/if} value="Y">Да</option>
            </select>

            {* Дата *}
            {if is__more_0($f_transaction_date)}
                {assign var="transaction_date" value=$transaction_date|fn_parse_date|date_format:"%d/%m/%Y"}
            {/if}
            &nbsp;&nbsp;&nbsp;
            <span class="transaction_date {if $f_transaction_status == "N"} hidden {/if}">{$transaction_date}</span>

            {literal}
                <script type="text/javascript">
                    function fn_toggle_transaction(sel) {
                        var value = sel.options[sel.selectedIndex].value;
                        if (value == "Y") $('span.transaction_date').removeClass('hidden');
                        else $('span.transaction_date').addClass('hidden');

                    }
                </script>
            {/literal}
        </div>


    {/if}
{/if}



{/strip}