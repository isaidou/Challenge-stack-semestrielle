<?php

declare(strict_types=1);

class Write extends ProtectedController
{
	private $writeModel;
	private $articleModel;

	use WriteTraits;

	public function __grandchildConstruct()
	{
		$this->writeModel = $this->model('WriteModel');
		$this->articleModel = $this->model("ArticleModel");
	}

	/**
	 * Redirect to new article page
	 * 
	 * @route
	 */
	public function index()
	{
		Server::redirect('write/new');
	}

	/**
	 * Show view to create new article / draft
	 * 
	 * @route
	 */
	public function new()
	{
		$this->view('write/index');
	}


	/**
	 * Delete article by article id
	 * Verify username
	 * Article Delete must delete related aliases [tags, reactions, comments etc] (ArticleModel)
	 * 
	 * @route true
	 */
	public function deleteArticle(string $articleId)
	{

		if (empty($articleId)) Server::die_404();

		if ($this->writeModel->isUserArticle($articleId)) {
			if ($this->writeModel->deleteArticle($articleId)) {
				Session::alert("alert_article_delete", "Article supprimé avec succès");
			} else {
				Session::alert("alert_article_delete", "Erreur rencontrée lors de la suppression de l'article");
			}
		}
		Server::redirect('write/articles');
	}

	/**
	 * Return list of articles requested asynchronously
	 * Format title, tagline, content
	 * Format datetypes to correct format
	 * Get all articles of id < lastId
	 * Default last_id = 0
	 * 
	 * @route true
	 * @param int $lastId - Last article id recieved by user
	 */
	public function loadArticles(int $lastId)
	{
		$data = [];
		$data['articles'] = $this->writeModel->fetchArticles($this->articlesOnPage, $lastId);

		$data['last_id'] = 0;

		foreach ($data['articles'] as $article) {
			$data['status'] = 200;
			$data['last_id'] = $article->id;
			$article->title = Str::stripNewLines($article->title);
			$article->tagline = Str::stripNewLines($article->tagline);
			$article->content = Html::getChars($article->content);
			$article->created_at = Str::formatEpoch(strtotime($article->created_at), "d/m H:i");
			$article->last_edited = Str::formatEpoch(strtotime($article->last_updated), "d/m H:i");
		}
	}


	/**
	 * Parse Article Tags [comma(,) seperated]
	 * Replace all whitespace
	 * Unset empty values
	 * 
	 * @param string $tags 
	 * @return array $tags List of valid tag values
	 */
	private function parseTags(string $tags): array
	{
		$tags = Str::trimWhiteSpaces($tags);
		$tags = explode(",", $tags);

		foreach ($tags as $key => $tag) if (Str::isEmptyStr($tag)) unset($tags[$key]);

		return $tags;
	}


	/**
	 * Check draft contents for errors
	 * title, tagline, content, image count, iframes
	 * 
	 * @param array $errors - List of errors
	 * @param array $draft - Contents of draft
	 * 
	 * @return array $errors - list of errors in draft
	 */
	private function checkArticleErrors(array $errors, array $draft, bool $isDraft): array
	{
		$title = isset($draft['title']) ? $draft['title'] : '';
		$tagline = isset($draft['tagline']) ? $draft['tagline'] : '';
		$content = isset($draft['content']) ? $draft['content'] : '';
		$tags = isset($draft['tags']) ? $draft['tags'] : [];
		$draftName = isset($draft['draft_name']) ? $draft['draft_name'] : '';

		// Check draft name if it exists and if in draft mode
		if ($isDraft) {
			if ($draftName !== '') {
				$draftName = Str::trimWhiteSpaces($draftName);
				if (mb_strlen($draftName) > $this->maxDraftName || mb_strlen($draftName) < $this->minDraftName) {
					$errors['draft_name_err'] = "Le nom du brouillon doit comprendre de {$this->minDraftName} à {$this->maxDraftName} caractères";
				}
			} else {
				$errors['draft_name_err'] = "Le nom du brouillon est requis.";
			}
		}



		if (Str::trimWhiteSpaces($title) === "")
			$errors['title_err'] = "Veuillez ajouter un titre";
		else if (mb_strlen($title) > $this->articleLimits["title"])
			$errors['title_err'] = "Le titre doit comporter moins de {$this->articleLimits["title"]} caractères";


		if (Str::trimWhiteSpaces($tagline) === "")
			$errors['tagline_err'] = "Veuillez ajouter une tagline";
		else if (mb_strlen($tagline) > $this->articleLimits["tagline"])
			$errors['tagline_err'] = "La tagline doit comporter moins de {$this->articleLimits["tagline"]} caractères";


		if (Str::trimWhiteSpaces($content) === "")
			$errors['content_err'] = "Veuillez ajouter du contenu";
		else if (mb_strlen($content) > $this->articleLimits["content"])
			$errors['content_err'] = "La longueur du contenu dépasse {$this->articleLimits["content"]} caractères";

		if (!$isDraft) {
			if (empty($tags))
				$errors['tags_err'] = "Veuillez ajouter au moins un tag";
			else if (count($tags) > $this->articleLimits["tags"])
				$errors['tags_err'] = "Un maximum de 5 tags est autorisé";

			foreach ($tags as $tag)
				if (!preg_match($this->tagRegex, $tag)) $errors['tags_err'] = "Un ou plusieurs tags sont invalides";
		}

		return $errors;
	}


