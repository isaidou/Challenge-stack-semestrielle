<?php

declare(strict_types=1);

class Admin extends Controller
{

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


    public function index()
    {
        if (!Session::isAdmin()) {
            Server::redirect("home");
        }
    }

    // dashboard page
    public function dashboard()
    {
        if (!Session::isAdmin()) {
            Server::redirect("home");
        }

        $data = [];


        $this->view("admin/dashboard", $data);
    }
}