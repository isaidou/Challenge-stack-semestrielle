<?php

declare(strict_types=1);

class Article extends Controller
{
	/* Maximum number of comments per page */
	use ArticleTraits;

	private $articleModel;
	private $userModel;
	private $commentModel;
	private $adminModel;
	private $mailModel;

	/**
	 * Load models in controller
	 */
	public function __childConstruct()
	{
		$this->articleModel = $this->model("ArticleModel");
		$this->commentModel = $this->model("CommentModel");
		$this->userModel = $this->model("UserModel");
		$this->adminModel = $this->model("AdminModel");
		$this->mailModel = $this->model("MailerModel");
	}

	/**
	 * Load article view
	 * Article ID passed as get parameter
	 * Check if article exists
	 * Add View if user is logged in
	 * Get view count
	 * 
	 * @route
	 */
	public function index()
	{
		if (!Server::getParamsExist($_GET, ['id'])) Server::die_404();

		$articleId = $_GET['id'];
		$article = $this->articleModel->fetchArticle($articleId);
		$comments = $this->commentModel->getcomments($articleId);

		if (!$article) Server::die_404();

		$this->articleModel->addView($articleId);

		$data = [];
		$data['views'] = $this->articleModel->getViews($articleId);
		$data['article'] = $article;
		$data['user'] = $this->userModel->getInfoById($article->user_id);
		$data['tags'] = $this->articleModel->fetchTags($articleId);
		$data['comments'] = $comments;


		$this->view("article/index", $data);
	}

	/**
	 * Add comment for article
	 * Content must not be empty
	 * And less than 1500 chars
	 * 
	 * Check For User Spam
	 * @route true
	 * @postParams [article_id, content]
	 */
	public function addComment()
	{
		Server::checkPostReq(['article_id', 'content'], true, true);
		$articleId = $_POST['article_id'];
		$content = ht(Str::strip2lines($_POST['content']));

		$data = [];
		// check if content is valid
		if (mb_strlen($content) > $this->maxCommentContent) {
			$data['comment_err'] = "Le commentaire ne doit pas dépassé  $this->maxCommentContent caractères";
		} elseif (Str::isEmptyStr($content)) {
			$data['comment_err'] = "Le commentaire est obligatoire";
		}

		// check details
		if (!$this->articleModel->fetchArticle($articleId)) { // article doesnt exist
			$data['article_err'] = "L'article n'existe pas";
		}

		if (Str::emptyStrings($data)) {
			$id = $this->commentModel->addComment($content, $articleId);
			if (!$id) {
				$data['comment_err'] = "Erreur survenue lors de l'inserstion du commentaire";
			}
		}

		// $article = $this->articleModel->fetchArticle($articleId);
		// $data['article'] = $article;
		// $data['user'] = $this->userModel->getInfoById($article->user_id);
		// $data['tags'] = $this->articleModel->fetchTags($articleId);

		Server::redirect("article/?id=$articleId");
		//$this->view('article/index', $data);


	}
	/*
	 * Report comment
	 * Check if comment exists
	 * Send mail to all admins
	 */

	public function reportComment()
	{
		if (!Server::getParamsExist($_GET, ['idComment', 'idArticle'])) Server::die_404();

		$commentId = (int)$_GET['idComment'];
		$articleId = $_GET['idArticle'];
		$commentExist = $this->commentModel->commentExists($commentId);


		if ($commentExist) {
			$admins = $this->adminModel->getUserAdmin();
			$linkTag = "<a href='" . URLROOT . "/article/?id=$articleId" . "#comment-$commentId'>Accéder au commentaire signaler</a>";
			$body = "Ce commentaire vient d'etre signalé: $linkTag. <br>";
			if (!Utils::isNull($admins)) {
				foreach ($admins as $admin) {
					$mailStatus = $this->mailModel->sendMail($admin->email, 'Commentaire signalé', $body);
				}
			} else {
				$mailStatus = $this->mailModel->sendMail(SUPER_ADMIN_EMAIL, 'Commentaire signalé', $body);
			}

			if ($mailStatus) {
				Session::flash('report_success', 'Signalement effectué avec succès');
			} else {
				Session::flash('report_error', 'Erreur lors du signalement', 'alert alert-danger');
			}
		} else {
			Session::flash('report_error', 'Le commentaire n\'existe plus', 'alert alert-danger');
		}
		Server::redirect("article/?id=$articleId");
	}



	/**
	 * Delete a comment
	 * Check if comment belongs to user
	 * @param int $commentId
	 * @route true
	 */
	public function deleteComment()
	{
		if (!Server::getParamsExist($_GET, ['idComment', 'idArticle'])) Server::die_404();

		$commentId = (int) $_GET['idComment'];
		$articleId = $_GET['idArticle'];

		if (!$this->commentModel->isUserComment($commentId) && Session::isAdmin() === false) {
			Session::flash('delete_error', "Impossible de supprimer le commentaire car c'est pas le votre veuillez le signaler s'il est innaproprié !", 'alert alert-danger');
		} else {
			$deleteComment = $this->commentModel->deleteComment($commentId);
			if ($deleteComment) {
				Session::flash('delete_success', 'Commentaire supprimé avec succès');
			}
		}
		Server::redirect("article/?id=$articleId");
	}


	/**
	 * Show comments for article limited to max per page
	 * Accessed by article id and page
	 * Get article_id and page numbe
	 * 
	 * @param string $articleId
	 * @param int $page
	 * @route
	 */
	public function comments(string $articleId, int $page)
	{
		$article = $this->articleModel->fetchArticle($articleId);

		if (!$article || $page === 1) Server::die_404();

		$data['article'] = $article;
		$userId = $_SESSION['user_id'] ?? 0;
		$data['comments'] = $this->commentModel->getParentComments($articleId, $userId, $this->maxCommentsOnPage, $page);

		if (count($data['comments']) === 0) Server::die_404();

		$data['total_comments'] = $this->commentModel->totalParentCommentsCount($articleId);
		$data['comment_page_count'] = ceil($data['total_comments'] / $this->maxCommentsOnPage);

		$this->view("article/comments", $data);
	}
	// add view
	public function addView(string $articleId)
	{
		$this->articleModel->addView($articleId);
	}
}