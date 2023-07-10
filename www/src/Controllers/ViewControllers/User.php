<?php

declare(strict_types=1);

class User extends GuestController
{
	private $userModel;
	private $mailModel;

	public function __grandchildConstruct()
	{
		$this->userModel = $this->model('UserModel');
		$this->mailModel = $this->model('MailerModel');
	}

	/**
	 * @route
	 */
	public function index(): void
	{
		Server::redirect("user/login");
	}

	/**
	 * Login view for guest user
	 * verify email/username and password
	 * Check if account has been verified by email 
	 * Set login cookie for browser
	 * Set session and redirect to home
	 * 
	 * @route
	 */
	public function login(): void
	{
		$data = [
			'email_or_username_err' => '',
			'password_err' => '',
			'email_or_username' => '',
			'password' => ''
		];

		if (Server::checkPostReq(["email_or_username", "password"])) {
			if (!ALLOW_LOGIN) {
				$data['password_err'] = "La connexion a été désactivée";
				$this->view('user/login', $data);
				return;
			}

			$data['email_or_username'] = $_POST['email_or_username'];
			$data['password'] = $_POST['password'];

			if (empty($data['email_or_username'])) {
				$data['email_or_username_err'] = "Veuillez remplir les informations de connexion";
			} else if (
				!$this->userModel->ifUsernameExists($data['email_or_username']) &&
				!$this->userModel->ifEmailExists($data['email_or_username'])
			) {
				$data['email_or_username_err'] = "Utilisateur non trouvé";
			}

			if (empty($data['password'])) $data['password_err'] = "Veuillez entrer le mot de passe";

			if (Str::emptyStrings([$data['email_or_username_err'], $data['password_err']])) {

				if ($this->userModel->isVerifiedUser($data['email_or_username'])) {
					$verify = $this->userModel->verifyUser($data['email_or_username'], $data['password']);

					if (!$verify) {
						$data['password_err'] = "Mot de passe incorrect";
					} else {
						// Connexion réussie
						// Mettre en place la session
						$roles = $this->userModel->getUserRolesById($verify->id);
						Session::sessionSet([
							"username" => $verify->username,
							"uniq_id" => $verify->uniq_id,
							"pseudo" => $verify->pseudo,
							"user_id" => $verify->id,
							"about" => $verify->about,
							"email" => $verify->email,
							"roles" => $roles,
							'profile_img' => $verify->profile_img
						]);

						$loginToken = Utils::randToken();
						Cookie::createCookie('login_token', $loginToken, 30 * 24 * 60 * 60);

						$this->userModel->insertLoginToken($verify->id, $loginToken);

						$isProfileComplete = $this->userModel->isProfileComplete($verify->id);


						Server::redirect("home/");
					}
				} else {
					// Utilisateur non vérifié
					$append = filter_var($data['email_or_username'], FILTER_VALIDATE_EMAIL)
						? '?email=' . $data['email_or_username']  : '';

					$data['email_or_username_err'] =
						"L'utilisateur n'est pas vérifié. 
					<a class='link-primary text-decoration-none'
					href='" . URLROOT . "/user/resend-verification$append'>Renvoyer l'email</a>";
				}
			}
		}

		$this->view('user/login', $data);
	}

	/**
	 * First Step sign up validation
	 * 
	 * Check pseudo, username, email, gender, password, confirmPassword
	 * 
	 * @param array $data
	 * @param string $pseudo
	 * @param string $username
	 * @param string $email
	 * @param string $gender
	 * @param string $password
	 * @param string $confirmPassword
	 * 
	 * @return array $data [list of errors]
	 */
	private function signUpValidate(
		array $data,
		string $pseudo,
		string $username,
		string $email,
		string $gender,
		string $password,
		string $confirmPassword
	): array {
		if (empty($pseudo)) {
			$data['pseudo_err'] = 'Entrez un pseudo';
		} else if (!Str::isValidPseudo($pseudo)) {
			$data['pseudo'] = 'Le pseudo doit être inférieur à 30 caractères';
		}

		if (empty($username)) {
			$data['username_err'] = 'Entrez un nom d\'utilisateur';
		} else if (!Str::isValidUserName($username)) {
			$data['username_err'] = 'Nom d\'utilisateur invalide';
		} else if ($this->userModel->ifUsernameExists($username)) {
			$data['username_err'] = 'Ce nom d\'utilisateur existe déjà';
		}

		if (empty($email)) {
			$data['email_err'] = 'Entrez un email';
		} else if (!Str::isValidEmail($email)) {
			$data['email_err'] = 'Entrez une adresse e-mail valide';
		} else if ($this->userModel->ifEmailExists($email)) {
			$data['email_err'] = 'L\'email est déjà utilisé';
		} else if (!$this->userModel->isSecureMail($email)) {
			$data['email_err'] = 'Impossible de vérifier l\'email';
		}

		if (empty($gender) || !in_array($gender, ['male', 'female', 'other'])) {
			$data["gender_err"] = "Sélectionnez le sexe";
		}

		if (!Str::isValidPassword($password)) {
			$data['password_err'] = "Entrez un mot de passe valide";
		}

		if ($password !== $confirmPassword) {
			$data['confirm_password_err'] = "Les mots de passe ne correspondent pas";
		}

		return $data;
	}

