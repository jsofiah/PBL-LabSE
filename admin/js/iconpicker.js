let selectedIconClass = '';
let allIcons = [];

async function loadFontAwesomeIcons() {
    try {
        const response = await fetch('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
        const cssText = await response.text();
        
        const regex = /\.fa-([a-z0-9-]+):before/g;
        const iconSet = new Set();
        let match;

        while ((match = regex.exec(cssText)) !== null) {
            const iconName = match[1];
            if (!iconName.match(/^(solid|regular|light|thin|brands|duotone|xs|sm|lg|[0-9]x|fw|ul|li|border|pull-left|pull-right|spin|pulse|rotate|flip|stack|inverse|layers|sr-only)$/)) {
                iconSet.add(iconName);
            }
        }

        allIcons = Array.from(iconSet).sort();
        renderIcons(allIcons);

    } catch (error) {
        console.error('Error loading icons:', error);
        document.getElementById('iconPickerBody').innerHTML = `
            <div class="no-results">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <p>Gagal memuat icon. Silakan refresh halaman.</p>
            </div>
        `;
    }
}

function renderIcons(icons) {
    const body = document.getElementById('iconPickerBody');
    
    if (icons.length === 0) {
        body.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p>Icon tidak ditemukan</p>
            </div>
        `;
        return;
    }

    const grid = document.createElement('div');
    grid.className = 'icon-grid';

    icons.forEach(iconName => {
        const iconClass = `fa-solid fa-${iconName}`;
        const item = document.createElement('div');
        item.className = 'icon-item';
        item.dataset.icon = iconClass;

        item.innerHTML = `
            <i class="${iconClass}"></i>
            <span>${iconName}</span>
        `;

        item.addEventListener('click', function() {
            document.querySelectorAll('.icon-item').forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
            selectedIconClass = this.dataset.icon;
        });

        grid.appendChild(item);
    });

    body.innerHTML = '';
    body.appendChild(grid);
}

document.getElementById('searchInput').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    renderIcons(allIcons.filter(i => i.includes(term)));
});

document.getElementById('openPickerBtn').addEventListener('click', () => {
    document.getElementById('iconPickerModal').classList.add('show');
    if (allIcons.length === 0) loadFontAwesomeIcons();
});

function closeModal() {
    document.getElementById('iconPickerModal').classList.remove('show');
}

document.getElementById('closeModal').addEventListener('click', closeModal);
document.getElementById('cancelBtn').addEventListener('click', closeModal);

document.getElementById('selectBtn').addEventListener('click', () => {
    if (!selectedIconClass) {
        alert('Silakan pilih icon terlebih dahulu!');
        return;
    }

    document.getElementById('iconInput').value = selectedIconClass;
    document.getElementById('iconPreview').className = `icon-preview ${selectedIconClass}`;
    document.getElementById('selectedIcon').className = selectedIconClass;
    document.getElementById('selectedCode').textContent = selectedIconClass;
    document.getElementById('selectedDisplay').classList.add('show');
    closeModal();
});

document.getElementById('iconPickerModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
