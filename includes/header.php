<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Конкурс талантов'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">Конкурс Талантов</a>
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php#about">О конкурсе</a></li>
                    <li><a href="index.php#gallery">Галерея</a></li>
                    <li><a href="index.php#reviews">Отзывы</a></li>
                    <li><a href="index.php#contacts">Контакты</a></li>
                    <li><a href="register.php" class="btn-register">Регистрация</a></li>
                    <?php if (isAdmin()): ?>
                    <li><a href="admin.php" class="btn-admin">Админ-панель</a></li>
                    <li><a href="logout.php" class="btn-logout">Выйти</a></li>
                    <?php else: ?>
                    <li><a href="login.php" class="btn-login">Вход</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

