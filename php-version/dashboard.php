        <aside class="sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-graduation-cap"></i> Doğru Hoca</h1>
                <p>Admin Paneli</p>
            </div>

            <nav class="sidebar-nav">
                <a href="#overview" class="nav-link active" data-section="overview">
                    <i class="fas fa-home"></i>
                    <span>Genel Bakış</span>
                </a>
                <a href="#videos" class="nav-link" data-section="videos">
                    <i class="fas fa-video"></i>
                    <span>Videolar</span>
                </a>
                <a href="#documents" class="nav-link" data-section="documents">
                    <i class="fas fa-file-pdf"></i>
                    <span>Dökümanlar</span>
                </a>
                <a href="#links" class="nav-link" data-section="links">
                    <i class="fas fa-link"></i>
                    <span>Faydalı Linkler</span>
                </a>
                <a href="#site-info" class="nav-link" data-section="site-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Site Bilgileri</span>
                </a>
                <a href="{{ url_for('list_users') }}" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Kullanıcılar</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ url_for('home') }}" class="btn-secondary">
                    <i class="fas fa-globe"></i> Ana Sayfa
                </a>
                <a href="{{ url_for('logout') }}" class="btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Çıkış
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Overview Section -->
            <section id="overview-section" class="content-section active">
                <div class="section-header">
                    <h2><i class="fas fa-chart-line"></i> Genel Bakış</h2>
                    <p class="section-description">Dashboard'a hoş geldiniz! Buradan tüm site içeriğini
                        yönetebilirsiniz.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="fas fa-video"></i></div>
                        <div class="stat-info">
                            <h3 id="video-count">0</h3>
                            <p>Toplam Video</p>
                        </div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon"><i class="fas fa-file-pdf"></i></div>
                        <div class="stat-info">
                            <h3 id="document-count">0</h3>
                            <p>Toplam Döküman</p>
                        </div>
                    </div>
                    <div class="stat-card orange">
                        <div class="stat-icon"><i class="fas fa-link"></i></div>
                        <div class="stat-info">
                            <h3 id="link-count">0</h3>
                            <p>Toplam Link</p>
                        </div>
                    </div>
                </div>

                <div class="welcome-card">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <h3>Nasıl Kullanılır?</h3>
                        <p>Sol menüden yönetmek istediğiniz bölümü seçin. Her bölümde içerik ekleyebilir, düzenleyebilir
                            veya silebilirsiniz.</p>
                    </div>
                </div>
            </section>

            <!-- Videos Section -->
            <section id="videos-section" class="content-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fas fa-video"></i> Video Yönetimi</h2>
                        <p class="section-description">Ana sayfada gösterilecek YouTube videolarını buradan yönetin</p>
                    </div>
                    <button class="btn-primary" onclick="openVideoModal()">
                        <i class="fas fa-plus"></i> Yeni Video Ekle
                    </button>
                </div>
                <div id="videos-list" class="content-list"></div>
            </section>

            <!-- Documents Section -->
            <section id="documents-section" class="content-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fas fa-file-pdf"></i> Döküman Yönetimi</h2>
                        <p class="section-description">PDF, Word veya PowerPoint dosyalarını yükleyin ve paylaşın</p>
                    </div>
                    <button class="btn-primary" onclick="openDocumentModal()">
                        <i class="fas fa-plus"></i> Yeni Döküman Ekle
                    </button>
                </div>
                <div id="documents-list" class="content-list"></div>
            </section>

            <!-- Links Section -->
            <section id="links-section" class="content-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fas fa-link"></i> Faydalı Linkler</h2>
                        <p class="section-description">Öğrenciler için faydalı web sitelerinin linklerini ekleyin</p>
                    </div>
                    <button class="btn-primary" onclick="openLinkModal()">
                        <i class="fas fa-plus"></i> Yeni Link Ekle
                    </button>
                </div>
                <div id="links-list" class="content-list"></div>
            </section>

            <!-- Site Info Section -->
            <section id="site-info-section" class="content-section">
                <div class="section-header">
                    <div>
                        <h2><i class="fas fa-info-circle"></i> Site Bilgileri</h2>
                        <p class="section-description">Ana sayfadaki "Hakkımda" ve "İletişim" bölümlerini buradan
                            düzenleyin</p>
                    </div>
                </div>

                <form id="site-info-form" class="info-form">
                    <div class="form-card">
                        <h3><i class="fas fa-user"></i> Hakkımda Bölümü</h3>
                        <div class="form-group">
                            <label for="about_text">Hakkımda Metni</label>
                            <textarea id="about_text" name="about_text" rows="6" class="form-control"
                                placeholder="Kendiniz hakkında bilgi yazın..."></textarea>
                            <small>Bu metin ana sayfanın "Hakkımda" bölümünde görünecektir</small>
                        </div>
                    </div>

                    <div class="form-card">
                        <h3><i class="fas fa-envelope"></i> İletişim Bilgileri</h3>
                        <div class="form-group">
                            <label for="contact_email">E-posta Adresi</label>
                            <input type="email" id="contact_email" name="contact_email" class="form-control"
                                placeholder="ornek@email.com">
                        </div>
                        <div class="form-group">
                            <label for="contact_phone">Telefon Numarası</label>
                            <input type="text" id="contact_phone" name="contact_phone" class="form-control"
                                placeholder="+90 XXX XXX XX XX">
                        </div>
                        <div class="form-group">
                            <label for="contact_address">Adres</label>
                            <input type="text" id="contact_address" name="contact_address" class="form-control"
                                placeholder="Şehir, Ülke">
                        </div>
                    </div>

                    <button type="submit" class="btn-success">
                        <i class="fas fa-save"></i> Değişiklikleri Kaydet
                    </button>
                </form>
            </section>
        </main>
    </div>

    <!-- Video Modal -->
    <div id="video-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="video-modal-title">Yeni Video Ekle</h3>
                <button class="close-btn" onclick="closeVideoModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="video-form">
                <input type="hidden" id="video-id">
                <div class="form-group">
                    <label for="video-title">Video Başlığı *</label>
                    <input type="text" id="video-title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="video-url">YouTube Embed URL *</label>
                    <input type="url" id="video-url" class="form-control"
                        placeholder="https://www.youtube.com/embed/..." required>
                    <small>YouTube'da video → Paylaş → Yerleştir → URL'yi kopyalayın</small>
                </div>
                <div class="form-group">
                    <label for="video-description">Açıklama</label>
                    <textarea id="video-description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="video-order">Sıra Numarası</label>
                    <input type="number" id="video-order" class="form-control" value="0" min="0">
                    <small>Küçük numara önce gösterilir (0, 1, 2...)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeVideoModal()">İptal</button>
                    <button type="submit" class="btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Document Modal -->
    <div id="document-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="document-modal-title">Yeni Döküman Ekle</h3>
                <button class="close-btn" onclick="closeDocumentModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="document-form" enctype="multipart/form-data">
                <input type="hidden" id="document-id">
                <div class="form-group">
                    <label for="document-title">Döküman Başlığı *</label>
                    <input type="text" id="document-title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="document-description">Açıklama</label>
                    <textarea id="document-description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="document-file">Dosya Seç *</label>
                    <input type="file" id="document-file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx">
                    <small>PDF, Word veya PowerPoint dosyası yükleyebilirsiniz</small>
                </div>
                <div class="form-group">
                    <label for="document-order">Sıra Numarası</label>
                    <input type="number" id="document-order" class="form-control" value="0" min="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeDocumentModal()">İptal</button>
                    <button type="submit" class="btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Link Modal -->
    <div id="link-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="link-modal-title">Yeni Link Ekle</h3>
                <button class="close-btn" onclick="closeLinkModal()"><i class="fas fa-times"></i></button>
            </div>
            <form id="link-form">
                <input type="hidden" id="link-id">
                <div class="form-group">
                    <label for="link-title">Link Başlığı *</label>
                    <input type="text" id="link-title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="link-url">URL *</label>
                    <input type="url" id="link-url" class="form-control" placeholder="https://..." required>
                </div>
                <div class="form-group">
                    <label for="link-order">Sıra Numarası</label>
                    <input type="number" id="link-order" class="form-control" value="0" min="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeLinkModal()">İptal</button>
                    <button type="submit" class="btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ url_for('static', filename='admin-dashboard.js') }}"></script>
</body>

</html>