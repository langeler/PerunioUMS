<?php

class Controller extends Obj {

	private $tplPrefix = '../pages/';

	public function index() {

		$this->getTpl()->render($this->tplPrefix . 'index');
	}

	public function register() {

		$_data = array(
			'username' => '',
			'password' => '',
			'confirm_password' => '',
			'email' => '',
			'display_name' => ''
		);

		if(Utils::isPost()) {

			$itsOk = true;

			$data = (array)$_POST['data'] + $_data;

			//check if any field is empty or not a string
			foreach ($_data as $k => $v) {

				if(!is_scalar($data[$k]) || $data[$k] == ''){

					$itsOk = false;

					Utils::addFlash(
						lang('users_all_fields_are_required'),
						'danger'
					);

					break;
				}
			}

			//captcha challenge
			if($itsOk && !Utils::checkCaptcha()) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_captcha_fail'),
					'danger'
				);
			}

			if($itsOk){

				//check if valid username
				if(!Utils::isAlphanumeric($data['username'])){

					$itsOk = false;

					Utils::addFlash(
						lang('users_username_invalid_characters'),
						'danger'
					);
				}

				if($itsOk && (strlen($data['username']) < 3 || strlen($data['username']) > 25)) {

					$itsOk = false;

					Utils::addFlash(
						lang('users_short_username'),
						'danger'
					);
				}

				//check if valid email
				if($itsOk && !Utils::isEmail($data['email'])) {

					$itsOk = false;

					Utils::addFlash(
						lang('users_email_not_valid'),
						'danger'
					);
				}

				//check if username is available
				if($itsOk && !User::make()->usernameAvailable($data['username'])) {

					$itsOk = false;

					Utils::addFlash(
						lang('users_username_not_available'),
						'danger'
					);
				}

				//check if email is available
				if($itsOk && !User::make()->emailAvailable($data['email'])) {

					$itsOk = false;

					Utils::addFlash(
						lang('users_email_not_available'),
						'danger'
					);
				}

				//check if password length is correct
				if($itsOk && strlen( $data['password']) < 8 ) {

					$itsOk = false;

					Utils::addFlash(
						lang('users_short_password'),
						'danger'
					);
				}

				//check password confirmation
				if($itsOk && $data['password'] != $data['confirm_password']) {

					$itsOk = false;

					Utils::addFlash(
						lang('users_password_confirmation_not_correct'),
						'danger'
					);
				}

				if($itsOk) {

					if(User::make()->add($data)) {

						if((int)getOption('email_activation_required',0) == 0) {

							Utils::addFlash(
								lang('users_registered_success'),
								'success'
							);
						}

						else {

							Utils::addFlash(
								lang('users_registered_success_email_activation_required'),
								'success'
							);
						}

						Utils::redirect('/login');
					}

					else {

						Utils::addFlash(
							lang('unknown_error'),
							'danger'
						);
					}
				}
			}
		}

		else {
			$data = $_data;
		}

		$data = Utils::h($data);

		$this->getTpl()->render($this->tplPrefix . 'register',compact('data'));
	}

	public function login() {

		$data = array(
			'username' => '',
			'password' => '',
		);

		if(Utils::isPost()) {

			$data = (array)$_POST['data'] + $data;

			if(Utils::checkCaptcha()) {

				$user = User::make()->loginUser($data);

				if($user === false) {

					Utils::addFlash(
						lang('users_username_or_password_incorrect'),
						'danger'
					);
				}

				else {

					if((int)$user['active'] == 0) {

						Utils::addFlash(
							lang('users_active_account_required'),
							'warning'
						);
					}

					else {

						Utils::addFlash(
							lang('users_welcome_message'),
							'success'
						);

						Utils::authUser($user);
						Utils::redirect('/');
					}
				}
			}

			else {

				Utils::addFlash(
					lang('users_captcha_fail'),
					'danger'
				);
			}
		}

		$data = Utils::h($data);

		$this->getTpl()->render($this->tplPrefix . 'login',compact('data'));
	}

	public function logout() {

		Utils::logoutUser();

		Utils::redirect('/');
	}

	private function _editAccount($user = array()) {

		$data = (array)$_POST['data'];

		if(isset($data['username'])) {

			$itsOk = true;

			if(!Utils::isEmail($data['email'])) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_email_not_valid'),
					'danger'
				);
			}

			//check if email is available
			if($itsOk && $data['email'] != $user['email'] && !User::make()->emailAvailable($data['email'])) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_email_not_available'),
					'danger'
				);
			}

			//check if password length is correct
			if($itsOk && $data['new_password'] != '' && strlen( $data['new_password'] ) < 8) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_short_password'),
					'danger'
				);
			}

			if($itsOk) {

				$dataToUpdate = array(
					'display_name' => $data['display_name'],
					'email' => $data['email'],
				);

				//change password only if the new password is not empty
				if($data['new_password'] != '') {
					$dataToUpdate['password'] = Utils::hashPassword($data['new_password']);
				}

				$res = $this->getDb()->update('users', $dataToUpdate, array(
					'where' => array(
						'id' => $user['id']
					)
				));

				if($res) {

					Utils::addFlash(
						lang('users_account_updated_successfully'),
						'success'
					);

					$user = User::make()->get( Utils::currentUserInfo('username') );

					Utils::authUser($user);
				}

				else {

					Utils::addFlash(
						lang('unknown_error'),
						'danger'
					);
				}
			}
		}

		else {
			$data = $data + $user;
		}

		$data = Utils::h($data);

		$edit_account = true;

		$this->getTpl()->render($this->tplPrefix . 'account',compact('data','edit_account'));
	}

	public function account() {

		$user = User::make()->get( Utils::currentUserInfo('username') );

		if(empty($user)) {
			Utils::redirect('/');
		}

		if(Utils::isPost()) {

			$data = (array)$_POST['data'] + array('password'=>'');

			if(Utils::checkPassword($data['password'] , $user['password'])) {
				return $this->_editAccount($user);
			}

			else {

				Utils::addFlash(
					lang('users_password_incorrect'),
					'danger'
				);
			}
		}

		$this->getTpl()->render($this->tplPrefix . 'account');
	}

	public function forgotPassword() {

		if(Utils::isPost()) {

			$data = (array)$_POST['data'] + array(
					'email' => ''
				);

			$itsOk = true;

			//captcha challenge
			if(!Utils::checkCaptcha()) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_captcha_fail'),
					'danger'
				);
			}

			if($itsOk && User::make()->emailAvailable($data['email'])) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_email_not_found'),
					'danger'
				);
			}

			if($itsOk) {

				if(User::make()->sendPasswordRecovery($data['email'])) {

					Utils::addFlash(
						lang('email_recovery_message_sent'),
						'success'
					);

					Utils::redirect('/');
				}

				else {
					Utils::addFlash(
						lang('unknown_error'),
						'danger'
					);
				}
			}
		}

		$this->getTpl()->render($this->tplPrefix . 'forgot-password');
	}

	public function recoverPassword() {

		$username = (isset($_GET['username']) ? $_GET['username'] : null);
		$token = (isset($_GET['token']) ? $_GET['token'] : null);

		if(is_null($username) || is_null($token) || empty($username) || empty($token)) {
			Utils::redirect('/');
		}

		$user = $this->getDb()->getOne('users',
			array(
				'where' => array(
					'username' => $username,
					'token' => $token
				)
			)
		);

		if(empty($user)) {
			Utils::redirect('/');
		}

		if(Utils::isPost()) {

			$data = (array)$_POST['data'] + array(
				'password' => '',
				'confirm_password' => ''
			);

			$itsOk = true;

			//check if password length is correct
			if(strlen( $data['password'] ) < 8 ) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_short_password'),
					'danger'
				);
			}

			//check password confirmation
			if($itsOk && $data['password'] != $data['confirm_password']) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_password_confirmation_not_correct'),
					'danger'
				);
			}

			if($itsOk) {

				if(User::make()->changeUserPassword($user['id'], $data['password'])) {
					Utils::addFlash(
						lang('users_password_changed_success'),
						'success'
					);

					Utils::redirect('/login');
				}

				else {

					Utils::addFlash(
						lang('unknown_error'),
						'danger'
					);
				}
			}
		}

		$this->getTpl()->render($this->tplPrefix . 'recover-password');
	}

	public function activateAccount() {

		$username = (isset($_GET['username']) ? $_GET['username'] : null);
		$token = (isset($_GET['token']) ? $_GET['token'] : null);

		if(User::make()->activateAccount($username,$token)) {

			Utils::addFlash(
				lang('users_account_activated'),
				'success'
			);
		}

		Utils::redirect('/');
	}

	public function resendActivation() {

		if(Utils::isPost()){

			$data = (array)$_POST['data'] + array(
				'email' => ''
			);

			$itsOk = true;

			//captcha challenge
			if(!Utils::checkCaptcha()) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_captcha_fail'),
					'danger'
				);
			}

			if($itsOk && User::make()->emailAvailable($data['email'])) {

				$itsOk = false;

				Utils::addFlash(
					lang('users_email_not_found'),
					'danger'
				);
			}

			if($itsOk) {

				if(User::make()->resendActivation($data['email'])) {

					Utils::addFlash(
						lang('email_activation_sent'),
						'success'
					);

					Utils::redirect('/');
				}

				else {

					Utils::addFlash(
						lang('unknown_error'),
						'danger'
					);
				}
			}
		}

		$this->getTpl()->render($this->tplPrefix . 'resend-activation');
	}

	/* admin stuff */
	public function adminPermissions() {

		$action = (isset($_GET['action']) ? $_GET['action'] : 'list');
		$per_id = (isset($_GET['id']) ? $_GET['id'] : null);

		if($action == 'add'){

			$data = array(
				'name' => ''
			);

			if(Utils::isPost()) {

				$data = (array)$_POST['data'] + $data;

				Permissions::make()->add($data['name']);

				Utils::addFlash(
					lang('permissions_added_successfully'),
					'success'
				);

				Utils::redirect('/admin_permissions');
			}

			$this->getTpl()->setVar('data',$data);
		}

		elseif($action == 'delete') {

			if($per_id == 1) {

				Utils::addFlash(
					'Cannot delete first permission',
					'danger'
				);

				Utils::redirect('/admin_permissions');
			}

			$res = $this->getDb()->delete('permissions',
				array(
					'where' => array(
						'id' => $per_id
					)
				)
			);

			if($res) {

				//cleanup all permission relations
				$this->getDb()->query(
					sprintf('DELETE FROM %spermissions_users WHERE permission_id = ? ',
						$this->getDb()->getPrefix()
					),array(
					$per_id
				));

				$this->getDb()->query(
					sprintf('DELETE FROM %spages_permissions WHERE permission_id = ? ',
					$this->getDb()->getPrefix()
				),array(
					$per_id
				));

				Utils::addFlash(
					lang('permissions_deleted_successfully'),
					'success'
				);
			}

			else {
				Utils::addFlash(
					lang('unknown_error'),
					'danger'
				);
			}

			Utils::redirect('/admin_permissions');
		}

		elseif($action == 'edit') {

			$permission = Permissions::make()->get($per_id);

			if(empty($permission)) {
				Utils::redirect('/admin_permissions');
			}

			$data = $permission;

			if(Utils::isPost()) {

				$data = (array)$_POST['data'] + $data;

				Permissions::make()->update($data['name'],$per_id);

				Utils::addFlash(
					lang('permissions_updated_successfully'),
					'success'
				);

				Utils::redirect('/admin_permissions');
			}

			$this->getTpl()->setVar('data',$data);
		}

		else {

			$permissions = Permissions::make()->getAll();
			$permissions = Utils::h($permissions);

			$this->getTpl()->setVar('permissions',$permissions);
		}

		$this->getTpl()->render($this->tplPrefix . 'admin_permissions',compact('action'));
	}

	public function adminUsers() {

		$action = (isset($_GET['action']) ? $_GET['action'] : 'list');
		$user_id = (isset($_GET['id']) ? $_GET['id'] : null);

		if($action == 'edit') {

			$user = User::make()->get($user_id,'id');
			unset($user['password']);

			if(empty($user)){
				Utils::redirect('/admin_users');
			}

			$data = $user;

			if(Utils::isPost()) {

				$data = (array)$_POST['data'] + $data;
				$edit_type = isset($_POST['edit_type']) ? $_POST['edit_type'] : null;

				if($edit_type == 'account'){

					$itsOk = true;

					if(!Utils::isEmail($data['email'])){

						$itsOk = false;

						Utils::addFlash(
							lang('users_email_not_valid'),
							'danger'
						);
					}

					//check if email is available
					if($itsOk && $data['email'] != $user['email'] && !User::make()->emailAvailable($data['email'])) {

						$itsOk = false;

						Utils::addFlash(
							lang('users_email_not_available'),
							'danger'
						);
					}

					//check if password length is correct
					if($itsOk && $data['new_password'] != '' && strlen( $data['new_password'] ) < 8 ) {

						$itsOk = false;

						Utils::addFlash(
							lang('users_short_password'),
							'danger'
						);
					}

					if($itsOk) {

						//keep the admin active
						if($user['id'] == 1) {
							$data['active'] = 1;
						}

						$dataToUpdate = array(
							'display_name' => $data['display_name'],
							'email' => $data['email'],
							'active' => $data['active'],
						);

						//change password only if the new password is not empty
						if($data['new_password'] != '') {
							$dataToUpdate['password'] = Utils::hashPassword($data['new_password']);
						}

						$res = $this->getDb()->update('users',
							$dataToUpdate,
							array(
								'where' => array(
									'id' => $user['id']
								)
							)
						);

						if($res) {
							Utils::addFlash(
								lang('users_account_updated_successfully'),
								'success'
							);
						}

						else {
							Utils::addFlash(
								lang('unknown_error'),
								'danger'
							);
						}
					}
				}

				elseif($edit_type == 'permissions'){

					$permissions = isset($data['permissions']) ? $data['permissions'] : array();

					Permissions::make()->removeUserPermissions($user_id);

					foreach ($permissions as $per) {
						Permissions::make()->assignPermissionToUser($per,$user_id);
					}

					Utils::addFlash(
						lang('users_permissions_updated_successfully'),
						'success'
					);
				}
			}

			$userPermissions = Permissions::make()->getUserPermissions($user_id);

			$data = Utils::h($data);

			$this->getTpl()->setVar('data',$data);
			$this->getTpl()->setVar('userPermissions',$userPermissions);
		}

		elseif($action == 'delete') {

			$res = $this->getDb()->delete('users',
				array(
					'where' => array(
						'id' => $user_id
					)
				)
			);

			if($res) {

				//cleanup all user permission
				$this->getDb()->query(

					sprintf(
						'DELETE FROM %spermissions_users WHERE user_id = ? ',
						$this->getDb()->getPrefix()
					),

					array($user_id)
				);

				Utils::addFlash(
					lang('users_deleted_successfully'),
					'success'
				);
			}

			else {
				Utils::addFlash(
					lang('unknown_error'),
					'danger'
				);
			}

			Utils::redirect('/admin_users');
		}

		else {

			$users = User::make()->getAll();
			$users = Utils::h($users);

			$this->getTpl()->setVar('users',$users);
		}

		$this->getTpl()->render($this->tplPrefix . 'admin_users',compact('action'));
	}

	public function adminPages() {

		$action = (isset($_GET['action']) ? $_GET['action'] : 'list');
		$page_id = (isset($_GET['id']) ? $_GET['id'] : null);

		if($action == 'edit') {

			$page = Pages::make()->get($page_id);
			$data = $page;

			if(empty($page)) {
				Utils::redirect('/admin_pages');
			}

			if(Utils::isPost()) {

				$edit_type = isset($_POST['edit_type']) ? $_POST['edit_type'] : null;
				$data = (array)$_POST['data'] + $data;

				if($edit_type == 'page') {

					$itsOk = true;

					if((int)$data['private'] <= 0) {

						if(!Pages::make()->isAllowedToBePublic($page_id)) {

							$itsOk = false;

							Utils::addFlash(
								lang('pages_private_only'),
								'danger'
							);

							$data = $page;
						}
					}

					else {

						if(!Pages::make()->isAllowedToBePrivate($page_id)) {

							$itsOk = false;

							Utils::addFlash(
								lang('pages_public_only'),
								'danger'
							);

							$data = $page;
						}
					}

					if($itsOk) {

						$this->getDb()->update('pages',
							array(
								'private' => ((int)$data['private'] > 0 ? 1 : 0)
							),
							array(
								'where' => array(
									'id' => $page_id
								)
							)
						);

						Utils::addFlash(
							lang('pages_updated_successfully'),
							'success'
						);
					}
				}

				elseif($edit_type == 'permissions') {

					$permissions = isset($data['permissions']) ? $data['permissions'] : array();

					Permissions::make()->removePagePermissions($page_id);

					foreach ($permissions as $per) {
						Permissions::make()->assignPermissionToPage($per, $page_id);
					}

					Utils::addFlash(
						lang('pages_permissions_updated_successfully'),
						'success'
					);
				}
			}

			$pagePermissions = Permissions::make()->getPagePermissions($page_id);
			$data = Utils::h($data);

			$this->getTpl()->setVar('pagePermissions',$pagePermissions);
			$this->getTpl()->setVar('data',$data);
		}

		else {

			//check for new pages
			$pages = Pages::make()->checkForNewPages();

			$this->getTpl()->setVar('pages',$pages);
		}

		$this->getTpl()->render($this->tplPrefix . 'admin_pages',compact('action'));
	}

	public function adminConfiguration() {

		$defaultOptions = array(
			'website_name' => 'PerunioCMS',
			'email_activation_required' => '1',
			'email_from_email' => 'hello@perunio-cms.com',
			'email_from_name' => 'PerunioCMS',
			'website_language' => 'en',
			'default_permission' => 2,
		);

		if(Utils::isPost()) {

			foreach ($defaultOptions as $k => $v) {

				if(isset($_POST['data'][$k])) {

					//remove special characters in just is case !
					if($k == 'website_language'){
						$_POST['data'][$k] = preg_replace('/[^\p{L}\p{N}]/u','',$_POST['data'][$k]);
					}

					Options::make()->set($k,$_POST['data'][$k]);
				}
			}

			Utils::addFlash(
				lang('admin_website_settings_updated'),
				'success'
			);

			Utils::redirect('/admin_configuration');
		}

		$data = Options::make()->getAll('list') + $defaultOptions;
		$languages = Utils::getDirectoryFiles(LANGUAGE_DIR);
		$permissions = Permissions::make()->getAll('list');

		$data = Utils::h($data);
		$languages = Utils::h($languages);
		$permissions = Utils::h($permissions);

		$this->getTpl()->render($this->tplPrefix . 'admin_configuration',
			compact('data','languages','permissions')
		);
	}

	/**
	 * @param string $page_name custom page name in pages directory
	 */
	public function customPage($page_name) {

		// this method handle custom pages
		$this->getTpl()->render($this->tplPrefix . $page_name);
	}
}