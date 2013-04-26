<?php
include_once "header.php";

// This script syncs your database with Startup Genome.
// It checks to see if your local database is missing any
// organizations that have been added to your Startup Genome map.
// If it finds any, it will add them to your local database.

// This script will only run if we haven't checked for new only 
// if the frequency interval specified in db.php has already passed.

$interval_query = mysql_query("SELECT sg_lastupdate FROM settings LIMIT 1");
if(mysql_num_rows($interval_query) == 1) {
  $interval_info = mysql_fetch_assoc($interval_query);
  if((time()-$interval_info[sg_lastupdate]) > $sg_frequency || $_GET['override'] == "true") {

    //grab the location configuration. We can probably remove this and just use $sg_location directly. 
    $config['search_location'] = $sg_location;
    
    // set the header required by the API. 
    $headers = array("AUTH-CODE: {$sg_auth_code}");

    // connect to startup genome API using cURL
    $curl = curl_init();

    // Set some options
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, "http://startupgenome.com/api/organizations{$config['search_location']}");

    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    
    // Close request to clear up some resources
    curl_close($curl);    
    
      $places_arr = json_decode($resp, 1);
      
      // update organizations in local db
      $org_array = Array();
      foreach ($places_arr['response'] as $key => $place) {
        if (!$place['categories'][0]['parent_category_id'])
          $place['categories'][0]['parent_category_id'] = $place['categories'][0]['category_id'];
        switch ($place['categories'][0]['parent_category_id']) {
          default:
          case '2': $place[type] = 'startup'; break;
          case '3': $place[type] = 'investor'; break;
          case '4': $place[type] = 'accelerator'; break;
          case '5': $place[type] = 'incubator'; break;
          case '6': $place[type] = 'coworking'; break;
        }
        
        // format the address for display
        $place[address] = $place['address1'];
        $place[address] .= ($place['address2']?($place[address]?', ':'').$place['address2']:'');
        $place[address] .= ($place['city']?($place[address]?', ':'').$place['city']:'');
        $place[address] .= ($place['state']?($place[address]?', ':'').(isset($states_arr[$place['state']])?$states_arr[$place['state']]:$place['state']):'');
        $place[address] .= ($place['zip']?($place[address]?', ':'').$place['zip']:'');
        $place[address] .= ($place['country']?($place[address]?', ':'').(isset($countries_arr[$place['country']])?$countries_arr[$place['country']]:$place['country']):'');
        $types_arr[$place[type]][] = $place;
        $org_array[] = $place['organization_id'];
        $count[$place[type]]++;
        $marker_id++;

        $place_query = mysql_query("SELECT id FROM places WHERE sg_organization_id='".$place['organization_id']."' LIMIT 1") or die(mysql_error());
        
        // organization doesn't exist, add it to the db
        if(mysql_num_rows($place_query) == 0) {
          mysql_query("INSERT INTO places (approved,
                                          title, 
                                          type,
                                          lat,
                                          lng,
                                          address,
                                          uri,
                                          description,
                                          sg_organization_id
                                          ) VALUES (
                                          '1', 
                                          '".parseInput($place['name'])."', 
                                          '".parseInput($place['type'])."', 
                                          '".parseInput($place['latitude'])."', 
                                          '".parseInput($place['longitude'])."', 
                                          '".parseInput($place['address'])."',
                                          '".parseInput($place['url'])."',
                                          '".parseInput($place['description'])."',
                                          '".parseInput($place['organization_id'])."'
                                          )") or die(mysql_error());
          
        // organization already exists, update it with new info if necessary
        } else if(mysql_num_rows($place_query) == 1) {
          $place_info = mysql_fetch_assoc($place_query);
          if($place_info['title'] != $place['name'] || $place_info['type'] != $place['type'] || $place_info['lat'] != $place['latitude'] || $place_info['lng'] != $place['longitude'] || $place_info['address'] != $place['address'] || $place_info['uri'] != $place['url'] || $place_info['description'] != $place['description']) {
            mysql_query("UPDATE places SET title='".parseInput($place['name'])."',
                                           type='".parseInput($place['type'])."',
                                           lat='".parseInput($place['latitude'])."',
                                           lng='".parseInput($place['longitude'])."',
                                           address='".parseInput($place['address'])."',
                                           uri='".parseInput($place['url'])."',
                                           description='".parseInput($place['description'])."'
                                           WHERE sg_organization_id='".parseInput($place['organization_id'])."' LIMIT 1");
          }
          
        }
      }
      
      // delete any old markers that have already been deleted on SG
      $org_array = implode(",", $org_array);
      $deleted = mysql_query("DELETE FROM places WHERE sg_organization_id NOT IN ({$org_array})") or die(mysql_error());

      // update settings table with the timestamp for this sync
      mysql_query("UPDATE settings SET sg_lastupdate='".time()."'");
  }
}





?>
