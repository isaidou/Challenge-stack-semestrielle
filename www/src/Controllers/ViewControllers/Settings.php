<?php

declare(strict_types=1);

class Settings extends ProtectedController
{
	private $userModel;
	public function __grandchildConstruct()
	{
		$this->userModel = $this->model("UserModel");
	}

	/**
	 * @route
	 */
	public function index()
	{
		Server::redirect("settings/edit-profile");
	}

	/**
	 * Update User Profile
	 * Reset Session details
	 * 
	 * @route true
	 * 
	 */
	public function editProfile()
	{
		if (Server::checkPostReq(['pseudo', 'username', 'about'])) {
			$pseudo = $_POST['pseudo'];
			$username = $_POST['username'];
			$about = Str::stripNewLines($_POST['about']);

			$data = [];

			if (!Str::isValidUserName($username)) {
				$data['username_err'] = "Nom d'utilisateur invalide";
			} else if ($this->userModel->ifUsernameExists($username) && $username !== $_SESSION['username']) {
				$data['username_err'] = "Le nom d'utilisateur est déjà utilisé";
			}

			if (!Str::isValidPseudo($pseudo)) $data['pseudo_err'] = "Pseudo invalide";

			if (!Str::isValidDescription($about)) $data['about_err'] = "La description doit contenir au moins 10 caractères et au plus 300 caractères";

			$img = $_FILES['image'] ?? false;
			$newImg = $_SESSION['profile_img'];

			if ($img && Image::isValidImg($img, 8)) {
				$cloudinaryUrl = Image::uploadImageToCloudinary($img);

				if ($cloudinaryUrl) {
					$newImg = $cloudinaryUrl;
				} else {
					$data['profile_img_err'] = 'Erreur lors du téléchargement de l\'image';
				}
			}

			if (Str::emptyStrings($data)) {
				$details = [
					"pseudo" => $pseudo,
					"username" => $username,
					"about" => $about,
					"profile_img" => $newImg
				];

				if ($this->userModel->updateProfile($details)) {
					Session::sessionSet([
						"about" => $about,
						"username" => $username,
						"pseudo" => $pseudo,
						"profile_img" => $newImg
					]);
				} else {
					$data['total_err'] = "Une erreur s'est produite";
				}
			}
			$this->view("settings/edit-profile", $data);
		} else {
			$this->view("settings/edit-profile");
		}
	}
}