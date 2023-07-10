<link rel="stylesheet" href="/css/footer.css">
<div class="container">
    <footer class="pt-4 my-md-5 pt-md-5 border-top" id='page-footer'>
        <div class="row">
            <div class="col-12 col-md">
                <a href="#">
                    <img class="mb-3 ms-4" src="/assets/logo/logo_b.png" alt="" width="45">
                </a>
                <small class="d-block mb-3 text-muted">© 2023-2024</small>
            </div>
            <div class="col-6 col-md">
                <h5>Resources</h5>
                <ul class="list-unstyled text-small">
                    <li><a class="link-secondary <?= View::activeFooter('') ?>" href="<?= URLROOT ?>">Page d'accueil</a>
                    </li>
                    <li><a class="link-secondary <?= View::activeFooter("/info/privacy") ?>"
                            href="<?= URLROOT ?>/info/privacy">Politique privée</a></li>
                </ul>
            </div>
            <div class="col-6 col-md">
                <h5>Users</h5>
                <ul class="list-unstyled text-small">
                    <li><a class="link-secondary <?= View::activeFooter("/user/sign-up") ?>"
                            href="<?= URLROOT ?>/user/sign-up">S'inscrire</a></li>
                    <li><a class="link-secondary <?= View::activeFooter("/user/login") ?>"
                            href="<?= URLROOT ?>/user/login">Connexion</a></li>
                </ul>
            </div>
            <div class="col-6 col-md">
                <ul class="list-unstyled text-small">
                    <li><a class="link-primary" href="#">Vers le haut <i class="fas fa-caret-up"></i></a></li>
                </ul>
            </div>
        </div>
    </footer>
</div>