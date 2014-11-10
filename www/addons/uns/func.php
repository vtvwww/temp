<?php

if(!defined('AREA')){
    die('Access denied');
}


/**
 * ИНИЦИАЛИЗАЦИЯ БАЗЫ ДАННЫХ ПОД UNS
 */
function fn_uns_before_dispatch() {
    db_initiate("localhost", "uns_db", "yrhveXtSVQaypKxA", UNS_DB, UNS_DB, "uns_");
}

function is__more_0($i) {
    $numargs = func_num_args();
    if(!is_numeric($numargs) || $numargs <= 0){
        return false;
    } else{
        foreach(func_get_args() as $arg){
            if(!is_numeric($arg) || $arg <= 0){
                return false;
            }
        }
    }
    return true;
}

function to__array($i) {
    if(is_array($i)){
        return $i;
    } elseif(!is_array($i) && is_numeric($i) && $i > 0){
        return ((array) $i);
//    } elseif(!is_array($i) && is_string($i) && strlen($i)){
//        return ((array) $i);
    } else return false;
}

function is__array($i) {
    if(is_array($i) && !empty($i)){
        return true;
    } else return false;
}

function in__range($val, $min, $max) {
    return ($val >= $min && $val <= $max);
}

function trim__data($data) {
    if(!is__array($data)){
        $data = trim($data);
    } else{
        foreach($data as $k_d => $v_d){
            if(!is__array($v_d)){
                $data[$k_d] = trim($v_d);
            } else{
                $data[$k_d] = trim__data($v_d);
            }
        }
    }
    return $data;
}


?>