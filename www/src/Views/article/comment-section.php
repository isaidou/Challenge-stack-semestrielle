<link rel="stylesheet" href="/css/comments.css">

<div class="row" id="comment-add-box">
    <div class="col-2 col-md-1">
        <?php
        $commentImage = DEFAULT_PROFILE_PATH;
        if (Session::isLoggedIn()) $commentImage = "{$_SESSION['profile_img']}";
        ?>
        <div class="user-img" style="background-image: url(<?= $commentImage ?>); border: 1px solid #ebebeb"></div>
    </div>
    <input type="hidden" class='token' id="comment-info" data-parent-id='0' data-method="add" data-edit-id="0">
    <div class="col-md-8 col-10">
        <form action="<?= URLROOT ?>/article/add-comment" method="POST">
            <textarea name="content" id="comment-area" cols="10" rows="8" class="form-control"
                <?= !empty($data['comment_err']) ? 'is-invalid' : '' ?>></textarea>
            <input type="hidden" name="article_id" value="<?= $data['article']->article_id ?>" />
            <?= View::formToken() ?>
            <p class="invalid-feedback pt-1"><?= $data['comment_err'] ?></p>
            <button class="btn btn-success float-end mt-4" id='comment-btn'>Ajouter un commentaire</button>
        </form>
    </div>
    <div class="col-0 col md-3"></div>
</div>
<br><br><br>
<div id="comments-box">
    <!-- Fetch Comments -->
    <?php foreach ($data['comments'] as $k => $comment) : ?>
    <div class="comment row" id="comment-<?= $comment->id; ?>">
        <div class="col-2 col-md-1">
            <div class="user-img"
                style="background-image: url(<?= $comment->profile_img ?>); border: 1px solid #ebebeb">
            </div>
        </div>
        <div class="col-md-8 col-10 mt-1">
            <h6>
                <p class='text-decoration-none text-dark d-inline-block me-2'><?= ht($comment->pseudo) ?></p>
                <small class='text-muted fw-normal'
                    id='username-<?= $comment->id ?>'><?= date("d M Y (H:i)", strtotime($comment->created_at)) ?>
                    <?= $comment->is_edited ? "&nbsp;(edited)" : "" ?></small>
            </h6>
            <p id='content-<?= $comment->id ?>'><?= ht($comment->content) ?></p>

            <div class="dropup m-0 p-0 d-inline-block float-end">
                <button class="btn btn-white float-end d-inline-block p-0 m-0" id="dropdownMenuButton1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v" style='font-size: 15px'></i>
                </button>
                <ul class="dropdown-menu m-0 float-start comment-dropdown" aria-labelledby="dropdownMenuButton1">
                    <?php if (($_SESSION['user_id'] ?? "") === $comment->user_id) : ?>
                    <li class='delete-comment'>
                        <a class="dropdown-item" <a class="dropdown-item"
                            href="<?= URLROOT ?>/article/delete-comment/?idComment=<?= $comment->id ?>&idArticle=<?= $data['article']->article_id ?>"
                            onclick="return confirm('Etes-vous sur de vouloir supprimer ce commentaire ?');"
                            data-comment-id="<?= $comment->id ?>">Supprimer</a>
                    </li>
                    <?php endif; ?>
                    <li><a class="dropdown-item"
                            href="<?= URLROOT ?>/article/report-comment/?idComment=<?= $comment->id ?>&idArticle=<?= $data['article']->article_id ?>"
                            onclick="return confirm('Etes-vous sur de vouloir signaler ce commentaire ?');"
                            data-comment-id="<?= $comment->id ?>">Signaler</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<br><br>
<?php endforeach; ?>
</div>