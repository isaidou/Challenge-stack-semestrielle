<?php

declare(strict_types=1);

class Home extends ProtectedController
{
	private $articleModel;
	private $userModel;
	private $commentModel;

	public function __grandchildConstruct()
	{
		$this->commentModel = $this->model("CommentModel");
		$this->userModel = $this->model("UserModel");
		$this->articleModel = $this->model("ArticleModel");
	}

	use HomeTraits;

	/**
	 * Home Page 
	 * Fetch Articles
	 * @route
	 */
	public function index(): void
	{
		$perPage = $this->maxArticles;

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		if ($page < 1) $page = 1;

		$totalArticlesObj = $this->articleModel->getNbArticles();
		$totalArticles = 0;
		if ($totalArticlesObj && $totalArticlesObj->nb_articles > 0) {
			$totalArticles = $totalArticlesObj->nb_articles;
		}

		$totalPages = (int)ceil($totalArticles / $perPage);

		if ($totalPages > 0 && $page > $totalPages) $page = $totalPages;

		$offset = ($page - 1) * $perPage;

		$data = [];

		$articles = $this->articleModel->fetchArticles($perPage, $offset);

		foreach ($articles as $key => $article) {
			$articles[$key]->views_count = $this->articleModel->getViews($article->article_id);
			$articles[$key]->comments_count = $this->commentModel->totalCommentsCount($article->article_id);
			$articles[$key]->user_info = $this->userModel->getInfoById($article->user_id);
		}


		$data['articles'] = $articles ?? [];
		$data['current_page'] = $page;
		$data['total_pages'] = $totalPages;

		$this->view('home/index', $data);
	}
}