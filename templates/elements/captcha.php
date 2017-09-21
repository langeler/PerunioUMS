<?php if(!defined('IN_APP')){exit;} /* don't remove this line */ ?>
<style>
.captcha_control{
    height: 36px;
}
.captcha_control .captcha_img img{
    max-width: 100%;
    height: 36px;
    cursor: pointer;
}
.captcha_control .captcha_img{
    float: left;
    display: inline-block;
    height: 36px;
    width: 45%;
    text-align:center;
}
.captcha_control .captcha_input{
    float: right;
    display: inline-block;
    width: 55%;
}
</style>
<div class="form-group">
    <label >Captcha</label>

    <div class="captcha_control">
        <div class="captcha_img">
            <img onclick="(function(i){i.src=i.src+'0';})(this)" class="" src="<?php echo
            Utils::assetUrl('./app/captcha.php') ;?>?_=" alt="Captcha" title="click to get new one"/>
        </div>
        <div class="captcha_input">
            <input required="required" type="text" name="captcha_code" class="form-control " placeholder="Captcha code">
        </div>

        <div class="clearfix"></div>
    </div>

</div>
