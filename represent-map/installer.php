<?
if(isset($_POST['installer_submitted'])) {
	// validate fields
	$error = '';
	if(!$_POST['db_hostname']) $error .= 'Please enter a database hostname.<br />';
	if(!$_POST['db_name']) $error .= 'Please enter a database name.<br />';
	if(!$_POST['db_username']) $error .= 'Please enter a database username.<br />';
	if(!$_POST['db_password']) $error .= 'Please enter a database password.<br />';
	if(!$_POST['admin_username']) $error .= 'Please enter an administrative username.<br />';
	if(!$_POST['admin_pass']) $error .= 'Please enter an administrative password.<br />';

	// if no basic validation errors, check to make sure database info actually works
	if(!$error) {
		if(!@mysql_connect($_POST['db_hostname'], $_POST['db_username'], $_POST['db_password'])) $error .= 'Your database host, username, or password information were not correct.<br />';
		if(!$error) {
			if(!@mysql_select_db($_POST['db_name'])) $error .= 'Your database name is not valid/could not be found.<br />';
		}
	}

	// if not errors, proceed with installer by first creating db file
	if(!$error) {
		$file = 'include/db.php';
		if(!$file_handle = fopen($file, 'w')) {
			$error .= '
				<p style="color:red;"><strong>We were unable to update the "/install" directory\'s permissions, which is required for this installer to run. Please do so manually using the below instructions:</p>
				<p><a href="http://www.cubecartforums.org/docs/appendix/how-to-chmod-directories.html" target="_blank">http://www.cubecartforums.org/docs/appendix/how-to-chmod-directories.html</a></p>
			';
		}
		fclose($file_handle);
	}

	// if file was created succesfully, write to it:
	if(!$error) {
		$config_file_contents = file_get_contents('include/db_example.php');
		$config_file_contents = str_replace('[db_host]',$_POST['db_hostname'],$config_file_contents);
		$config_file_contents = str_replace('[db_name]',$_POST['db_name'],$config_file_contents);
		$config_file_contents = str_replace('[db_user]',$_POST['db_username'],$config_file_contents);
		$config_file_contents = str_replace('[db_pass]',$_POST['db_password'],$config_file_contents);
		$config_file_contents = str_replace('[admin_user]',$_POST['admin_username'],$config_file_contents);
		$config_file_contents = str_replace('[admin_pass]',$_POST['admin_pass'],$config_file_contents);

		$file = 'include/db.php';
		$file_handle = fopen($file, 'w');
		if(!fwrite($file_handle, $config_file_contents)) {
			$error .= '
				<p style="color:red;"><strong>We were unable to update the "/install" directory\'s permissions, which is required for this installer to run. Please do so manually using the below instructions:</p>
				<p><a href="http://www.cubecartforums.org/docs/appendix/how-to-chmod-directories.html" target="_blank">http://www.cubecartforums.org/docs/appendix/how-to-chmod-directories.html</a></p>
			';
		}
		fclose($file_handle);
	}

	// if config file is created, lets connect to the database and install our tables
	if(!$error) {
		mysql_connect($_POST['db_hostname'], $_POST['db_username'], $_POST['db_password']) or die(mysql_error());
		mysql_select_db($_POST['db_name']) or die(mysql_error());

		if(!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'events'"))) {
			if(!mysql_query("CREATE TABLE IF NOT EXISTS `events` (
			  `id` int(9) NOT NULL AUTO_INCREMENT,
			  `id_eventbrite` varchar(15) NOT NULL,
			  `title` varchar(200) NOT NULL,
			  `created` int(14) NOT NULL,
			  `organizer_name` varchar(100) NOT NULL,
			  `uri` varchar(200) NOT NULL,
			  `start_date` int(14) NOT NULL,
			  `end_date` int(14) NOT NULL,
			  `lat` float NOT NULL,
			  `lng` float NOT NULL,
			  `address` varchar(200) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;")) {
				die(mysql_error());
			}
		}

		if(!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'places'"))) {
			if(!mysql_query("CREATE TABLE IF NOT EXISTS `places` (
			  `id` int(9) NOT NULL AUTO_INCREMENT,
			  `approved` int(1) DEFAULT NULL,
			  `title` varchar(100) NOT NULL,
			  `type` varchar(20) NOT NULL,
			  `lat` float NOT NULL,
			  `lng` float NOT NULL,
			  `address` varchar(200) NOT NULL,
			  `uri` varchar(200) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  `sector` varchar(50) NOT NULL,
			  `owner_name` varchar(100) NOT NULL,
			  `owner_email` varchar(100) NOT NULL,
			  `sg_organization_id` int(9) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;")) {
				die(mysql_error());
			}
		}

		if(!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'settings'"))) {
			if(!mysql_query("CREATE TABLE IF NOT EXISTS `settings` (
			  `sg_lastupdate` int(14) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;")) {
				die(mysql_error());
			}

			if(!mysql_query("INSERT INTO `settings` (`sg_lastupdate`) VALUES (0)")) {
				die(mysql_error());
			}
		}
	}

	// if no errors, then setup completed message
	if(!$error) {
		$complete = 1;
	}
}
?>
<html>
	<head>
		<title>RepresentMap Installer</title>
		<link href='/bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' />
		<link href='/bootstrap/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' />
		<link rel='stylesheet' href='admin/admin.css' type='text/css' />
		<script src='/bootstrap/js/bootstrap.js' type='text/javascript' charset='utf-8'></script>
		<script src='/scripts/jquery-1.7.1.js' type='text/javascript' charset='utf-8'></script>
	</head>
	<body>
		<div id="content">
			<form class="well form-inline" action="" id="login" method="post">
				<h1>RepresentMap Installer</h1>

				<? if(isset($complete)) : ?>
					<div class="alert alert-success">
						<p><strong>Congratulations! The installation has been completed. Please click the following link to access your site:</strong></p>
						<p>
							<a class="btn" href="/">Launch Site</a> &nbsp;&nbsp;&nbsp; <a class="btn" href="/admin">Launch Admin Area</a>
						</p>
					</div>

				<? else : ?>
					<p><strong>We have detected that you have not installed the represent.ia system. This installer will help you with that process.</strong></p>

					<?= (isset($error) && $error?'<div class="alert alert-danger">'.$error.'</div>':''); ?>

					<hr />

					<? $dirperms = substr(sprintf('%o', fileperms('include')), -4); ?>
					<? if($dirperms != '0777' && $dirperms != '0755' && !chmod("include", 0755)) : ?>
						<div class="alert alert-danger">
							<p style="color:red;"><strong>We were unable to update the "/install" directory's permissions, which is required for this installer to run. Please do so manually using the below instructions:</p>
							<p><a href="http://www.cubecartforums.org/docs/appendix/how-to-chmod-directories.html" target="_blank">http://www.cubecartforums.org/docs/appendix/how-to-chmod-directories.html</a></p>
						</div>

					<? else : ?>
						<div class="control-group"><h4>Database Settings</h4></div>
						<p>This installer requires that you have a database setup on your server and can provide the following information:</p>
						<div class="control-group">
							<input type="text" name="db_hostname" class="input-large" value="<?= (isset($_POST['db_hostname'])?$_POST['db_hostname']:''); ?>" placeholder="Hostname (e.g. localhost)" />
						</div>
						<div class="control-group">
							<input type="text" name="db_name" class="input-large" value="<?= (isset($_POST['db_name'])?$_POST['db_name']:''); ?>" placeholder="Database Name" />
						</div>
						<div class="control-group">
							<input type="text" name="db_username" class="input-large" value="<?= (isset($_POST['db_username'])?$_POST['db_username']:''); ?>" placeholder="Username" />
						</div>
						<div class="control-group">
							<input type="password" name="db_password" class="input-large" value="<?= (isset($_POST['db_password'])?$_POST['db_password']:''); ?>" placeholder="Password" />
						</div>

						<hr />

						<div class="control-group"><h4>Administrative Settings</h4></div>
						<p>The following is the admin username and password you will use to access the adminstrative control panel of this system:</p>
						<div class="control-group">
							<input type="text" name="admin_username" class="input-large" value="<?= (isset($_POST['admin_username'])?$_POST['admin_username']:''); ?>" placeholder="Admin Username" />
						</div>
						<div class="control-group">
							<input type="password" name="admin_pass" class="input-large" value="<?= (isset($_POST['admin_pass'])?$_POST['admin_pass']:''); ?>" placeholder="Admin Password" />
						</div>

						<hr />

						<input type="submit" class="btn btn-info" name="installer_submitted" value="Install System" />
					<? endif; ?>
				<? endif; ?>
			</form>
		</div> <!-- /#content -->
	</body>
</html>

<? exit(); ?>