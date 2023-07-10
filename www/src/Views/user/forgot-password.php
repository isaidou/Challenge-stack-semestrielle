<?php View::header(true, "Mot de passe oublié") ?>
<link rel="stylesheet" href="/css/single-form-pages.css">
<style>
#nav-items-3 {
    display: none;
}
</style>
<br><br>
<form action="<?= URLROOT ?>/user/forgot-password" method='POST' id='page-form' class='mt-3'>
    <h3 class='text-center mb-5'>Mot de passe oublié</h3>
    <div class="form-group mb-3">
        <input type="text" class="form-control <?= !empty($data['error']) ?  'is-invalid' : '' ?>" name='email'
            placeholder="Adresse email" value="<?= ht($data['email']) ?>">
        <p class="invalid-feedback pt-1">
            <?= $data['error'] ?>
        </p>
    </div>
    <input type="hidden" name="csrf_token" class='token' value="<?= Session::csrfToken() ?>">
    <input type="submit" value="Send Email" class='btn btn-success w-100'>
    <br>
    <div class="container-fluid w-100 p-0 mt-4 mb-0" style='height: 50px'>
        <a href="<?= URLROOT ?>/user/login" class="btn text-primary float-start">Se connecter</a>
        <a href="<?= URLROOT ?>/user/sign-up" class="btn text-black float-end text-dark">S'inscrire</a>
    </div>
</form>
<?php View::footer(false) ?>