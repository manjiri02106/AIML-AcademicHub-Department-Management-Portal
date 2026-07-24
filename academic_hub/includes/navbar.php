<?php
// Top navigation bar for Academic ERP Placement module
$currentPage = basename($_SERVER['PHP_SELF']);
$breadcrumbs = [
    'dashboard.php' => ['Home', 'Dashboard'],
    'companies.php' => ['Home', 'Companies'],
    'placement-drives.php' => ['Home', 'Placement Drives'],
    'students.php' => ['Home', 'Student Registration'],
    'reports.php' => ['Home', 'Reports'],
    'settings.php' => ['Home', 'Settings'],
];
$trail = $breadcrumbs[$currentPage] ?? ['Home', 'Dashboard'];
$pageTitle = $trail[1];
?>
<nav class="navbar navbar-expand-lg navbar-light topbar py-3 mb-3">
  <div class="container-fluid px-3">
    <div class="d-flex align-items-center flex-grow-1">
      <div>
        <h1 class="h5 mb-1 fw-semibold"><?= htmlspecialchars($pageTitle) ?></h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-sm mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($pageTitle) ?></li>
          </ol>
        </nav>
      </div>
    </div>

    <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopContent" aria-controls="navbarTopContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTopContent">
      <form class="d-flex ms-auto my-3 my-lg-0 topbar-search" role="search">
        <div class="input-group input-group-sm">
          <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
          <input class="form-control border-start-0" type="search" placeholder="Search workspace" aria-label="Search workspace">
        </div>
      </form>
      <ul class="navbar-nav align-items-center ms-lg-3">
        <li class="nav-item me-2">
          <a class="nav-link notification-link text-muted position-relative" href="#" aria-label="Notifications">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <?php $navUser = getCurrentUser(); ?>
          <a class="nav-link dropdown-toggle d-flex align-items-center text-dark" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="avatar me-2">
              <i class="bi bi-person-fill"></i>
            </div>
            <div class="d-none d-sm-block text-start">
              <div class="small text-muted"><?= htmlspecialchars($navUser['role'] ?? 'TPO', ENT_QUOTES, 'UTF-8') ?></div>
              <div class="fw-semibold"><?= htmlspecialchars($navUser['full_name'] ?? $navUser['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= url('/auth/logout.php') ?>">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
