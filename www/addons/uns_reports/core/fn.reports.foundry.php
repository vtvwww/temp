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
    $pdf->Cell(82, 0, 'Дата плавки', 1, 0, 'C', 0, '', 0);
    $pdf->Cell(22, 0, 'Чугун, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(22, 0, 'Сталь, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(22, 0, 'Алюминий, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell(22, 0, 'Чугун б., кг', 1, 1, 'C', 0, '', 1);

    $workdays = null; // Массив рабочих дней литейного цеха
    $pdf->uns_SetFont("R", 11);
    $i = 1;
    if (is__array($data['documents'])){
        foreach ($data['documents'] as $doc){
            // Добавить рабочий день литейного цеха
            $workdays[fn_date_format($doc['date_cast'], "%a %d/%m/%Y")] += 1;

            $pdf->Cell(10, 0, $i++, 1, 0, 'C', 0, '', 0);
            $pdf->Cell(82, 0, fn_date_format($doc['date_cast'], "%a %d/%m/%Y"), 1, 0, 'L', 0, '', 0);
            $pdf->Cell(22, 0, ($doc['weight']['C'])?number_format($doc['weight']['C'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
            $pdf->Cell(22, 0, ($doc['weight']['S'])?number_format($doc['weight']['S'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
            $pdf->Cell(22, 0, ($doc['weight']['A'])?number_format($doc['weight']['A'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
            $pdf->Cell(22, 0, ($doc['weight']['W'])?number_format($doc['weight']['W'], 1, ".", " "):"", 1, 1, 'R', 0, '', 1);
        }
    }

    $pdf->uns_SetFont("BI", 12);
    $pdf->Cell(10, 0, '', 0, 0, 'C', 0, '', 0);
    $pdf->Cell(82, 0, 'ИТОГО, кг', 0, 0, 'R', 0, '', 0);
    $pdf->Cell(22, 0, ($total_weight['C'])?number_format($total_weight['C'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
    $pdf->Cell(22, 0, ($total_weight['S'])?number_format($total_weight['S'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
    $pdf->Cell(22, 0, ($total_weight['A'])?number_format($total_weight['A'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
    $pdf->Cell(22, 0, ($total_weight['W'])?number_format($total_weight['W'], 1, ".", " "):"", 1, 1, 'R', 0, '', 1);
    if (count(array_keys($workdays))){
        $v_C = ($total_weight['C'])?number_format($total_weight['C']/count(array_keys($workdays)), 1, ".", " "):"";
        $v_S = ($total_weight['S'])?number_format($total_weight['S']/count(array_keys($workdays)), 1, ".", " "):"";
        $v_A = ($total_weight['A'])?number_format($total_weight['A']/count(array_keys($workdays)), 1, ".", " "):"";
        $v_W = ($total_weight['W'])?number_format($total_weight['W']/count(array_keys($workdays)), 1, ".", " "):"";
        $pdf->uns_SetFont("BI", 12);
        $pdf->Cell(92, 0, "Ср. знач. за " . count(array_keys($workdays)) . " дней плавок, кг", 0, 0, 'R', 0, '', 0);
        $pdf->Cell(22, 0, $v_C, 1, 0, 'R', 0, '', 1);
        $pdf->Cell(22, 0, $v_S, 1, 0, 'R', 0, '', 1);
        $pdf->Cell(22, 0, $v_A, 1, 0, 'R', 0, '', 1);
        $pdf->Cell(22, 0, $v_W, 1, 1, 'R', 0, '', 1);
    }

    $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);
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
    $pdf->uns_SetFont("B", 11);
    $pdf->Cell( 92, 0, 'Назначение', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 22, 0, 'Чугун, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 22, 0, 'Сталь, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 22, 0, 'Алюминий, кг', 1, 0, 'C', 0, '', 1);
    $pdf->Cell( 22, 0, 'Чугун б., кг', 1, 1, 'C', 0, '', 1);

    // TBODY
    $pdf->uns_SetFont("R", 11);
    $pdf->Cell( 92, 0, 'На изготовление насосов', 1, 0, 'L', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['C']['total_weight'])?number_format($data['for_production_pumps']['C']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['S']['total_weight'])?number_format($data['for_production_pumps']['S']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['A']['total_weight'])?number_format($data['for_production_pumps']['A']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['W']['total_weight'])?number_format($data['for_production_pumps']['W']['total_weight'], 1, ".", " "):"", 1, 1, 'R', 0, '', 0);

    $pdf->Cell( 92, 0, 'На собственные нужды', 1, 0, 'L', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_internal']['C']['total_weight'])?number_format($data['for_internal']['C']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_internal']['S']['total_weight'])?number_format($data['for_internal']['S']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_internal']['A']['total_weight'])?number_format($data['for_internal']['A']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_internal']['W']['total_weight'])?number_format($data['for_internal']['W']['total_weight'], 1, ".", " "):"", 1, 1, 'R', 0, '', 0);

    $pdf->Cell( 92, 0, 'На продажу', 1, 0, 'L', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_sale']['C']['total_weight'])?number_format($data['for_sale']['C']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_sale']['S']['total_weight'])?number_format($data['for_sale']['S']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_sale']['A']['total_weight'])?number_format($data['for_sale']['A']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_sale']['W']['total_weight'])?number_format($data['for_sale']['W']['total_weight'], 1, ".", " "):"", 1, 1, 'R', 0, '', 0);

    // TFOOT
    $pdf->uns_SetFont("BI", 12);
    $pdf->Cell( 92, 0, 'ИТОГО, кг', 0, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['C']['total_weight']+$data['for_internal']['C']['total_weight']+$data['for_sale']['C']['total_weight'])?number_format($data['for_production_pumps']['C']['total_weight']+$data['for_internal']['C']['total_weight']+$data['for_sale']['C']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['S']['total_weight']+$data['for_internal']['S']['total_weight']+$data['for_sale']['S']['total_weight'])?number_format($data['for_production_pumps']['S']['total_weight']+$data['for_internal']['S']['total_weight']+$data['for_sale']['S']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['A']['total_weight']+$data['for_internal']['A']['total_weight']+$data['for_sale']['A']['total_weight'])?number_format($data['for_production_pumps']['A']['total_weight']+$data['for_internal']['A']['total_weight']+$data['for_sale']['A']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);
    $pdf->Cell( 22, 0, ($data['for_production_pumps']['W']['total_weight']+$data['for_internal']['W']['total_weight']+$data['for_sale']['W']['total_weight'])?number_format($data['for_production_pumps']['W']['total_weight']+$data['for_internal']['W']['total_weight']+$data['for_sale']['W']['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 0);

    $pdf->Cell(0, 5, '', 0, 1, 'L', 0, '', 0);


    //--------------------------------------------------------------------------
    // 3. ОТПУСК ОТЛИВОК
    //--------------------------------------------------------------------------
    $pdf->ln(5);
    $data = fn_get_SALES();
    if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
    $h          = 6;
    $border     = 1;
    $align      = 'C';
    $fill       = true;
    $ln         = 0;
    $x = $y     = '';
    $reseth     = true;
    $stretch    = 0;
    $ishtml     = false;
    $autopadding= false;
    $maxh       = $h;
    $valign     = 'M';
    $fitcell    = true;
    $w = array(6, 25, 57, 14, 14, 16, 16, 16, 16);
    //************************
    $pdf->Cell(0, 10, '2. Отпуск отливок со склада литья', 0, 1, 'L', 0, '', 0);
    $pdf->SetFillColor(255); // BLACK = 0 WHITE = 255
    $pdf->SetTextColor(0); // BLACK = 0

    if (!is__array($data)){
        $pdf->uns_SetFont("R", 12);
        $pdf->MultiCell(170,  10, "  Нет данных за указанный период!", 0, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }else{
        // THEAD --------------------------------
        $pdf->uns_SetFont("B", 11);
        $h = $maxh = 8;
        $k = 0;
        $pdf->MultiCell($w[$k++],  $h, "№", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Дата", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Наименование", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Вес,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Кол-во,\nшт", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Сталь,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Алюминий,\nкг", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, "Чугун б.,\nкг", $border, "C", $fill, 1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

        // TBODY --------------------------------
        $pdf->uns_SetFont("R", 11);
        $h = $maxh = 6;

        $i = 0;
        $weights = array("C" => array(), "S" =>  array(), "A" =>  array(), "W" =>  array());
        foreach ($data as $doc){
            foreach ($doc["items"] as $item){
                if ($pdf->GetY() >= 280){$pdf->AddPage();$pdf->ln(2);}
                $i++;
                $k = 0;

                $name = $item["item_info"]["material_name"] . ((strlen($item["item_info"]["material_no"]))?" [".$item["item_info"]["material_no"]."]":"");
                $weight = array("C" => 0, "S" => 0, "A" => 0, "W" => 0, );
                $weight[$item["item_info"]["type_casting"]] = $item["weight"];
                $weights[$item["item_info"]["type_casting"]][] = $item["weight"]*$item["quantity"];

                $pdf->MultiCell($w[$k++],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_date_format($doc['date'], "%a %d/%m/%y"), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, $name, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($item["weight"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, fn_fvalue($item["quantity"]), $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["C"])?"":fn_fvalue($item["quantity"]*$weight["C"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["S"])?"":fn_fvalue($item["quantity"]*$weight["S"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["A"])?"":fn_fvalue($item["quantity"]*$weight["A"], 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($w[$k++],  $h, (!$weight["W"])?"":fn_fvalue($item["quantity"]*$weight["W"], 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            }
        }

        // TFOOT --------------------------------
        $pdf->uns_SetFont("BI", 12);
        $k = 0;
        $pdf->MultiCell($w[$k++] + $w[$k++] + $w[$k++] + $w[$k++] + $w[$k++],  $h, "ИТОГО, кг", 0, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["C"]))?"":fn_fvalue(array_sum($weights["C"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["S"]))?"":fn_fvalue(array_sum($weights["S"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["A"]))?"":fn_fvalue(array_sum($weights["A"]), 1, false), $border, "R", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $pdf->MultiCell($w[$k++],  $h, (!array_sum($weights["W"]))?"":fn_fvalue(array_sum($weights["W"]), 1, false), $border, "R", $fill, 1,   $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }

    //--------------------------------------------------------------------------
    // 4. СПИСОК выпущенного литья по назанчению
    //--------------------------------------------------------------------------
    $data = fn_get_CASTING_LIST();

    $pdf->uns_SetFont("B", 13);
    $pdf->Cell(0, 10, '3. Список выпущенного литья по назанчению', 0, 1, 'L', 0, '', 0);

    $pdf->uns_SetFont("R", 11);
    if (is__array($data)){
        $t_w = array('C' => 0, 'S'=>0, 'A' => 0, 'W'=>0,);
        $t_q = 0;
        foreach ($data as $k=>$target){
            $pdf->uns_SetFont("B", 11);
            if     ($k == "for_production_pumps")  $pdf->Cell(0, 10, 'На изгот. насосов', 0, 1, 'L', 0, '', 0);
            elseif ($k == "for_internal")          $pdf->Cell(0, 10, 'На собст. нужды', 0, 1, 'L', 0, '', 0);
            elseif ($k == "for_sale")              $pdf->Cell(0, 10, 'На продажу', 0, 1, 'L', 0, '', 0);

            $pdf->uns_SetFont("B", 11);
            $pdf->Cell( 8, 0, '№', 1, 0, 'C', 0, '', 0);
            $pdf->Cell(74, 0, 'Наименование', 1, 0, 'C', 0, '', 0);
            $pdf->Cell(13, 0, 'Вес, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(13, 0, 'Кол, шт', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(18, 0, 'Чугун, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(18, 0, 'Сталь, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(18, 0, 'Алюминий, кг', 1, 0, 'C', 0, '', 1);
            $pdf->Cell(18, 0, 'Чугун б., кг', 1, 1, 'C', 0, '', 1);

            $w = array('C' => 0, 'S'=>0, 'A' => 0, 'W'=>0,);
            $q = 0;
            $i = 1;

            $pdf->uns_SetFont("R", 11);
            foreach ($target as $d){
                $pdf->Cell( 8, 0, $i++, 1, 0, 'C', 0, '', 0);
                $pdf->Cell(74, 0, $d['name'], 1, 0, 'L', 0, '', 1);
                $pdf->Cell(13, 0, fn_fvalue($d['weight'], 1, false), 1, 0, 'R', 0, '', 1);
                $pdf->Cell(13, 0, fn_fvalue($d['total_quantity']), 1, 0, 'C', 0, '', 1);
                $pdf->Cell(18, 0, ($d['type_casting']=='C')?number_format($d['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
                $pdf->Cell(18, 0, ($d['type_casting']=='S')?number_format($d['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
                $pdf->Cell(18, 0, ($d['type_casting']=='A')?number_format($d['total_weight'], 1, ".", " "):"", 1, 0, 'R', 0, '', 1);
                $pdf->Cell(18, 0, ($d['type_casting']=='W')?number_format($d['total_weight'], 1, ".", " "):"", 1, 1, 'R', 0, '', 1);

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


            $pdf->uns_SetFont("BI", 12);
            $pdf->Cell(95, 0, 'Подытог, шт/кг', 0, 0, 'R', 0, '', 1);
            $pdf->Cell(13, 0, $q, 1, 0, 'C', 0, '', 1);
            $pdf->Cell(18, 0, ($w['C'])?number_format($w['C'], 1, ".", " "):'', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(18, 0, ($w['S'])?number_format($w['S'], 1, ".", " "):'', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(18, 0, ($w['A'])?number_format($w['A'], 1, ".", " "):'', 1, 0, 'R', 0, '', 1);
            $pdf->Cell(18, 0, ($w['W'])?number_format($w['W'], 1, ".", " "):'', 1, 1, 'R', 0, '', 1);
        }

        $pdf->Cell( 0, 5, '', 0, 1, 'L', 0, '', 0);
        $pdf->uns_SetFont("BI", 12);
        $pdf->Cell(95, 0, 'ИТОГО, шт/кг', 0, 0, 'R', 0, '', 1);
        $pdf->Cell(13, 0, $t_q, 1, 0, 'C', 0, '', 1);
        $pdf->Cell(18, 0, ($t_w['C'])?number_format($t_w['C'], 1, ".", " "):'', 1, 0, 'R', 0, '', 1);
        $pdf->Cell(18, 0, ($t_w['S'])?number_format($t_w['S'], 1, ".", " "):'', 1, 0, 'R', 0, '', 1);
        $pdf->Cell(18, 0, ($t_w['A'])?number_format($t_w['A'], 1, ".", " "):'', 1, 0, 'R', 0, '', 1);
        $pdf->Cell(18, 0, ($t_w['W'])?number_format($t_w['W'], 1, ".", " "):'', 1, 1, 'R', 0, '', 1);
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
    $p = array("type" => 7, "o_id" => 8, "only_active" =>  true, "with_items" =>  true, "info_unit"=>false, "info_item" => false, "sorting_schemas" => "view_asc"); // RO = 7; Sklad Litya = 8
    $sales_VLC = array_shift(fn_uns__get_documents(array_merge($_REQUEST, $p)));
//    fn_print_r($sales_VLC);
    return $sales_VLC;
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
