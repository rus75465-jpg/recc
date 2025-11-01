
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
        
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
            });
        });
    }

    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    
    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            }
        });
    }
    

    const musicInput = document.getElementById('music');
    const musicPreview = document.getElementById('musicPreview');
    
    if (musicInput && musicPreview) {
        musicInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                musicPreview.innerHTML = '<p><strong>Выбран файл:</strong> ' + file.name + '</p>' +
                                       '<p><strong>Размер:</strong> ' + (file.size / 1024 / 1024).toFixed(2) + ' MB</p>';
            }
        });
    }
    
    const showReviewFormBtn = document.getElementById('showReviewForm');
    const reviewForm = document.getElementById('reviewForm');
    
    if (showReviewFormBtn && reviewForm) {
        showReviewFormBtn.addEventListener('click', function() {
            if (reviewForm.style.display === 'none') {
                reviewForm.style.display = 'block';
                reviewForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                showReviewFormBtn.textContent = 'Скрыть форму';
            } else {
                reviewForm.style.display = 'none';
                showReviewFormBtn.textContent = 'Оставить отзыв';
            }
        });
    }
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const fadeElements = document.querySelectorAll('.fade-in-up');
    fadeElements.forEach(el => {
        observer.observe(el);
    });
    
    const registerForm = document.querySelector('.register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const age = document.getElementById('age');
            if (age && (age.value < 7 || age.value > 100)) {
                e.preventDefault();
                alert('Возраст должен быть от 7 до 100 лет');
                age.focus();
                return false;
            }
            
            const phone = document.getElementById('phone');
            if (phone) {
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                if (!phoneRegex.test(phone.value)) {
                    e.preventDefault();
                    alert('Пожалуйста, введите корректный номер телефона');
                    phone.focus();
                    return false;
                }
            }
        });
    }
});

function showMessage(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type;
    alertDiv.textContent = message;
    document.body.insertBefore(alertDiv, document.body.firstChild);
    setTimeout(function() {
        alertDiv.style.transition = 'opacity 0.5s';
        alertDiv.style.opacity = '0';
        setTimeout(function() {
            alertDiv.remove();
        }, 500);
    }, 5000);
}

