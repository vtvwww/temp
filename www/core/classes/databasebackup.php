<?php


class databasebackup
{
    public      $error            = false;
    protected   $file_name        = null;
    protected   $fd               = null;
    const       DB_MAX_ROWS       = 1000;
    private     $db_prefix        = "";

    public function __construct($params){
        $this->file_name = $params["file_name"];

    }

    public function export ($db_prefix="", $tables = null){
        if (strlen($db_prefix)) $this->db_prefix = $db_prefix;

        if (is_null($tables)){
            $tables = db_get_hash_array($this->db_prefix . "SHOW TABLE STATUS", 'Name');
        }

        if (is__array($tables)){
            if (!($this->fd = @fopen($this->file_name, 'w'))) {
                $this->error = "dump_cant_create_file";
                return false;
            }

            // Записать общую информацию
            $this->_export_info();

            // Настройка mysql
            $this->_export_set_config("lock", $tables);

            // Экспорт каждой таблицы
            foreach ($tables as $table){
                $export_log = $this->_export_table($table);
            }

            $this->_export_set_config("unlock");

            fclose($this->fd);
            if ($this->_create_zip()){
                unlink($this->file_name);
                $this->file_name = $this->file_name . ".zip\n";
                echo ("Дамп: " . $this->file_name . "");
            }
        }

        return true;
    }

    private function _create_zip (){
        $zip = new ZipArchive();
      		if($zip->open($this->file_name . ".zip", ZIPARCHIVE::CREATE) !== true) return false;
            $zip->addFile($this->file_name,basename($this->file_name));
      		return $zip->close(); //close the zip -- done!
    }

    private function _export_table ($table){
        $name = $table["Name"];
        $lines = array();
        $lines[] = "";
        $lines[] = "";
        $lines[] = "#===========================================================";
        $lines[] = "# Структура таблицы `" . $name . "`";
        $lines[] = "DROP TABLE IF EXISTS `$name`;";
        $lines[] = str_replace("\n", "\r\n", array_pop(db_get_row($this->db_prefix . "SHOW CREATE TABLE `" . $name . "`")));
        $lines[] = ";";
        $lines[] = "# Дамп данных таблицы `" . $name . "`";
        $this->_export_add_line($lines);

        $fields = db_get_hash_array($this->db_prefix . "SHOW COLUMNS FROM " . $name, "Field");
        $insert = "INSERT INTO `{$name}` (`" . implode('`, `', array_keys($fields)) . "`) VALUES";
        $total_rows = db_get_field($this->db_prefix . "SELECT COUNT(*) FROM $name");

        for ($i = 0; $i < $total_rows; $i += self::DB_MAX_ROWS) {
            $data_lines = array();
            $data_lines[] = "";
            $data_lines[] = $insert;
            $table_data = db_get_array($this->db_prefix . "SELECT * FROM $name LIMIT $i, " . self::DB_MAX_ROWS);
            foreach ($table_data as $_tdata) {
                $_tdata = fn_add_slashes($_tdata, true);
                $values = array();
                foreach ($_tdata as $v) {
                    $values[] = ($v !== null) ? "'$v'" : 'NULL';
                }
                $data_lines[] = "(" . implode(',', $values) . "),";
            }
            $max_key = max(array_keys($data_lines));
            $data_lines[$max_key] = substr($data_lines[$max_key], 0, -1) . ";";
            $this->_export_add_line($data_lines);
        }
    }

    private function _export_info (){
        $lines = array();
        $lines[] = "#===========================================================";
        $lines[] = "#     База данных: " . db_get_field($this->db_prefix . "SELECT DATABASE()");
        $lines[] = "#  Время создания: " . fn_date_format(TIME, "%Y/%m/%d %H:%M:%S");
        $lines[] = "#===========================================================";
        $lines[] = "";

        $this->_export_add_line($lines);
    }

    private function _export_set_config ($s, $ts){
        if ($s == "lock"){
            db_query($this->db_prefix . "LOCK TABLES `" . implode("` WRITE,`", array_keys($ts)) . "` WRITE;");
        }elseif ($s == "unlock"){
            db_query($this->db_prefix . "UNLOCK TABLES;");
        }
    }

    private function _export_add_line ($str){
        if (is__array($str)){
            foreach ($str as $s){
                fwrite($this->fd, $s . "\r\n" );
            }
        }else fwrite($this->fd, $str . "\r\n" );
    }
}