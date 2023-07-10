<?php

class Info extends Controller
{
	public function __childConstruct()
	{
	}

	/**
	 * Landing main page of website
	 * Redirected user to home page instead of landing page
	 * 
	 * @route
	 */
	public function index(): void
	{
		Session::redirectUser();
		$this->view('info/index');
	}

	/**
	 * About page
	 * 
	 * @route about
	 */
	public function privacy(): void
	{
		$this->view('info/privacy');
	}
}