<?php

/**
 * Class planning
 * Для расчета планирования
 */
class planning {


    /**
     * Получить список отчетных месяцев
     * @param $time
     * @return array
     */
    public function get_last_months ($time){
        $curr_year  = date("Y", $time);
        $curr_month = date("Y", $time);


        $res = array();
        foreach (array($curr_year-1, $curr_year) as $year){
            foreach (array(1,2,3,4,5,6,7,8,9,10,11,12) as $month){
                $begin = $year . "-" . $month . "-" . "1";
                $res[$year][$month]["date"]["begin"] = $begin;
                $res[$year][$month]["date"]["end"]          = $year . "-" . $month . "-" . date("t", strtotime($begin));
            }
        }
        return $res;
    }


    public function get_sales ($periods){
        foreach ($periods as $k_y=>$y){
            foreach ($y as $k_m=>$m){
                $balance = self::get_sales_by_month ($m["date"]);
                $periods[$k_y][$k_m]["balance"] = $balance;
                $periods[$k_y][$k_m]["total_balance"] = array_sum($balance);
            }
        }
        return $periods;
    }

    private function get_sales_by_month ($m){
        list($pumps) = fn_uns__get_pumps(array("only_active"=>true, ));
        $params = array(
            "time_from"     => strtotime($m["begin"]),
            "time_to"       => strtotime($m["end"]),
            "type"          => 7,           // Расходные ордера
            "o_id"          => 19,          // Склад готовой продукции
            "only_active"   => true,
            "item_type"     => array("P", "PF", "PA"),
            "with_items"    => true,
            "info_category" => false,
            "info_item"     => true,
            "info_unit"     => false,
        );
        list($docs) = fn_uns__get_documents($params);

        if(!is__array($docs)) return null;

        $res = array();
        foreach ($docs as $d){
            if (is__array($d["items"])){
                foreach ($d["items"] as $i){
                    $res[$pumps[$i["item_id"]]["ps_name"]] += $i["quantity"];
                }
            }
        }

        return $res;
    }

}