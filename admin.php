<?php
require_once 'config.php';
requireAdmin();

$pageTitle = 'Админ-панель';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status' && isset($_POST['application_id']) && isset($_POST['status'])) {
        $app_id = intval($_POST['application_id']);
        $status = $_POST['status'];
        
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            try {
                $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
                $stmt->execute([$status, $app_id]);
            } catch(PDOException $e) {
                $error = 'Ошибка при обновлении статуса';
            }
        }
    }
    
    if ($_POST['action'] === 'approve_review' && isset($_POST['review_id'])) {
        $review_id = intval($_POST['review_id']);
        try {
            $stmt = $pdo->prepare("UPDATE reviews SET approved = 1 WHERE id = ?");
            $stmt->execute([$review_id]);
        } catch(PDOException $e) {
            $error = 'Ошибка при одобрении отзыва';
        }
    }
    
    if ($_POST['action'] === 'delete_review' && isset($_POST['review_id'])) {
        $review_id = intval($_POST['review_id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
        } catch(PDOException $e) {
            $error = 'Ошибка при удалении отзыва';
        }
    }
}


$applications = [];
try {
    $stmt = $pdo->query("SELECT a.*, c.name as category_name FROM applications a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC");
    $applications = $stmt->fetchAll();
} catch(PDOException $e) {
    $applications = [];
}


$pending_reviews = [];
try {
    $stmt = $pdo->query("SELECT * FROM reviews WHERE approved = 0 ORDER BY created_at DESC");
    $pending_reviews = $stmt->fetchAll();
} catch(PDOException $e) {
    $pending_reviews = [];
}


$stats = [
    'total' => count($applications),
    'pending' => count(array_filter($applications, fn($a) => $a['status'] === 'pending')),
    'approved' => count(array_filter($applications, fn($a) => $a['status'] === 'approved')),
    'rejected' => count(array_filter($applications, fn($a) => $a['status'] === 'rejected'))
];

include 'includes/header.php';
?>

<section class="section admin-section">
    <div class="container">
        <h2 class="section-title">Админ-панель</h2>
        <p class="admin-welcome">Добро пожаловать, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
        
        <div class="admin-stats">
            <div class="stat-card">
                <h3><?php echo $stats['total']; ?></h3>
                <p>Всего заявок</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['pending']; ?></h3>
                <p>На рассмотрении</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['approved']; ?></h3>
                <p>Одобрено</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['rejected']; ?></h3>
                <p>Отклонено</p>
            </div>
        </div>
        
        <div class="admin-section-content">
            <h3>Заявки участников</h3>
            <div class="applications-table-wrapper">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Возраст</th>
                            <th>Категория</th>
                            <th>Фото</th>
                            <th>Музыка</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($applications)): ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?php echo $app['id']; ?></td>
                                    <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['phone']); ?></td>
                                    <td><?php echo $app['age']; ?></td>
                                    <td><?php echo htmlspecialchars($app['category_name'] ?? 'Не указано'); ?></td>
                                    <td>
                                        <?php if ($app['photo_path']): ?>
                                            <a href="<?php echo htmlspecialchars($app['photo_path']); ?>" target="_blank">Просмотр</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($app['music_path']): ?>
                                            <a href="<?php echo htmlspecialchars($app['music_path']); ?>" target="_blank">Слушать</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $app['status']; ?>">
                                            <?php 
                                            echo match($app['status']) {
                                                'pending' => 'На рассмотрении',
                                                'approved' => 'Одобрено',
                                                'rejected' => 'Отклонено',
                                                default => $app['status']
                                            };
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($app['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $app['status'] === 'pending' ? 'selected' : ''; ?>>На рассмотрении</option>
                                                <option value="approved" <?php echo $app['status'] === 'approved' ? 'selected' : ''; ?>>Одобрено</option>
                                                <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Отклонено</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">Нет заявок</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if (!empty($pending_reviews)): ?>
        <div class="admin-section-content">
            <h3>Отзывы на модерации</h3>
            <div class="pending-reviews">
                <?php foreach ($pending_reviews as $review): ?>
                    <div class="review-card admin-review-card">
                        <div class="review-header">
                            <h4><?php echo htmlspecialchars($review['author_name']); ?></h4>
                            <div class="review-rating">
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    <span class="star">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                        <span class="review-date"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></span>
                        <div class="review-actions">
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <input type="hidden" name="action" value="approve_review">
                                <button type="submit" class="btn btn-primary btn-small">Одобрить</button>
                            </form>
                            <form method="POST" style="display: inline-block;" onsubmit="return confirm('Удалить отзыв?');">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <input type="hidden" name="action" value="delete_review">
                                <button type="submit" class="btn btn-danger btn-small">Удалить</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

