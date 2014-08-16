<?php

include '_common.php';

$file = '../data/events.json';
$handle = getReadLock();
$data = file_get_contents( $file );
releaseReadLock( $handle );

header('Content-Type: application/json');
echo $data;

?>