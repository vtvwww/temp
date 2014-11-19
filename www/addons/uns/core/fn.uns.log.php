<?php
//$l->p(__FILE__, __LINE__);

class uns_log {
    private $time = 0;
    private $delta = 0;
    public function __construct(){
        $this->upd_time();
    }

    private function get_mctime(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    private function upd_time(){
        $this->time = $this->get_mctime();
        $this->delta = $this->get_mctime();
    }

    private function g_interval(){
        $t = $this->get_mctime()-$this->time;
        $this->upd_time();
        return $t;
    }


    public function p ($file, $line){
        $t = $this->get_mctime();
        $time = number_format($t-$this->time, 6, ".", " ");
        $d    = number_format($t-$this->delta, 6, ".", " ");
        $this->delta = $t;
        print( basename($file).  "; Line: " . $line . "; time: " . $time . " (+" . $d . ")<br>");
    }
}
