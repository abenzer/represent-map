<?php
  include_once "header.php";

  $types = Array(
    Array('startup', 'Startups'),
    Array('accelerator','Accelerators'),
    Array('incubator', 'Incubators'),
    Array('coworking', 'Coworking'),
    Array('investor', 'Investors'),
    Array('service', 'Consulting'),
    Array('hackerspace', 'Hackerspaces'),
    Array('event', 'Events'),
  );
  $marker_id = 0;
  $markers = Array();
  foreach($types as $type) {
    $places = mysql_query("SELECT * FROM places WHERE approved='1' AND type='$type[0]' ORDER BY title");
    $places_total = mysql_num_rows($places);
    while($place = mysql_fetch_assoc($places)) {
      $place[title] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[title])));
      $place[description] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[description])));
      $place[uri] = addslashes(htmlspecialchars($place[uri]));
      $place[address] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[address])));
      $markers[$type[0]][$marker_id] = $place;
      $marker_id++;
    }
  }
  echo(json_encode($markers));
?>
