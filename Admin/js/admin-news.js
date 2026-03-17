// Permission flags from PHP (set in HTML)
const canEdit = typeof window.canEdit !== 'undefined' ? window.canEdit : true;
const canDelete = typeof window.canDelete !== 'undefined' ? window.canDelete : true;

// Global variables
let categories = [];
let reporters = [];
let allNews = [];
let filteredNews = [];
let currentPage = 1;
let totalPages = 1;
const recordsPerPage = 10;
let isEditMode = false;
let currentEditId = null;

// Initialize TinyMCE
tinymce.init({
    selector: '.tinymce-editor',
    height: 300,
    menubar: false,
    plugins: 'lists link image code',
    toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
    content_style: 'body { font-family: SolaimanLipi, Arial, sans-serif; font-size: 16px; }'
});

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadReporters();
    loadNews();
    document.getElementById('newsForm').addEventListener('submit', handleFormSubmit);
});

async function loadCategories() {
    try {
        const response = await fetch('api.php?action=get_categories');
        categories = await response.json();
        const select = document.getElementById('category_id');
        const filter = document.getElementById('category-filter');
        categories.forEach(cat => {
            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            filter.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        });
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

async function loadReporters() {
    try {
        const response = await fetch('api.php?action=get_reporters');
        reporters = await response.json();
        const select = document.getElementById('reporter_id');
        reporters.forEach(rep => {
            select.innerHTML += `<option value="${rep.id}">${rep.name}</option>`;
        });
    } catch (error) {
        console.error('Error loading reporters:', error);
    }
}

async function loadNews() {
    try {
        const response = await fetch('api.php?action=get_news');
        allNews = await response.json();
        filteredNews = [...allNews];
        updatePagination();
        displayNews();
    } catch (error) {
        showMessage('error', 'সংবাদ লোড করতে ব্যর্থ');
    }
}

function dateWithAgo(dateString) {
    const now = new Date();
    const past = new Date(dateString);
    const diffSeconds = Math.floor((now - past) / 1000);
    let ago = '';
    if (diffSeconds < 60) ago = diffSeconds + ' সেকেন্ড আগে';
    else if (diffSeconds < 3600) ago = Math.floor(diffSeconds / 60) + ' মিনিট আগে';
    else if (diffSeconds < 86400) ago = Math.floor(diffSeconds / 3600) + ' ঘণ্টা আগে';
    else if (diffSeconds < 2592000) ago = Math.floor(diffSeconds / 86400) + ' দিন আগে';
    else if (diffSeconds < 31536000) ago = Math.floor(diffSeconds / 2592000) + ' মাস আগে';
    else ago = Math.floor(diffSeconds / 31536000) + ' বছর আগে';
    const date = past.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    return `${date} · ${ago}`;
}

function displayNews() {
    const tbody = document.getElementById('news-table-body');
    if (!filteredNews || filteredNews.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:#666;">কোনো নিবন্ধ পাওয়া যায়নি</td></tr>';
        return;
    }
    const startIndex = (currentPage - 1) * recordsPerPage;
    const currentRecords = filteredNews.slice(startIndex, startIndex + recordsPerPage);
    tbody.innerHTML = currentRecords.map(article => {
        const category = categories.find(c => c.id == article.category_id);
        const reporter = reporters.find(r => r.id == article.reporter_id);
        const dateAgo = dateWithAgo(article.created_at);
        return `
            <tr>
                <td><strong>#${article.id}</strong></td>
                <td style="max-width:300px;">
                    <div style="font-weight:600;color:#2c3e50;margin-bottom:4px;">${article.headline}</div>
                    <div style="font-size:12px;color:#666;">${article.short_description || ''}</div>
                </td>
                <td><span style="background:#e9ecef;padding:4px 8px;border-radius:12px;font-size:12px;">${category ? category.name : 'N/A'}</span></td>
                <td>${reporter ? reporter.name : 'নিজস্ব প্রতিবেদক'}</td>
                <td><div style="font-size:13px;color:#666;">${dateAgo}</div></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" onchange="toggleStatus(${article.id}, this)" ${article.is_active == 1 ? 'checked' : ''}>
                        <span class="slider round"></span>
                    </label>
                </td>
                <td class="actions">
                    <button class="btn" onclick="viewNews(${article.id})">👁</button>
                    ${canEdit ? `<button class="btn" onclick="editNews(${article.id})">✏️</button>` : ''}
                    ${canDelete ? `<button class="btn" onclick="deleteNews(${article.id})">🗑️</button>` : ''}
                </td>
            </tr>
        `;
    }).join('');
}

function updatePagination() {
    totalPages = Math.ceil(filteredNews.length / recordsPerPage) || 1;
    document.getElementById('total-records').textContent = filteredNews.length;
    document.getElementById('current-page').textContent = currentPage;
    document.getElementById('total-pages').textContent = totalPages;
    document.getElementById('first-btn').disabled = currentPage === 1;
    document.getElementById('prev-btn').disabled = currentPage === 1;
    document.getElementById('next-btn').disabled = currentPage === totalPages;
    document.getElementById('last-btn').disabled = currentPage === totalPages;

    const pageNumbers = document.getElementById('page-numbers');
    pageNumbers.innerHTML = '';
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    for (let i = startPage; i <= endPage; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.onclick = () => changePage(i);
        if (i === currentPage) button.classList.add('active');
        pageNumbers.appendChild(button);
    }
}

function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    displayNews();
    updatePagination();
}

function applyFilters() {
    const search = document.getElementById('search-filter').value.toLowerCase();
    const dateFrom = document.getElementById('date-from-filter').value;
    const dateTo = document.getElementById('date-to-filter').value;
    const catFilter = document.getElementById('category-filter').value;
    
    filteredNews = allNews.filter(article => {
        const matchSearch = !search || article.headline.toLowerCase().includes(search);
        const matchCat = !catFilter || article.category_id == catFilter;
        const articleDate = new Date(article.created_at).toISOString().split('T')[0];
        const matchDateFrom = !dateFrom || articleDate >= dateFrom;
        const matchDateTo = !dateTo || articleDate <= dateTo;
        return matchSearch && matchCat && matchDateFrom && matchDateTo;
    });
    currentPage = 1;
    updatePagination();
    displayNews();
}

function clearFilters() {
    document.getElementById('search-filter').value = '';
    document.getElementById('date-from-filter').value = '';
    document.getElementById('date-to-filter').value = '';
    document.getElementById('category-filter').value = '';
    filteredNews = [...allNews];
    currentPage = 1;
    updatePagination();
    displayNews();
}

async function handleFormSubmit(e) {
    e.preventDefault();
    tinymce.triggerSave();
    const formData = new FormData(this);
    formData.append('action', isEditMode ? 'update_news' : 'create_news');
    if (isEditMode) formData.append('id', currentEditId);
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = isEditMode ? 'আপডেট হচ্ছে...' : 'প্রকাশ হচ্ছে...';
    submitBtn.disabled = true;

    try {
        const response = await fetch('api.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            showMessage('success', isEditMode ? 'নিবন্ধ আপডেট হয়েছে!' : 'নিবন্ধ প্রকাশিত হয়েছে!');
            cancelEdit();
            loadNews();
        } else {
            showMessage('error', result.message || 'ত্রুটি হয়েছে');
        }
    } catch (error) {
        showMessage('error', 'সংবাদ সংরক্ষণ করতে ব্যর্থ');
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
}

async function editNews(id) {
    try {
        if (!categories.length) await loadCategories();
        if (!reporters.length) await loadReporters();

        const response = await fetch(`api.php?action=get_news&id=${id}`);
        const news = await response.json();
        if (!news || !news.id) throw new Error('Invalid data');

        isEditMode = true;
        currentEditId = news.id;
        document.getElementById('form-section').classList.add('edit-mode');
        
        document.getElementById('category_id').value = news.category_id || '';
        document.getElementById('reporter_id').value = news.reporter_id || '';
        document.getElementById('headline').value = news.headline || '';
        document.getElementById('short_description').value = news.short_description || '';
        document.getElementById('slug').value = news.slug || '';
        
        ['news_1', 'news_2', 'news_3', 'news_4'].forEach(id => {
            const editor = tinymce.get(id);
            if (editor) editor.setContent(news[id] || '');
        });
        
        document.getElementById('quote_1').value = news.quote_1 || '';
        document.getElementById('quote_2').value = news.quote_2 || '';
        document.getElementById('auture_1').value = news.auture_1 || '';
        document.getElementById('auture_2').value = news.auture_2 || '';
        
        ['image_url', 'image_2', 'image_3', 'image_4', 'image_5'].forEach(field => {
            const titleEl = document.getElementById(field + '_title');
            if (titleEl) titleEl.value = news[field + '_title'] || '';
            
            const preview = document.getElementById('preview_' + field);
            const box = document.getElementById('box_' + field);
            if (preview && news[field]) {
                preview.src = 'img/' + news[field];
                preview.style.display = 'block';
                preview.onerror = () => { preview.style.display = 'none'; };
                if (box) box.classList.add('has-image');
            }
        });

        const sectionTitle = document.querySelector('#form-section .section-title');
        if (sectionTitle) sectionTitle.textContent = 'সংবাদ সম্পাদনা (ID: ' + news.id + ')';
        
        const submitBtn = document.querySelector('#newsForm button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'আপডেট করুন';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-warning');
        }
        
        document.getElementById('form-section').scrollIntoView({ behavior: 'smooth' });
        showMessage('success', 'সম্পাদনার জন্য লোড হয়েছে');
    } catch (error) {
        showMessage('error', 'লোড করতে ব্যর্থ: ' + error.message);
    }
}

function cancelEdit() {
    isEditMode = false;
    currentEditId = null;
    document.getElementById('form-section').classList.remove('edit-mode');
    document.getElementById('newsForm').reset();
    
    ['news_1', 'news_2', 'news_3', 'news_4'].forEach(id => {
        const editor = tinymce.get(id);
        if (editor) editor.setContent('');
    });
    
    document.querySelectorAll('.image-preview').forEach(img => { img.style.display = 'none'; img.src = ''; });
    document.querySelectorAll('.image-upload-box').forEach(box => box.classList.remove('has-image'));
    
    ['delete_image_url', 'delete_image_2', 'delete_image_3', 'delete_image_4', 'delete_image_5'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '0';
    });
    
    const sectionTitle = document.querySelector('#form-section .section-title');
    if (sectionTitle) sectionTitle.textContent = 'নতুন নিবন্ধ তৈরি করুন';
    
    const submitBtn = document.querySelector('#newsForm button[type="submit"]');
    if (submitBtn) {
        submitBtn.textContent = 'সংবাদ প্রকাশ করুন';
        submitBtn.classList.remove('btn-warning');
        submitBtn.classList.add('btn-success');
    }
}

