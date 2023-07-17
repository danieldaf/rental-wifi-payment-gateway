<ul class="nav nav-pills flex-column">

    <?php
        if (!$auth->isAdmin()): ?>

    <li class="nav-item">
        <a class="nav-link <?= ($page === "home") ? 'active': '' ?>"  href="index.php">My Vouchers</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($page === "buy") ? 'active': '' ?>" href="buy.php">Buy Voucher</a>
    </li>

        <?php
        else:
        ?>

    <li class="nav-item">
        <a class="nav-link <?= ($page === "vouchers") ? 'active': '' ?>"  href="vouchers.php">Manage Vouchers</a>
    </li>

<!--    <li class="nav-item">-->
<!--        <a class="nav-link" href="orders.php">Order History</a>-->
<!--    </li>-->

    <?php endif; ?>

    <?php if(!$auth->isGuest()): ?>
        <li class="nav-item">
            <a class="nav-link <?= ($page === "settings") ? 'active': '' ?>" href="settings.php">Account Setting</a>
        </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link " href="logout.php">Logout</a>
    </li>
</ul>
