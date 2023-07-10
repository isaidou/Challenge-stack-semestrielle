<?php View::header(false, 'Inscription') ?>
<?php require_once(APPROOT . '/Views/inc/guest/sign-up-navbar.php') ?>
<link rel="stylesheet" href="/css/sign-up.css">
<main class="sign-up">
    <h3 class="text-center p-0 m-0 mb-5">Créer un compte</h3>
    <form action="<?= URLROOT ?>/user/sign-up" id="signup_form" method="POST">
        <div class="form-group mb-3">
            <label for="email" class="mb-2">Email</label>
            <input type="email" class="form-control form-input <?= !empty($data['email_err']) ? 'is-invalid' : '' ?>"
                name="email" id="email" placeholder="" value="<?= isset($data['email']) ? ht($data['email']) : '' ?>">
            <p class="invalid-feedback pt-1"><?= $data['email_err'] ?></p>
        </div>
        <div class="form-group mb-3">
            <label for="gender" class="mb-2">Genre</label>
            <select class="form-select form-input <?= !empty($data['gender_err']) ? 'is-invalid' : '' ?>"
                aria-label="Default select example" name="gender" id="gender">
                <option value="" disabled selected hidden>Sélectionnez votre genre</option>
                <option value="male" <?= ($data['gender'] == 'male') ? 'selected' : '' ?>>Homme</option>
                <option value="female" <?= ($data['gender'] == 'female') ? 'selected' : '' ?>>Femme</option>
                <option value="other" <?= ($data['gender'] == 'other') ? 'selected' : '' ?>>Autre</option>
            </select>

            <p class="invalid-feedback pt-1"><?= $data['gender_err'] ?></p>
        </div>
        <div class="form-group mb-3">
            <label for="pseudo" class="mb-2">Pseudo</label>
            <input type="text" class="form-control form-input <?= !empty($data['pseudo_err']) ? 'is-invalid' : '' ?>"
                id="pseudo" name="pseudo" placeholder=""
                value="<?= isset($data['pseudo']) ? ht($data['pseudo']) : '' ?>">
            <p class="invalid-feedback pt-1"><?= $data['pseudo_err'] ?></p>
        </div>
        <div class="form-group mb-3">
            <label for="username" class="mb-2">Nom d'utilisateur</label>
            <input type="text" class="form-control form-input <?= !empty($data['username_err']) ? 'is-invalid' : '' ?>"
                name="username" placeholder="" value="<?= isset($data['username']) ? ht($data['username']) : '' ?>">
            <p class="invalid-feedback pt-1"><?= $data['username_err'] ?></p>
        </div>
        <div class="form-group mb-3">
            <label for="password" class="mb-2">Mot de passe</label>
            <div class="input-group">
                <input type="password"
                    class="form-control form-input <?= !empty($data['password_err']) ? 'is-invalid' : '' ?>"
                    placeholder="" name="password" id="pwd-input"
                    value="<?= isset($data['password']) ? ht($data['password']) : '' ?>">
                <span class="input-group-btn">
                    <button class="btn btn-light border" type="button" id="pwd-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </span>
            </div>
            <small class="mt-2 d-block text-muted">
                Le mot de passe doit contenir au moins 8 caractères, dont une lettre, un chiffre et un caractère spécial
            </small>
            <p class="invalid-feedback pt-1"><?= $data['password_err'] ?></p>
        </div>
        <div class="form-group mb-4">
            <label for="confirm_password" class="mb-2">Confirmer le mot de passe</label>
            <input type="password"
                class="form-control form-input <?= !empty($data['confirm_password_err']) ? 'is-invalid' : '' ?>"
                placeholder="" name="confirm_password">
            <p class="invalid-feedback pt-1"><?= $data['confirm_password_err'] ?></p>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-4" id="submit_btn">
            <span>Enregistrer</span>
        </button>
    </form>
</main>
<script src="/js/password-toggle.js" type="module"></script>
<?php View::footer(false) ?>