<?php
//// if allow == true all modes in controller are allowed, no changes needed.
//if (!(isset($schema['exim']['allow']) && $schema['exim']['allow'] === true)) {
//	$schema['exim']['allow']['cron_export'] = true;
//}

// Разрешить работу $controller="uns_db" с $mode="backup" без авторизации на сайте
$schema['uns_db']['allow']['backup'] = true;
