<?php

declare(strict_types=1);

class UserModel extends Model
{
	public function getInfoById(int $id)
	{
		// Checked if user exists in controller
		$this->db->query("SELECT email, username, pseudo, about, created_at, profile_img from blog_users WHERE id = :id");
		$this->db->bind(":id", $id);
		$this->db->execute();
		$row = $this->db->fetchRow();

		return $row;
	}

	public function getInfoByUsername(string $username)
	{
		// Checked if user exists in controller
		$this->db->query("SELECT * FROM blog_users WHERE username = :username");
		$this->db->bind(":username", $username);
		$this->db->execute();
		return $this->db->fetchRow();
	}

	public function getInfoByUniqId(string $uniq)
	{
		// Checked if user exists in controller
		$this->db->query("SELECT * FROM blog_users WHERE uniq_id = :uniq");
		$this->db->bind(":uniq", $uniq);
		$this->db->execute();
		return $this->db->fetchRow();
	}

	public function ifUserExistsById(int $id): bool
	{
		$this->db->query("SELECT username from blog_users WHERE id = :id");
		$this->db->bind(":id", $id);
		$this->db->execute();

		return $this->db->rowCount() === 1;
	}

	public function ifEmailExists(string $email): bool
	{
		$this->db->query("SELECT id FROM blog_users WHERE email = :email");
		$this->db->bind(":email", $email);
		$this->db->execute();

		return $this->db->rowCount() === 1;
	}

	public function ifUsernameExists(string $username): bool
	{
		$this->db->query("SELECT id FROM blog_users WHERE username = :username");
		$this->db->bind(":username", $username);
		$this->db->execute();

		return $this->db->rowCount() === 1;
	}

	public function verifyUser(string $login, string $password)
	{
		// Checked if correct login
		$this->db->query("SELECT * from blog_users WHERE username = :username OR email = :email");
		$this->db->bind(":username", $login);
		$this->db->bind(":email", $login);
		$this->db->execute();
		$row = $this->db->fetchRow();
		return password_verify($password, $row->password) ? $row : false;
	}

	public function insertLoginToken(int $id, string $token): bool
	{
		$result = $this->db->dbInsert('blog_login_tokens', [
			'user_id' => $id,
			'token' => $token
		]);

		return $result;
	}

	public function addUser(array $data): bool
	{
		$result = $this->db->dbInsert('blog_users', $data);
		if ($result) {
			$userId = $this->db->lastInsertId();
			$this->db->query("SELECT id FROM blog_roles WHERE role_name = 'user'");
			$role_user_id = $this->db->fetchRow()->id;
			$roleResult = $this->db->dbInsert('blog_user_roles', [
				'user_id' => $userId,
				'role_id' => $role_user_id,  // default role is user
			]);
			return $roleResult;
		}
		return false;
	}




