// ========== NAVIGATION ==========
document.addEventListener('DOMContentLoaded', () => {
	// Navigation links
	const navLinks = document.querySelectorAll('.nav-link[data-section]');
	const sections = document.querySelectorAll('.content-section');

	navLinks.forEach(link => {
		link.addEventListener('click', (e) => {
			e.preventDefault();
			const sectionId = link.dataset.section;

			// Update active nav link
			navLinks.forEach(l => l.classList.remove('active'));
			link.classList.add('active');

			// Show selected section
			sections.forEach(section => {
				section.classList.remove('active');
				if (section.id === `${sectionId}-section`) {
					section.classList.add('active');
				}
			});
		});
	});


	// Load initial data
	loadVideos();
	loadDocuments();
	loadLinks();
	loadSiteInfo();
	updateStats();
});

// ========== STATS ==========
async function updateStats() {
	try {
		const [videos, documents, links] = await Promise.all([
			fetch('/api/videos.php').then(r => r.json()),
			fetch('/api/documents.php').then(r => r.json()),
			fetch('/api/links.php').then(r => r.json())
		]);

		document.getElementById('video-count').textContent = videos.videos?.length || 0;
		document.getElementById('document-count').textContent = documents.documents?.length || 0;
		document.getElementById('link-count').textContent = links.links?.length || 0;
	} catch (error) {
		console.error('İstatistikler yüklenemedi:', error);
	}
}

// ========== VIDEOS ==========
async function loadVideos() {
	try {
		const response = await fetch('/api/videos.php');
		const data = await response.json();
		const container = document.getElementById('videos-list');

		if (!data.videos || data.videos.length === 0) {
			container.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 2rem;">Henüz video eklenmemiş</p>';
			return;
		}

		container.innerHTML = data.videos.map(video => `
            <div class="content-item">
                <div class="content-item-info">
                    <h4>${escapeHtml(video.title)}</h4>
                    <p>${escapeHtml(video.description || 'Açıklama yok')}</p>
                </div>
                <div class="content-item-actions">
                    <button class="btn-edit" onclick="editVideo(${video.id})">
                        <i class="fas fa-edit"></i> Düzenle
                    </button>
                    <button class="btn-delete" onclick="deleteVideo(${video.id})">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </div>
            </div>
        `).join('');
	} catch (error) {
		showAlert('Videolar yüklenemedi', 'error');
	}
}

function openVideoModal(videoId = null) {
	const modal = document.getElementById('video-modal');
	const form = document.getElementById('video-form');
	const title = document.getElementById('video-modal-title');

	form.reset();
	document.getElementById('video-id').value = '';

	if (videoId) {
		title.textContent = 'Video Düzenle';
		// Load video data
		fetch(`/api/videos.php`)
			.then(r => r.json())
			.then(data => {
				const video = data.videos.find(v => v.id === videoId);
				if (video) {
					document.getElementById('video-id').value = video.id;
					document.getElementById('video-title').value = video.title;
					document.getElementById('video-url').value = video.youtube_url;
					document.getElementById('video-description').value = video.description || '';
					document.getElementById('video-order').value = video.order_index || 0;
				}
			});
	} else {
		title.textContent = 'Yeni Video Ekle';
	}

	modal.classList.add('active');
}

function closeVideoModal() {
	document.getElementById('video-modal').classList.remove('active');
}

document.getElementById('video-form').addEventListener('submit', async (e) => {
	e.preventDefault();

	const videoId = document.getElementById('video-id').value;
	const data = {
		title: document.getElementById('video-title').value,
		youtube_url: document.getElementById('video-url').value,
		description: document.getElementById('video-description').value,
		order_index: parseInt(document.getElementById('video-order').value)
	};

	try {
		const url = videoId ? `/api/videos.php?id=${videoId}` : '/api/videos.php';
		const method = videoId ? 'PUT' : 'POST';

		const response = await fetch(url, {
			method,
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(data)
		});

		if (response.ok) {
			showAlert(videoId ? 'Video güncellendi' : 'Video eklendi', 'success');
			closeVideoModal();
			loadVideos();
			updateStats();
		} else {
			showAlert('İşlem başarısız', 'error');
		}
	} catch (error) {
		showAlert('Bir hata oluştu', 'error');
	}
});

function editVideo(id) {
	openVideoModal(id);
}

async function deleteVideo(id) {
	if (!confirm('Bu videoyu silmek istediğinizden emin misiniz?')) return;

	try {
		const response = await fetch(`/api/videos.php?id=${id}`, { method: 'DELETE' });
		if (response.ok) {
			showAlert('Video silindi', 'success');
			loadVideos();
			updateStats();
		}
	} catch (error) {
		showAlert('Silme işlemi başarısız', 'error');
	}
}

