<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Cem Ozan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="iletisim.php">İletişim</a></li>
                <li class="nav-item"><a class="nav-link" href="new.php">Yazılar</a></li>
            </ul>
            <form class="d-flex ms-3" role="search" method="get" action="index.php">
                <input class="form-control me-2" type="search" name="q" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>" placeholder="Bir şeyler yazın..." aria-label="Search">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</nav> 