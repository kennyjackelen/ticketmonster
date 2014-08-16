<?php

include '_common.php';

$handle = getReadLock();
$data = file_get_contents( EVENT_FILE );
releaseReadLock( $handle );

header('Content-Type: application/json');
echo $data;

?>