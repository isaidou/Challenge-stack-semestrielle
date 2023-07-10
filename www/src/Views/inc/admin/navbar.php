<link rel="stylesheet" href="/css/navbar.css">
<link rel="stylesheet" href="/css/user-navbar.css">
<script src="/js/toggle-theme.js" type='module'></script>
<!-- Nav Super Container -->
<div class="nav-container-super fixed-top">
    <!-- Main Navbar -->
    <nav class="navbar navbar-expand navbar-dark bg-dark ">
        <div class="container-fluid px-lg-5">
            <!-- Mobile Toggler -->
            <button class="btn nav-toggler p-1 me-2 shadow-none" id='nav-toggle-mobile'>
                <i class="fas fa-bars" style='color: white; -webkit-text-stroke: 0px white;'></i>
            </button>
            <!-- Nav Brand -->
            <a class="navbar-brand pe-3" href="<?= URLROOT ?>">
                <img src="/assets/logo/logo_w.png" class='navbar-logo mb-1'
                    style='width: 40px; margin-top: -6px; margin-right: 7px;'>
                <span class='nav-brand-title'><?= SITENAME ?></span>
            </a>
            <!-- Nav Items -->
            <div class="navbar-collapse">
                <!-- Nav Items 1 -->
                <ul class="navbar-nav me-auto mb-2 mb-md-0" id='nav-items-1'>
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= View::activeLink('/bookmarks') ?>"
                            href="<?= URLROOT ?>/admin/dashoard">Dashboard</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= View::activeLink('/bookmarks') ?>"
                            href="<?= URLROOT ?>/admin/backoffice">Backoffice</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a href="<?= URLROOT ?>/write/new"
                            class="btn text-dark nav-link btn-light py-1 ms-2 border-0 article-btn"
                            style='margin-top: 5px; font-size: 15px;'>
                            <i class="fas fa-plus new-article-icon"></i>
                            Ajouter un article
                        </a>
                    </li>
                </ul>
            </div>
            <?php require_once 'user-dropdown.php' ?>
        </div>
    </nav>
</div>