// ========== DOCUMENTS ==========
async function loadDocuments() {
	try {
		const response = await fetch('/api/documents.php');
		const data = await response.json();
		const container = document.getElementById('documents-list');

		if (!data.documents || data.documents.length === 0) {
			container.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 2rem;">Henüz döküman eklenmemiş</p>';
			return;
		}

		container.innerHTML = data.documents.map(doc => `
            <div class="content-item">
                <div class="content-item-info">
                    <h4>${escapeHtml(doc.title)}</h4>
                    <p>${escapeHtml(doc.description || 'Açıklama yok')}</p>
                </div>
                <div class="content-item-actions">
                    <button class="btn-edit" onclick="editDocument(${doc.id})">
                        <i class="fas fa-edit"></i> Düzenle
                    </button>
                    <button class="btn-delete" onclick="deleteDocument(${doc.id})">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </div>
            </div>
        `).join('');
	} catch (error) {
		showAlert('Dökümanlar yüklenemedi', 'error');
	}
}

function openDocumentModal(docId = null) {
	const modal = document.getElementById('document-modal');
	const form = document.getElementById('document-form');
	const title = document.getElementById('document-modal-title');

	form.reset();
	document.getElementById('document-id').value = '';

	if (docId) {
		title.textContent = 'Döküman Düzenle';
		fetch(`/api/documents.php`)
			.then(r => r.json())
			.then(data => {
				const doc = data.documents.find(d => d.id === docId);
				if (doc) {
					document.getElementById('document-id').value = doc.id;
					document.getElementById('document-title').value = doc.title;
					document.getElementById('document-description').value = doc.description || '';
					document.getElementById('document-order').value = doc.order_index || 0;
				}
			});
	} else {
		title.textContent = 'Yeni Döküman Ekle';
	}

	modal.classList.add('active');
}

function closeDocumentModal() {
	document.getElementById('document-modal').classList.remove('active');
}

document.getElementById('document-form').addEventListener('submit', async (e) => {
	e.preventDefault();

	const docId = document.getElementById('document-id').value;
	const formData = new FormData();

	formData.append('title', document.getElementById('document-title').value);
	formData.append('description', document.getElementById('document-description').value);
	formData.append('order_index', document.getElementById('document-order').value);

	const fileInput = document.getElementById('document-file');
	if (fileInput.files[0]) {
		formData.append('file', fileInput.files[0]);
	} else if (!docId) {
		showAlert('Lütfen bir dosya seçin', 'error');
		return;
	}

	try {
		const url = docId ? `/api/documents.php?id=${docId}` : '/api/documents.php';
		const method = docId ? 'PUT' : 'POST';

		const response = await fetch(url, {
			method,
			body: formData
		});

		if (response.ok) {
			showAlert(docId ? 'Döküman güncellendi' : 'Döküman eklendi', 'success');
			closeDocumentModal();
			loadDocuments();
			updateStats();
		} else {
			showAlert('İşlem başarısız', 'error');
		}
	} catch (error) {
		showAlert('Bir hata oluştu', 'error');
	}
});

function editDocument(id) {
	openDocumentModal(id);
}

async function deleteDocument(id) {
	if (!confirm('Bu dökümanı silmek istediğinizden emin misiniz?')) return;

	try {
		const response = await fetch(`/api/documents.php?id=${id}`, { method: 'DELETE' });
		if (response.ok) {
			showAlert('Döküman silindi', 'success');
			loadDocuments();
			updateStats();
		}
	} catch (error) {
		showAlert('Silme işlemi başarısız', 'error');
	}
}

// ========== LINKS ==========
async function loadLinks() {
	try {
		const response = await fetch('/api/links.php');
		const data = await response.json();
		const container = document.getElementById('links-list');

		if (!data.links || data.links.length === 0) {
			container.innerHTML = '<p style="color: #6b7280; text-align: center; padding: 2rem;">Henüz link eklenmemiş</p>';
			return;
		}

		container.innerHTML = data.links.map(link => `
            <div class="content-item">
                <div class="content-item-info">
                    <h4>${escapeHtml(link.title)}</h4>
                    <p><a href="${escapeHtml(link.url)}" target="_blank" style="color: #4f46e5;">${escapeHtml(link.url)}</a></p>
                </div>
                <div class="content-item-actions">
                    <button class="btn-edit" onclick="editLink(${link.id})">
                        <i class="fas fa-edit"></i> Düzenle
                    </button>
                    <button class="btn-delete" onclick="deleteLink(${link.id})">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </div>
            </div>
        `).join('');
	} catch (error) {
		showAlert('Linkler yüklenemedi', 'error');
	}
}

