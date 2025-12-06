let selectedIconClass = '';
let allIcons = [];
let currentStyle = 'fas';

async function loadFontAwesomeIcons() {
    try {
        const response = await fetch('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
        const cssText = await response.text();
        
        const regex = /\.fa-([a-z0-9-]+):before/g;
        const iconSet = new Set();
        let match;
        
        while ((match = regex.exec(cssText)) !== null) {
            const iconName = match[1];
            if (!iconName.match(/^(solid|regular|light|thin|brands|duotone|xs|sm|lg|[0-9]x|fw|ul|li|border|pull-left|pull-right|spin|pulse|rotate|flip|stack|inverse|layers|sr-only|w|h|size)$/)) {
                iconSet.add(iconName);
            }
        }
        
        const allIconsList = Array.from(iconSet).sort();
        
        const brandIcons = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'github', 
                           'whatsapp', 'telegram', 'discord', 'tiktok', 'google', 'apple', 
                           'microsoft', 'amazon', 'spotify', 'reddit', 'pinterest', 'snapchat',
                           'paypal', 'stripe', 'wordpress', 'drupal', 'android', 'windows',
                           'linux', 'bitcoin', 'ethereum', 'chrome', 'firefox', 'safari'];
        
        allIcons = {
            'fas': allIconsList,
            'far': allIconsList,
            'fab': allIconsList.filter(icon => 
                brandIcons.some(brand => icon.includes(brand)) || 
                icon.match(/^(cc-|reddit|yarn|npm|node|react|angular|vue|bootstrap|css3|html5|js|python|java|php|laravel|sass|less)/)
            )
        };

        console.log(`Total icons loaded: ${allIconsList.length}`);
        console.log(`- Solid (fas): ${allIcons.fas.length}`);
        console.log(`- Regular (far): ${allIcons.far.length}`);
        console.log(`- Brands (fab): ${allIcons.fab.length}`);
        
        renderIcons(allIcons[currentStyle] || []);

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
        const iconClass = `${currentStyle} fa-${iconName}`;
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

document.querySelectorAll('.style-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.style-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        currentStyle = this.dataset.style;
        
        document.getElementById('searchInput').value = '';
        renderIcons(allIcons[currentStyle] || []);
    });
});

document.getElementById('searchInput').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const iconsToFilter = allIcons[currentStyle] || [];
    renderIcons(iconsToFilter.filter(i => i.includes(term)));
});

document.getElementById('openPickerBtn').addEventListener('click', () => {
    document.getElementById('iconPickerModal').classList.add('show');
    if (Object.keys(allIcons).length === 0) loadFontAwesomeIcons();
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