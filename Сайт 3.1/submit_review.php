<?php
require_once 'config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_name = trim($_POST['author_name'] ?? '');
    $review_text = trim($_POST['review_text'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    
    $errors = [];
    if (empty($author_name)) {
        $errors[] = 'Пожалуйста, укажите ваше имя';
    }
    if (empty($review_text)) {
        $errors[] = 'Пожалуйста, напишите отзыв';
    }
    if ($rating < 1 || $rating > 5) {
        $rating = 5;
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (author_name, review_text, rating) VALUES (?, ?, ?)");
            $stmt->execute([$author_name, $review_text, $rating]);
            
            $message = 'Спасибо за ваш отзыв! Он будет опубликован после модерации.';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Ошибка при отправке отзыва. Попробуйте позже.';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
} else {
    header('Location: index.php');
    exit;
}

$_SESSION['review_message'] = $message;
$_SESSION['review_message_type'] = $messageType;

header('Location: index.php#reviews');
exit;

