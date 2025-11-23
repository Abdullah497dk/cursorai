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
	<meta name="description" content="Doğru Hoca - Kişisel Eğitim Portalı. Eğitim videoları, dökümanlar ve faydalı linkler ile öğreniminizi geliştirin.">
	<meta name="keywords" content="Eğitim, Öğretim, Videolar, Dökümanlar, Faydalı Linkler">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<meta name="author" content="Doğru Hoca">
	<link rel="stylesheet" href="static/style.css">
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
	<script type="text/javascript">
		(function(){
			emailjs.init({
				publicKey: "zSgGEoZd5YJ8c7TOP",
			});
		})();
	</script>
    <link rel="icon" type="image/png" href="static/img/logo.png">
	<title>Doğru Hoca - Kişisel Eğitim Portalı</title>
	<style>
		/* User Dropdown Styles */
		.user-menu {
			position: relative;
		}
		
		.user-button {
			display: flex;
			align-items: center;
			gap: 0.5rem;
			cursor: pointer;
		}
		
		.user-dropdown {
			display: none;
			position: absolute;
			top: 100%;
			right: 0;
			background: white;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
			min-width: 180px;
			margin-top: 0.5rem;
			z-index: 1000;
		}
		
		.user-dropdown.active {
			display: block;
		}
		
		.user-dropdown a {
			display: block;
			padding: 0.75rem 1rem;
			color: #2c3e50 !important;
			text-decoration: none;
			transition: background 0.2s;
		}
		
		.user-dropdown a:hover {
			background: #f8f9fa;
		}
	</style>
