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

    // connect to startup genome API
    if(strpos($_SERVER['SERVER_NAME'],'.local') !== false) {
      $config = array('api_url' => 'startupgenome.com.local/api/');
    } else {
      $config = array('api_url' => 'startupgenome.co/api');
    }
    $config['search_location'] = $sg_location;
    $http = Http::connect($config['api_url'],false,'http');

    try {
      $r = $http->doGet("login/{$sg_auth_code}");
      $j = json_decode($r,1);
      $http->setHeaders(array("AUTH-CODE: {$sg_auth_code}"));
      $user = $j['response'];

    } catch(Exception $e) {
      $error = "<div class='error'>".print_r($e)."</div>";
      exit();
    }

    // get organizations
    try {
      $r = $http->doGet("/organizations{$config['search_location']}");
      $places_arr = json_decode($r, 1);

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

    // show errors if there were any issues
    } catch (Exception $e) {
      echo "<div class='error'>";
      print_r($e);
      echo "</div>";
      exit();
    }



  }
}





?>