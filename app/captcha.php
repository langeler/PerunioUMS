<?php

require 'init.php';

require INC_DIR . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'EasyCaptcha' . DIRECTORY_SEPARATOR . 'php-captcha.inc.php';

$fonts = array(
	'',
	'Bd',
	'BI',
	'It',
	'MoBd',
	'MoBI',
	'MoIt',
	'Mono',
	'Se',
	'SeBd'
);

for($i = 0; $i < count($fonts); $i++ ){
	$fonts[$i] = CAPTCHA_DIR . 'ttf-bitstream-vera-1.10/Vera'.$fonts[$i].'.ttf';
}

$alphabet = 'a_b_c_d_e_f_g_h_i_j_k_l_m_n_o_p_q_r_s_t_u_v_w_x_y_z';
$alphabet .= '_'.strtoupper($alphabet).'_1_2_3_4_5_6_7_8_9_0';

$alphabet = explode('_', $alphabet);
shuffle($alphabet);

$captchaText = '';

/* captcha length */
$cLen = 5;

for($i = 0; $i < $cLen; $i++ ) {
	$captchaText .= $alphabet[$i];
}

$_SESSION['CAPTCHA_SEC_CODE'] = $captchaText;

$oVisualCaptcha = new PhpCaptcha($fonts, strlen($captchaText) * 30+10, 36);

$oVisualCaptcha->UseColour(true);

$oVisualCaptcha->SetNumLines(rand(40,50));

$oVisualCaptcha->Create($captchaText);
