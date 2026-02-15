<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | <?= $title ?? 'Dashboard' ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link text-danger" href="/logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/" class="brand-link">
      <span class="brand-text font-weight-light">Modular Core</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block text-info"><?= auth()->user()->username ?? 'User' ?></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-header">MENU</li>
          
          <?php 
          $menus = \Rahpt\Ci4ModuleNav\MenuRegistry::all();
          foreach ($menus as $menu): 
            $hasItems = !empty($menu['items']);
            $isActive = isset($menu['route']) && menu_is_active($menu['route']);
            
            $openClass = '';
            if ($hasItems) {
                foreach ($menu['items'] as $subItem) {
                    if (isset($subItem['route']) && menu_is_active($subItem['route'])) {
                        $openClass = 'menu-open';
                        $isActive = true;
                        break;
                    }
                }
            }
          ?>
          <li class="nav-item <?= $hasItems ? 'has-treeview ' . $openClass : '' ?>">
            <?php 
              $link = $menu['route'] ?? '#';
              $href = ($link === '#' || empty($link)) ? '#' : base_url($link);
            ?>
            <a href="<?= $href ?>" class="nav-link <?= $isActive ? 'active' : '' ?>">
              <i class="nav-icon <?= $menu['icon'] ?? 'fas fa-cube' ?>"></i>
              <p>
                <?= $menu['label'] ?? $menu['title'] ?? 'Sem Título' ?>
                <?= $hasItems ? '<i class="right fas fa-angle-left"></i>' : '' ?>
              </p>
            </a>
            <?php if ($hasItems): ?>
            <ul class="nav nav-treeview">
              <?php foreach ($menu['items'] as $subItem): ?>
              <li class="nav-item">
                <a href="<?= base_url($subItem['route']) ?>" class="nav-link <?= menu_is_active($subItem['route']) ? 'active' : '' ?>">
                  <i class="<?= $subItem['icon'] ?? 'far fa-circle' ?> nav-icon"></i>
                  <p><?= $subItem['label'] ?? $subItem['title'] ?? 'Sem Título' ?></p>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>

          <li class="nav-item">
            <a href="/" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Voltar ao Site</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?= $title ?? '' ?></h1>
          </div>
          <div class="col-sm-6">
            <?= render_breadcrumbs() ?>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <?= $this->renderSection('content') ?>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2026 <a href="#">Modular Core</a>.</strong>
  </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