	/**
	 * Fetch drafts by model and format contents
	 * Format datetime to d/m H:i format
	 * 
	 * @route true
	 * @param int $lastId - Last draft id recieved by user
	 */
	public function loadDrafts(int $lastId)
	{
		$drafts = $this->writeModel->fetchDrafts($this->draftsOnPage, $lastId);

		$data = [];
		$data['last_id'] = 0;

		foreach ($drafts as $draft) {
			$data['last_id'] = $draft->id;
			$draft->title = Str::stripNewLines($draft->title);
			$draft->tagline = Str::stripNewLines($draft->tagline);
			$draft->content = Html::getChars($draft->content);
			$draft->created_at = Str::formatEpoch(strtotime($draft->created_at), "d/m H:i");
			$draft->last_edited = Str::formatEpoch(strtotime($draft->last_updated), "d/m H:i");
		}

		$data['drafts'] = $drafts;
		$this->view("write/drafts", $data);
	}


	/**
	 * Rename Draft to new name
	 * Validate if name is correct
	 * 
	 * @route true
	 * @postParams [new_name, draft_id]
	 */


	/**
	 * Delete draft of user
	 * Check for confirmation username
	 * 
	 * @route true
	 */
	public function deleteDraft(string $draftId)
	{

		if (empty($draftId)) Server::die_404();
		if ($this->writeModel->isUserDraft($draftId)) {
			if ($this->writeModel->deleteDraft($draftId)) {
				Session::alert("alert_draft_delete", "Brouillon supprimé avec succès");
			} else {
				Session::alert("alert_draft_delete", "Eerreur rencontrée lors de la suppression du brouillon");
			}
		}
		Server::redirect('write/drafts');
	}

	/**
	 * Edit Draft Page
	 * Check if draft exists or not
	 * Load title, draft name, tagline, content to draft
	 * 
	 * @param string $draftId
	 * @route
	 */
	public function editDraft(string $draftId)
	{
		$draft = $this->writeModel->fetchDraft($draftId);
		$data = [];

		if ($draft) {
			$data['draft_id'] = $draftId;
			$data['title'] = $draft->title;
			$data['draft_name'] = $draft->draft_name;
			$data['tagline'] = $draft->tagline;
			$data['content'] = $draft->content;
		} else {
			Server::die_404();
		}

		$this->view('write/index', $data);
	}

	/**
	 * @route
	 */
	public function editArticle(string $articleId)
	{
		$article = $this->writeModel->getUserArticle($articleId);
		$data = [];

		if ($article) {
			$data['article_id'] = $articleId;
			$data['title'] = $article->title;
			$data['tagline'] = $article->tagline;
			$data['content'] = $article->content;
			$data['last_updated'] = $article->last_updated;
			$data['created_at'] = $article->created_at;
		} else {
			Server::die_404();
		}
		$tagsString = implode(", ", $this->articleModel->fetchTags($articleId));
		$data['tags'] = $tagsString;

		$this->view('write/index', $data);
	}

	/**
	 * @route
	 */
	public function drafts()
	{
		$drafts = $this->writeModel->fetchDrafts($this->draftsOnPage);

		$data = [];
		$data['last_draft_id'] = 0;


		foreach ($drafts as $draft) {
			$data['last_draft_id'] = $draft->id;
			$draft->tagline = Str::stripNewLines($draft->tagline);
			$draft->content =  Str::stripNewLines($draft->content);
		}

		$data['count'] = count($drafts);
		$data['drafts'] = $drafts;

		$this->view("write/drafts", $data);
	}

