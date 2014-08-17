<?php

include '_common.php';

$event_id = $_POST['event_id'];

$cookie = getTicketmasterCookie();

$resale_url = resaleURL( $event_id );
$resale_data = getJSON( $resale_url, $cookie );

if ( resaleInvalid( $resale_data ) )
{
    // TODO: some kind of handling for non-resale events
    echo buildError('No resale data returned from Ticketmaster. This event may have expired or may not be eligible for resale.');
    return;
}

header('Content-Type: application/json');
echo json_encode( $resale_data );

?>