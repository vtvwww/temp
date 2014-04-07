<?php

if(!defined('AREA')){
    die('Access denied');
}

// ТОЛЬКО ДЛЯ ОБСЛУЖИВАНИЯ DB - ЗАПРОСОВ
if($_SERVER['REQUEST_METHOD'] == 'GET' && !defined('AJAX_REQUEST')){
    if($mode == 'backup'){
        $dir_backup_db = DIR_ROOT . '/uns_db_backup/backups/';
        if (!file_exists($dir_backup_db)) mkdir($dir_backup_db);

        $config = Registry::get('config');



        // TEMP
        $db_backup  = new databasebackup (array("file_name"=>$dir_backup_db . fn_date_format(TIME, "%Y-%m-%d__%H-%M-%S") . "_" . UNS_DB . ".sql"));
        $t = microtime_float();
        $db_backup->export(UNS_DB_PREFIX);
        echo "Время выполнения '" . UNS_DB . "': " . round(microtime_float()-$t, 3) . " сек.\n\n";
        unset($db_backup);

        // CSCART
        $db_backup  = new databasebackup (array("file_name"=>$dir_backup_db . fn_date_format(TIME, "%Y-%m-%d__%H-%M-%S") . "_" . $config['db_name'] . ".sql"));
        $t = microtime_float();
        $db_backup->export();
        echo "Время выполнения '". $config['db_name'] . "': " . round(microtime_float()-$t, 3) . " сек.\n\n";
        unset($db_backup);


    }
}

exit;