function openLinkModal(linkId = null) {
	const modal = document.getElementById('link-modal');
	const form = document.getElementById('link-form');
	const title = document.getElementById('link-modal-title');

	form.reset();
	document.getElementById('link-id').value = '';

	if (linkId) {
		title.textContent = 'Link Düzenle';
		fetch(`/api/links.php`)
			.then(r => r.json())
			.then(data => {
				const link = data.links.find(l => l.id === linkId);
				if (link) {
					document.getElementById('link-id').value = link.id;
					document.getElementById('link-title').value = link.title;
					document.getElementById('link-url').value = link.url;
					document.getElementById('link-order').value = link.order_index || 0;
				}
			});
	} else {
		title.textContent = 'Yeni Link Ekle';
	}

	modal.classList.add('active');
}

function closeLinkModal() {
	document.getElementById('link-modal').classList.remove('active');
}

document.getElementById('link-form').addEventListener('submit', async (e) => {
	e.preventDefault();

	const linkId = document.getElementById('link-id').value;
	const data = {
		title: document.getElementById('link-title').value,
		url: document.getElementById('link-url').value,
		order_index: parseInt(document.getElementById('link-order').value)
	};

	try {
		const url = linkId ? `/api/links.php?id=${linkId}` : '/api/links.php';
		const method = linkId ? 'PUT' : 'POST';

		const response = await fetch(url, {
			method,
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(data)
		});

		if (response.ok) {
			showAlert(linkId ? 'Link güncellendi' : 'Link eklendi', 'success');
			closeLinkModal();
			loadLinks();
			updateStats();
		} else {
			showAlert('İşlem başarısız', 'error');
		}
	} catch (error) {
		showAlert('Bir hata oluştu', 'error');
	}
});

function editLink(id) {
	openLinkModal(id);
}

async function deleteLink(id) {
	if (!confirm('Bu linki silmek istediğinizden emin misiniz?')) return;

	try {
		const response = await fetch(`/api/links.php?id=${id}`, { method: 'DELETE' });
		if (response.ok) {
			showAlert('Link silindi', 'success');
			loadLinks();
			updateStats();
		}
	} catch (error) {
		showAlert('Silme işlemi başarısız', 'error');
	}
}

// ========== SITE INFO ==========
async function loadSiteInfo() {
	try {
		const response = await fetch('/api/site-info.php');
		const data = await response.json();

		if (data.site_info) {
			document.getElementById('about_text').value = data.site_info.about_text || '';
			document.getElementById('contact_email').value = data.site_info.contact_email || '';
			document.getElementById('contact_phone').value = data.site_info.contact_phone || '';
			document.getElementById('contact_address').value = data.site_info.contact_address || '';
		}
	} catch (error) {
		showAlert('Site bilgileri yüklenemedi', 'error');
	}
}

document.getElementById('site-info-form').addEventListener('submit', async (e) => {
	e.preventDefault();

	const data = {
		about_text: document.getElementById('about_text').value,
		contact_email: document.getElementById('contact_email').value,
		contact_phone: document.getElementById('contact_phone').value,
		contact_address: document.getElementById('contact_address').value
	};

	try {
		const response = await fetch('/api/site-info.php', {
			method: 'PUT',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(data)
		});

		if (response.ok) {
			showAlert('Site bilgileri güncellendi', 'success');
		} else {
			showAlert('Güncelleme başarısız', 'error');
		}
	} catch (error) {
		showAlert('Bir hata oluştu', 'error');
	}
});

// ========== UTILITIES ==========
function showAlert(message, type = 'info') {
	const alertDiv = document.createElement('div');
	alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#4f46e5'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
	alertDiv.textContent = message;

	document.body.appendChild(alertDiv);

	setTimeout(() => {
		alertDiv.style.animation = 'slideOut 0.3s ease-out';
		setTimeout(() => alertDiv.remove(), 300);
	}, 3000);
}

function escapeHtml(text) {
	const div = document.createElement('div');
	div.textContent = text;
	return div.innerHTML;
}

// Close modals on outside click
document.querySelectorAll('.modal').forEach(modal => {
	modal.addEventListener('click', (e) => {
		if (e.target === modal) {
			modal.classList.remove('active');
		}
	});
});
