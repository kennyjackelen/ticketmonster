#!/usr/bin/php

<?php

include '_common.php';

if( !isCRON() )
{
  return;  // don't allow from web server--this should be invoked by cron only
}

$handle = openFile();

if ( !$handle )
{
  return;
}

$cookie = getTicketmasterCookie();

$events = getEvents();
foreach ( $events as $event )
{
  if ( $event->expired ) {
    continue;
  }

  $event_id = $event->event_id;

  $resale_data = getResaleData( $event_id );

  if ( resaleInvalid( $resale_data ) ) {
    $event->expired = true;
    continue;
  }

  $rightNow = time() * 1000;
  $cur_low_price = array( $rightNow, $resale_data->summary->minDisplayPrice->amount );
  if ( !is_array( $event->low_price_trend ) ) {
    $event->low_price_trend = array();
  }
  array_push( $event->low_price_trend, $cur_low_price );

  $cur_num_tickets = array( $rightNow, $resale_data->summary->totalTickets );
  if ( !is_array( $event->num_ticket_trend ) ) {
    $event->num_ticket_trend = array();
  }
  array_push( $event->num_ticket_trend, $cur_num_tickets );

  $event->price_data->min = $resale_data->summary->minDisplayPrice->amount;
  $event->price_data->first_q = $resale_data->summary->firstQuartileMaxPrice->amount;
  $event->price_data->median = $resale_data->summary->medianPrice->amount;
  $event->price_data->third_q = $resale_data->summary->thirdQuartileMaxPrice->amount;
  $event->price_data->max = $resale_data->summary->maxDisplayPrice->amount;
}

putEvents( $events );
closeFile( $handle );

?>