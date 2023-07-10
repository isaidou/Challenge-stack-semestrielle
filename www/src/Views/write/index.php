<?php
View::header(false, "Écrire");
View::customNav([
    "Brouillons enregistrés" => "write/drafts",
    "Mes articles" => "write/articles",
]);

?>
<?php $handleLink = URLROOT . "/write/handle-submission/" ?>
<?php if (isset($data['article_id'])) $handleLink .= $data['article_id'] ?>
<?php if (isset($data['draft_id'])) $handleLink .=  $data['draft_id'] ?>

<main>
    <?php Session::alert("alert_edit") ?>
    <div style='margin-bottom: 100px'></div>
    <div class="container-lg px-lg-2">
        <div class="row">
            <h4 class='d-block'>Écrire un nouvel article</h4>
            <div class="pb-md-1 pb-3"></div>
            <div class="col-md-8 col-12 order-md-1 order-2 ps-md-3 pe-md-0 py-4 p-0">
                <div class="p-md-4 bg-white rounded p-2 py-4" style='border: 1px solid #d7d7d7'>
                    <form action="<?= $handleLink ?>" method="POST" enctype="multipart/form-data">
                        <input type="text"
                            class='form-control bg-white <?= !empty($data['title_err']) ? 'is-invalid' : '' ?>'
                            placeholder="Titre" name='title' value="<?= getValueFromArray($data, 'title') ?>">
                        <p class="invalid-feedback"><?= $data['title_err'] ?></p>
                        <br>
                        <textarea
                            class='form-control bg-white shadow-none <?= !empty($data['tagline_err']) ? 'is-invalid' : '' ?>'
                            placeholder="Tagline" name="tagline"><?= getValueFromArray($data, 'tagline') ?></textarea>
                        <p class="invalid-feedback"><?= $data['tagline_err'] ?></p>
                        <br>
                        <br>
                        <textarea id="content" name="content"
                            class='form-control <?= !empty($data['content_err']) ? 'is-invalid' : '' ?>' rows="10"
                            placeholder="Ecrire votre contenu ici"><?= getValueFromArray($data, 'content') ?></textarea>
                        <p class="invalid-feedback"><?= $data['content_err'] ?></p>
                        <br>
                        <br>
                        <input type="text" class='form-control <?= !empty($data['tags_err']) ? 'is-invalid' : '' ?>'
                            placeholder="Ajouter des tags (séparés par des virgules)" name="tags"
                            value="<?= getValueFromArray($data, 'tags') ?>">
                        <p class="invalid-feedback"><?= $data['tags_err'] ?></p>
                        <br>
                        <div class="mb-3">
                            <label for="image" class="form-label">Charger une photo</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <p class="invalid-feedback"><?= $data['img_err'] ?></p>
                        </div>
                        <br>
                        <div class="d-flex justify-content-between">
                            <?= View::formToken() ?>
                            <div>
                                <label class=" form-label" for="draft_name">Ce bouton enregistrera
                                    l'article en tant que brouillon.</label>
                                <input type="text"
                                    class="form-control <?= !empty($data['draft_name_err']) ? 'is-invalid' : '' ?>"
                                    placeholder="Nom du brouillon" name="draft_name" id='draft_name'
                                    value="<?= getValueFromArray($data, 'draft_name') ?>">
                                <p class="invalid-feedback"><?= $data['draft_name_err'] ?></p>
                                <div class="mt-2">
                                    <button class="btn btn-primary" type="submit" name="save_draft">Enregistrer</button>
                                </div>
                            </div>

                            <div class="align-self-end">
                                <button class="btn btn-success" type="submit" value="publish_article">Publier</button>
                            </div>
                        </div>

                    </form>
                    <div style='margin-bottom: 40px'></div>
                </div>
            </div>
        </div>
    </div>

</main>
<?php View::footer(false) ?>