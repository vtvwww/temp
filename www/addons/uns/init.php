<?php
if ( !defined('AREA') ) { die('Access denied'); }

define('uns', 'uns');



// include configs
require_once(DIR_ADDONS . 'uns/config.php');

// include functions
foreach (fn_get_dir_contents(DIR_ADDONS . 'uns/core', false, true, 'php') as $fns){
    require_once(DIR_ADDONS . 'uns/core/' . $fns);
}

fn_register_hooks(
    "before_dispatch"
);

// Проверка на выполнение резервирование базы данных.
if (uns_check_locking_db()){
    $msg = "Внимание, сейчас выполняется резервное копирование базы даных программы.<br>Резервное копирование выполняется по будням в 8:00, 11:00, 14:00 и 16:00 часов в течение до 1 минуты.<br>Пожалуйста, ожидайте это время. ;-)";
    $ms_ = "РЕЗЕРВНОЕ КОПИРОВАНИЕ\\n\\nВнимание, сейчас выполняется резервное копирование базы даных программы.\\nРезервное копирование выполняется по будням в 8:00, 11:00, 14:00 и 16:00 часов в течение до 1 минуты.\\nПожалуйста, ожидайте это время. ;-)";
    if (defined('AJAX_REQUEST')){
        fn_set_notification("E", "Резервное копирование", $msg, "K");
        exit;
    }else{
        fn_echo(iconv("utf-8", "windows-1251", "<script type='text/javascript'>alert('{$ms_}');</script>"));
        die;
    }
}



function uns_check_locking_db (){
    $config = Registry::get('config');
    $status_uns_db      = db_get_row(UNS_DB_PREFIX . "SHOW OPEN TABLES FROM " . UNS_DB . " LIKE 'uns_is_lock';");
//    fn_set_notification("E", "Резервное копирование", print_r($status_uns_db,1), "K");
//    $status_unscscart   = db_get_row("SHOW OPEN TABLES FROM " . $config['db_name'] . " LIKE 'uns_is_lock';");
    return ($status_uns_db["In_use"]);
}