async function deleteNews(id) {
    if (!confirm('আপনি কি নিশ্চিত?')) return;
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_news&id=${id}`
        });
        const result = await response.json();
        if (result.success) {
            showMessage('success', 'মুছে ফেলা হয়েছে');
            loadNews();
        } else {
            showMessage('error', 'মুছতে ব্যর্থ');
        }
    } catch (error) {
        showMessage('error', 'ত্রুটি হয়েছে');
    }
}

async function toggleStatus(id, checkbox) {
    const is_active = checkbox.checked ? 1 : 0;
    checkbox.disabled = true;
    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=toggle_status&id=${id}&is_active=${is_active}`
        });
        const result = await response.json();
        if (!result.success) {
            checkbox.checked = !checkbox.checked;
            showMessage('error', 'স্ট্যাটাস পরিবর্তন ব্যর্থ');
        } else {
            showMessage('success', is_active ? 'প্রকাশিত' : 'অপ্রকাশিত');
        }
    } catch (error) {
        checkbox.checked = !checkbox.checked;
    } finally {
        checkbox.disabled = false;
    }
}

async function viewNews(id) {
    try {
        const response = await fetch(`api.php?action=get_news&id=${id}`);
        const news = await response.json();
        if (!news || !news.id) throw new Error('Invalid data');
        
        let html = '<div class="news-content-wrapper">';
        if (news.headline) html += `<div class='news-view-headline'>${news.headline}</div>`;
        
        let meta = [];
        if (news.created_at) {
            const date = new Date(news.created_at).toLocaleDateString('bn-BD', { year: 'numeric', month: 'short', day: 'numeric' });
            meta.push(`তারিখ: ${date}`);
        }
        const category = categories.find(c => c.id == news.category_id);
        if (category) meta.push(`ক্যাটাগরি: ${category.name}`);
        const reporter = reporters.find(r => r.id == news.reporter_id);
        if (reporter) meta.push(`রিপোর্টার: ${reporter.name}`);
        if (meta.length) html += `<div class='news-view-meta'>${meta.join(' | ')}</div>`;
        
        if (news.short_description) html += `<div style="text-align:center;color:#666;margin-bottom:20px;">${news.short_description}</div>`;
        if (news.image_url) html += `<img class='news-view-img' src='img/${news.image_url}' onerror="this.style.display='none'">`;
        if (news.news_1) html += `<div class='news-view-section'>${news.news_1}</div>`;
        if (news.quote_1) {
            html += `<div class='news-view-quote'>${news.quote_1}</div>`;
            if (news.auture_1) html += `<div class='news-view-author'>— ${news.auture_1}</div>`;
        }
        
        const imgs23 = [news.image_2, news.image_3].filter(Boolean);
        if (imgs23.length) {
            html += `<div class='news-view-gallery'>`;
            imgs23.forEach(img => html += `<img src='img/${img}' onerror="this.style.display='none'">`);
            html += `</div>`;
        }
        
        if (news.news_2) html += `<div class='news-view-section'>${news.news_2}</div>`;
        if (news.news_3) html += `<div class='news-view-section'>${news.news_3}</div>`;
        
        if (news.quote_2) {
            html += `<div class='news-view-quote'>${news.quote_2}</div>`;
            if (news.auture_2) html += `<div class='news-view-author'>— ${news.auture_2}</div>`;
        }
        
        const imgs45 = [news.image_4, news.image_5].filter(Boolean);
        if (imgs45.length) {
            html += `<div class='news-view-gallery'>`;
            imgs45.forEach(img => html += `<img src='img/${img}' onerror="this.style.display='none'">`);
            html += `</div>`;
        }
        
        if (news.news_4) html += `<div class='news-view-section'>${news.news_4}</div>`;
        html += '</div>';
        
        document.getElementById('news-view-body').innerHTML = html;
        document.getElementById('news-view-popup').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        showMessage('error', 'লোড করতে ব্যর্থ');
    }
}

