<?php

declare(strict_types=1);

class ArticleModel extends Model
{
	public function fetchArticle(string $articleId)
	{
		$this->db->query("SELECT * from blog_articles WHERE article_id = :article_id");
		$this->db->bind(":article_id", $articleId);
		$this->db->execute();

		$row = $this->db->fetchRow();

		if ($row) return $row;
		return false;
	}

	// fetch all articles
	public function fetchAllArticles(int $limit = 10, int $offset = 0)
	{
		$this->db->query("SELECT * FROM blog_articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
		$this->db->bind(":limit", $limit);
		$this->db->bind(":offset", $offset);
		$this->db->execute();

		$rows = $this->db->fetchRows();

		if ($rows) return $rows;
		return false;
	}


	// get nb of articles
	public function getNbArticles()
	{
		$this->db->query("SELECT COUNT(*) as nb_articles from blog_articles");
		$this->db->execute();

		$row = $this->db->fetchRow();

		if ($row) return $row;
		return false;
	}

	// get all articles
	public function fetchArticles(int $limit = 10, int $offset = 0)
	{
		$this->db->query("SELECT * from blog_articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
		$this->db->bind(":limit", $limit);
		$this->db->bind(":offset", $offset);
		$this->db->execute();

		$rows = $this->db->fetchRows();

		if ($rows) return $rows;
		return false;
	}


	public function getViews(string $articleId): int
	{
		$this->db->query("SELECT id from blog_article_views WHERE article_id = :article_id");
		$this->db->bind(":article_id", $articleId);
		$this->db->execute();

		return $this->db->rowCount();
	}

	public function addView(string $articleId)
	{
		// Try Catch on unique constraint b/w article_id and user_id of table
		// Use ip if not logged in
		try {
			$id = $_SESSION['user_id'] ?? Server::getIpAddress();
			$this->db->dbInsert("blog_article_views", [
				"user_id" => $id,
				"article_id" => $articleId
			]);
		} catch (Exception $e) {
		}
	}

	public function fetchTags(string $articleId)
	{
		$this->db->query("SELECT * from blog_article_tags WHERE article_id = :article_id");
		$this->db->bind(":article_id", $articleId);
		$this->db->execute();

		$rows = $this->db->fetchRows();
		foreach ($rows as $key => $value) {
			$rows[$key] = $value->tag;
		}
		return $rows;
	}
}
