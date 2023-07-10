<div class="me-auto"></div>
<div class="me-auto"></div>
<div>
    <form style="display: none; visibility: hidden;" action="<?= URLROOT ?>/user-actions/logout" method="post">
        <input name='logout' value='true' readonly>
        <?= View::formToken() ?>
        <button type="submit" id="logout_btn"> </button>
    </form>
    <div class="collapse navbar-collapse mr-16" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle img-dropdown" href="#" id="navbarDropdownMenuLink" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <div class='nav-img' style='background-image: url("<?= Session::userProfilePath() ?>")'>
                    </div>
                    <?= $_SESSION['username'] ?>
                    <!-- Ajout du pseudo du profil -->
                </a>

                <!-- Ajout de la classe dropdown-menu-end -->
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="<?= URLROOT ?>/settings">Modifier le profil</a>
                    <a class="dropdown-item" href="<?= URLROOT ?>/write/articles">Voir mes articles</a>
                    <a class="dropdown-item" href="<?= URLROOT ?>/write/drafts">Voir articles en
                        bruillons</a>
                    <div class="dropdown-divider"></div>
                    <label class="dropdown-item" for='logout_btn' style='cursor: pointer'>
                        Se deconnecter
                    </label>
                </div>
            </li>
        </ul>
    </div>
</div>