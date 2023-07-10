<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="/css/user-navbar.css">

<script src="/js/toggle-theme.js" type='module'></script>
<script src="/js/custom-navbar.js" type='module'></script>

<form style="display: none; visibility: hidden;" action="<?= URLROOT ?>/user-actions/logout" method="post">
    <input name='logout' value='true' readonly>
    <?= View::formToken() ?>
    <button type="submit" id="logout_btn"> </button>
</form>
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
                    <?php foreach ($customNavItems as $name => $url) : ?>
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= View::activeLink("$url") ?>"
                            href="<?= URLROOT . "/$url" ?>"><?= $name ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <!-- Nav items 2 -->
                <?php require_once 'user-dropdown.php' ?>
            </div>
        </div>
    </nav>
</div>