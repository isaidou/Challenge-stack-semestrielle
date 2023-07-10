<?php View::header() ?>
<link rel="stylesheet" href="/css/info-home.css">
<link rel="stylesheet" href="/css/particles.css">

<!-- Particles container -->
<div id="particles-js">
    <div class="container mt-5 pt-5 col-lg-8 col-md-10 col-sm-10 container justify-content-center">
        <div class="col mt-4 pt-3 text-center">
            <h3 class=''><a href="<?= URLROOT ?>/user/sign-up" class='text-decoration-none text-white'><?= SITENAME ?> -
                    Rejoignez-nous</a></h3>
            <p class='mt-3 lead text-light'>La plateforme de blog moderne pour les développeurs, experts, lecteurs,
                auteurs et — pour tous.</p>
        </div>
    </div>
</div>

<!-- Content -->
<div class="container mt-3">
    <p class='pt-2 pb-0 mb-0' style='font-size: 18px;'><?= SITENAME ?> est
        une plateforme de blog moderne pour les utilisateurs du monde entier <br><br>
    <ul>
        <li>Lisez des articles sur le blog<br><br></li>
        <li>Utilisez une interface conviviale pour créer des posts et sauvegarder des brouillons<br><br></li>
        <li>Personnalisez votre profil<br><br></li>
        <li>Commentez les articles<br><br></li>
    </ul>
    </p>

    <p align="center"><b>Lisez des articles sur le blog</b></p>
    </p>
    <p align="center" class=''><b>Créez des brouillons et des articles</b></p>
    <p><br></p>

    </p>
    <p align="center" class=''><b>Ajoutez des commentaires et ayez la possibilité de les supprimer ou de les signaler
            s'ils sont inappropriés</b></p>
    <p><br></p>

    <p align="center">
    </p>
    <p align="center"><b>Personnalisez votre profil</b></p>
</div>

<script src="/js/particles.js"></script>
<?php View::footer() ?>