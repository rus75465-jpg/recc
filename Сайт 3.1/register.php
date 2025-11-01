<?php
require_once 'config.php';

$pageTitle = 'Регистрация на конкурс';
$message = '';
$messageType = '';

$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    
  
    $errors = [];
    if (empty($full_name)) {
        $errors[] = 'Пожалуйста, укажите ФИО';
    }
    if (empty($phone)) {
        $errors[] = 'Пожалуйста, укажите телефон';
    } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
        $errors[] = 'Неверный формат телефона';
    }
    if ($age < 7 || $age > 100) {
        $errors[] = 'Возраст должен быть от 7 до 100 лет';
    }
    if ($category_id <= 0) {
        $errors[] = 'Пожалуйста, выберите категорию конкурса';
    }
    
    $photo_path = '';
    $music_path = '';
    
    if (empty($errors)) {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            if ($file['size'] > MAX_FILE_SIZE) {
                $errors[] = 'Файл фото слишком большой (макс. 10MB)';
            } elseif (!in_array($file['type'], ALLOWED_PHOTO_TYPES)) {
                $errors[] = 'Неверный тип файла фото. Разрешены: JPEG, PNG, GIF, WebP';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('photo_') . '.' . $ext;
                $target_path = UPLOAD_DIR . 'photos/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $photo_path = 'uploads/photos/' . $filename;
                } else {
                    $errors[] = 'Ошибка при загрузке фото';
                }
            }
        } else {
            $errors[] = 'Пожалуйста, загрузите фото';
        }
        
        if (isset($_FILES['music']) && $_FILES['music']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['music'];
            if ($file['size'] > MAX_FILE_SIZE) {
                $errors[] = 'Файл музыки слишком большой (макс. 10MB)';
            } elseif (!in_array($file['type'], ALLOWED_MUSIC_TYPES)) {
                $errors[] = 'Неверный тип файла музыки. Разрешены: MP3, WAV, OGG';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('music_') . '.' . $ext;
                $target_path = UPLOAD_DIR . 'music/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $music_path = 'uploads/music/' . $filename;
                } else {
                    $errors[] = 'Ошибка при загрузке музыки';
                }
            }
        } else {
            $errors[] = 'Пожалуйста, загрузите музыку';
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO applications (full_name, phone, age, category_id, photo_path, music_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $phone, $age, $category_id, $photo_path, $music_path]);
            
            $message = 'Регистрация успешно завершена! Мы свяжемся с вами в ближайшее время.';
            $messageType = 'success';
            
           
            $full_name = $phone = $age = $category_id = '';
        } catch(PDOException $e) {
            $message = 'Ошибка при сохранении данных. Попробуйте позже.';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}

include 'includes/header.php';
?>

<section class="section register-section">
    <div class="container">
        <h2 class="section-title fade-in-up">Регистрация на конкурс</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> fade-in-up">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="register-form-wrapper fade-in-up">
            <form action="register.php" method="POST" enctype="multipart/form-data" class="register-form">
                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($full_name ?? ''); ?>" 
                           required placeholder="Иванов Иван Иванович">
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($phone ?? ''); ?>" 
                           required placeholder="+7 (999) 123-45-67">
                </div>
                
                <div class="form-group">
                    <label for="age">Возраст *</label>
                    <input type="number" id="age" name="age" 
                           value="<?php echo htmlspecialchars($age ?? ''); ?>" 
                           required min="7" max="100">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Выбор конкурсного номера *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Выберите категорию --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($categories) && !empty($categories[0]['description'])): ?>
                        <small class="form-help"><?php echo htmlspecialchars($categories[0]['description']); ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="photo">Фото участника *</label>
                    <input type="file" id="photo" name="photo" accept="image/*" required>
                    <small class="form-help">Форматы: JPEG, PNG, GIF, WebP. Макс. размер: 10MB</small>
                    <div class="file-preview" id="photoPreview"></div>
                </div>
                
                <div class="form-group">
                    <label for="music">Музыкальный файл *</label>
                    <input type="file" id="music" name="music" accept="audio/*" required>
                    <small class="form-help">Форматы: MP3, WAV, OGG. Макс. размер: 10MB</small>
                    <div class="file-preview" id="musicPreview"></div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large">Отправить заявку</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

