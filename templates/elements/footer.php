<?php if(!defined('IN_APP')){exit;} /* don't remove this line */ ?>
<footer>
    <div class="row">
        <div class="col-lg-12">
            <p class="pull-left">
                <a href="<?php echo
                Router::url('/');?>"><?php echo Utils::h(
                        getOption('website_name','PerunioUMS')
                    ) ;?></a>
            </p>
        </div>
    </div>
</footer>