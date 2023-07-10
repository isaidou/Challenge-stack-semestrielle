<?php View::header(true, "Page d'accueil") ?>
<link rel="stylesheet" href="/css/hljs-theme.css">
<link rel="stylesheet" href="/css/article.css">

<div class="container p-md-5 pb-md-0 mb-md-0">

    <div class="p-4 mt-4 p-sm-0">
        <br><br><br>
        <?php Session::flash('report_success') ?> <?php Session::flash('report_error') ?>
        <?php Session::flash('delete_error') ?> <?php Session::flash('delete_success') ?>
    </div>

    <div class="mx-md-5 main-article px-md-3">
        <div class="row">
            <?php if (empty($data['articles'])) : ?>
            <p>Il n'y a pas encore d'articles. <a href="<?= URLROOT ?>/write/new">Cliquez ici</a> pour en créer un.</p>
            <?php else : ?>
            <?php foreach ($data['articles'] as $article) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img class="user-img" src="<?= Session::userProfilePath() ?>" alt="<?= $article->pseudo ?>">
                    <b class='d-block'><?= ht($_SESSION['pseudo']) ?></b>
                    <small class="text-muted">@<?= ht($_SESSION['username']) ?></small>
                    <div class="card-body">
                        <h2 class="card-title"><?= ht($article->title) ?></h2>
                        <h4 class="card-subtitle mb-2 text-muted"><?= ht($article->tagline, 100) ?></h4>
                        <?php if ($article->preview_img) : ?>
                        <img class="preview-img" src="<?= $article->preview_img ?>" alt="<?= $article->title ?>">
                        <?php endif; ?>
                        <p class="card-text"><?= ht($article->content, 200) ?>...</p>
                        <p class="text-muted">Publié le <?= date('d/m/Y à H:i', strtotime($article->created_at)) ?></p>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span><i class="fas fa-comment"></i>
                                    <?= Str::formatCommentCount($article->comments_count) ?></span>
                                <span><i class="fas fa-eye"></i>
                                    <?= Str::formatArticleViews($article->views_count) ?></span>
                            </div>
                            <a class="btn btn-primary"
                                href="<?= URLROOT ?>/article/?id=<?= $article->article_id ?>">Voir
                                l'article</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between my-4">
            <?php if ($data['current_page'] > 1) : ?>
            <a class="btn btn-primary" href="?page=<?= $data['current_page'] - 1 ?>" class="previous">Page
                précédente</a>
            <?php endif; ?>
            <span>Page <?= $data['current_page'] ?> sur <?= $data['total_pages'] ?></span>
            <?php if ($data['current_page'] < $data['total_pages']) : ?>
            <a class="btn btn-primary" href="?page=<?= $data['current_page'] + 1 ?>" class="next">Page suivante</a>
            <?php endif; ?>
        </div>

    </div>
    <?= View::formToken($data['last_id'], "last_id") ?>
    <script src="/js/home.js" type="module"></script>
</div>
<?php View::footer() ?>