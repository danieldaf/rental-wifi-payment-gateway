<ul class="nav nav-pills flex-column">
    <li class="nav-item">
        <a class="nav-link <?= ($page === "home") ? 'active': '' ?>"  href="index.php">My Vouchers</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($page === "buy") ? 'active': '' ?>" href="buy.php">Buy Voucher</a>
    </li>

    <?php if(!$auth->isGuest()): ?>
        <li class="nav-item">
            <a class="nav-link <?= ($page === "settings") ? 'active': '' ?>" href="settings.php">Account Setting</a>
        </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link " href="logout.php">Logout</a>
    </li>
</ul>
