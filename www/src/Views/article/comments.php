<?php View::header(true, $data['article']->title . " - comments") ?>

<br><br>

<div class="container m-auto">
    <a href="<?= URLROOT ?>/article?a=<?= $data['article']->article_id ?>" class="btn btn-light">
        <i class="fas fa-arrow-left pe-2"></i>
        Return to article
    </a>

    <br><br>
    <h4>Comments</h4>
    <br><br>

    <?php require_once "comment-section.php"; ?>
</div>

<?= View::formToken($data['article']->article_id, "article_id") ?>
<?php View::footer() ?>