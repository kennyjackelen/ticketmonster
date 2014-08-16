<?php
  include '_common.php';

  $event_id = $_POST['event_id'];
  $user_price = $_POST['user_price'];

  updatePrice( $event_id, $user_price );
?>