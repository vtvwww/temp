<?php
if ( !defined('AREA') ) { die('Access denied'); }

define("UNS_DIR_JS", "skins/basic/admin/addons/uns/js");
define("UNS_DIR_CSS", "skins/basic/admin/addons/uns/css");
define("UNS_DIR_VIEW", "skins/basic/admin/addons/uns/views");

// Указатели на базу данных
define("UNS_DB", PROJECT_NAME);
define("UNS_DB_PREFIX", UNS_DB . "#");

// Формат Даты
define("UNS_DATE", "d/m/Y");

// Служебные константы
define("UNS_DATA_NOT_BE_DELETED",   "Удаление невозможно!");
define("UNS_DATA_UPDATED",          "Данные обновлены");
define("UNS_NO_DATA", "Нет данных");
define("UNS_NO_DATA_FORMAT", "<span class='info_warning'>" . UNS_NO_DATA . "</span>");
define("UNS_ERROR", "<span class='info_warning'>Ошибка №84</span>");

define("UNS_ITEMS_PER_PAGE", 40);

define ("UNS_UNIT_CATEGORY__AREA",      4);     // ID категории ед. изм. Единицы площади
define ("UNS_UNIT_WEIGHT",              21);    // ID ед. изм. "кг"
define ("UNS_MATERIAL_CATEGORY__CAST",  27);    // ID категории отливок

// Состояние
define("UNS_Y_N", "|Y|N|");

// Типы единиц
define("UNS_ITEM_TYPES", "|P|PF|PA|M|D|B|");

// Исполнения
define("UNS_TYPESIZE__M_name",  "Исп. ном.");       // Исполнение НОМИНАЛЬНОЕ
define("UNS_TYPESIZE__A_name",  "Исп. с подр. А");  // Исполнение A
define("UNS_TYPESIZE__B_name",  "Исп. с подр. Б");  // Исполнение Б
define("UNS_TYPESIZE__M",       "M");    // Исполнение НОМИНАЛЬНОЕ
define("UNS_TYPESIZE__A",       "A");       // Исполнение A
define("UNS_TYPESIZE__B",       "B");       // Исполнение Б
define("UNS_TYPESIZES",         "|M|A|B|");

// Тип движения
define("UNS_MOTION_TYPE__I",    "I");       // Input приход
define("UNS_MOTION_TYPE__O",    "O");       // Output расход
define("UNS_MOTION_TYPE__IO",   "IO");       // Input/Output расход
define("UNS_MOTION_TYPES",      "|I|O|IO|");

// Статус сопроводительного листа 'OP','CL','CN'
define("UNS_STATUS_SHEET__OP",    "OP");       // Open
define("UNS_STATUS_SHEET__CL",    "CL");       // Close
define("UNS_STATUS_SHEET__CN",    "CN");       // Cancel
define("UNS_STATUS_SHEET",      "|OP|CL|CN|");

// Статус KIT
define("UNS_KIT_STATUS__O",    "O");
define("UNS_KIT_STATUS__K",    "K");
define("UNS_KIT_STATUS__U",    "U");
define("UNS_KIT_STATUS__Z",    "Z");
define("UNS_KIT_STATUS__A",    "A");
define("UNS_KIT_STATUS",      "|O|K|U|Z|A|");

// Тип KIT
define("UNS_KIT_TYPE__P",    "P"); // Насосы
define("UNS_KIT_TYPE__D",    "D"); // Детали
define("UNS_KIT_TYPE",      "|P|D|");


// Комплектация
define("UNS_PACKING_TYPE__ITEM",  "I");       // Позиция относится к комплектации ЕДИНИЧНОГО насоса
define("UNS_PACKING_TYPE__SERIES","S");       // Позиция относится к комплектации СЕРИИ насосов
define("UNS_PACKING_TYPES",       "|I|S|");

define("UNS_PACKING_PART__PUMP",  "P");
define("UNS_PACKING_PART__FRAME", "F");
define("UNS_PACKING_PART__MOTOR", "M");
define("UNS_PACKING_PARTS",       "|P|F|M|");  // Части агрегата: Насос, Рама, Двигатель

// Замещение
define("UNS_PACKING_REPLACEMENT__DELETE",  "D");
define("UNS_PACKING_REPLACEMENT__REPLACE", "R");
define("UNS_PACKING_REPLACEMENTS", "|D|R|");

// LANGS
define("L_material_no", "Клеймо");
define("L_detail_no", "Обозначение");



