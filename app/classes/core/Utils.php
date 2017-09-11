<?php

class Utils {

	public static function getDirectoryFiles($dir = null, $pattern = '*.php',$file_name_only = true) {

		if(is_null($dir)) {
			return array();
		}

		$output = array();
		$files = glob(rtrim($dir,'/') . '/' . $pattern);

		foreach ($files as $file) {

			if($file_name_only) {
				$output[] = pathinfo($file,PATHINFO_FILENAME);
			}

			else {
				$output[] = $file;
			}
		}

		return $output;
	}

	public static function h($value) {

		if(is_array($value)) {

			foreach ($value as $k => $v) {
				$value[$k] = Utils::h($v);
			}

			return $value;
		}

		return htmlspecialchars($value, ENT_QUOTES , "UTF-8");
	}

	public static function getTime($format = 'Y-m-d H:i:s') {
		return date($format,time());
	}

	public static function trimfy($value = '') {
		return is_array($value) ? array_map('trimfy', $value) : trim($value);
	}

	/**
	 * @param bool $fullBase
	 * @return string
	 */

	public static function baseUrl($fullBase = false) {

		$scriptName = $_SERVER['SCRIPT_NAME'];

		$parts = explode('/',$scriptName);

		array_pop($parts);

		$basePath = implode('/',$parts);

		if($fullBase) {

			$host = $_SERVER['HTTP_HOST'];
			$host = rtrim($host,'/');

			$basePath = sprintf('%s://%s%s',
				(isset($_SERVER['HTTPS'] )?'https':'http'),
				$host,
				$basePath
			);
		}

		return $basePath;
	}

	public static function header($type = 'html', $code = 200) {

		$origType = $type;
		$type = strtolower($type);

		$proto = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';

		if($type == 401) {
			header($proto . ' 401 Unauthorized',true,401);
		}

		elseif($type == 404){
			header($proto . ' 404 Not Found',true,404);
		}

		elseif($type == 500){
			header($proto . ' 500 Internal Server Error',true,500);
		}

		elseif($type == 'json'){
			header('Content-Type: application/json; charset=UTF-8',true,200);
		}

		elseif($type == 'js'){
			header('Content-Type: application/javascript; charset=UTF-8',true,200);
		}

		elseif($type == 'css'){
			header('Content-Type: text/css; charset=UTF-8',true,200);
		}

		elseif($type == 'html'){
			header('Content-Type: text/html; charset=UTF-8',true,200);
		}

		else{
			header($origType,true,$code);
		}
	}

	public static function hashPassword($plain = '') {

		if(function_exists('password_hash')) {
			return password_hash($plain,PASSWORD_BCRYPT);
		}

		$salt = '@#Ahyd68581s5';

		$hash = sha1($plain.$salt);

		for($i = 0;$i < 10;++$i) {
			$hash = sha1($hash.$salt.$i);
		}

		return $hash;
	}

	public static function checkPassword($plain, $hash) {

		if(function_exists('password_hash')) {
			return password_verify($plain, $hash);
		}

		return (self::hashPassword($plain) === $hash);
	}

	public static function redirect($path = '/') {

		if(preg_match('/^https?:\/\/|\/\//', $path) == 0) {
			$path = Router::Url($path);
		}

		header('Location: '. $path);
		exit;
	}

	public static function assetUrl($url = null) {
		return Router::url($url, true, true);
	}

	public static function isPost() {
		return (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
	}

	public static function addFlash($message = '', $type = 'info') {

		if(!isset($_SESSION['_FLASHES'])){
			$_SESSION['_FLASHES'] = array();
		}

		$_SESSION['_FLASHES'][] = array('message' => $message,'type' => $type);
	}

	public static function getFlashes() {

		$flashes = (isset($_SESSION['_FLASHES']) ? $_SESSION['_FLASHES'] : array());

		unset($_SESSION['_FLASHES']);
		return $flashes;
	}

	/* captcha */
	public static function checkCaptcha() {

		if(!isset($_SESSION['CAPTCHA_SEC_CODE'])) {
			return false;
		}

		if(!isset($_POST['captcha_code'])) {
			return false;
		}

		$captchaCode = $_SESSION['CAPTCHA_SEC_CODE'];

		unset($_SESSION['CAPTCHA_SEC_CODE']);
		return (strtolower($captchaCode) === strtolower($_POST['captcha_code']));
	}

	/* validations */
	public static function isAlphanumeric($txt = null) {

		if(empty($txt)) {
			return false;
		}

		if(preg_match('/[^a-z_0-9]/i', $txt)) {
			return false;
		}

		return true;
	}

	public static function isEmail($email) {

		if(empty($email)) {
			return false;
		}

		if (strpos($email, '@', 1 ) === false) {
			return false;
		}

		list($local, $domain) = explode('@', $email, 2);

		if (!preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local)) {
			return false;
		}

		if (preg_match('/\.{2,}/', $domain)) {
			return false;
		}

		if (trim($domain, " \t\n\r\0\x0B.") !== $domain) {
			return false;
		}

		$subs = explode('.', $domain);

		if (2 > count($subs)) {
			return false;
		}

		foreach ($subs as $sub) {

			if (trim($sub, " \t\n\r\0\x0B-") !== $sub) {
				return false;
			}

			if (!preg_match('/^[a-z0-9-]+$/i', $sub)) {
				return false;
			}
		}

		return true;
	}

	/* mailer */
	public static function sendMail($to, $subject = '', $vars = array(), $template = 'default') {

		if(Template::make()->templateExists('emails/'.$template)) {

			ob_start();

			Template::make()->_includeFile(
				Template::make()->getTemplatePath('emails/'.$template),
				$vars
			);

			$body = ob_get_clean();
		}

		else {

			$body = '=== ' . $subject . ' ===' . "\n\n";

			foreach ($vars as $k => $v) {
				$body .= ucfirst($k) . ' : ' . $v . "\n";
			}
		}

		$mail = new PHPMailer();

		$mail->CharSet = 'UTF-8';

		$mail->setFrom(
			getOption(
				'email_from_email',
				'no-reply@example.com'
			),
			getOption(
				'email_from_name',
				'no-reply'
			)
		);

		$mail->addAddress($to);

		$mail->isHTML(false);

		$mail->Subject = $subject;
		$mail->Body = $body;

		return $mail->send();
	}

	/* sessions */
	public static function authUser($user = null) {
		$_SESSION['Auth'] = $user;
	}

	public static function currentUserInfo($key = null) {

		if(static::isUserLoggedIn()) {

			if(!is_null($key)) {

				if(isset($_SESSION['Auth'][$key])) {
					return $_SESSION['Auth'][$key];
				}

				return null;
			}

			return $_SESSION['Auth'];
		}

		return null;
	}

	public static function isUserLoggedIn() {
		return isset($_SESSION['Auth']);
	}

	public static function logoutUser() {

		unset($_SESSION['Auth']);
		session_destroy();
	}
}