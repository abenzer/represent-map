<?php
include "header.php";


if(isset($_GET['place_id'])) {
  $place_id = htmlspecialchars($_GET['place_id']); 
} else if(isset($_POST['place_id'])) {
  $place_id = htmlspecialchars($_POST['place_id']);
} else {
  exit; 
}


// get place info
$place_query = mysql_query("SELECT * FROM places WHERE id='$place_id' LIMIT 1");
if(mysql_num_rows($place_query) != 1) { exit; }
$place = mysql_fetch_assoc($place_query);


// do place edit if requested
if($task == "doedit") {
  $title = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['title'] ) );
  $type = $_POST['type'];
  $address = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['address'] ) );
  $uri = $_POST['uri'];
  $description = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['description'] ) );
  $owner_name = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['owner_name'] ) );
  $owner_email = $_POST['owner_email'];
  $lat = (float) $_POST['lat'];
  $lng = (float) $_POST['lng'];
  
  mysql_query("UPDATE places SET title='$title', type='$type', address='$address', uri='$uri', lat='$lat', lng='$lng', description='$description', owner_name='$owner_name', owner_email='$owner_email' WHERE id='$place_id' LIMIT 1") or die(mysql_error());
  
  // geocode
  //$hide_geocode_output = true;
  //include "../geocode.php";
  
  header("Location: index.php?view=$view&search=$search&p=$p");
  exit;
}

?>



<? echo $admin_head; ?>

<form id="admin" class="form-horizontal" action="edit.php" method="post">
  <h1>
    Edit Place
  </h1>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="">Title</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="title" value="<?=$place[title]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Type</label>
      <div class="controls">
        <select class="input input-xlarge" name="type">
          <option<? if($place[type] == "startup") {?> selected="selected"<? } ?>>startup</option>
          <option<? if($place[type] == "accelerator") {?> selected="selected"<? } ?>>accelerator</option>
          <option<? if($place[type] == "incubator") {?> selected="selected"<? } ?>>incubator</option>
          <option<? if($place[type] == "coworking") {?> selected="selected"<? } ?>>coworking</option>
          <option<? if($place[type] == "food") {?> selected="selected"<? } ?>>food</option>
          <option<? if($place[type] == "service") {?> selected="selected"<? } ?>>service</option>
          <option<? if($place[type] == "hackerspace") {?> selected="selected"<? } ?>>hackerspace</option>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Address</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="address" value="<?=$place[address]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="uri" value="<?=$place[uri]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Description</label>
      <div class="controls">
        <textarea class="input input-xlarge" name="description"><?=$place[description]?></textarea>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Name</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_name" value="<?=$place[owner_name]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Email</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_email" value="<?=$place[owner_email]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Location</label>
      <div class="controls">
        <input type="hidden" name="lat" id="mylat" value="<?=$place[lat]?>"/>
        <input type="hidden" name="lng" id="mylng" value="<?=$place[lng]?>"/>
        <div id="map" style="width:80%;height:300px;">
        </div>
        <script type="text/javascript">
          var map = new google.maps.Map( document.getElementById('map'), {
            zoom: 17,
            center: new google.maps.LatLng( <?=$place[lat]?>, <?=$place[lng]?> ),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false
          });
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng( <?=$place[lat]?>, <?=$place[lng]?> ),
            map: map,
            draggable: true
          });
          google.maps.event.addListener(marker, 'dragend', function(e){
            document.getElementById('mylat').value = e.latLng.lat().toFixed(6);
            document.getElementById('mylng').value = e.latLng.lng().toFixed(6);
          });
        </script>
      </div>
    </div>    
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <input type="hidden" name="task" value="doedit" />
      <input type="hidden" name="place_id" value="<?=$place[id]?>" />
      <input type="hidden" name="view" value="<?=$view?>" />
      <input type="hidden" name="search" value="<?=$search?>" />
      <input type="hidden" name="p" value="<?=$p?>" />
      <a href="index.php" class="btn" style="float: right;">Cancel</a>
    </div>
  </fieldset>
</form>



<? echo $admin_foot; ?>
