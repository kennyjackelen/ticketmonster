<?php

define('EVENT_FILE', '../../data/events.json');
define('MAX_RETRIES', 5000);

function getTicketmasterCookie() {

  /* STEP 1. let’s create a cookie file */
  $ckfile = tempnam('/tmp', 'CURLCOOKIE');
  /* STEP 2. visit the homepage to set the cookie properly */
  $ch = curl_init('http://www.ticketmaster.com/');
  curl_setopt( $ch, CURLOPT_COOKIEJAR, $ckfile ); 
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  $output = curl_exec( $ch );

  return $ckfile;
}

function getJSON( $url, $cookie ) {
  $ch = curl_init( $url );
  curl_setopt( $ch, CURLOPT_COOKIEFILE, $cookie ); 
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  $jsonData = json_decode( curl_exec( $ch ) );
  if ( json_last_error() )
  {
    return false;
  }
  return $jsonData;
}

function eventInvalid( $event_data ) {
  if ( !$event_data )
  {
    return true;
  }
  if ( !array_key_exists( 'event', $event_data ) )
  {
    return true;
  }
  return false;
}

function resaleInvalid( $resale_data ) {
  if ( !$resale_data )
  {
    return true;
  }
  if ( !array_key_exists( 'summary', $resale_data ) )
  {
    return true;
  }
  return false;
}

function buildError( $msg ) {
  return json_encode( array( 'error_msg' => $msg ) );
} 

function addEventToFile( $event ) {
  $handle = openFile();
  if ( $handle )
  {
    $events = getEvents();
    array_push( $events, $event );
    putEvents( $events );
    closeFile( $handle );
  }
}

function removeEventFromFile( $event_id ) {
  $handle = openFile();
  if ( $handle )
  {
    $events = getEvents();
    $i = 0;
    foreach ( $events as $event )
    {
      if ( $event->event_id == $event_id )
      {
        array_splice( $events, $i, 1 );
        break;
      }
      $i++;
    }
    putEvents( $events );
    closeFile( $handle );
  }
}

function updatePrice( $event_id, $event_price ) {
  $handle = openFile();
  if ( $handle )
  {
    $events = getEvents();
    foreach ( $events as $event )
    {
      if ( $event->event_id == $event_id )
      {
        $event->my_price = $event_price;
        break;
      }
    }
    putEvents( $events );
    closeFile( $handle );
  } 
}

function getEvents() {
  // assumes a lock on EVENT_FILE
  return json_decode( file_get_contents( EVENT_FILE, true ) );
}

function putEvents( $events ) {
  // assumes a lock on EVENT_FILE
  file_put_contents( EVENT_FILE, json_encode( $events ) );
}

function eventURL( $event_id ) {
  return 'http://www.ticketmaster.com/json/event?event_id=' . $event_id;
}

function resaleURL( $event_id ) {
  return 'http://www.ticketmaster.com/json/resale?command=get_resale_listings&event_id=' . $event_id;
}

function openFile() {
  $handle = fopen( EVENT_FILE, 'r+' );
  $retries = 0;

  if ( !$handle )
  {
    return false;
  }

  do
  { 
    if ( $retries > 0 ) { 
      usleep( rand( 1, 10000 ) ); 
    } 
    $retries += 1; 
  }
  while ( !flock( $handle, LOCK_EX ) and $retries <= MAX_RETRIES );

  if ( $retries == MAX_RETRIES )
  {
    error_log('Unable to lock data file!');
    return false;
  }

  return $handle;
}

function closeFile( $handle ) {
  flock( $handle, LOCK_UN );
  fclose( $handle );
}

function getReadLock() {
  $handle = fopen( EVENT_FILE, 'r' );
  $retries = 0;

  if ( !$handle )
  {
    return false;
  }

  do
  { 
    if ( $retries > 0 ) { 
      usleep( rand( 1, 10000 ) ); 
    } 
    $retries += 1; 
  }
  while ( !flock( $handle, LOCK_SH ) and $retries <= MAX_RETRIES );

  if ( $retries == MAX_RETRIES )
  {
    error_log('Unable to lock data file!');
    return false;
  }

  return $handle;

}

function releaseReadLock( $handle ) {
  flock( $handle, LOCK_UN );
  fclose( $handle );
}

function isCRON( ) {
  // this assumes that you configure your cron job to invoke
  // the script with --cron as an argument
  if ( !isset( $_SERVER[ 'argv' ][ 1 ] ) )
  {
    return false;
  }
  if ( $_SERVER[ 'argv' ][ 1 ] != '--cron' )
  {
    return false;
  }
  return true;
}

?>