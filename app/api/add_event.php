<?php
include '_common.php';

$cookie = getTicketmasterCookie();

$event_id = $_POST['event_id'];

$event_url = eventURL( $event_id );
$event_data = getJSON( $event_url, $cookie );

if ( eventInvalid( $event_data ) )
{
    // TODO: some kind of handling for invalid events
    echo buildError('No event data returned from Ticketmaster. This event doesn\'t seem to be valid.');
    return;
}

$resale_data = getResaleData( $event_id );

if ( resaleInvalid( $resale_data ) )
{
    // TODO: some kind of handling for non-resale events
    echo buildError('No resale data returned from Ticketmaster. This event may have expired or may not be eligible for resale.');
    return;
}

$rightNow = time() * 1000;
$cur_low_price = array( $rightNow, $resale_data->summary->minDisplayPrice->amount );
$low_price_trend = array( $cur_low_price );

$cur_num_tickets = array( $rightNow, $resale_data->summary->totalTickets );
$num_ticket_trend = array( $cur_num_tickets );

$new_event = array(
    'event_id' => $event_data->event->id,
    'name' => $event_data->event->name,
    'date' => $event_data->event->date,
    'venue_name' => $event_data->event->venue->name,
    'venue_city' => $event_data->event->venue->city,
    'venue_state' => $event_data->event->venue->state,
    'my_price' => '',
    'low_price_trend' => $low_price_trend,
    'num_ticket_trend' => $num_ticket_trend,
    'price_data' => array(
        'min' => $resale_data->summary->minDisplayPrice->amount,
        'first_q' => $resale_data->summary->firstQuartileMaxPrice->amount,
        'median' => $resale_data->summary->medianPrice->amount,
        'third_q' => $resale_data->summary->thirdQuartileMaxPrice->amount,
        'max' => $resale_data->summary->maxDisplayPrice->amount
      ),
    'expired' => false
  );

addEventToFile( $new_event );

header('Content-Type: application/json');
echo json_encode( $new_event );

?>