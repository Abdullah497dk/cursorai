<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olimpiyat - Doğru Hoca</title>
    <link rel="stylesheet" href="static/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="icon" type="image/png" href="static/img/logo.png">
</head>
<body class="homepage">
    <header>
        <h1>Dogruhoca</h1>
        <div class="header-content">
            <p>Olimpiyat Soruları</p>
        </div>
    </header>

    <nav>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="nav-links">
            <li><a href="index.php"><i class="fa-solid fa-home"></i> Ana Sayfa</a></li>
            <li><a href="Olimpiyat.php"><i class="fa-solid fa-trophy"></i> Olimpiyat</a></li>
            <?php if (isLoggedIn()): ?>
            <li class="user-menu">
                <a href="#" class="user-button" onclick="toggleUserMenu(event)">
                    <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <i class="fa-solid fa-chevron-down"></i>
                </a>
                <div class="user-dropdown" id="userDropdown">
                    <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Çıkış Yap</a>
                </div>
            </li>
            <?php else: ?>
            <li><a href="login.php" class="login-button"><i class="fa-solid fa-sign-in-alt"></i> Giriş Yap</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <section class="content-section">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 class="section-title">🏆 Olimpiyat Soruları</h2>
                <p>Matematik ve fen olimpiyatlarına hazırlık soruları</p>
                <?php if (isAdmin()): ?>
                    <button class="add-question-btn" onclick="openQuestionModal()" style="background: #3498db; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-size: 1rem; margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Yeni Soru Ekle
                    </button>
                <?php endif; ?>
            </div>

            <?php if (!isLoggedIn()): ?>
                <div style="text-align: center; padding: 1rem; background: #fff3cd; border-radius: 8px; color: #856404; margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i> Sorulara cevap yazmak için <a href="login.php">giriş yapın</a>
                </div>
            <?php endif; ?>

            <div id="questions-container"></div>
        </section>
    </div>

    <footer style="display: flex; justify-content: center; align-items: center; gap: 10px;">
        <img src="static/img/logo.png" alt="Logo" style="border-radius: 50%; width: 50px; height: auto;">
        <p>&copy; 2024 Doğru Hoca - Tüm Hakları Saklıdır</p>
        <img src="static/img/dvlp.cc.logo.png" alt="Developer Logo" style="border-radius: 50%; width: 70px; height: auto;">
    </footer>

    <!-- Add Question Modal -->
    <div id="question-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; color: #2c3e50;">Yeni Soru Ekle</h3>
                <button class="close-modal" onclick="closeQuestionModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #7f8c8d;">×</button>
            </div>
            <form id="question-form" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="question-text" style="display: block; margin-bottom: 0.5rem; color: #2c3e50; font-weight: 500;">Soru Metni *</label>
                    <textarea id="question-text" name="question_text" required placeholder="Sorunuzu buraya yazın..." style="width: 100%; padding: 0.75rem; border: 1px solid #bdc3c7; border-radius: 6px; font-family: inherit; font-size: 1rem; resize: vertical; min-height: 120px;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="question-image" style="display: block; margin-bottom: 0.5rem; color: #2c3e50; font-weight: 500;">Fotoğraf (İsteğe Bağlı)</label>
                    <input type="file" id="question-image" name="image" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #bdc3c7; border-radius: 6px;">
                    <small style="color: #7f8c8d;">JPG, PNG veya GIF formatında</small>
                </div>
                <div class="modal-buttons" style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeQuestionModal()" style="background: #95a5a6; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer;">İptal</button>
                    <button type="submit" class="btn-primary" style="background: #3498db; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer;">Ekle</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        const isAdmin = <?php echo isAdmin() ? 'true' : 'false'; ?>;

        // User Dropdown Toggle
        function toggleUserMenu(event) {
            event.preventDefault();
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        // Hamburger menu
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Load questions on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadQuestions();
            
            // Make container and content sections visible
            setTimeout(() => {
                const container = document.querySelector('.container');
                const contentSections = document.querySelectorAll('.content-section');
                const footer = document.querySelector('footer');
                
                if (container) container.classList.add('visible');
                contentSections.forEach(section => section.classList.add('visible'));
                if (footer) footer.classList.add('visible');
            }, 100);
        });

        async function loadQuestions() {
            try {
                console.log('Loading questions from API...');
                const response = await fetch('api/Olimpiyat-questions.php');
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Received data:', data);
                
                displayQuestions(data.questions || []);
            } catch (error) {
                console.error('Sorular yüklenemedi:', error);
                const container = document.getElementById('questions-container');
                container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i><p>Sorular yüklenirken bir hata oluştu: ' + error.message + '</p></div>';
            }
        }

        function displayQuestions(questions) {
            console.log('displayQuestions called with:', questions);
            const container = document.getElementById('questions-container');
            console.log('Container element:', container);
            
            if (!container) {
                console.error('Container not found!');
                return;
            }
            
            if (questions.length === 0) {
                container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #7f8c8d;"><i class="fas fa-inbox"></i><p>Henüz soru eklenmemiş</p></div>';
                return;
            }

            try {
                const html = questions.map(q => {
                    console.log('Processing question:', q);
                    return `
                        <div class="video-card" style="margin-bottom: 2rem;">
                            <h3>${escapeHtml(q.question_text)}</h3>
                            ${q.image_path ? `<img src="static/${escapeHtml(q.image_path)}" alt="Soru görseli" style="max-width: 100%; border-radius: 8px; margin: 1rem 0;">` : ''}
                            <p style="font-size: 0.85rem; color: #7f8c8d;">
                                <i class="fas fa-user"></i> ${escapeHtml(q.creator_username || 'Admin')} • 
                                <i class="fas fa-clock"></i> ${formatDate(q.created_at)}
                                ${isAdmin ? `<button onclick="deleteQuestion(${q.id})" style="background: #e74c3c; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer; margin-left: 1rem;"><i class="fas fa-trash"></i> Sil</button>` : ''}
                            </p>
                            
                            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
                                <h4 style="font-size: 1rem; color: #34495e; margin-bottom: 1rem;">
                                    <i class="fas fa-comments"></i> Cevaplar (${(q.answers || []).length})
                                </h4>
                                ${(q.answers || []).map(a => `
                                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem;">
                                        <p style="color: #2c3e50; margin-bottom: 0.5rem;">${escapeHtml(a.answer_text)}</p>
                                        ${a.image_path ? `<img src="static/${escapeHtml(a.image_path)}" alt="Cevap görseli" style="max-width: 100%; border-radius: 8px; margin: 0.5rem 0;">` : ''}
                                        <p style="font-size: 0.85rem; color: #7f8c8d;">
                                            <span style="font-weight: 600; color: #3498db;">${escapeHtml(a.user_name || a.username || 'Anonim')}</span> • ${formatDate(a.created_at)}
                                            ${isAdmin ? `<button onclick="deleteAnswer(${a.id})" style="background: #e74c3c; color: white; border: none; padding: 0.3rem 0.6rem; border-radius: 6px; cursor: pointer; margin-left: 0.5rem; font-size: 0.8rem;"><i class="fas fa-trash"></i></button>` : ''}
                                        </p>
                                    </div>
                                `).join('')}
                                
                                ${isLoggedIn ? `
                                    <div id="answer-form-${q.id}" style="display: none; margin-top: 1rem; padding: 1rem; background: #ecf0f1; border-radius: 8px;">
                                        <textarea id="answer-text-${q.id}" placeholder="Cevabınızı yazın..." style="width: 100%; padding: 0.75rem; border: 1px solid #bdc3c7; border-radius: 6px; font-family: inherit; resize: vertical; min-height: 80px;"></textarea>
                                        <div style="margin-top: 0.75rem;">
                                            <label style="display: block; margin-bottom: 0.5rem; color: #2c3e50; font-weight: 500;">Fotoğraf (İsteğe Bağlı)</label>
                                            <input type="file" id="answer-image-${q.id}" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #bdc3c7; border-radius: 6px; margin-bottom: 0.75rem;">
                                        </div>
                                        <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                                            <button onclick="submitAnswer(${q.id})" style="background: #27ae60; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Gönder</button>
                                            <button onclick="hideAnswerForm(${q.id})" style="background: #95a5a6; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">İptal</button>
                                        </div>
                                    </div>
                                    <button id="show-btn-${q.id}" onclick="showAnswerForm(${q.id})" style="background: #3498db; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-size: 0.9rem; margin-top: 0.5rem;">
                                        <i class="fas fa-reply"></i> Cevap Yaz
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
                
                console.log('Generated HTML length:', html.length);
                container.innerHTML = html;
                console.log('HTML set successfully');
            } catch (error) {
                console.error('Error in displayQuestions:', error);
                container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i><p>Sorular gösterilirken bir hata oluştu: ' + error.message + '</p></div>';
            }
        }

        function showAnswerForm(questionId) {
            document.getElementById(`answer-form-${questionId}`).style.display = 'block';
            document.getElementById(`show-btn-${questionId}`).style.display = 'none';
        }

        function hideAnswerForm(questionId) {
            document.getElementById(`answer-form-${questionId}`).style.display = 'none';
            document.getElementById(`show-btn-${questionId}`).style.display = 'block';
            document.getElementById(`answer-text-${questionId}`).value = '';
            document.getElementById(`answer-image-${questionId}`).value = '';
        }

        async function submitAnswer(questionId) {
            const answerText = document.getElementById(`answer-text-${questionId}`).value.trim();
            const imageFile = document.getElementById(`answer-image-${questionId}`).files[0];
            
            if (!answerText) {
                alert('Lütfen cevabınızı yazın');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('question_id', questionId);
                formData.append('answer_text', answerText);
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                const response = await fetch('api/Olimpiyat-answers.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    loadQuestions();
                } else {
                    const error = await response.json();
                    alert('Cevap gönderilemedi: ' + (error.error || 'Bilinmeyen hata'));
                }
            } catch (error) {
                alert('Bir hata oluştu: ' + error.message);
            }
        }

        function openQuestionModal() {
            document.getElementById('question-modal').style.display = 'flex';
        }

        function closeQuestionModal() {
            document.getElementById('question-modal').style.display = 'none';
            document.getElementById('question-form').reset();
        }

        document.getElementById('question-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            if (!formData.get('question_text').trim()) {
                alert('Lütfen soru metnini girin');
                return;
            }

            try {
                const response = await fetch('api/Olimpiyat-questions.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    closeQuestionModal();
                    loadQuestions();
                } else {
                    const error = await response.json();
                    alert('Soru eklenemedi: ' + (error.error || 'Bilinmeyen hata'));
                }
            } catch (error) {
                alert('Bir hata oluştu: ' + error.message);
            }
        });

        async function deleteQuestion(id) {
            if (!confirm('Bu soruyu silmek istediğinizden emin misiniz? Tüm cevaplar da silinecek.')) return;

            try {
                const response = await fetch(`api/Olimpiyat-questions.php?id=${id}`, { method: 'DELETE' });
                if (response.ok) {
                    loadQuestions();
                }
            } catch (error) {
                alert('Silme işlemi başarısız');
            }
        }

        async function deleteAnswer(id) {
            if (!confirm('Bu cevabı silmek istediğinizden emin misiniz?')) return;

            try {
                const response = await fetch(`api/Olimpiyat-answers.php?id=${id}`, { method: 'DELETE' });
                if (response.ok) {
                    loadQuestions();
                }
            } catch (error) {
                alert('Silme işlemi başarısız');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('tr-TR', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Modal styles
        const style = document.createElement('style');
        style.textContent = `
            .modal.active { display: flex !important; }
            .user-menu { position: relative; }
            .user-dropdown { display: none; position: absolute; top: 100%; right: 0; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 180px; margin-top: 0.5rem; z-index: 1000; }
            .user-dropdown.active { display: block; }
            .user-dropdown a { display: block; padding: 0.75rem 1rem; color: #2c3e50 !important; text-decoration: none; transition: background 0.2s; }
            .user-dropdown a:hover { background: #f8f9fa; }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