	/**
	 * @route
	 */
	public function articles()
	{
		$articles = $this->writeModel->fetchArticles($this->articlesOnPage);

		$data = [];
		$data['last_article_id'] = 0;

		foreach ($articles as $article) {
			$data['last_article_id'] = $article->id;
			$article->tagline = Str::stripNewLines($article->tagline);
			$article->content = Html::getChars($article->content);
		}

		$data['count'] = count($articles);
		$data['articles'] = $articles;
		$this->view("write/articles", $data);
	}

	public function handleSubmission(string $entityId = null)
	{
		$isUpdate = Utils::isNull($entityId);

		$data = [
			'title' => '',
			'tagline' => '',
			'content' => '',
			'tags' => '',
			'draft_name' => '',
		];

		if (!Server::checkPostReq(['title', 'tagline', 'content', 'tags', 'draft_name'])) {
			$this->view('write/new', $data);
			return;
		}

		$isDraft = isset($_POST['save_draft']);
		$content = ht($_POST['content']);
		$dataInput = ['content' => Str::stripNewLines($content)];

		$draftName = isset($_POST['draft_name']) ? ht(trim($_POST['draft_name'])) : "";
		$title = isset($_POST['title']) ? ht(Str::stripNewLines($_POST['title'])) : "";
		$tagline = isset($_POST['tagline']) ? ht(Str::stripNewLines($_POST['tagline'])) : "";
		$tags = $this->parseTags(ht($_POST['tags']));
		$tagsString = implode(", ", $tags);

		$dataInput = array_merge($dataInput, ['draft_name' => $draftName, 'title' => $title, 'tagline' => $tagline, 'tags' => $tags]);
		$data = array_merge($data, $dataInput); // Merge user input to data
		$data['tags'] = $tagsString;

		// Check for errors using the new method
		$errors = $this->checkArticleErrors([], $dataInput, $isDraft);
		$data = array_merge($data, $errors); // Merge errors to data


		if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
			$img = $_FILES['image'];
			if (Image::isValidImg($img, 8)) {
				$prev_img = Image::uploadImageToCloudinary($img);
				if ($prev_img) $data['image_url'] = $prev_img;
				else $data['image_err'] = 'Erreur lors du téléchargement de l\'image';
				if ($prev_img) $data['image_url'] = $prev_img;
				else $data['image_err'] = 'Erreur lors du téléchargement de l\'image';
			} else {
				$data['image_err'] = 'Format d\'image non valide ou la taille dépasse la limite';
			}
		}

		if (Str::emptyStrings($errors) && !isset($data['image_err'])) {

			if ($isUpdate) {
				if ($isDraft) {
					$entityId = $this->writeModel->updateDraft($entityId, $dataInput['draft_name'], $dataInput['title'], $dataInput['tagline'], $dataInput['content']);
					if ($entityId) {
						Session::alert("alert_edit", "Brouillon modifié avec succès");
						Server::redirect('write/drafts');
					}
				} else {
					$entityId = $this->writeModel->updateArticle($entityId, $dataInput['title'], $dataInput['tagline'], $dataInput['content'], $data['image_url']);
					if ($entityId) {
						$this->writeModel->deleteTags($entityId);
						$this->writeModel->insertTags($entityId, $tags);
						Session::alert("alert_edit", "Article modifié avec succès");
						Server::redirect('write/articles');
					}
				}
			} else {
				if ($isDraft) {
					$draftId = $this->writeModel->createDraft($dataInput['draft_name'], $dataInput['title'], $dataInput['tagline'], $dataInput['content']);
					if ($draftId) {
						Session::alert("alert_edit", "Brouillon créé avec succès");
						Server::redirect('write/drafts');
					}
				} else {
					$articleId = $this->writeModel->createArticle($dataInput['title'], $dataInput['tagline'], $dataInput['content'], $data['image_url']);
					$this->writeModel->insertTags($articleId, $dataInput['tags']);
					Session::alert("alert_edit", "Article créé avec succès");
					Server::redirect('write/articles');
				}
			}

			if ($entityId) {
				if ($isDraft) {
					$data['draft_id'] = $entityId;
				} else {
					$data['article_id'] = $entityId;
				}
			} else {
				Session::alert("alert_edit", "Eerreur rencontrée lors de la modification ");
			}
		}
		$this->view('write/index', $data);
	}
}