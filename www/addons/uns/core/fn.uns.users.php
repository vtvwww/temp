<?php



function fn_uns__get_user_name($id){
    return  db_get_field("SELECT lastname FROM ?:users WHERE user_id = ?i", $id);
}
