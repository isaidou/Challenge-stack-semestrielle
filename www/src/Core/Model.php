<?php

declare(strict_types=1);



class Model
{
	protected $db;

	public function __construct()
	{
		$this->db = DB::getInstance();
	}
}