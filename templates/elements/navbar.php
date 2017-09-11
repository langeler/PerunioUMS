<?php if(!defined('IN_APP')){exit;} /* don't remove this line */ ?>
<div class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <a href="<?php echo Router::url('/') ;?>" class="navbar-brand"><?php echo
                Utils::h(
                    getOption('website_name','PerunioUMS')
                ) ;?></a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav">
            </ul>
            <ul class="nav navbar-nav navbar-right">
            <?php if(!Utils::isUserLoggedIn()){ ?>
                <li>
                    <a href="<?php echo Router::url('/login') ;?>" ><?php echo lang('Login') ;?></a>
                </li>
                <li>
                    <a href="<?php echo Router::url('/register') ;?>" ><?php echo lang('Register') ;?></a>
                </li>
            <?php }else{ ?>
                <li>
                    <a href="<?php echo
                    Router::url('/account') ;?>" ></a>
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" >Hello
                        ,<strong><?php
                                if(Utils::currentUserInfo('display_name') != ''){
                                    echo Utils::h(
                                        Utils::currentUserInfo('display_name')
                                    );
                                }else{
                                    echo Utils::currentUserInfo('username');
                                }
                            ?></strong>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="download">
                        <li>
                            <a href="<?php echo Router::url('/account') ;?>">Account settings</a>
                        </li>

                    <?php if(Permissions::make()->isAdmin(
                                Utils::currentUserInfo('id'))
                            ){ ?>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo Router::url('/admin_users') ;?>">Users</a>
                        </li>
                        <li>
                            <a href="<?php echo Router::url('/admin_permissions') ;?>">Permissions</a>
                        </li>
                        <li>
                            <a href="<?php echo Router::url('/admin_pages') ;?>">Pages</a>
                        </li>
                        <li>
                            <a href="<?php echo Router::url('/admin_configuration') ;?>">Website settings</a>
                        </li>
                    <?php } ?>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo Router::url('/logout') ;?>">Logout</a>
                        </li>
                    </ul>
                </li>


            <?php } ?>
            </ul>

        </div>
    </div>
</div>