	/**
	 * Verify all fields and add user to database
	 * 
	 * @route
	 */
	public function signUp(): void
	{
		$data = [
			'pseudo_err' => '',
			'username_err' => '',
			'email_err' => '',
			'gender_err' => '',
			'password_err' => '',
			'confirm_password_err' => '',
		];

		if (Server::checkPostReq(['pseudo', 'username', 'email',  'password', 'confirm_password'], false, false)) {
			$data['pseudo'] = $_POST['pseudo'];
			$data['username'] = $_POST['username'];
			$data['email'] = $_POST['email'];
			$data['gender'] = isset($_POST['gender']) ? $_POST['gender'] : '';
			$data['password'] = $_POST['password'];
			$data['confirm_password'] = $_POST['confirm_password'];

			$data = $this->signUpValidate(
				$data,
				$data['pseudo'],
				$data['username'],
				$data['email'],
				$data['gender'],
				$data['password'],
				$data['confirm_password']
			);

			if (!ALLOW_SIGNUP) {
				$data['total_err'] = 'Sign up has been disabled';
				return;
			}

			if (Str::emptyStrings([$data['pseudo_err'], $data['email_err'], $data['username_err'], $data['password_err'], $data['gender_err'], $data['confirm_password_err']])) {
				// Default errors set before success
				$data['total_err'] = 'Something Went Wrong';

				$user = [
					'pseudo' => $data['pseudo'],
					'username' => $data['username'],
					'email' => $data['email'],
					'password' => password_hash($data['password'], PASSWORD_DEFAULT),
					'uniq_id' => Utils::randToken(16)
				];

				if ($this->userModel->addUser($user)) {
					// Send verification email
					$newToken = Utils::randToken();
					$linkTag = "<a href='" . URLROOT . "/user/verify-email/$newToken'>Vérifier</a>";
					$body = "Votre lien de vérification est : $linkTag. Veuillez l'ignorer si ce n'était pas vous.";
					$mailStatus = $this->mailModel->sendMail($data['email'], 'Lien de vérification de compte', $body);

					if ($this->userModel->insertNewEmailToken($data['email'], $newToken) && $mailStatus) {
						Session::flash('register_success', 'Succès ! Vérifiez votre boîte de réception et votre dossier de spam pour vérifier votre compte');
						$this->userModel->deleteOldEmailTokens($data['email'], $newToken);
					}

					Server::redirect("user/login");
				}
				$this->view('user/sign-up', $data);
			}
		}

		$this->view('user/sign-up', $data);
	}



	/**
	 * Resend verification for unverified emails
	 * Allow after 10 minutes of last request
	 * Add new token to database
	 * Resend email for verification
	 * 
	 * @route
	 */
	public function resendVerification(): void
	{
		$data['error'] = '';
		$data['default_mail'] = $_GET['email'] ?? '';

		if (Server::checkPostReq(['email'])) {
			$email = $_POST['email'];
			$lastIssued = strtotime($this->userModel->checkVerificationRequestTime($email));
			if (!$this->userModel->ifEmailExists($email)) {
				$data['error'] = "Email non trouvé";
			} else if ($this->userModel->isVerifiedEmail($email)) {
				$data['error'] = "Email déjà vérifié";
			} else if (time() - $lastIssued < 600) {
				$data['error'] = "Veuillez attendre 10 minutes pour renvoyer le mail";
			}

			if (Str::emptyStrings([$data['error']])) {
				$newToken = Utils::randToken();
				$linkTag = "<a href='" . URLROOT . "/user/verify-email/$newToken" . "'>Vérifier</a>";
				$body = "Votre lien de vérification est: $linkTag. Veuillez ignorer ce mail si ce n'était pas vous";
				$mailStatus = $this->mailModel->sendMail($email, 'Nouveau lien de vérification', $body);

				if ($this->userModel->insertNewEmailToken($email, $newToken) && $mailStatus) {
					$this->userModel->deleteOldEmailTokens($email, $newToken);
					Session::flash('email_token_sent', 'Vérifiez votre email et le dossier spam pour le nouveau lien de vérification');
					Server::redirect('user/login');
				} else {
					$data['error'] = "Une erreur est survenue";
				}
			}
		}

		$this->view('user/resend-verification', $data);
	}