function closeNewsView() {
    document.getElementById('news-view-popup').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('click', function(e) {
    const popup = document.getElementById('news-view-popup');
    if (e.target === popup) closeNewsView();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const popup = document.getElementById('news-view-popup');
        if (popup && !popup.classList.contains('hidden')) closeNewsView();
        if (isEditMode && confirm('সম্পাদনা বাতিল করতে চান?')) cancelEdit();
    }
});

function generateSlug(text) {
    if (!text) return '';
    return text.trim().toLowerCase()
        .replace(/[।!@#$%^&*()+=\[\]{};:'",.<>?\/\\|`~]/g, ' ')
        .replace(/\s+/g, '_')
        .substring(0, 100);
}

function handleImagePreview(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('preview_' + this.id);
        const box = document.getElementById('box_' + this.id);
        const sizeInfo = document.getElementById('size_' + this.id);
        
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.src = ev.target.result;
            preview.style.display = 'block';
            if (box) box.classList.add('has-image');
        };
        reader.readAsDataURL(file);
        
        if (sizeInfo) {
            const size = file.size;
            const sizeStr = size < 1024 ? size + ' B' : size < 1048576 ? (size/1024).toFixed(1) + ' KB' : (size/1048576).toFixed(1) + ' MB';
            sizeInfo.textContent = 'আকার: ' + sizeStr;
        }
    }
}

function removeImage(fieldId) {
    document.getElementById(fieldId).value = '';
    document.getElementById('preview_' + fieldId).style.display = 'none';
    document.getElementById('box_' + fieldId).classList.remove('has-image');
    const deleteField = document.getElementById('delete_' + fieldId);
    if (deleteField) deleteField.value = '1';
    const sizeInfo = document.getElementById('size_' + fieldId);
    if (sizeInfo) sizeInfo.textContent = '';
}

let popupTimeout = null;
function showMessage(type, message) {
    const popup = document.getElementById('popup-message');
    const popupText = document.getElementById('popup-text');
    popupText.textContent = message;
    popup.classList.remove('hidden', 'success', 'error');
    popup.classList.add(type);
    if (popupTimeout) clearTimeout(popupTimeout);
    popupTimeout = setTimeout(hidePopupMessage, 5000);
}

function hidePopupMessage() {
    document.getElementById('popup-message').classList.add('hidden');
}
