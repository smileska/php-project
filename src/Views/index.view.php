<?php require('parts/head.php') ?>
<?php require('parts/navbar.php') ?>
<?php require('parts/banner.php') ?>
<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <p>How are you today, <?= $_SESSION['username'] ?? 'guest' ?>?</p>
    </div>
</main>
</html>