<?php

declare(strict_types=1);

trait WriteTraits
{
	protected $draftLimits = [
		'title' => 1000,
		'tagline' => 2500,
		'content' => 150000,
		'draft_name' => 40,
		"img" => 20,
		"iframe" => 8,
	];

	protected $articleLimits = [
		'title' => 120,
		'tagline' => 600,
		'content' => 100000,
		"img" => 20,
		"iframe" => 5,
		"tags" => 5
	];

	protected $draftsOnPage = 5;
	protected $articlesOnPage = 5;
	protected $maxUserArticles = 500;
	protected $minDraftName = 5;
	protected $maxDraftName = 50;
	protected $tagRegex = "/^[a-z_]{1,12}$/";
}