<?php
if(!defined('AREA')){
    die('Access denied');
}

define("UNS_ACC_DIR_JS", "skins/basic/admin/addons/uns_acc/js");
define("UNS_ACC_DIR_CSS", "skins/basic/admin/addons/uns_acc/css");
define("UNS_ACC_DIR_VIEW", "skins/basic/admin/addons/uns_acc/views");



// Типы документов
define("UNS_DOCUMENT__PRIH_ORD",        "PRIH_ORD");  // Приходный ордер
define("UNS_DOCUMENT__PRIH_ORD_NAME",   "Приходный ордер");  // Приходный ордер
define("UNS_DOCUMENT__RASH_ORD",        "RASH_ORD");  // Расходный ордер
define("UNS_DOCUMENT__RASH_ORD_NAME",   "Расходный ордер");  // Расходный ордер
define("UNS_DOCUMENT__NOPM",            "NOPM");      // Накладная на отпуск/перемещение материалов
define("UNS_DOCUMENT__NOPM_NAME",       "Накладная на ОП материалов");      // Накладная на отпуск/перемещение материалов
define("UNS_DOCUMENT__SDAT_N",          "SDAT_N");    // Сдаточная накладная
define("UNS_DOCUMENT__SDAT_N_NAME",     "Сдаточная накладная");    // Сдаточная накладная
define("UNS_DOCUMENT__INPM",            "INPM");    // Накладная на внутреннее перемещение
define("UNS_DOCUMENT__INPM_NAME",       "Накладная на внутреннее перемещение");    // Сдаточная накладная
define("UNS_DOCUMENTS",                 "|PRIH_ORD|RASH_ORD|NOPM|SDAT_N|INPM|");

define("UNS_MOTION__IN",    "IN");          // ПРИХОД
define("UNS_MOTION__OUT",   "OUT");         // РАСХОД
define("UNS_MOTIONS",       "|IN|OUT|");

// Пакеты документов
define("UNS_PACKAGE_TYPE__N",    "N");          // None - не пакет документов
define("UNS_PACKAGE_TYPE__SL",   "SL");         // SL - сопроводительный лист
define("UNS_PACKAGE_TYPE__PN",   "PN");         // PN - партия насосов
define("UNS_PACKAGE_TYPES",      "|SL|PN|N|");    //


define("DOC_TYPE__PO",      6);   //Приходный ордер	10
define("DOC_TYPE__VLC",     1);   //Выпуск Литейного цеха
define("DOC_TYPE__MCP",     2);   //Межцеховое перемещения
define("DOC_TYPE__VCP",     3);   //Внутрицеховые перемещения
define("DOC_TYPE__VCP_COMPLETE",     12);   //Внутрицеховые перемещения
define("DOC_TYPE__ID",      4);   //Изготовление детали(-ей)
define("DOC_TYPE__SD",      5);   //Сборка деталей
define("DOC_TYPE__RO",      7);   //Расходный ордер
define("DOC_TYPE__AIO",     8);   //Акт изменения остатков
define("DOC_TYPE__AS_VLC",  9);   //Акт списания материалов на Литейный цех
define("DOC_TYPE__PVP",     10);   //Акт списания материалов на Литейный цех
define("DOC_TYPE__BRAK",    11);   //Акт списания материалов на Литейный цех
define("DOC_TYPE__VN",      13);   //Выпуск насосов
define("DOC_TYPE__VD",      14);   //Выпуск деталей
