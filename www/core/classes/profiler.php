<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

if ( !defined('AREA') )	{ die('Access denied');	}

class Profiler {
	static public $checkpoints = array();
	static public $queries = array();
	static public $queries_time = array();
	
	static function checkpoint($name, $display = true)
	{
		if (!defined('PROFILER')) {
			return false;
		}

		self::$checkpoints[$name] = array(
			'time' => self::microtime(),
			'memory' => memory_get_usage(),
			'included_files' => count(get_included_files()),
			'queries' => count(self::$queries),
			'display' => $display,
		);
	}

	static function microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	static function display()
	{
		if (!defined('PROFILER')) {
			return false;
		}
		
		if (defined('PROFILER_FOR_ADMIN') && empty($_SESSION['PROFILER_FOR_ADMIN'])) {
			$user_id = !empty($_SESSION['auth']['user_id']) ? $_SESSION['auth']['user_id'] : 0;
			if (isset($_REQUEST['PROFILER_FOR_ADMIN']) || $user_id == 1) {
				$_SESSION['PROFILER_FOR_ADMIN'] = true;
			} else {
				return false;
			}
		}
		
		$previous = array();
		$cummulative = array();

		$first = true;
		
		if (defined('PROFILER_SQL')) {
			echo '<ul style="list-style:none; border: 1px solid #cccccc; padding: 3px;">';
			foreach (self::$queries as $key => $query) {
				$time = self::$queries_time[$key];
				$color = ($time > LONG_QUERY_TIME) ? '#FF0000' : (($time > 0.2) ? '#FFFFCC' : '');
				echo '<li ' . ($color ? "style=\"background-color: $color\">" : ($key % 2 ? 'style="background-color: #eeeeee;">' : '>')) . $time . ' - ' . $query . '</li>';
			}
			echo '</ul>';
		}

		echo '<div class="profiler">';

		foreach (self::$checkpoints as $name => $c) {
            if ($c['display']){
                echo "<table><tr>";
    			echo "<td><b>$name:</b></td>";

//    			echo '<b>' . $name . '</b><br />';
//    			echo '-------------------<br />';
            }
			if ($first == false) {
				if ($c['display']){
                    echo  "<td>" . ($c['queries'] - $previous['queries']) . "/" . sprintf("%.3f", $c['time'] - $previous['time']) . "</td>";
//                    echo  "<td>Queries: " . ($c['queries'] - $previous['queries']) . "</td>"
//                        . "<td>Time: " . sprintf("%.4f", $c['time'] - $previous['time']) . "</td>";
//                    echo '&nbsp;&nbsp;&nbsp;&nbsp;Files:   ' . ($c['included_files'] - $previous['included_files']) . '<br />';
//                    echo '&nbsp;&nbsp;&nbsp;Memory:  ' . (self::formatBytes($c['memory'] - $previous['memory'])) . '<br />';
                }
			} else {
                if ($c['display']){
                    echo '- Memory: ' . number_format($c['memory']) . '<br />';
                    echo '- Files: ' . $c['included_files'] . '<br />';
                    echo '- Queries: ' . $c['queries'] . '<br />';
                }

				$first = false;
				$cummulative = $c;
			}
			$previous = $c;

		}
        // Total BEGIN
        echo "<td><b>Total:</b></td>";
        echo  "<td>" . $previous['queries'] . "/" . sprintf("%.3f", $previous['time']-$cummulative['time']) . "</td>";
//        echo  "<td>Queries: " . $previous['queries'] . "</td>"
//            . "<td>Time: " . sprintf("%.4f", $previous['time']-$cummulative['time']) . "</td>";
//        echo 'Queries T: ' . sprintf("%.4f", array_sum(self::$queries_time)) . '<br />';
//        echo '&nbsp;&nbsp;&nbsp;&nbsp;Files:   ' . $previous['included_files'] . '<br />';
//        echo '&nbsp;&nbsp;&nbsp;Memory:  ' . self::formatBytes($previous['memory']) . '<br />';
        // Total END

        echo '</div>';
	}

	static function set_query($query, $time)
	{
		if (!defined('PROFILER')) {
			return false;
		}

		self::$queries[] = $query;
		self::$queries_time[] = sprintf('%.5f', $time);
	}

    static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}

?>