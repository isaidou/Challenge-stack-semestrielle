<?php

declare(strict_types=1);


class Session
{
	// Flash Message Helper
	public static function flash(string $name = '', string $msg = '', string $class = 'alert alert-success mb-3'): void
	{
		if (!empty($name)) {
			$sessionClass = $name . '_class';
			if (!empty($msg) && empty($_SESSION[$name])) {
				if (!empty($_SESSION[$sessionClass])) unset($_SESSION[$sessionClass]);

				$_SESSION[$name] = $msg;
				$_SESSION[$sessionClass] = $class;
			} else if (empty($msg) && !empty($_SESSION[$name])) {
				$class = !empty($_SESSION[$sessionClass]) ? $_SESSION[$sessionClass] : '';
				echo "<div class='$class' id='msg-flash'>" . $_SESSION[$name] . "</div>";
				unset($_SESSION[$name], $_SESSION[$sessionClass]);
			}
		}
	}

	public static function alert(string $name = '', string $msg = '', string $class = 'alert alert-success mb-3'): void
	{
		if (!empty($name)) {
			$sessionClass = $name . '_class';
			if (!empty($msg) && empty($_SESSION[$name])) {
				if (!empty($_SESSION[$sessionClass])) unset($_SESSION[$sessionClass]);

				$_SESSION[$name] = $msg;
				$_SESSION[$sessionClass] = $class;
			} else if (empty($msg) && !empty($_SESSION[$name])) {
				$class = !empty($_SESSION[$sessionClass]) ? $_SESSION[$sessionClass] : '';
				echo '<div class="' . $class . '" role="alert">
						' . $_SESSION[$name] . '
						<button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
					  </div>';
				unset($_SESSION[$name], $_SESSION[$sessionClass]);
			}
		}
	}

	public static function sessionSet(array $arr): void
	{
		foreach ($arr as $key => $value) $_SESSION[$key] = $value;
	}

	public static function sessionUnset(array $arr): void
	{
		foreach ($arr as $key => $value) unset($_SESSION[$key]);
	}

	public static function isLoggedIn(): bool
	{
		return isset($_SESSION['user_id']);
	}
	public static function isCompleteProfile(): bool
	{
		return isset($_SESSION['is_complete_profile']) && $_SESSION['is_complete_profile'] === true;
	}

	public static function isAdmin(): bool
	{
		return isset($_SESSION['user_id']) && Session::hasRole('admin');
	}

	public static function hasRole(string $role): bool
	{
		$roles = $_SESSION['roles'];
		if ($roles === null) {
			return false;
		}
		return isset($_SESSION['user_id']) && in_array($role, $_SESSION['roles'], true);
	}
	public static function setRoles(array $roles): void
	{
		// Assurez-vous que l'utilisateur est connecté avant de modifier ses rôles
		if (isset($_SESSION['user_id'])) {
			$_SESSION['roles'] = $roles;
		}
	}




	public static function csrfToken(): string
	{
		return $_SESSION['csrf_token'];
	}

	// get the value of session
	public static function getUserId(string $name): ?int
	{
		return $_SESSION[$name] ?? null;
	}

	public static function redirectUser(): void
	{
		if (self::isLoggedIn()) {
			Server::redirect("home");
			exit;
		}
	}

	public static function userProfilePath(): string
	{
		if (isset($_SESSION['profile_img']) && !empty($_SESSION['profile_img'])) {
			return  $_SESSION['profile_img'];
		}
		return DEFAULT_PROFILE_PATH;
	}
}