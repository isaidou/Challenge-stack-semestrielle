<?php View::header(true, "Articles") ?>
<br><br>
<style>
.prev-img {
    max-height: 400px;
}
</style>
<link rel="stylesheet" href="/css/drafts.css">
<div class="container-lg container-fluid">
    <?php Session::alert("alert_article_delete") ?>
    <div class="container-fluid container-lg">
        <?php if ($data['count'] > 0) : ?>
        <h2>Mes Articles</h2>
        <?php else : ?>
        <h1>0 résultats <a href="<?= URLROOT ?>/write/new" class='h4'>Créer un article</a></h1>
        <?php endif; ?>
        <br><br>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="articles-container">
            <?php foreach ($data['articles'] as $article) : ?>
            <?php
                $edit_link = URLROOT . '/write/edit-article/' . $article->article_id;
                $view_link = URLROOT . '/article?a=' . $article->article_id;
                $delete_link = URLROOT . '/write/delete-article/' . $article->article_id;
                ?>
            <div class="col mb-3">
                <div class="card shadow-sm">
                    <?php if (!Str::isEmptyStr($article->preview_img)) : ?>
                    <img src="<?= $article->preview_img ?>" class="card-img-top prev-img" alt="...">
                    <?php endif; ?>
                    <div class="card-body">
                        <h4 class='draft-name'><a href="<?= $view_link ?>"
                                class='text-dark text-decoration-none'><?= ht($article->title, 40) ?></a></h4>
                        <div class="p-2"></div>
                        <?php if (!Str::isEmptyStr($article->tagline)) : ?>
                        <p class="card-text tagline"><?= ht($article->tagline, 100) ?></p>
                        <?php endif; ?>
                        <div class="py-2">
                            <small class="text-muted"><span style='font-weight: 500; font-size: 14px;'>Créé le: </span>
                                <?= Str::formatEpoch(strtotime($article->created_at), "d/m H:i") ?></small>
                            <br>
                            <small class="text-muted"><span style='font-weight: 500; font-size: 14px;'>Dernière
                                    modification: </span>
                                <?= Str::formatEpoch(strtotime($article->last_updated), "d/m H:i") ?></small>
                        </div>
                        <br>
                        <div class="btn-group mx-auto">
                            <a class="btn btn-sm btn-outline-danger"
                                href="<?= $delete_link ?>">&nbsp;Supprimer&nbsp;</a>
                            <a class="btn btn-sm btn-outline-primary " href="<?= $edit_link ?>">Modifier</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>

        <?php if ($data['count'] > 3) : ?>
        <button class="btn btn-primary m-auto mt-5 mb-0 d-block" style='margin-bottom: -40px!important'
            id="articles-more-btn">Charger Plus </button>
        <?php endif; ?>
    </div>
</div>


<?= View::formToken($data['last_article_id'], "last_article_id") ?>
<?php View::footer() ?>