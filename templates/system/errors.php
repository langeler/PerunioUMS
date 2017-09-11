<?php
	/* don't remove this line */
	if(!defined('IN_APP')) {
		exit;
	}

	$message = (isset($message) ? $message : 'page not found');
	$errorCode = (isset($errorCode) ? $errorCode : 404);
?>

<div class="bs-component">
    <div class="jumbotron">
        <h1><?php echo $errorCode ;?></h1>
        <p><?php echo $message ;?></p>
        <p><a href="<?php echo
            Router::url('/');?>" class="btn btn-primary btn-lg"><?php echo lang('back_to_home_page') ;?></a></p>
    </div>
</div>