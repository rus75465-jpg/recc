<?php
require_once 'config.php';

$pageTitle = 'Главная - Конкурс Прожектор';

$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    $categories = [];
}
$gallery = [];
try {
    $stmt = $pdo->query("SELECT g.*, c.name as category_name FROM gallery g LEFT JOIN categories c ON g.category_id = c.id ORDER BY g.created_at DESC LIMIT 12");
    $gallery = $stmt->fetchAll();
} catch(PDOException $e) {
    $gallery = [];
}
$reviews = [];
try {
    $stmt = $pdo->query("SELECT * FROM reviews WHERE approved = 1 ORDER BY created_at DESC LIMIT 6");
    $reviews = $stmt->fetchAll();
} catch(PDOException $e) {
    $reviews = [];
}

include 'includes/header.php';
?>
<section class="hero">
    <div class="hero-background"></div>
    <div class="container">
        <div class="hero-content fade-in-up">
            <h1 class="hero-title">Раскройте свой талант!</h1>
            <p class="hero-subtitle">Примите участие в конкурсе талантов и покажите миру свои способности</p>
            <a href="register.php" class="btn btn-primary btn-large">Зарегистрироваться</a>
        </div>
    </div>
</section>
<section id="about" class="section about-section">
    <div class="container">
        <h2 class="section-title fade-in-up">О конкурсе</h2>
        <div class="about-content">
            <div class="about-card fade-in-up">
                <h3>Правила конкурса</h3>
                <p>Участие открыто для всех желающих. Каждый участник может подать заявку в одной или нескольких категориях. Все выступления оцениваются профессиональным жюри по установленным критериям.</p>
                <ul>
                    <li>Возраст участников: от 7 лет</li>
                    <li>Длительность номера: до 5 минут</li>
                    <li>Требуется предоставление фото и аудио/видео материала</li>
                </ul>
            </div>
            
            <div class="about-card fade-in-up">
                <h3>Расписание</h3>
                <p><strong>Регистрация:</strong> до 1 июня 2024</p>
                <p><strong>Отборочный тур:</strong> 10-15 июня 2024</p>
                <p><strong>Финал:</strong> 25 июня 2024</p>
                <p><strong>Награждение:</strong> 25 июня 2024, 18:00</p>
            </div>
            
            <div class="about-card fade-in-up">
                <h3>Категории</h3>
                <div class="categories-list">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-badge"><?php echo htmlspecialchars($category['name']); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="gallery" class="section gallery-section">
    <div class="container">
        <h2 class="section-title fade-in-up">Галерея работ</h2>
        <h3 class="section-title fade-in-up">Наша вокалистка</h3>
      
        <iframe width="1200" height="545" src="https://www.youtube.com/embed/PJ4DVnCJcb4?list=RDPJ4DVnCJcb4" title="Это Нечто! Она Воспроизвела Звуки Природы! | Central Asia&#39;s Got Talent" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        <h3 class="section-title fade-in-up">Наша танцовщица</h3>
          <iframe width="1200" height="545" src="https://www.youtube.com/embed/XaLvulUnwmo" title="Её Танец Вызвал Бурю Эмоций | Central Asia&#39;s Got Talent" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>  
        <div class="gallery-grid">
            <?php if (!empty($gallery)): ?>
                <?php foreach ($gallery as $item): ?>
                    <div class="gallery-item fade-in-up">
                        <div class="gallery-card">
                            <div class="gallery-image">
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="gallery-info">
                                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                <?php if (!empty($item['category_name'])): ?>
                                    <span class="gallery-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['description'])): ?>
                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
           
                
            <?php endif; ?>
        </div>
    </div>
</section>
<section id="reviews" class="section reviews-section">
    <div class="container">
        <h2 class="section-title fade-in-up">Отзывы участников</h2>
        
        <?php if (isset($_SESSION['review_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['review_message_type']; ?> fade-in-up">
                <?php echo $_SESSION['review_message']; ?>
            </div>
            <?php 
            unset($_SESSION['review_message']);
            unset($_SESSION['review_message_type']);
            ?>
        <?php endif; ?>
        <div class="reviews-grid">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card fade-in-up">
                        <div class="review-header">
                            <h4><?php echo htmlspecialchars($review['author_name']); ?></h4>
                            <div class="review-rating">
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    <span class="star">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                        <span class="review-date"><?php echo date('d.m.Y', strtotime($review['created_at'])); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-content">Пока нет отзывов. Станьте первым!</p>
            <?php endif; ?>
        </div>
        <div class="reviews-form-wrapper fade-in-up">
            <button class="btn btn-secondary" id="showReviewForm">Оставить отзыв</button>
            <div class="review-form" id="reviewForm" style="display: none;">
                <h3>Оставить отзыв</h3>
                <form action="submit_review.php" method="POST">
                    <div class="form-group">
                        <label for="author_name">Ваше имя *</label>
                        <input type="text" id="author_name" name="author_name" required>
                    </div>
                    <div class="form-group">
                        <label for="rating">Оценка *</label>
                        <select id="rating" name="rating" required>
                            <option value="5">5 звезд</option>
                            <option value="4">4 звезды</option>
                            <option value="3">3 звезды</option>
                            <option value="2">2 звезды</option>
                            <option value="1">1 звезда</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="review_text">Ваш отзыв *</label>
                        <textarea id="review_text" name="review_text" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить отзыв</button>
                </form>
            </div>
        </div>
    </div>
</section>
<section id="contacts" class="section contacts-section">
    <div class="container">
        <h2 class="section-title fade-in-up">Контакты</h2>
        <div class="contacts-content">
            <div class="contact-info fade-in-up">
                <h3>Контактная информация</h3>
                <div class="contact-item">
                    <strong>Адрес:</strong>
                    <p>пр. Ленина, 61, Барнаул, Алтайский край.</p>
                </div>
                <div class="contact-item">
                    <strong>Телефон:</strong>
                    <p>8(385)2777422</p>
                </div>
                <div class="contact-item">
                    <strong>Email:</strong>
                    <p>asu@mail.ru</p>
                </div>
                <div class="contact-item">
                    <strong>Время работы:</strong>
                    <p>Пн-Пт: 7:00 - 21:00<br>Сб-Вс: 8:00 - 20:00</p>
                </div>
            </div>
            <div class="contact-map fade-in-up">
                <h3>Карта</h3>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d76177.61372767124!2d83.61456476989777!3d53.3692003065893!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x42dda48d940b3e17%3A0x4e9d35a365be72d3!2z0JDQu9GC0LDQudGB0LrQuNC5INCz0L7RgdGD0LTQsNGA0YHRgtCy0LXQvdC90YvQuSDRg9C90LjQstC10YDRgdC40YLQtdGC!5e0!3m2!1sru!2sru!4v1761994112725!5m2!1sru!2sru" 
                            width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

