<?php
View::header(false, "Modifier le profil");
View::customNav();
?>
<link rel="stylesheet" href="/css/edit-profile.css">
<br><br><br>
<div class="container mb-5 mt-5">
    <h2 class="text-center">Modifier le profil</h2>
    <form action="<?= URLROOT ?>/settings/edit-profile" method="post" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="mb-3">
                    <label for="displayName" class="form-label">Pseudo</label>
                    <input type="text" class="form-control <?= !empty($data['pseudo_err']) ? 'is-invalid' : '' ?>"
                        id="displayName" name="pseudo" value="<?= ht($_SESSION['pseudo']) ?>" required>
                    <p class="invalid-feedback pt-1"><?= $data['pseudo_err'] ?></p>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control <?= !empty($data['username_err']) ? 'is-invalid' : '' ?>"
                        id="username" name="username" value="<?= ht($_SESSION['username']) ?>" required>
                    <p class="invalid-feedback pt-1"><?= $data['username_err'] ?></p>
                </div>
                <div class="mb-3">
                    <label for="about" class="form-label">Description</label>
                    <textarea name="about" id="about" rows="5"
                        class='form-control <?= !empty($data['about_err']) ? 'is-invalid' : '' ?>'><?= ht($_SESSION['about']) ?></textarea>
                    <p class="invalid-feedback pt-1"><?= $data['about_err'] ?></p>
                </div>
                <div class="mb-3">
                    <?= View::formToken() ?>
                    <button class="btn btn-success w-100" id='save'>Enregistrer les modifications</button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="text-center">
                    <img src="<?= Session::userProfilePath() ?>" class="img-thumbnail" alt="Image de profil"
                        id='preview-img'>
                    <div class="mt-3">
                        <label for="image" class="btn btn-dark">Télécharger <i class="fas fa-upload"></i>
                            <input type="file" name="image" id="image"
                                class="d-none <?= !empty($data['image_err']) ? 'is-invalid' : '' ?>">
                            <p class="invalid-feedback pt-1"><?= $data['profile_img_err'] ?></p>
                        </label>
                        <p class="invalid-feedback pt-1"><?= $data['image_err'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php View::footer() ?>