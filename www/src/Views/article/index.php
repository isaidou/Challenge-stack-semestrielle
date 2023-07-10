<?php View::header(true, $data['article']->title) ?>

<link rel="stylesheet" href="/css/hljs-theme.css">
<link rel="stylesheet" href="/css/article.css">

<div class="container p-md-5 pb-md-0 mb-md-0">

    <div class="p-4 mt-4 p-sm-0">
        <br><br><br>
        <?php Session::flash('report_success') ?> <?php Session::flash('report_error') ?>
        <?php Session::flash('delete_error') ?> <?php Session::flash('delete_success') ?>
    </div>

    <div class="mx-md-5 main-article px-md-3">
        <a class='d-block text-center h2 text-decoration-none text-reset'
            href="<?= URLROOT ?>/article?id=<?= $data['article']->article_id ?>">
            <?= ht($data['article']->title) ?>
        </a>
        <?php if (($_SESSION['user_id'] ?? "") === $data['article']->user_id) : ?>
        <a class="btn border-dark p-2 px-3 me-2"
            href="<?= URLROOT ?>/write/edit-article/<?= $data['article']->article_id ?>">Modifier l'article</a>
        <a class="btn btn-outline-danger border-dark p-2 px-3 me-2"
            href="<?= URLROOT ?>/write/delete-article/<?= $data['article']->article_id ?>">Supprimer l'article</a>
        <?php endif; ?>

        <br><br>
        <p class=" fs-5"><?= $data['views'] === 1 ? $data['views'] . " view" : $data['views'] . " views" ?></p>
        <pre class="mb-1">Posté le: <?= date("d M Y (H:i)", strtotime($data['article']->created_at)) ?></pre>
        <pre class="">Dernière modification: <?= date("d M Y (H:i)", strtotime($data['article']->last_updated)) ?></pre>
        <br>

        <div class="user-details" style="width: 250px; height: 80px; cursor: pointer">
            <div class="row">
                <div class="col-3">
                    <div class="user-img" style="background-image: url(<?= $data['user']->profile_img ?>);"></div>
                </div>
                <div class="col-9">
                    <b><?= ht($data['user']->pseudo) ?></b>
                    <p class="text-muted">@<?= ht($data['user']->username) ?></p>
                </div>
            </div>
        </div>


        <?php if (!Str::isEmptyStr($data['article']->preview_img)) : ?>
        <img src="<?= $data['article']->preview_img ?>" alt="..." class="preview-img">
        <br><br>
        <?php endif; ?>

        <div class="show-fixed-bar d-block">
            <div class="content-area">
                <?php if (!Str::isEmptyStr($data['article']->tagline)) : ?>
                <i class='text-muted'>
                    <?= ht($data['article']->tagline) ?>
                    <hr>
                </i>
                <?php endif; ?>
                <?= $data['article']->content ?>
            </div>
        </div>
    </div>
    <br>
    <br>

    <h4>Comments</h4>
    <br>


    <?php require_once "comment-section.php"; ?>
</div>
<?= View::formToken($data['article']->article_id, "article_id") ?>
<?php View::footer() ?>