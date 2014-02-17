<?php

function fn_rpt__foundry($data){
    $period = fn_get_period_name($data['search']['period'], $data['search']['time_from'], $data['search']['time_to']);
    $params = array(
        'header_title' =>   "ООО «ТОРГОВЫЙ ДОМ «УКРНАСОССЕРВИС»",
        'header_text' =>    "ОТЧЕТ ПО РАБОТЕ ЛИТЕЙНОГО ЦЕХА " . fn_strtolower($period),
    );
    $pdf = new UNS_TCPDF();
    $pdf->uns_set_config($params);

    $pdf->AddPage();

    $d = array();

    $pdf->uns_SetFont("BI", 19);
    $pdf->Cell(0, 0, 'ОТЧЕТ ПО РАБОТЕ ЛИТЕЙНОГО ЦЕХА', 0, 1, 'C', 0, '', 0);
    $pdf->uns_SetFont("B", 16);
    $pdf->Cell(0, 0, $period, 0, 1, 'C', 0, '', 0);
    $pdf->Cell(0, 0, '', 0, 1, 'L', 0, '', 0);

    //--------------------------------------------------------------------------
    // 1. ПОСУТОЧНЫЙ ОБЪЕМ ВЫПУСКА ПРОДУКЦИИ
    //--------------------------------------------------------------------------
    $total_weight = $data['search']['total_weight'];

    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(0, 10, 'ПОСУТОЧНЫЙ ОБЪЕМ ВЫПУСКА ОТЛИВОК', 0, 1, 'L', 0, '', 0);
    $pdf->uns_SetFont("B", 11);
    $pdf->Cell(10, 0, '№', 1, 0, 'C', 0, '', 0);
    $pdf->Cell(90, 0, 'Дата плавки', 1, 0, 'L', 0, '', 0);
    $pdf->Cell(20, 0, 'Чугун, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(20, 0, 'Сталь, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(20, 0, 'Алюминий, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(20, 0, 'Чугун б., кг', 1, 1, 'C', 0, '', 1);

    $pdf->uns_SetFont("R", 12);
    $i = 1;
    if (is__array($data['documents'])){
        foreach ($data['documents'] as $doc){
            $pdf->Cell(10, 0, $i++, 1, 0, 'C', 0, '', 0);
            $pdf->Cell(90, 0, fn_date_format($doc['date_cast'], "%a %d/%m/%Y"), 1, 0, 'L', 0, '', 0);
            $pdf->Cell(20, 0, ($doc['weight']['C'])?$doc['weight']['C']:'0', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($doc['weight']['S'])?$doc['weight']['S']:'0', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($doc['weight']['A'])?$doc['weight']['A']:'0', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($doc['weight']['W'])?$doc['weight']['W']:'0', 1, 1, 'R', 0, '', 1);
        }
    }

    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(10, 0, '', 0, 0, 'C', 0, '', 0);
    $pdf->Cell(90, 0, 'ИТОГО:', 0, 0, 'C', 0, '', 0);
    $pdf->Cell(20, 0, ($total_weight['C'])?$total_weight['C']:'0', 1, 0, 'R', 0, '', 1);
    $pdf->Cell(20, 0, ($total_weight['S'])?$total_weight['S']:'0', 1, 0, 'R', 0, '', 1);
    $pdf->Cell(20, 0, ($total_weight['A'])?$total_weight['A']:'0', 1, 0, 'R', 0, '', 1);
    $pdf->Cell(20, 0, ($total_weight['W'])?$total_weight['W']:'0', 1, 1, 'R', 0, '', 1);

    $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);
    $pdf->ln(10);
    $avg = (($total_weight['C'])?$total_weight['C']:0)/count($data['documents']);
    $pdf->Cell(200, 0, "Среднее значение выпуска серого чугуна: " . fn_fvalue($avg, 1, false) . " кг", 0, 1, 'L', 0, '', 1);
    $pdf->ln(10);

    $pdf->AddPage();

    //--------------------------------------------------------------------------
    // 2. ПРОИЗВОДСТВО ОТЛИВОК ПО НАЗНАЧЕНИЮ
    //--------------------------------------------------------------------------
    $data = fn_get_targets ();
    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(0, 10, 'ПОДРОБНЫЙ ОТЧЕТ РАБОТЫ ЛИТЕЙНОГО ЦЕХА', 0, 1, 'L', 0, '', 0);
    $pdf->Cell(0, 10, '1. Производство отливок по назначению', 0, 1, 'L', 0, '', 0);

    // THEAD
    $pdf->uns_SetFont("B", 12);
    $pdf->Cell(100, 0, 'Назначение', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 20, 0, 'Чугун, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 20, 0, 'Сталь, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 20, 0, 'Алюминий, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 20, 0, 'Чугун б., кг', 1, 1, 'C', 0, '', 1);

    // TBODY
    $pdf->uns_SetFont("R", 12);
    $pdf->Cell(100, 0, 'На изгот. насосов', 1, 0, 'L', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_production_pumps']['C']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['C']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_production_pumps']['S']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['S']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_production_pumps']['A']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['A']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_production_pumps']['W']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['W']['total_weight'], 1, false), 1, 1, 'R', 0, '', 0);

    $pdf->Cell(100, 0, 'На собст. нужды', 1, 0, 'L', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_internal']['C']['total_weight'])?"0":fn_fvalue($data['for_internal']['C']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_internal']['S']['total_weight'])?"0":fn_fvalue($data['for_internal']['S']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_internal']['A']['total_weight'])?"0":fn_fvalue($data['for_internal']['A']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_internal']['W']['total_weight'])?"0":fn_fvalue($data['for_internal']['W']['total_weight'], 1, false), 1, 1, 'R', 0, '', 0);

    $pdf->Cell(100, 0, 'На продажу', 1, 0, 'L', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_sale']['C']['total_weight'])?"0":fn_fvalue($data['for_sale']['C']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_sale']['S']['total_weight'])?"0":fn_fvalue($data['for_sale']['S']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_sale']['A']['total_weight'])?"0":fn_fvalue($data['for_sale']['A']['total_weight'], 1, false), 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 20, 0, (!$data['for_sale']['W']['total_weight'])?"0":fn_fvalue($data['for_sale']['W']['total_weight'], 1, false), 1, 1, 'R', 0, '', 0);

    // TFOOT
    $pdf->uns_SetFont("B", 12);
    $pdf->Cell(100, 0, '', 0, 0, 'C', 0, '', 0);
    $pdf->Cell( 20, 0, !($data['for_production_pumps']['C']['total_weight']+$data['for_internal']['C']['total_weight']+$data['for_sale']['C']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['C']['total_weight']+$data['for_internal']['C']['total_weight']+$data['for_sale']['C']['total_weight'], 1, false), 1, 0, 'R', 0, '', 1);
    $pdf->Cell( 20, 0, !($data['for_production_pumps']['S']['total_weight']+$data['for_internal']['S']['total_weight']+$data['for_sale']['S']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['S']['total_weight']+$data['for_internal']['S']['total_weight']+$data['for_sale']['S']['total_weight'], 1, false), 1, 0, 'R', 0, '', 1);
    $pdf->Cell( 20, 0, !($data['for_production_pumps']['A']['total_weight']+$data['for_internal']['A']['total_weight']+$data['for_sale']['A']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['A']['total_weight']+$data['for_internal']['A']['total_weight']+$data['for_sale']['A']['total_weight'], 1, false), 1, 0, 'R', 0, '', 1);
    $pdf->Cell( 20, 0, !($data['for_production_pumps']['W']['total_weight']+$data['for_internal']['W']['total_weight']+$data['for_sale']['W']['total_weight'])?"0":fn_fvalue($data['for_production_pumps']['W']['total_weight']+$data['for_internal']['W']['total_weight']+$data['for_sale']['W']['total_weight'], 1, false), 1, 1, 'R', 0, '', 1);

    $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);


    //--------------------------------------------------------------------------
    // 3. РЕАЛИЗАЦИЯ ОТЛИВОК
    //--------------------------------------------------------------------------
    $data = fn_get_SALES();
    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(0, 10, '2. Реализация отливок со склада литья', 0, 1, 'L', 0, '', 0);

    // THEAD
    $pdf->uns_SetFont("B", 12);
    $pdf->Cell(10, 0, '№', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(38, 0, 'Дата', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(77, 0, 'Наименование', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(15, 0, 'Кол-во', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(20, 0, 'Вес, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(20, 0, 'Итого, кг', 1, 1, 'C', 0, '', 1);

    // TBODY
    $pdf->uns_SetFont("R", 13);
    $i = 1;
    $w = 0;
    if (is__array($data)){
        foreach ($data as $doc){
            $pdf->Cell(10, 0, $i++, 1, 0, 'C', 0, '', 1);
            $pdf->Cell(38, 0, fn_date_format($doc['date'], "%a %d/%m/%Y"), 1, 0, 'L', 0, '', 1);
            $pdf->Cell(77, 0, $doc['name'], 1, 0, 'L', 0, '', 1);
            $pdf->Cell(15, 0, fn_fvalue($doc['quantity']), 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, fn_fvalue($doc['weight'],1,false), 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, fn_fvalue($doc['total_weight'],1,false), 1, 1, 'R', 0, '', 1);
            $w += $doc['total_weight'];
        }
    }

    // TFOOT
    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(10, 0, '', 0, 0, 'C', 0, '', 1);
    $pdf->Cell(38, 0, '', 0, 0, 'L', 0, '', 1);
    $pdf->Cell(77, 0, '', 0, 0, 'C', 0, '', 1);
    $pdf->Cell(15, 0, '', 0, 0, 'R', 0, '', 1);
    $pdf->Cell(20, 0, '', 0, 0, 'R', 0, '', 1);
    $pdf->Cell(20, 0, fn_fvalue($w,1,false), 1, 1, 'R', 0, '', 1);

    $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);

    //--------------------------------------------------------------------------
    // 4. СПИСОК выпущенного литья по назанчению
    //--------------------------------------------------------------------------
    $data = fn_get_CASTING_LIST();

    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(0, 10, '3. Список выпущенного литья по назанчению', 0, 1, 'L', 0, '', 0);

    $pdf->uns_SetFont("R", 12);
    if (is__array($data)){
        $t_w = array('C' => 0, 'S'=>0, 'A' => 0, 'W'=>0,);
        $t_q = 0;
        foreach ($data as $k=>$target){
            $pdf->uns_SetFont("B", 12);
            if     ($k == "for_production_pumps")  $pdf->Cell(0, 10, 'На изгот. насосов', 0, 1, 'L', 0, '', 0);
            elseif ($k == "for_internal")          $pdf->Cell(0, 10, 'На собст. нужды', 0, 1, 'L', 0, '', 0);
            elseif ($k == "for_sale")              $pdf->Cell(0, 10, 'На продажу', 0, 1, 'L', 0, '', 0);

            $pdf->uns_SetFont("B", 11);
            $pdf->Cell(8, 0, '№', 1, 0, 'C', 0, '', 0);
            $pdf->Cell(62, 0, 'Наименование', 1, 0, 'C', 0, '', 0);
            $pdf->Cell(15, 0, 'Вес, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(15, 0, 'Кол-во', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(20, 0, 'Чугун, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(20, 0, 'Сталь, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(20, 0, 'Алюминий, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(20, 0, 'Чугун б., кг', 1, 1, 'C', 0, '', 1);

            $w = array('C' => 0, 'S'=>0, 'A' => 0, 'W'=>0,);
            $q = 0;
            $i = 1;

            $pdf->uns_SetFont("R", 12);
            foreach ($target as $d){
                $pdf->Cell(8, 0, $i++, 1, 0, 'C', 0, '', 0);
                $pdf->Cell(62, 0, $d['name'], 1, 0, 'L', 0, '', 1);
                $pdf->Cell(15, 0, fn_fvalue($d['weight'], 1, false), 1, 0, 'R', 0, '', 1);
                $pdf->Cell(15, 0, fn_fvalue($d['total_quantity']), 1, 0, 'R', 0, '', 1);
                $pdf->Cell(20, 0, ($d['type_casting']=='C')?fn_fvalue($d['total_weight'], 1, false):"", 1, 0, 'R', 0, '', 1);
                $pdf->Cell(20, 0, ($d['type_casting']=='S')?fn_fvalue($d['total_weight'], 1, false):"", 1, 0, 'R', 0, '', 1);
                $pdf->Cell(20, 0, ($d['type_casting']=='A')?fn_fvalue($d['total_weight'], 1, false):"", 1, 0, 'R', 0, '', 1);
                $pdf->Cell(20, 0, ($d['type_casting']=='W')?fn_fvalue($d['total_weight'], 1, false):"", 1, 1, 'R', 0, '', 1);

                $q += $d['total_quantity'];

                if ($d['type_casting']=='C') $w['C'] += $d['total_weight'];
                if ($d['type_casting']=='S') $w['S'] += $d['total_weight'];
                if ($d['type_casting']=='A') $w['A'] += $d['total_weight'];
                if ($d['type_casting']=='W') $w['W'] += $d['total_weight'];
            }
            $t_q += $q;
            $t_w['C'] += $w['C'];
            $t_w['S'] += $w['S'];
            $t_w['A'] += $w['A'];
            $t_w['W'] += $w['W'];


            $pdf->uns_SetFont("B", 12);
            $pdf->Cell(8,  0, '', 0, 0, 'C', 0, '', 0);
            $pdf->Cell(62, 0, '', 0, 0, 'L', 0, '', 1);
            $pdf->Cell(15, 0, '', 0, 0, 'R', 0, '', 1);
            $pdf->Cell(15, 0, $q, 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($w['C'])?fn_fvalue($w['C'], 1, false):'0', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($w['S'])?fn_fvalue($w['S'], 1, false):'0', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($w['A'])?fn_fvalue($w['A'], 1, false):'0', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(20, 0, ($w['W'])?fn_fvalue($w['W'], 1, false):'0', 1, 1, 'R', 0, '', 1);
        }

        $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);
        $pdf->uns_SetFont("B", 13);
        $pdf->Cell(85, 0, 'Итого:', 0, 0, 'R', 0, '', 1);
        $pdf->Cell(15, 0, $t_q, 1, 0, 'R', 0, '', 1);
        $pdf->Cell(20, 0, ($t_w['C'])?fn_fvalue($t_w['C'], 1, false):'0', 1, 0, 'R', 0, '', 1);
        $pdf->Cell(20, 0, ($t_w['S'])?fn_fvalue($t_w['S'], 1, false):'0', 1, 0, 'R', 0, '', 1);
        $pdf->Cell(20, 0, ($t_w['A'])?fn_fvalue($t_w['A'], 1, false):'0', 1, 0, 'R', 0, '', 1);
        $pdf->Cell(20, 0, ($t_w['W'])?fn_fvalue($t_w['W'], 1, false):'0', 1, 1, 'R', 0, '', 1);
        $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);
    }

    $pdf->Output(str_replace(array('fn.reports.', '.php'), '', basename(__FILE__)) . "_" . strftime("%Y-%m-%d_%H-%M", time()) . ".pdf", 'I');
    return true;
}


// Получение данных ПРОИЗВОДСТВА ОТЛИВОК ПО НАЗНАЧЕНИЮ
function fn_get_targets (){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $data = array();
    foreach (array("for_production_pumps", "for_internal", "for_sale") as $target){
        $data[$target] = fn_acc__purpose_VLC($target, $_REQUEST['time_from'], $_REQUEST['time_to']);
    }
    return (is__array($data))?$data:false;
}


// Получение данных РЕАЛИЗАЦИИ ОТЛИВОК СО СКЛАДА ЛИТЬЯ
function fn_get_SALES (){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    return fn_acc__sales_VLC($_REQUEST['time_from'], $_REQUEST['time_to']);
}


// Получить список выпущенного литья
function fn_get_CASTING_LIST (){
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $data = array();
    foreach (array("for_production_pumps", "for_internal", "for_sale") as $target){
        $data[$target] = fn_acc__casting_list_VLC($target, $_REQUEST['time_from'], $_REQUEST['time_to']);
    }
    return (is__array($data))?$data:false;
}
