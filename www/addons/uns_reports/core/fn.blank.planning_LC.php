<?php
// mb_convert_case($group['group'], MB_CASE_UPPER, "utf-8")
function fn_rpt__planning_LC($pumps_data){
    $pdf = new UNS_TCPDF('L');
    $pdf->SetMargins(20, 20, 20);


//    fn_print_r($data);
    $search     = array("Крышка",   "подшипника",   "подшипников",  "Корпус",   "улитки",   "Фланец сальника",  "Полумуфта",    "Колесо рабочее",   "сальника");
    $replace    = array("Кр.",      "подш.",        "подш.",        "Корп.",    "ул.",      "Фланец сал.",           "П/м",          "Р/К",              "сал.");

    $s = array(
        /*0=>*/8,
        /*1=>*/45,
        /*2=>*/30,
        /*3=>*/15,
        /*4=>*/13,
        /*5=>*/17,
        /*6=>*/12,
        /*7=>*/12,
        /*8=>*/15,
        /*9=>*/15,
        /*10=>*/15,
        /*11=>*/15,
        /*12=>*/55,
    );


    foreach ($pumps_data as $data){
        $pdf->AddPage();
        $pdf->uns_SetFont("BI", 22);
        $pdf->Cell(0, 0, $pdf->uns__strtoupper("Насос " . $data['pump']['p_name']), 0, 1, 'C', 0, '', 0);


        $i = 1;
        if (is__array($data['items'])){
            $h = 18;
            $pdf->uns_SetFont("B", 12);

            $pdf->MultiCell($s[0], 0, "", 0, "L", 0, 0, "", "", true, 1);
            $pdf->MultiCell($s[1], 0, "", 0, "L", 0, 0, "", "", true, 1);
            $pdf->MultiCell($s[2], 0, "", 0, "L", 0, 0, "", "", true, 1);
            $pdf->MultiCell($s[3], 0, "", 0, "L", 0, 0, "", "", true, 1);
            $pdf->MultiCell($s[4], 0, "", 0, "L", 0, 0, "", "", true, 1);
            $pdf->MultiCell($s[5], 0, "", 0, "L", 0, 0, "", "", true, 1);
            $pdf->MultiCell($s[6]+$s[7], 0, "Модели", 1, "C", 0, 1, "", "", true, 1);

            $h          = 12;
            $border     = 1;
            $align      = 'C';
            $fill       = false;
            $ln         = 0;
            $x = $y     = '';
            $reseth     = true;
            $stretch    = 0;
            $ishtml     = false;
            $autopadding= false;
            $maxh       = $h;
            $valign     = 'M';
            $fitcell    = false;

            $pdf->MultiCell($s[0],  $h, "№",                 $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[1],  $h, "Наименование",      $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[2],  $h, "Применяе-мость",    $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[3],  $h, "Клей-мо",               $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[4],  $h, "Кол-во",            $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[5],  $h, "Вес,кг\n(1 шт)",           $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[6],  $h, "703",               $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[7],  $h, "КЗ",                $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[8],  $h, "Стерж-ни",           $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[9],  $h, "План",              $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[10], $h, "Скл.\nЛит.",        $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[11], $h, "Мех. цех",          $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
            $pdf->MultiCell($s[12], $h, "Выпуск",            $border, $align, $fill,   1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);

            $pdf->uns_SetFont("R", 14);
            $align      = 'L';
            $h          = 10;
            $maxh       = $h;

            foreach ($data['items'] as $item){
                $n = str_replace($search, $replace, $item['info']['material_name']);
                $a = $item['info']['material_name_accounting'];
                $no = $item['info']['material_no'];
                $q = fn_fvalue($item['quantity']);
                $w = fn_fvalue($item['info']['accounting_data']['weight'],2);

                $pdf->MultiCell($s[0],  $h, $i, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[1],  $h, $n, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->Cell($s[1],  $h,  $n ,$border, 0, "L", false, "", 1);

                $pdf->uns_SetFont("R", 11);
                $pdf->MultiCell($s[2],  $h, $a, $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->uns_SetFont("R", 15);
//                $pdf->MultiCell($s[3],  $h, '44'.$no,$border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->Cell($s[3],  $h,  $no,$border, 0, "C", false, "", 1);
                $pdf->MultiCell($s[4],  $h, $q, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[5],  $h, $w, $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[6],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[7],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[8],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[9],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[10], $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[11], $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $pdf->MultiCell($s[12], $h, "", $border, "L", $fill,   1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
                $i++;
            }
//            for ($i=0;$i<2;$i++){
//                $pdf->MultiCell($s[0],  $h, "", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[1],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[2],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[3],  $h, "", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[4],  $h, "", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[5],  $h, "", $border, "C", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[6],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[7],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[8],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[9],  $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[10], $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[11], $h, "", $border, "L", $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//                $pdf->MultiCell($s[12], $h, "", $border, "L", $fill,   1, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
//            }

        }
    }


//******************************************************************************



    $pdf->Output('example_001.pdf', 'I');
    return true;
}
