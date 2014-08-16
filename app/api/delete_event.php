<?php
include '_common.php';

$event_id = $_POST['event_id'];

removeEventFromFile( $event_id );

?>