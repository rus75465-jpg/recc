<?php
require_once 'config.php';

$pageTitle = 'Вход для администратора';
$error = '';

if (isAdmin()) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header('Location: admin.php');
                exit;
            } else {
                $error = 'Неверное имя пользователя или пароль';
            }
        } catch(PDOException $e) {
            $error = 'Ошибка при входе. Попробуйте позже.';
        }
    }
}

include 'includes/header.php';
?>

<section class="section login-section">
    <div class="container">
        <div class="login-wrapper fade-in-up">
            <h2 class="section-title">Вход для администратора</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large">Войти</button>
            </form>
            
            <p class="login-hint">По умолчанию: admin / admin123</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

