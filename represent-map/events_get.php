<?php
include_once "header.php";



// get new events from eventbrite
if($eb_app_key != "") {
  @getEventbriteEvents($eb_keywords, $eb_city, $eb_within_radius);
}

// geocode eany new markers
include "geocode.php";


// get events from eventbrite.com
function getEventbriteEvents($eb_keywords, $eb_city, $eb_proximity) {
  global $eb_app_key;

  $xml_url = "https://www.eventbrite.com/xml/event_search?...&within=50&within_unit=M&keywords=".$eb_keywords."&city=".$eb_city."&date=This+month&app_key=".$eb_app_key;
  echo $xml_url."<br />";
  $xml = simplexml_load_file($xml_url);
  $count = 0;

  // loop over events from eventbrite xml
  foreach($xml->event as $event) {  

    // add event if it doesn't already exist
    $event_query = mysql_query("SELECT * FROM events WHERE id_eventbrite=$event->id") or die(mysql_error());
    if(mysql_num_rows($event_query) == 0) {
      echo $event_id." ";

      // get event url
      foreach($event->organizer as $organizer) {
        $event_organizer_url = $organizer->url;
      }
      // get event address
      foreach($event->venue as $venue) {
        $event_venue_address = $venue->address;
        $event_venue_city = $venue->city;
        $event_venue_postal_code = $venue->postal_code;
      }
      // get event title
      $event_title = str_replace(array("\r\n", "\r", "\n"), ' ', $event->title);  

      // add event to database
      mysql_query("INSERT INTO events (id_eventbrite, 
                                      title,
                                      created, 
                                      organizer_name, 
                                      uri, 
                                      start_date, 
                                      end_date, 
                                      address
                                      ) VALUES (
                                      '$event->id',
                                      '".parseInput($event_title)."',
                                      '".strtotime($event->created)."',
                                      '".trim(parseInput($event->organizer->name))."',
                                      '$event_organizer_url',
                                      '".strtotime($event->start_date)."',
                                      '".strtotime($event->end_date)."',
                                      '$event_venue_address, $event_venue_city, $event_venue_postal_code'
                                      )") or die(mysql_error()); 
    }

    $count++;

  }
  
}

?>
