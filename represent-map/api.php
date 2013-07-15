<?php
  include_once "header.php";

  header('Content-type: application/json');

  $_escape = function ($str){
     return preg_replace("!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str);
  };

  $marker_id = 0;
  $places = mysql_query("SELECT * FROM places WHERE approved='1' ORDER BY title");
  $places_total = mysql_num_rows($places);
  
  echo '{ "type": "FeatureCollection", "features": [';
  
  while($place = mysql_fetch_assoc($places)) {
    $newplace = Array( );
    $newplace["type"] = "Feature";
    $newplace["properties"] = Array(
      "title" => $_escape( $place[title] ),
      "description" => $_escape( $place[description] ),
      "uri" => $_escape( $place[uri] ),
      "address" => $_escape( $place[address] ),
      "type" => $_escape( $place[type] )
    );
    $newplace["geometry"] = Array(
      "type" => "Point",
      "coordinates" => Array( $place[lng] * 1.0, $place[lat] * 1.0 )
    );

    if( $marker_id > 0 ){
      echo ',';
    }
    echo json_encode( $newplace );
    
    $marker_id++;
  }
  
  echo '] }';
  
?>