<?php

declare(strict_types=1);

class Image
{
	public static function isValidImg($img, $mb, $allowedTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG]): bool
	{
		if ($img['tmp_name']) {
			$detectedType = @exif_imagetype($img['tmp_name']);
			$bytes = $mb * 1048576;

			return in_array($detectedType, $allowedTypes) && $img['size'] <= $bytes;
		}
		return false;
	}

	public static function validateImgUrl(string $url): bool
	{
		// Allow Error Image
		if (trim($url) === IMG_404_PATH) return true;

		if (filter_var($url, FILTER_VALIDATE_URL) && strpos($url, IMG_VALIDATE_URL) === 0) {
			$headers = get_headers($url, true);
			if (strpos($headers['Content-Type'], 'image/') === 0 && strpos($headers[0], '200') !== false)
				return true;
		}

		return false;
	}

	/**
	 * Uploads an image to the server and then to Cloudinary, and returns the secure URL.
	 *
	 * @param array $image The $_FILES array for the uploaded image.
	 * @return string|null The secure URL of the uploaded image, or null if the upload failed.
	 */
	public static function uploadImageToCloudinary(array $image): ?string
	{
		if (!isset($image['tmp_name'], $image['type'], $image['name'])) {
			return null;
		}

		if (!is_dir(PROFILE_IMG_DIR)) {
			mkdir(PROFILE_IMG_DIR, 0777, true);
		}

		if (!is_readable(PROFILE_IMG_DIR)) {
			chmod(PROFILE_IMG_DIR, 0777);
		}

		$newImagePath = PROFILE_IMG_DIR . '/' . basename($image['name']);
		if (!move_uploaded_file($image['tmp_name'], $newImagePath)) {
			return null;
		}

		$timestamp = time();
		$signature = sha1('folder=' . IMG_API_FOLDER . '&timestamp=' . $timestamp . '&upload_preset=' . IMG_API_PRESET . IMG_API_SECRET);

		$data = array(
			"file" => curl_file_create($newImagePath, $image['type'], $image['name']),
			"upload_preset" => IMG_API_PRESET,
			"api_key" => IMG_API_KEY,
			"timestamp" => $timestamp,
			"signature" => $signature,
			"folder" => IMG_API_FOLDER
		);

		$ch = curl_init(IMG_UPLOAD_URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			return null;
		}

		curl_close($ch);
		$response_data = json_decode($response);

		if (!isset($response_data->secure_url)) {
			return null;
		}

		@unlink($newImagePath);
		return $response_data->secure_url;
	}
	// get profile image
	public static function getProfileImg(string $imgUrl): string
	{
		if (self::validateImgUrl($imgUrl)) return $imgUrl;
		return DEFAULT_PROFILE_PATH;
	}
}