	public function updateProfile(array $details): bool
	{
		$this->db->query("UPDATE blog_users SET 
						  username = :username,
						  pseudo = :pseudo,
						  about = :about,
						  profile_img = :profile_img
						  WHERE id = :id
						 ");
		$this->db->bind(":username", $details['username']);
		$this->db->bind(":pseudo", $details['pseudo']);
		$this->db->bind(":about", $details['about']);
		$this->db->bind(":profile_img", $details['profile_img']);
		$this->db->bind(":id", $_SESSION['user_id']);

		return $this->db->execute();
	}

	public function completeProfile(int $userId, string $imageUrl, string $about): bool
	{
		$this->db->query("UPDATE blog_users SET 
						  about = :about,
						  profile_img = :profile_img
						  WHERE id = :id
						 ");

		$this->db->bind(":about", $about);
		$this->db->bind(":profile_img", $imageUrl);
		$this->db->bind(":id", $userId);

		return $this->db->execute();
	}

	// check profile is complete
	public function isProfileComplete(int $userId): bool
	{
		$this->db->query("SELECT about, profile_img FROM blog_users WHERE id = :id");
		$this->db->bind(":id", $userId);
		$this->db->execute();

		$row = $this->db->fetchRow();

		return !empty($row->about) && !empty($row->profile_img);
	}

	public function getUserRolesById(int $id)
	{
		// Checked if user exists in controller
		$this->db->query("SELECT blog_roles.role_name FROM blog_roles 
                      INNER JOIN blog_user_roles ON blog_roles.id = blog_user_roles.role_id 
                      WHERE blog_user_roles.user_id = :id");
		$this->db->bind(":id", $id);
		$this->db->execute();

		$rows = $this->db->fetchRows();

		// Extract role from each row and return as array
		return array_map(function ($row) {
			return $row->role_name;
		}, $rows);
	}


	public function isVerifiedEmail(string $email): bool
	{
		// Already checked if user exists
		$this->db->query("SELECT verified from blog_users WHERE email = :email");
		$this->db->bind(":email", $email);
		$this->db->execute();
		$count = $this->db->rowCount();
		$row = $this->db->fetchRow();

		return $count && $row->verified == true;
	}

	public function userHasRole(int $id, string $role): bool
	{
		$this->db->query("SELECT COUNT(*) as count FROM blog_roles 
                      INNER JOIN blog_user_roles ON blog_roles.id = blog_user_roles.role_id 
                      WHERE blog_user_roles.user_id = :id AND blog_roles.role = :role");
		$this->db->bind(":id", $id);
		$this->db->bind(":role", $role);
		$this->db->execute();

		$row = $this->db->fetchRow();
		return $row->count > 0;
	}


	public function isVerifiedUser(string $email_or_username): bool
	{
		// Already checked if user exists
		$this->db->query("SELECT verified from blog_users WHERE email = :email OR username = :username");
		$this->db->bind(":email", $email_or_username);
		$this->db->bind(":username", $email_or_username);
		$this->db->execute();
		$count = $this->db->rowCount();
		$row = $this->db->fetchRow();

		return $count && $row->verified == true;
	}

	public function isSecureMail(string $mail): bool
	{
		$emailUrl = EMAIL_API . urlencode($mail);
		$emailJson = @file_get_contents($emailUrl);
		if ($emailJson) {
			$array = json_decode($emailJson, true);
			if (array_key_exists("valid", $array)) return true;
		}
		return false;
	}

	public function checkVerificationRequestTime($email)
	{
		// Min time 10 minutes per mail
		$this->db->query("SELECT created_at from blog_email_verification_tokens WHERE email = :email ORDER BY id DESC");
		$this->db->bind(":email", $email);
		$this->db->execute();

		$count = $this->db->rowCount();
		$row = $this->db->fetchRow();

		return $count ? $row->created_at : "0"; // return time if row exists else return 0 
	}

	public function checkResetPasswordRequestTime($email)
	{
		// Min time 10 minutes per mail
		$this->db->query("SELECT created_at from blog_forgot_password_tokens WHERE email = :email ORDER BY id DESC");
		$this->db->bind(":email", $email);
		$this->db->execute();

		$count = $this->db->rowCount();
		$row = $this->db->fetchRow();

		return $count ? $row->created_at : "0"; // return time if row exists else return 0 
	}

	public function insertNewEmailToken(string $email, string $token): bool
	{
		$result = $this->db->dbInsert('blog_email_verification_tokens', [
			'email' => $email,
			'token' => $token
		]);

		return $result; // true or false
	}

	public function insertNewPasswordToken(string $email, string $token): bool
	{
		$result = $this->db->dbInsert('blog_forgot_password_tokens', [
			'email' => $email,
			'token' => $token
		]);

		return $result; // true or false
	}

	public function deleteOldEmailTokens(string $email, string $token)
	{
		$this->db->query("DELETE from blog_email_verification_tokens WHERE email = :email AND token != :token");
		$this->db->bind(":email", $email);
		$this->db->bind(":token", $token);
		$this->db->execute();
	}


	public function deleteOldPasswordTokens(string $email, string $token)
	{
		$this->db->query("DELETE from blog_forgot_password_tokens WHERE email = :email AND token != :token");
		$this->db->bind(":email", $email);
		$this->db->bind(":token", $token);
		$this->db->execute();
	}

	public function verifyByToken(string $token): bool
	{
		// token exists and is less than day old
		$this->db->query("UPDATE blog_users SET verified = true
						  WHERE email = 
							(SELECT email FROM blog_email_verification_tokens WHERE token = :token) 
						  AND verified = false
						");
		$this->db->bind(":token", $token);
		$this->db->execute();

		// delete all tokens after verification
		$this->db->query("DELETE FROM blog_email_verification_tokens 
			 			  WHERE email IN (
 			 			  SELECT Temp.email FROM 
							(SELECT email FROM blog_email_verification_tokens WHERE token=:token) Temp
			 			  )
						  ");
		$this->db->bind(":token", $token);
		$this->db->execute();

		return $this->db->rowCount() ? true : false;
	}

	public function getEmailByPasswordToken(string $token)
	{
		// Min time 10 minutes per mail
		$this->db->query("SELECT email FROM blog_forgot_password_tokens WHERE token = :token");
		$this->db->bind(":token", $token);
		$this->db->execute();

		$row = $this->db->fetchRow();
		$count = $this->db->rowCount();

		return $count === 1 ? $row->email : false;
	}

	public function ipDetails(string $ip)
	{
		return @file_get_contents("http://ipinfo.io/$ip/json");
	}

	public function isPasswordTokenValid(string $token): bool
	{
		$this->db->query("SELECT created_at, is_used FROM blog_forgot_password_tokens WHERE token = :token");
		$this->db->bind(":token", $token);
		$this->db->execute();

		$row = $this->db->fetchRow();
		$created = strtotime($row->created_at);

		// Valid for a day and is_used false
		return time() - $created < 86400 && !$row->is_used;
	}

	public function updatePasswordByToken(string $token, string $password): bool
	{
		// already checked if token exists
		$this->db->query("UPDATE blog_users SET password = :password WHERE email = (SELECT email from blog_forgot_password_tokens WHERE token = :token)");
		$this->db->bind(":password", $password);
		$this->db->bind(":token", $token);
		$this->db->execute();

		$this->unsetPasswordToken($token);

		return $this->db->rowCount() ? true : false;
	}

	public function unsetPasswordToken(string $token): void
	{
		$this->db->query("UPDATE blog_forgot_password_tokens SET is_used = true WHERE token = :token");
		$this->db->bind(":token", $token);
		$this->db->execute();
	}

	public function deletePasswordTokens(string $token): void
	{
		// dont delete current token to check timestamp
		$this->db->query("DELETE FROM blog_forgot_password_tokens WHERE email = 
						  (SELECT email FROM blog_forgot_password_tokens WHERE token = :token)
						  AND token != :token
						");
		$this->db->bind(":token", $token);
		$this->db->bind(":token", $token);

		$this->db->execute();
	}

	public function verifyLoginCookie(string $token)
	{
		$this->db->query("SELECT *
						  FROM blog_users
						  INNER JOIN blog_login_tokens ON blog_users.id = (SELECT user_id from blog_login_tokens WHERE token = :token);
						");
		$this->db->bind(":token", $token);
		$this->db->execute();

		$rowCount = $this->db->rowCount();

		return $rowCount ? $this->db->fetchRow() : false;
	}
}
