<?php

//******************************************************************************
//*** Группы пользователей                                                   ***
//******************************************************************************
// http://uns/ua.php?dispatch=usergroups.manage
define("USER_GROUP__CHIEF",         8);  // Группа Начальник ПДО
define("USER_GROUP__DEPUTY_CHIEF",  6);  // Заместитель начальника ПДО
define("USER_GROUP__FOUNDRY",       7);  // Литейный цех
define("USER_GROUP__MECH_DEP",      9);  // Литейный цех
define("USER_GROUP__SALES_DEP",     10);  // Коммерческий отдел


if (is_numeric($_SESSION['auth']['usergroup_ids'][0])){

    $arr_addons = array(
        "uns",
        "uns_acc",
        "uns_foundry",
        "uns_mech",
        "uns_orders",
        "uns_plans",
        "uns_reports",
    );
    // Блокировать все контроллеры
    foreach ($arr_addons as $addon){
        foreach (fn_get_dir_contents(DIR_ADDONS . "$addon/controllers/admin", false, true, 'php') as $controller){
            $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = false;
            $schema['controllers'][substr($controller, 0, -4)]['modes']['calculation']['permissions'] = false;
            $schema['controllers'][substr($controller, 0, -4)]['modes']['tracking']['permissions'] = false;
        }
    }

    switch ($_SESSION['auth']['usergroup_ids'][0]){
        case USER_GROUP__DEPUTY_CHIEF:  // Заместитель начальника ПДО
            // UNS
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
            }
            // UNS_ACC
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns_acc/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
            }
            // UNS_FOUNDRY
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns_foundry/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
            }
            // uns_mech
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns_mech/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
            }
            // uns_mech
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns_orders/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
            }
            // uns_plans
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns_plans/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['calculation']['permissions'] = true;
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
                $schema['controllers'][substr($controller, 0, -4)]['modes']['tracking']['permissions'] = true;
            }
            // uns_reports
            foreach (fn_get_dir_contents(DIR_ADDONS . "uns_reports/controllers/admin", false, true, 'php') as $controller){
                $schema['controllers'][substr($controller, 0, -4)]['modes']['manage']['permissions'] = true;
                $schema['controllers'][substr($controller, 0, -4)]['modes']['get_report']['permissions'] = true;
            }
            break;

        case USER_GROUP__FOUNDRY:
        case USER_GROUP__MECH_DEP:
            $schema['controllers']['foundry_get_balance']['modes']['manage']['permissions'] = true;
            $schema['controllers']['foundry_get_report']['modes']['manage']['permissions'] = true;

            $schema['controllers']['uns_balance_mc_sk_su']['modes']['manage']['permissions'] = true;

            $schema['controllers']['uns_balance_sgp']['modes']['manage']['permissions'] = true;

            $schema['controllers']['uns_kits']['permissions']['GET'] = true;
            $schema['controllers']['uns_kits']['modes']['manage']['permissions'] = true;

            $schema['controllers']['uns_moving_mc_sk_su']['modes']['manage']['permissions'] = true;
            $schema['controllers']['uns_sheets']['modes']['manage']['permissions'] = true;

            $schema['controllers']['uns_plan_of_mech_dep']['modes']['manage']['permissions'] = true;
            $schema['controllers']['uns_balance_stores']['modes']['manage']['permissions'] = true;
        break;

        case USER_GROUP__SALES_DEP:
            $schema['controllers']['foundry_get_balance']   ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_balance_sgp']       ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_balance_mc_sk_su']  ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_plan_of_mech_dep']  ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_plan_of_sales']     ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_plan_of_sales']     ['modes']['tracking']   ['permissions'] = true;

            $schema['controllers']['uns_orders']            ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_customers']         ['modes']['manage']     ['permissions'] = true;
            $schema['controllers']['uns_balance_stores']    ['modes']['manage']['permissions'] = true;

            break;

    }

}




?>