<link rel="stylesheet" href="/css/navbar.css">
<!-- Nav Super Container -->
<div class="nav-container-super fixed-top">
    <!-- Main Navbar -->
    <nav class="navbar navbar-expand navbar-dark bg-dark ">
        <div class="container-lg">
            <!-- Mobile Toggler -->
            <button class="btn nav-toggler p-1 me-2 shadow-none" id='nav-toggle-mobile'>
                <i class="fas fa-bars" style='color: white'></i>
            </button>
            <!-- Nav Brand -->
            <a class="navbar-brand pe-3" href="<?= URLROOT ?>" style='padding-top: 8px;'>
                <img src="/assets/logo/logo_w.png" class='navbar-logo mb-1'
                    style='width: 40px; margin-top: -7px; margin-right: 7px;'>
                <span class='nav-brand-title'><?= SITENAME ?></span>
            </a>
            <!-- Nav Items -->
            <div class="navbar-collapse">
                <!-- Nav Items 1 -->
                <ul class="navbar-nav me-auto mb-2 mb-md-0" id='nav-items-1'>
                    <li class="nav-item active">
                        <a class="nav-link <?= View::activeLink("") ?>" aria-current="page" href="<?= URLROOT ?>">Page
                            d'accueille</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown"
                            aria-expanded="false">Plus </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown01" id='navbar-dropdown'>
                            <li><a class="dropdown-item" href=" <?= URLROOT ?>/info/privacy">Politique privée</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Nav items 2 -->
                <ul class="navbar-nav ms-auto mb-2 mb-md-0" id='nav-items-2'>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/user/login" tabindex="-1">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/user/sign-up" tabindex="-1">S'inscrire</a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
    <div style='margin-bottom: -20px;'></div>
</div>