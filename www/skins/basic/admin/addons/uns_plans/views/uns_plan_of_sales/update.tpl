{capture name="mainbox"}
    {assign var="p" value=$plan}
    {if is__array($p)}
        {assign var="id" value=$p.plan_id}
        {capture name="name"}{$months[$p.month]} {$p.year} г.{/capture}
        {assign var="name" value=$smarty.capture.name}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <hr>
            {include file="addons/uns_plans/views/uns_plan_of_sales/components/plan.tpl"}
            <hr>
            <b>Продажи / Плановая потребность = {$p.sum_ukr_curr} / {$p.sum_ukr_next} / {$p.sum_exp_curr} / {$p.sum_exp_next} шт.</b>
            <hr>
            <span class="info_warning">
                Для каждого насоса и детали можно установить приоритет. Если на деталь припадает и приоритет по насосу и приоритет по детали, то важнее приоритет детали.<br/>
                {literal}
                    <style>
                        table.simple td{
                            padding: 5px 0;
                        }
                    </style>
                {/literal}
                <table class="simple">
                    <thead>
                    <tr>
                        <th class="center" width="70">НАСОС<br>К20/30</th>
                        <th class="center" width="70">Колесо<br>К20/30</th>
                        <th class="center" width="70">Конечный<br>приоритет</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                    </tr>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                    </tr>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                    </tr>
                    </tbody>

                    <tbody>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                    </tr>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                    </tr>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                    </tr>
                    </tbody>

                    <tbody>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                    </tr>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                    </tr>
                    <tr>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                        <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"></td>
                    </tr>
                    </tbody>
                </table>
            </span>
            <hr>
            {include file="addons/uns_plans/views/uns_plan_of_sales/components/items.tpl"}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {if $mode == "add"}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]" hide_second_button=true}
                {else}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
                {/if}
            </div>
        </form>
        <br>
    </div>
{/capture}
{if $id > 0}
    {assign var="title" value="Редактирование: План продаж на `$name`"}
{else}
    {assign var="title" value="Добавить новый план продаж"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}