</head>
<body class="homepage">
	<header>
		<h1>Dogruhoca</h1>
		<div class="header-content">
			<p>Eğitim ve Öğretim Portalına Hoş Geldiniz</p>
		</div>
	</header>

	<nav>
		<div class="hamburger">
			<span></span>
			<span></span>
			<span></span>
		</div>
		<ul class="nav-links">
            <li><a href="Olimpiyat.php">
                <i class="fa-solid fa-trophy"></i> Olimpiyat
            </a></li>
			<li><a href="#hakkimda">
                <i class="fa-solid fa-address-card"></i> Hakkımda
            </a></li>
			<li><a href="#videolar">
                <i class="fa-solid fa-video"></i> Videolar
            </a></li>
			<li><a href="#dokumanlar">
                <i class="fa-solid fa-file-pdf"></i> Dökümanlar
            </a></li>
			<li><a href="#linkler">
                <i class="fa-solid fa-link"></i> Faydalı Linkler
            </a></li>
			<li><a href="#iletisim">
                <i class="fa-solid fa-address-book"></i> İletişim
            </a></li>
			<?php if (isLoggedIn()): ?>
			<li class="user-menu">
				<a href="#" class="user-button" onclick="toggleUserMenu(event)">
					<i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
					<i class="fa-solid fa-chevron-down"></i>
				</a>
				<div class="user-dropdown" id="userDropdown">
					<a href="logout.php">
						<i class="fa-solid fa-sign-out-alt"></i> Çıkış Yap
					</a>
				</div>
			</li>
			<?php else: ?>
			<li><a href="login.php" class="login-button">
				<i class="fa-solid fa-sign-in-alt"></i> Giriş Yap
			</a></li>
			<?php endif; ?>
		</ul>
	</nav>

	<div class="container">
		<section id="hakkimda" class="content-section">
			<h2 class="section-title">Hakkımda</h2>
			<p id="about-text">Merhaba, Ben Doğru Hoca.</p>
		</section>

		<section id="videolar" class="content-section">
			<h2 class="section-title">Eğitim Videoları</h2>
			<div class="video-container" id="video-container"></div>
		</section>

		<section id="dokumanlar" class="content-section">
			<h2 class="section-title">Ders Dökümanları</h2>
			<div class="documents" id="documents-container"></div>
		</section>

		<section id="linkler" class="content-section">
			<h2 class="section-title">Faydalı Linkler</h2>
			<ul class="useful-links" id="links-container"></ul>
		</section>

		<section id="iletisim" class="content-section contact-section">
			<h2 class="section-title">İletişim</h2>
			<div class="contact-details">
				<p><strong>E-posta:</strong> <a href="mailto:dogrumehmet@gmail.com" id="contact-email-link">dogrumehmet@gmail.com</a></p>
				<p><strong>Telefon:</strong> <a href="tel:+905057810760" id="contact-phone-link">+90 505 781 07 60</a></p>
				<p><strong>Adres:</strong> <span id="contact-address">İstanbul, Türkiye</span></p>
			</div>
			<div class="contact-form">
				<h3>Bize Ulaşın</h3>
					<form id="contact-form">
						<label for="name">Adınız:</label>
						<input type="text" id="name" name="name" placeholder="Adınızı girin" required>
						<label for="email">E-posta:</label>
						<input type="email" id="email" name="email" placeholder="E-posta adresinizi girin" required>
						<label for="message">Mesajınız:</label>
						<textarea id="message" name="message" rows="5" placeholder="Mesajınızı yazın" required></textarea>
						<button type="button" onclick="sendEmail()">Gönder</button>
					</form>
			</div>
		</section>
	</div>

	<footer style="display: flex; justify-content: center; align-items: center; gap: 10px;">
		<img src="static/img/logo.png" alt="Developer Logo" style="border-radius: 50%; width: 50px; height: auto;">
		<p>&copy; 2024 Doğru Hoca - Tüm Hakları Saklıdır</p>
		<img src="static/img/dvlp.cc.logo.png" alt="Developer Logo" style="border-radius: 50%; width: 70px; height: auto;">
	</footer>

	<script>
		// User Dropdown Toggle
		function toggleUserMenu(event) {
			event.preventDefault();
			const dropdown = document.getElementById('userDropdown');
			dropdown.classList.toggle('active');
		}
		
		// Close dropdown when clicking outside
		document.addEventListener('click', function(event) {
			const userMenu = document.querySelector('.user-menu');
			const dropdown = document.getElementById('userDropdown');
			if (userMenu && dropdown && !userMenu.contains(event.target)) {
				dropdown.classList.remove('active');
			}
		});
	
		// Hamburger menu functionality
		const hamburger = document.querySelector('.hamburger');
		const navLinks = document.querySelector('.nav-links');
		const navLinkItems = document.querySelectorAll('.nav-links a');

		hamburger.addEventListener('click', () => {
			hamburger.classList.toggle('active');
			navLinks.classList.toggle('active');
			document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : 'auto';
		});

		navLinkItems.forEach(link => {
			link.addEventListener('click', () => {
				hamburger.classList.remove('active');
				navLinks.classList.remove('active');
				document.body.style.overflow = 'auto';
			});
		});

		// Toggle navbar style on scroll
		window.addEventListener('scroll', function () {
			const nav = document.querySelector('nav');
			if (window.scrollY > document.querySelector('header').offsetHeight) {
				nav.classList.add('scrolled');
			} else {
				nav.classList.remove('scrolled');
			}
		});

		// Scroll animations
		document.addEventListener('DOMContentLoaded', () => {
			const elements = document.querySelectorAll('.container, .content-section, footer');
			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('visible');
					}
				});
			}, { threshold: 0.1 });
			elements.forEach(element => observer.observe(element));
			
			// Load dynamic content
			loadVideos();
			loadDocuments();
			loadLinks();
			loadSiteInfo();
		});

		// Smooth scrolling
		document.addEventListener('DOMContentLoaded', () => {
			const internalLinks = document.querySelectorAll('a[href^="#"]');
			internalLinks.forEach(link => {
				link.addEventListener('click', (event) => {
					event.preventDefault();
					const targetId = link.getAttribute('href').substring(1);
					const targetElement = document.getElementById(targetId);
					if (targetElement) {
						targetElement.scrollIntoView({ behavior: 'smooth' });
					}
				});
			});
		});

		// Load Videos from API
		async function loadVideos() {
			try {
				const response = await fetch('api/videos.php');
				const data = await response.json();
				const container = document.getElementById('video-container');
				
				if (data.videos && data.videos.length > 0) {
					container.innerHTML = data.videos.map(video => `
						<div class="video-card">
							<h3>${escapeHtml(video.title)}</h3>
							<iframe class="responsive-iframe" src="${escapeHtml(video.youtube_url)}" 
								title="${escapeHtml(video.title)}" frameborder="0" 
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
								referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
							</iframe>
							<p>${escapeHtml(video.description || '')}</p>
						</div>
					`).join('');
				} else {
					container.innerHTML = '<p style="text-align: center; color: #666;">Henüz video eklenmemiş.</p>';
				}
			} catch (error) {
				console.error('Videolar yüklenemedi:', error);
			}
		}

		// Load Documents from API
		async function loadDocuments() {
			try {
				const response = await fetch('api/documents.php');
				const data = await response.json();
				const container = document.getElementById('documents-container');
				
				if (data.documents && data.documents.length > 0) {
					container.innerHTML = data.documents.map(doc => `
						<div class="document-card">
							<h3>${escapeHtml(doc.title)}</h3>
							<p>${escapeHtml(doc.description || 'Döküman açıklaması')}</p>
							<a href="static/${escapeHtml(doc.file_path)}" target="_blank">İndir</a>
						</div>
					`).join('');
				} else {
					container.innerHTML = '<p style="text-align: center; color: #666;">Henüz döküman eklenmemiş.</p>';
				}
			} catch (error) {
				console.error('Dökümanlar yüklenemedi:', error);
			}
		}

		// Load Links from API
		async function loadLinks() {
			try {
				const response = await fetch('api/links.php');
				const data = await response.json();
				const container = document.getElementById('links-container');
				
				if (data.links && data.links.length > 0) {
					container.innerHTML = data.links.map(link => `
						<li><a href="${escapeHtml(link.url)}" target="_blank">${escapeHtml(link.title)}</a></li>
					`).join('');
				} else {
					container.innerHTML = '<li style="text-align: center; color: #666;">Henüz link eklenmemiş.</li>';
				}
			} catch (error) {
				console.error('Linkler yüklenemedi:', error);
			}
		}

		// Load Site Info from API
		async function loadSiteInfo() {
			try {
				const response = await fetch('api/site-info.php');
				const data = await response.json();
				
				if (data.site_info) {
					if (data.site_info.about_text) {
						document.getElementById('about-text').textContent = data.site_info.about_text;
					}
					if (data.site_info.contact_email) {
						const emailLink = document.getElementById('contact-email-link');
						emailLink.textContent = data.site_info.contact_email;
						emailLink.href = `mailto:${data.site_info.contact_email}`;
					}
					if (data.site_info.contact_phone) {
						const phoneLink = document.getElementById('contact-phone-link');
						phoneLink.textContent = data.site_info.contact_phone;
						phoneLink.href = `tel:${data.site_info.contact_phone.replace(/\s/g, '')}`;
					}
					if (data.site_info.contact_address) {
						document.getElementById('contact-address').textContent = data.site_info.contact_address;
					}
				}
			} catch (error) {
				console.error('Site bilgileri yüklenemedi:', error);
			}
		}

		// Utility function
		function escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}

		// EmailJS integration
		function sendEmail() {
			const name = document.getElementById("name").value;
			const email = document.getElementById("email").value;
			const message = document.getElementById("message").value;

			if (!name || !email || !message) {
				alert("Lütfen tüm alanları doldurun.");
				return;
			}

			const timestamp = new Date().toLocaleString("tr-TR", {
				year: "numeric",
				month: "long",
				day: "numeric",
				hour: "2-digit",
				minute: "2-digit",
				second: "2-digit",
			});

			emailjs.send("service_9s4uy1j", "template_jgm1tc3", {
				name: name,
				email: email,
				message: message,
				time: timestamp,
			}).then(
				(response) => {
					alert("Mesajınız başarıyla gönderildi!");
					document.getElementById("contact-form").reset();
				},
				(error) => {
					alert("Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.");
					console.error("EmailJS Error:", error);
				}
			);
		}
	</script>
</body>
</html>
