<?php
include "header.php";

$task = array_key_exists('task', $_GET) ? $_GET['task'] : null;

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
  $title = $_POST['title'];
  $type = $_POST['type'];
  $address = $_POST['address'];
  $uri = $_POST['uri'];
  $description = $_POST['description'];
  $owner_name = $_POST['owner_name'];
  $owner_email = $_POST['owner_email'];
  $type = $_POST['type'];
  
  mysql_query("UPDATE places SET title='$title', type='$type', address='$address', uri='$uri', lat='', lng='', description='$description', owner_name='$owner_name', owner_email='$owner_email' WHERE id='$place_id' LIMIT 1") or die(mysql_error());
  
  // geocode
  $hide_geocode_output = true;
  include "../geocode.php";
  
  header("Location: index.php?view=$view&search=$search&p=$p");
  exit;
}

?>



<?php echo $admin_head; ?>

<form id="admin" class="form-horizontal" action="edit.php" method="post">
  <h1>
    Edit Place
  </h1>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="">Title</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="title" value="<?php echo $place['title']; ?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Type</label>
      <div class="controls">
        <select class="input input-xlarge" name="type">
          <option<?php if($place['type'] == "startup") {?> selected="selected"<?php } ?>>startup</option>
          <option<?php if($place['type'] == "accelerator") {?> selected="selected"<?php } ?>>accelerator</option>
          <option<?php if($place['type'] == "incubator") {?> selected="selected"<?php } ?>>incubator</option>
          <option<?php if($place['type'] == "coworking") {?> selected="selected"<?php } ?>>coworking</option>
          <option<?php if($place['type'] == "investor") {?> selected="selected"<?php } ?>>investor</option>
          <option<?php if($place['type'] == "service") {?> selected="selected"<?php } ?>>service</option>
          <option<?php if($place['type'] == "hackerspace") {?> selected="selected"<?php } ?>>hackerspace</option>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Address</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="address" value="<?php echo $place['address']; ?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="uri" value="<?php echo $place['uri']; ?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Description</label>
      <div class="controls">
        <textarea class="input input-xlarge" name="description"><?php echo $place['description']; ?></textarea>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Name</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_name" value="<?php echo $place['owner_name']; ?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Email</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_email" value="<?php echo $place['owner_email']; ?>" id="">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <input type="hidden" name="task" value="doedit" />
      <input type="hidden" name="place_id" value="<?php echo $place['id']; ?>" />
      <input type="hidden" name="view" value="<?php echo $view; ?>" />
      <input type="hidden" name="search" value="<?php echo $search; ?>" />
      <input type="hidden" name="p" value="<?php echo $p; ?>" />
      <a href="index.php" class="btn" style="float: right;">Cancel</a>
    </div>
  </fieldset>
</form>



<?php echo $admin_foot; ?>
