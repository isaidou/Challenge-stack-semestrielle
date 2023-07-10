<?php View::header(false, "Réinitialiser le mot de passe") ?>
<?php require_once(APPROOT . '/Views/inc/guest/sign-up-navbar.php') ?>
<link rel="stylesheet" href="/css/single-form-pages.css">
<br><br>
<?php if (!empty($data['error'])) : ?>
<div class="container">
    <p class="display-6 text-center pt-5"><?= $data['error'] ?></p>
</div>
<?php else : ?>
<form action="<?= URLROOT ?>/user/reset-password/<?= $data['token'] ?>" method='POST' id='page-form' class='mt-3'>
    <h3 class='text-center mb-5'>Définir un nouveau mot de passe</h3>
    <div class="form-group mb-3">
        <div class="input-group">
            <input type="password" id='pwd-input'
                class="form-control <?= !empty($data['password_err']) ?  'is-invalid' : '' ?>" name='password'
                placeholder="Nouveau mot de passe" value="<?= ht($data['password']) ?>">
            <span class="input-group-btn">
                <button class="btn btn-light border" id='pwd-toggle' type='button'>
                    <i class="fas fa-eye"></i>
                </button>
            </span>
            <p class="invalid-feedback pt-1 mb-0">
                <?= $data['password_err'] ?>
            </p>
        </div>
        <small class="mt-2 d-block text-muted">
            Le mot de passe doit contenir au moins 8 caractères, au moins une lettre, un chiffre et un caractère spécial
        </small>
    </div>

    <div class="form-group mb-4">
        <input type="password" class="form-control <?= !empty($data['confirm_password_err']) ?  'is-invalid' : '' ?>"
            name='confirm_password' placeholder="Confirmer le mot de passe"
            value="<?= ht($data['confirm_password']) ?>">
        <p class="invalid-feedback pt-1 mb-0">
            <?= $data['confirm_password_err'] ?>
        </p>
    </div>

    <input type="hidden" name="csrf_token" class='token' value="<?= Session::csrfToken() ?>">
    <input type="submit" value="Réinitialiser le mot de passe" class='btn btn-success w-100'>
    <br>

    <div class="container-fluid w-100 p-0 mt-4 mb-0" style='height: 50px'>
        <a href="<?= URLROOT ?>/user/login" class="btn text-primary float-start"><i class="fas fa-arrow-left"></i>
            &nbsp;
            Retour à la connexion</a>
    </div>
</form>
<?php endif; ?>

<script src="/js/password-toggle.js"></script>
<?php View::footer(false) ?>