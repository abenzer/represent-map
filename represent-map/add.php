<?php
include "header.php";

// This is used to submit new markers for review.
// Markers won't appear on the map until they are approved.

$owner_name = htmlspecialchars($_POST['owner_name']);
$owner_email = htmlspecialchars($_POST['owner_email']);
$title = htmlspecialchars($_POST['title']);
$type = htmlspecialchars($_POST['type']);
$address = htmlspecialchars($_POST['address']);
$uri = htmlspecialchars($_POST['uri']);
$description = htmlspecialchars($_POST['description']);

// validate fields
if(empty($title) || empty($type) || empty($address) || empty($uri) || empty($description) || empty($owner_name) || empty($owner_email)) {
  echo "All fields are required - please try again.";
  exit;
  
} else {

  // insert into db, wait for approval
  $insert = mysql_query("INSERT INTO places (approved, title, type, address, uri, description, owner_name, owner_email) VALUES ('0', '$title', '$type', '$address', '$uri', '$description', '$owner_name', '$owner_email')") or die(mysql_error());

  // geocode new submission
  $hide_geocode_output = true;
  include "geocode.php";
  
  // if we got here, let the user now everything's OK
  echo "success";
  exit;
  
}


?>
