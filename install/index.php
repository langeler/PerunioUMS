<?php

// Define that this page is part of the Installer
define('IN_INSTALLER',true); // True / false

// Is the installer suppose to run in debug mode?
define('IS_DEBUG',true); // True / false

// Initilize the system
require '../app/init.php';

// If the installer is started before, continue
if( isset($_GET['step']) ){

	// If final step is reached
	if( isset($_SESSION['final_step']) ){

		// Check if the installer has completed step 2
		if(checkIfInstalled()){
			$_GET['step'] = 2;

		}

		// If it hasn't check if step 1 is completed
		else{
			$_GET['step'] = 1;
		}

	}

	// If the application is already installed!
	else{

		// Check installation
		if(checkIfInstalled()){
			Utils::header(404);

			// Return exception
			exit('Script already installed,
if you want to install the script again remove all tables first');
		}
	}
}

// Database schema
$databaseSchema = array(

	// Options table
	'options' => array(
		'create_table' =>
"CREATE TABLE IF NOT EXISTS `%table_prefix%options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
		'after_creation'=>
"INSERT INTO `%table_prefix%options` (`id`, `name`, `value`) VALUES
(1, 'website_name', 'PerunioCMS'),
(2, 'email_activation_required', '0'),
(3, 'email_from_email', 'hello@perunio-cms.com'),
(4, 'email_from_name', 'PerunioCMS'),
(5, 'website_language', 'en'),
(6, 'default_permission', '2');"
	),

	// Users table
	'users' => array(
		'create_table'=>
"CREATE TABLE IF NOT EXISTS `%table_prefix%users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `token` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0',
  `registered_at` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
	),

	// Pages table
	'pages' => array(
		'create_table'=>
"CREATE TABLE IF NOT EXISTS `%table_prefix%pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `private` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
	),

	// Permissions table
	'permissions'=>array(
		'create_table'=>
"CREATE TABLE IF NOT EXISTS `%table_prefix%permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
		'after_creation'=>
"INSERT INTO `%table_prefix%permissions` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Member');"
	),

	// Page permissions table
	'pages_permissions'=>array(
		'create_table'=>
"CREATE TABLE IF NOT EXISTS `%table_prefix%pages_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
	),

	// User permissions table
	'permissions_users'=>array(
		'create_table'=>
"CREATE TABLE IF NOT EXISTS `%table_prefix%permissions_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_id` (`permission_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
		'after_creation'=>
"INSERT INTO `%table_prefix%permissions_users` (`id`, `user_id`, `permission_id`) VALUES (1, 1, 1);"
	)
)
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">

	<title>
		PerunioUMS installer
	</title>

	<!-- SEO meta tags -->
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">

	<!-- Bootstrap framework (CSS) -->
	<link rel="stylesheet" href="<?php echo Utils::assetUrl('/../resources/css/bootstrap.min.css') ;?>">

	<!-- Custom stylesheet (CSS) -->
	<link rel="stylesheet" href="<?php echo Utils::assetUrl('/../resources/css/application.css') ;?>">

	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>

<div class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <a href="<?php Router::url('/') ;?>" class="navbar-brand">
                PerunioUMS installer
            </a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav">
            </ul>


        </div>
    </div>
</div>


<div class="container">


    <div class="row page">
        <div class="col-md-12">
            <div class="bs-component">
                <div class="jumbotron">

                <?php if(isset($_GET['step']) && $_GET['step'] == 2){
                    $error = '';
                    $data = array(
                                'username'=>'',
                                'email'=>'',
                                'password'=>'',
                            );

                    if(Utils::isPost()){
                        $data = isset($_POST['data']) ? (array)$_POST['data'] : $data;
                        $itsOk = true;

                        if(empty($data['username']) ||
                            empty($data['email']) ||
                            empty($data['password'])){
                            $itsOk = false;
                            $error = 'Please fill out all fields';
                        }

                        if($itsOk && !Utils::isAlphanumeric($data['username'])){
                            $itsOk = false;
                            $error = 'Username can only include alpha-numeric characters';
                        }
                        if($itsOk && !Utils::isEmail($data['email'])){
                            $itsOk = false;
                            $error = 'Please enter a valid email address';
                        }


                        if($itsOk){
                            DB::make()->insert('users',array(
                                'username'=>$data['username'],
                                'display_name'=>$data['username'],
                                'email'=>$data['email'],
                                'password'=>Utils::hashPassword($data['password']),
                                'active'=>1,
                                'registered_at'=>Utils::getTime(),
                                'last_seen'=>Utils::getTime(),
                            ));

                            unset($_SESSION['final_step']);

                            Utils::redirect(
                                Router::url('/', true, true) . '../'
                            );


                        }

                    }

                    ?>
                    <h3>Step 2 : setup admin account </h3>
                    <?php if(!empty($error)){ ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= $error ;?></div>
                    <?php } ?>
                    <form method="post">
                        <div class="form-group">
                            <label >Username</label>
                            <input required="required" type="text"
                                   name="data[username]" class="form-control"
                                   placeholder="Username" value="<?= $data['username'] ;?>">
                        </div>
                        <div class="form-group">
                            <label >Email</label>
                            <input required="required" type="email"
                                   name="data[email]" class="form-control"
                                   placeholder="Email" value="<?= $data['email'] ;?>">
                        </div>
                        <div class="form-group">
                            <label >Password</label>
                            <input required="required" type="password"
                                   name="data[password]" class="form-control" placeholder="Password">
                        </div>


                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>

                <?php }elseif(isset($_GET['step']) && $_GET['step'] == 1){ ?>

                    <h3>Step 1 : database tables creation</h3>
                    <ul>
                <?php foreach ($databaseSchema as $tableName => $queries) { ?>
                    <li>Creation of table <code><?=
                            DB::make()->getPrefix() . $tableName;?></code> .....<?php
                        $res = DB::make()->query(
                            sprintf('DROP TABLE IF EXISTS `%s%s`',
                                DB::make()->getPrefix(),
                                $tableName
                                )
                        );

                            $queries['create_table'] = str_replace(
                                '%table_prefix%',
                                DB::make()->getPrefix(),
                                $queries['create_table']
                            );

                            DB::make()->query($queries['create_table']);

                            if(isset($queries['after_creation'])){
                                $queries['after_creation'] = str_replace(
                                    '%table_prefix%',
                                    DB::make()->getPrefix(),
                                    $queries['after_creation']
                                );

                                DB::make()->query($queries['after_creation']);
                            }

                    ?>
                        <span class="label label-success">Done!</span>
                    </li>
                <?php }
                Pages::make()->checkForNewPages();
                $_SESSION = array();
                $_SESSION['final_step'] = true;
                ?>
                    </ul>
                    <a class="btn btn-info" href="index.php?step=2">Next step</a>
                <?php }else{?>
                    <h1>Welcome to PerunioUMS installer</h1>
                    <p>Click the button below to start the installation process</p>
                    <a class="btn btn-info" href="index.php?step=1">Start installation</a>

                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

	<!-- JQuery library (JavaScript) -->
	<script src="<?php echo Utils::assetUrl('/../resources/js/jquery.min.js') ;?>"></script>

	<!-- Bootstrap library (JavaScript) -->
	<script src="<?php echo Utils::assetUrl('/../resources/js/bootstrap.min.js') ;?>"></script>

	<!-- Custom scripts (JavaScript) -->
	<script src="<?php echo Utils::assetUrl('/../resources/js/application.js') ;?>"></script>
</body>
</html>