<?php if(!defined('IN_APP')){exit;} /* don't remove this line */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">

    <title>
    <?php echo
        Utils::h(
            getOption('website_name','PerunioUMS')
        );?>
    </title>

	<!-- SEO meta tags -->
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">

	<!-- Bootstrap framework (CSS) -->
	<link rel="stylesheet" href="<?php echo Utils::assetUrl('/./resources/css/bootstrap.min.css') ;?>">

	<!-- Custom stylesheet (CSS) -->
	<link rel="stylesheet" href="<?php echo Utils::assetUrl('/./resources/css/application.css') ;?>">

	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>

<?php echo $this->element('navbar') ;?>

<div class="container">


    <div class="row page">
        <div class="col-md-12">
            <?php echo $this->element('flashes') ;?>

            <?php echo $this->fetch('content') ;?>
        </div>
    </div>


    <?php echo $this->element('footer') ;?>


</div>

	<!-- JQuery library (JavaScript) -->
	<script src="<?php echo Utils::assetUrl('/./resources/js/jquery.min.js') ;?>"></script>

	<!-- Extended Bootstrap framework (JavaScript) -->
	<script src="<?php echo Utils::assetUrl('/./resources/js/bootstrap.min.js') ;?>"></script>

	<!-- Custom scripts (JavaScript) -->
	<script src="<?php echo Utils::assetUrl('/./resources/js/application.js') ;?>"></script>
</body>
</html>