	/**
	 * Verification page
	 * Check if verication was successful
	 * 
	 * @param string $token - Verification Token
	 * @route
	 */ public function verifyEmail(string $token): void
	{
		$data['message'] = '';

		if ($this->userModel->verifyByToken($token)) {
			Session::flash('email_verified', 'Email vérifié avec succès. Connectez-vous');
			Server::redirect('user/login');
		} else {
			$data['message'] = "Demande non valide. Réessayez";
		}

		$this->view('user/verify-email', $data);
	}

	/**
	 * Send request for forgotten password
	 * Allow after 10 minutes of resetting password
	 * Send mail with reset id 
	 * Add reset token to database
	 * 
	 * @route
	 */
	public function forgotPassword(): void
	{
		$data = [
			'error' => '',
			'email' => ""
		];

		if (Server::checkPostReq(['email'])) {
			$email = $_POST['email'];
			$data['email'] = $email;
			$lastIssued = strtotime($this->userModel->checkResetPasswordRequestTime($email));

			if (!$this->userModel->ifEmailExists($email)) {
				$data['error'] = "Email non trouvé";
			} else if (time() - $lastIssued < 600) {
				$data['error'] = "Veuillez attendre 10 minutes pour réinitialiser le mot de passe";
			}

			if (Str::emptyStrings([$data['error']])) {
				$newToken = Utils::randToken();
				$linkTag = "<a href='" . URLROOT . "/user/reset-password/$newToken" . "'>Réinitialiser</a>";
				$body = "Le lien de réinitialisation du mot de passe est: $linkTag. Veuillez ignorer ce mail si ce n'était pas vous";
				$mailStatus = $this->mailModel->sendMail($email, 'Réinitialisation du mot de passe', $body);

				if ($this->userModel->insertNewPasswordToken($email, $newToken) && $mailStatus) {
					$this->userModel->deleteOldPasswordTokens($email, $newToken);
					Session::flash('forgot_password', 'Vérifiez votre email et le dossier spam pour réinitialiser le mot de passe');
					Server::redirect('user/login');
				} else {
					$data['error'] = "Une erreur est survenue";
				}
			}
		}

		$this->view('user/forgot-password', $data);
	}


	/**
	 * Reset Password of user
	 * Check if token is still valid
	 * Reset password in database
	 * Send email specifying reset details for verification
	 * 
	 * @param string $token 
	 * @route
	 */
	public function resetPassword(string $token): void
	{
		$data = array(
			"password" => '',
			'confirm_password' => '',
			'password_err' => '',
			'confirm_password_err' => '',
			'token' => $token,
			'error' => ''
		);

		$email = $this->userModel->getEmailByPasswordToken($token);
		if (!$email) {
			$data['error'] = "Token non trouvé. Réessayez";
		} else if (!$this->userModel->isPasswordTokenValid($token)) {
			$data['error'] = "Token expiré. Réessayez";
		}

		if (Server::checkPostReq(['password', 'confirm_password'])) {
			$password = $_POST['password'];
			$confirmPassword = $_POST['confirm_password'];
			$data['password'] = $password;
			$data['confirm_password'] = $confirmPassword;

			if (!Str::isValidPassword($password)) {
				$data['password_err'] = "Entrez un mot de passe valide";
			}

			if ($password !== $confirmPassword) {
				$data['confirm_password_err'] = "Les mots de passe ne correspondent pas";
			}

			if (Str::emptyStrings([$data['password_err'], $data['confirm_password_err']])) {
				$password = password_hash($password, PASSWORD_DEFAULT);
				$data['confirm_password_err'] = "Une erreur est survenue";

				if ($this->userModel->updatePasswordByToken($token, $password)) {
					$ipDetails = "Impossible de déterminer les informations de l'utilisateur";
					$userDetails = $_SERVER['HTTP_USER_AGENT'] ?? "";

					$ip = Server::getIpAddress() ?? "";
					if (filter_var($ip, FILTER_VALIDATE_IP))
						$ipDetails = $this->userModel->ipDetails($ip);

					$body = "
                    Le mot de passe a récemment été réinitialisé pour votre compte " . SITENAME . " en utilisant l'adresse email $email.
                    <br>
                    <h2>Détails de l'activité</h2>
                    <b>$userDetails</b>
                    <pre>$ipDetails</pre>
                    Si vous ne reconnaissez pas cette activité, veuillez
                    <a href='" . URLROOT . "/user/forgot-password'>réinitialiser votre mot de passe</a>
                ";


					$mailStatus = $this->mailModel->sendMail($email, 'Réinitialisation du mot de passe pour ' . SITENAME, $body);

					if ($mailStatus) {
						Session::flash('password_reset', 'Mot de passe réinitialisé avec succès.');
						Server::redirect('user/login');
					}
				}
			}
		}

		$this->view('user/reset-password', $data);
	}
}