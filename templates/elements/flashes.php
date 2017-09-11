<?php if(!defined('IN_APP')){exit;} /* don't remove this line */ ?>
<?php


$flashes = Utils::getFlashes();


foreach ($flashes as $f) { ?>
    <div class="alert alert-<?php echo $f['type'] ;?>">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php echo $f['message'] ;?></div>
<?php
}

