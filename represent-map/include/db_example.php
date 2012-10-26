<?php

// mysql hostname
$db_host = "";

// database name
$db_name = "";

// database user name
$db_user = "";

// database password
$db_pass = "";

// admin username
$admin_user = "admin";

// admin password
$admin_pass = "letsgetmappy";


// StartupGenome.com integration (optional)
//
// We recommend integrating your map with the StartupGenome project. 
// It's easy to setup, it will allow people to keep their profiles update 
// over time, and it can help you show the world how your startup community 
// is growing. StartupGenome also has a great interface for curating your 
// map data.
//
// To use this feature, you need to be a curator for your city.
// If you're not yet a curator, learn more here: 
// http://www.startupgenome.com/curators/
// 
// If you are already a curator, find your API key on your
// Startup Genome profile and enter it below. You can manage the markers
// on your map from the Startup Genome website, rather than using the
// built-in admin panel.
// 
// You can turn on Startup Genome integration by changing
// $sg_enabled to "true".
$sg_enabled = false;

  // Put your SG API code here
  $sg_auth_code = '';
  
  // Choose your map's location here. If you're not sure
  // about this, check the URL on the Startup Genome website.
  $sg_location = '';
  // Examples:
  // $sg_location = '/city/los-angeles-ca';
  // $sg_location = '/state/ca-us';
  // $sg_location = '/country/chile';
  
  // We only check for new data from SG when people visit your map,
  // or when you run "startupgenome_get.php?override=true" manually.
  // You can limit how often this happens to avoid slow page loads.
  // Set the frequency below (in seconds).
  $sg_frequency = "3600";



// EventBrite.com integration (optional)
// 
// Show events on the map? If set to "true", an event
// category will appear in the marker list, and you can
// run events_get.php in your browser (or a chron) to populate
// it with data from eventbrite.
$show_events = true;

    // put your eventbrite api key here
    $eb_app_key = "";

    // search eventbrite for these keywords
    // add %20OR%20 between keywords for an "OR" search
    // example: startup%20OR%20demo+day
    $eb_keywords = "startup";

    // specify city to search in and around
    // example: Santa+Monica
    $eb_city = "";

    // specify search radius (in miles)
    $eb_within_radius = 50;



// HTML that goes just before </head>
$head_html = "";

// attribution (must leave link intact, per our license)
$attribution = "
  <span>
    Based on <a href='http://www.represent.la' target='_blank'>RepresentLA</a>
  </span>
";

// add startup genome to attribution if integration enabled
if($sg_enabled) {
  $attribution .= "
    <br /><br />
    Data from <a target='_blank' href='http://www.startupgenome.com'>StartupGenome</a>
  ";
}



?>
