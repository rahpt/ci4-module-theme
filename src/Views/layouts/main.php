<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CodeIgniter 4' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .navbar { margin-bottom: 2rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">ModularApp</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php 
                    $nav = \Rahpt\Ci4ModuleNav\MenuRegistry::all();
                    foreach ($nav as $menu): 
                        if (!empty($menu['items'])):
                    ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><?= $menu['label'] ?></a>
                            <ul class="dropdown-menu">
                                <?php foreach ($menu['items'] as $item): ?>
                                    <li><a class="dropdown-item" href="<?= base_url($item['route']) ?>"><?= $item['label'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link <?= menu_is_active($menu['route'] ?? '') ? 'active' : '' ?>" href="<?= base_url($menu['route'] ?? '#') ?>"><?= $menu['label'] ?></a></li>
                    <?php endif; endforeach; ?>

                    <?php if (auth()->loggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                        <li class="nav-item"><span class="nav-link text-info">Olá, <?= auth()->user()->username ?? 'User' ?></span></li>
                        <li class="nav-item"><a class="btn btn-outline-danger ms-2" href="/logout">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-outline-primary ms-2" href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
