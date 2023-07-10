<?php

declare(strict_types=1);

class WriteModel extends Model
{
	public function uploadImage($img)
	{

		return Image::uploadImageToCloudinary($img);
	}

	public function createDraft(string $draftName, string $title, string $tagline, string $content)
	{
		$uniq = Utils::randToken(16);
		return $this->db->dbInsert('blog_drafts', [
			"user_id" => $_SESSION['user_id'],
			"draft_id" => $uniq,
			"draft_name" => $draftName,
			"title" => $title,
			"tagline" => $tagline,
			"content" => $content
		]) ? $uniq : false;
	}

	// get userid of draft
	public function getDraftUserId(string $draftId)
	{
		$this->db->query("SELECT user_id from blog_drafts WHERE draft_id = :draft_id");
		$this->db->bind(":draft_id", $draftId);
		$this->db->execute();

		$row = $this->db->fetchRow();

		if ($row) return $row;
		return false;
	}

	public function createArticle(string $title, string $tagline, string $content, ?string $preview)
	{
		$uniq = Utils::randToken(16);
		return $this->db->dbInsert('blog_articles', [
			"user_id" => $_SESSION['user_id'],
			"article_id" => $uniq,
			"preview_img" => $preview,
			"title" => $title,
			"tagline" => $tagline,
			"content" => $content
		]) ? $uniq : false;
	}

	// update tags



	public function insertTags(string $articleId, array $tags)
	{
		foreach ($tags as $tag) {
			$this->db->dbInsert("blog_article_tags", [
				"tag" => $tag,
				"article_id" => $articleId,
				"user_id" => $_SESSION['user_id']
			]);
		}
	}

	public function isUserArticle(string $articleId): bool
	{
		$this->db->query("SELECT id from blog_articles WHERE article_id = :article_id AND user_id = :user_id");
		$this->db->bind(":article_id", $articleId);
		$this->db->bind(":user_id", $_SESSION['user_id']);
		$this->db->execute();

		$count = $this->db->rowCount();

		return $count === 1;
	}

	public function deleteTags(string $articleId)
	{
		$this->db->query("DELETE from blog_article_tags WHERE article_id = :article_id AND user_id = :user_id");
		$this->db->bind(":article_id", $articleId);
		$this->db->bind(":user_id", $_SESSION['user_id']);

		$this->db->execute();
	}

	public function isUserDraft(string $draftId): bool
	{
		$this->db->query("SELECT id from blog_drafts WHERE draft_id = :draft_id AND user_id = :user_id");
		$this->db->bind(":draft_id", $draftId);
		$this->db->bind(":user_id", $_SESSION['user_id']);
		$this->db->execute();

		$count = $this->db->rowCount();

		return $count === 1;
	}

	public function fetchAllDraft(string $draftId)
	{
		// Checked if correct login
		$this->db->query("SELECT * from blog_drafts WHERE draft_id = :draft_id AND user_id = :user_id");
		$this->db->bind(":draft_id", $draftId);
		$this->db->bind(":user_id", $_SESSION['user_id']);

		$row = $this->db->fetchRows();
		if (!empty($row)) return $row;
		return false;
	}

	public function getUserArticle(string $articleId)
	{
		// Checked if correct login
		$this->db->query("SELECT * from blog_articles WHERE article_id = :article_id AND user_id = :user_id");
		$this->db->bind(":article_id", $articleId);
		$this->db->bind(":user_id", $_SESSION['user_id']);

		$row = $this->db->fetchRow();
		if ($row) return $row;
		return false;
	}

	public function updateDraft(string $draftId, string $title, string $tagline, string $content)
	{
		$date = date(DB_TIMESTAMP_FMT);
		$this->db->query("UPDATE blog_drafts SET 
							title = :title,
							tagline = :tagline,
							content = :content,
							last_updated = :last_updated
						  WHERE draft_id = :draft_id
						");

		$this->db->bindMultiple([
			"title" => $title,
			"tagline" => $tagline,
			"content" => $content,
			"draft_id" => $draftId,
			"last_updated" => $date
		]);

		return $this->db->execute();
	}

	public function updateArticle(string $articleId, string $title, string $tagline, string $content, string $preview)
	{
		$this->db->query("UPDATE blog_articles SET
							title = :title,
							tagline = :tagline,
							content = :content, 
							last_updated = :last_updated,
							preview_img = :preview_img
						  WHERE article_id = :article_id
						");

		$this->db->bindMultiple([
			"title" => $title,
			"tagline" => $tagline,
			"content" => $content,
			"article_id" => $articleId,
			"last_updated" => date(DB_TIMESTAMP_FMT),
			"preview_img" => $preview
		]);

		return $this->db->execute();
	}


	public function deleteDraft(string $draftId)
	{
		$this->db->query("DELETE from blog_drafts WHERE draft_id = :draft_id");
		$this->db->bind(":draft_id", $draftId);

		return $this->db->execute();
	}

	public function deleteArticle(string $articleId)
	{
		$this->db->query("SELECT blog_delete_article_aliases(:article_id)");
		$this->db->bind(":article_id", $articleId);

		return $this->db->execute();
	}

	// fetch draft 
	public function fetchDraft(string $draftId)
	{
		$this->db->query("SELECT * from blog_drafts WHERE draft_id = :draft_id");
		$this->db->bind(":draft_id", $draftId);
		$this->db->execute();

		$row = $this->db->fetchRow();

		if ($row) return $row;
		return false;
	}

	public function fetchDrafts(int $limit, int $lastId = NULL)
	{
		$idConstraint = !is_null($lastId) ? " AND id < :id " : " ";
		$this->db->query("SELECT id, draft_id, draft_name, created_at,
							last_updated, LEFT(title, 100) as title,
							LEFT(tagline, 200) as tagline, 
							LEFT(content, 2000) as content
						  FROM blog_drafts WHERE user_id = :user_id $idConstraint
						  ORDER BY id DESC LIMIT $limit
						");



		$this->db->bind(":user_id", $_SESSION['user_id']);
		if (!is_null($lastId)) $this->db->bind(":id", $lastId);

		$this->db->execute();

		$rows = $this->db->fetchRows();
		return $rows;
	}

	public function fetchArticles(int $limit, int $lastId = NULL)
	{
		$append = !is_null($lastId) ? " AND id < :id " : " ";
		$this->db->query("SELECT id, article_id, created_at, preview_img,
						  last_updated, LEFT(title, 100) as title,
						  LEFT(tagline, 200) as tagline, 
						  LEFT(content, 2000) as content
						  FROM blog_articles WHERE user_id = :user_id $append
						  ORDER BY id DESC LIMIT $limit
						");

		$this->db->bind(":user_id", $_SESSION['user_id']);
		if (!is_null($lastId)) $this->db->bind(":id", $lastId);

		$this->db->execute();

		$rows = $this->db->fetchRows();
		return $rows;
	}
}
