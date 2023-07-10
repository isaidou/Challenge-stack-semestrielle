<?php

declare(strict_types=1);

/* 
* Controller Class
* Loads Models and Views
*/

/**
 * Controller Class
 * Extends BaseController to provide model and view functionality
 * Logs in user without session
 * 
 */
abstract class Controller extends BaseController
{
	abstract public function index();
	abstract public function __childConstruct();

	public $baseModel;

	public function __construct()
	{
		// Login By Cookie
		$this->baseModel = $this->model('UserModel');

		if (!Session::isLoggedIn() && isset($_COOKIE['login_token'])) {
			$token = $_COOKIE['login_token'];
			$data = ($this->model("UserModel"))->verifyLoginCookie($token);

			if ($data) {
				Session::sessionSet([
					"username" => $data->username,
					"uniq_id" => $data->uniq_id,
					"pseudo" => $data->pseudo,
					"user_id" => $data->id,
					"about" => $data->about,
					"email" => $data->email,
					"role" => $data->role,
					'profile_img' => $data->profile_img
				]);
			}
		}

		$this->__childConstruct();
	}
}
