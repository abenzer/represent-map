<?php
$base = 100000;
$x = 0;

//The API will return a non-200 status code and a 
// json encoded string with an 2d array of the following codes and
// some optional additional (depending on the code)

define("ERR_UNKNOWN",				$base+($x++)); //an unknown error

/////////////////////
// HTTP STATUS 417
/////////////////////
define("ERR_NO_DATA",				$base+($x++)); //the server is expecting data, but none was given
define("ERR_INVALID",				$base+($x++)); //the specified data was not valid
	// {"field": name}
define("ERR_BLANK",					$base+($x++)); //the specified data was blank 
	// {"field": name}
define("ERR_NOT_FOUND",			$base+($x++)); //the specified field was not found
	// {"field": name}
define("ERR_INVALID_EMAIL",	$base+($x++)); //the email address was not valid
	// {"field": name}
define("ERR_INVALID_PHONE",	$base+($x++)); //the phone number was not valid
	// {"field": name}
define("ERR_INVALID_URL",		$base+($x++)); //the url was not valid
	// {"field": name}
define("ERR_INVALID_PATH",	$base+($x++)); //the file path was not valid
	// {"field": name}
define("ERR_INVALID_BOOL",	$base+($x++)); //the boolean was not valid
	// {"field": name}
define("ERR_INVALID_MAC",	$base+($x++)); //the mac address was not valid
	// {"field": name}
define("ERR_TOO_SHORT",			$base+($x++)); //the field is too short
	// {"field": name}
define("ERR_TOO_LONG",			$base+($x++)); //the field is too long
	// {"field": name}
define("ERR_MISSING_REQ",		$base+($x++)); //a required field is missing
	// {"field": name}
define("ERR_EXISTS",			$base+($x++)); //the thing already exists in the system


/////////////////////
// HTTP STATUS 403
/////////////////////
define("ERR_NOT_SUPER",			$base+($x++)); //the action requires super user privs but you aren't
define("ERR_NOT_ADMIN",			$base+($x++)); //the action requires org admin or greater privs but you aren't
define("ERR_NOT_MANAGER",		$base+($x++)); //the action requires group manger or greater privs but you aren't

/////////////////////
// HTTP STATUS 500 
/////////////////////
define("ERR_AUTH_NET",			$base+($x++));

$_MESSAGES = array(
	ERR_UNKNOWN				=> 	"An unknown error occurred",
	ERR_NO_DATA				=> 	"No data was given",

	// Most 417 messages are written so that they can be appended to
	// a field name
	ERR_INVALID				=> 	"was not valid",
	ERR_BLANK 				=>	"cannot be blank",
	ERR_NOT_FOUND			=>	"was not found",
	ERR_INVALID_EMAIL =>	"is not a valid email address",
	ERR_INVALID_PHONE	=>	"is not a valid phone number",
	ERR_INVALID_URL		=>	"is not a valid url",
	ERR_INVALID_PATH	=>	"is not a valid file path",
	ERR_INVALID_BOOL	=>	"is not a valid boolean",
	ERR_INVALID_MAC		=>	"is not a valid mac address",
	ERR_TOO_SHORT			=>	"is too short",
	ERR_TOO_LONG 			=>	"is too long",
	ERR_MISSING_REQ 	=> "is missing",
	ERR_EXISTS				=> "already exists",

	ERR_NOT_SUPER			=> "You must be a super user to do that",
	ERR_NOT_ADMIN			=>	"You must be the organization admin to do that",
	ERR_NOT_MANAGER		=>	"You must be the group manager to do that"
);
?>
