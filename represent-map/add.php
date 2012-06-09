<?php
include "header.php";

// This is used to submit new markers for review.
// Markers won't appear on the map until they are approved.

function parseInput($value) {
  $value = htmlspecialchars($value);
  $value = str_replace("\r", "", $value);
  $value = str_replace("\n", "", $value);
  return $value;
}

$owner_name = parseInput($_POST['owner_name']);
$owner_email = parseInput($_POST['owner_email']);
$title = parseInput($_POST['title']);
$type = parseInput($_POST['type']);
$address = parseInput($_POST['address']);
$uri = parseInput($_POST['uri']);
$description = parseInput($_POST['description']);

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
