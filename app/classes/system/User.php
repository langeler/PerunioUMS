<?php

class User extends Obj {

	public function usernameAvailable($username = null) {

		if(empty($username)) {
			false;
		}

		$res = $this->db->getOne('users',
			array(
				'fields' => array(
					'id'
				),
				'where' => array(
					'username' => $username
				)
			)
		);

		return empty($res);
	}

	public function loginUser($data = array()) {

		if(empty($data)) {
			return false;
		}

		$user = $this->getDb()->getOne('users',
			array(
				'where' => array(
					'username' => $data['username']
				)
			)
		);

		if(empty($user)) {
			return false;
		}

		if(!Utils::checkPassword($data['password'],$user['password'])) {
			return false;
		}

		if((int)$user['active'] == 1){

			$this->getDb()->update('users',
				array(
					'token' => '0',
					'last_seen' => Utils::getTime()
				),
				array(
					'where' => array(
						'id' => $user['id']
					)
				)
			);
		}

		unset($user['password']);

		return $user;
	}

	public function emailAvailable($email = null) {

		if(empty($email)) {
			false;
		}

		$res = $this->db->getOne('users',
			array(
				'fields' => array(
					'id'
				),
				'where' => array(
					'email' => $email
				)
			)
		);

		return empty($res);
	}

	public function add($data = array()) {

		if(empty($data)) {
			return false;
		}

		$email_activation_required = (int)getOption(
			'email_activation_required',
			0
		);

		$user_active = 1;
		$token = 0;

		if($email_activation_required == 1) {

			$user_active = 0;

			$token = md5(time().uniqid());
		}

		$id = $this->getDb()->insert('users',
			array(
				'username' => $data['username'],
				'password' => Utils::hashPassword($data['password']),
				'email' => mb_strtolower($data['email'],'UTF-8'),
				'display_name' => $data['display_name'],
				'active' => $user_active,
				'token' => $token,
				'registered_at' => Utils::getTime(),
			)
		);

		if(!$id) {
			return false;
		}

		Permissions::make()->assignPermissionToUser(
			getOption(
				'default_permission',
				2
			),
			$id
		);

		$emailVars = array(
			'username' => $data['username'],
			'display_name' => $data['display_name'],
			'activation_url' => sprintf(
				'%s?username=%s&token=%s',
				Router::url(
					'/activate-account',
					true
				),
				$data['username'],
				$token
			)
		);

		if($email_activation_required == 1) {

			Utils::sendMail(
					mb_strtolower($data['email'],'UTF-8'),
					lang(
						'email_registration_subject',
						'Welcome'
					),
					$emailVars,
					'new_registration_activate_account'
				);
		}

		else {

			Utils::sendMail(

				mb_strtolower($data['email'],'UTF-8'),
				lang(
					'email_registration_subject',
					'Welcome'
				),
				$emailVars,
				'new_registration'
			);
		}

		return $id;
	}

	public function changeUserPassword($user_id = null,$new_password = null) {

		return $this->getDb()->update('users',
			array(
				'password' => Utils::hashPassword($new_password),
				'token' => '0',
				'active' => 1,
			),
			array(
				'where' => array(
					'id' => $user_id
				)
			)
		);
	}

	public function activateAccount($username = null, $token = null) {

		if(is_null($username) || is_null($token) || empty($username) || empty($token)) {
			return false;
		}

		$user = $this->getDb()->getOne('users',
			array(
				'where' => array(
					'username' => $username,
					'token' => $token,
					'active' => 0
				)
			)
		);

		if(empty($user)) {
			return false;
		}

		return $this->getDb()->update('users',
			array(
				'active' => 1,
				'token' => '0'
			),
			array(
				'where' => array(
					'id' => $user['id']
				)
			)
		);
	}

	public function sendPasswordRecovery($email = null) {

		if(empty($email)) {
			return false;
		}

		$token = md5(time().uniqid());

		$user = $this->getDb()->getOne('users',
			array(
				'where' => array(
					'email' => $email
				)
			)
		);

		if(empty($user)){
			return false;
		}

		$this->getDb()->update('users',
			array(
				'token' => $token
			),
			array(
				'where' => array(
					'id' => $user['id']
				)
			)
		);

		$emailVars = array(
			'username' => $user['username'],
			'recover_url' => sprintf('%s?username=%s&token=%s',
			Router::url(
				'/recover-password',
				true
			),
			$user['username'],
			$token
			)
		);

		return Utils::sendMail($email,
			lang('email_recovery_subject'),
			$emailVars,
			'password_recovery'
		);
	}

	public function resendActivation($email = null) {

		if(empty($email)) {
			return false;
		}

		$user = $this->getDb()->getOne('users',
			array(
				'where' => array(
					'email' => $email
				)
			)
		);

		if(empty($user)) {
			return false;
		}

		//user is already active !
		if($user['active'] == 1){
			Utils::redirect('/');
		}

		$emailVars = array(
			'username' => $user['username'],
			'display_name' => $user['display_name'],
			'activation_url' => sprintf(
				'%s?username=%s&token=%s',
				Router::url(
					'/activate-account',
					true
				),
				$user['username'],
				$user['token']
			)
		);

		return Utils::sendMail(
			$user['email'] ,
			lang(
				'email_registration_subject',
				'Welcome'
			),
			$emailVars,
			'new_registration_activate_account'
		);
	}

	public function get($value, $field = 'username') {

		return $this->getDb()->getOne('users',
			array(
				'where' => array(
					$field => $value
				)
			)
		);
	}

	public function getAll() {

		return $this->getDb()->get('users',
			array(
				'order' => array(
					'id',
					'desc'
				)
			)
		);
	}
}