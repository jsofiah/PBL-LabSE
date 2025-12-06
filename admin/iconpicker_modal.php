<div class="icon-picker-modal" id="iconPickerModal">
    <div class="icon-picker-content">
        
        <div class="icon-picker-header">
            <h3><i class="fa-solid fa-icons"></i> Pilih Icon Font Awesome</h3>
            <button type="button" class="close-modal" id="closeModal">&times;</button>
        </div>

        <div class="icon-picker-search">
            <div style="position: relative;">
                <div class="style-tabs">
                    <button type="button" class="style-tab active" data-style="fas">
                        <i class="fas fa-star"></i> Solid
                    </button>
                    <button type="button" class="style-tab" data-style="far">
                        <i class="far fa-star"></i> Regular
                    </button>
                    <button type="button" class="style-tab" data-style="fab">
                        <i class="fab fa-github"></i> Brands
                    </button>
                </div>
                <input type="text" class="search-input" id="searchInput" 
                    placeholder="Cari icon... contoh: user, home, star">
            </div>
        </div>

        <div class="icon-picker-body" id="iconPickerBody">
            <div class="loading">
                <i class="fa-solid fa-spinner fa-spin fa-3x"></i>
                <p class="mt-3">Memuat icon Font Awesome...</p>
            </div>
        </div>

        <div class="icon-picker-footer">
            <button type="button" class="btn-cancel" id="cancelBtn">Batal</button>
            <button type="button" class="btn-select" id="selectBtn">
                <i class="fa-solid fa-check"></i> Gunakan Icon Ini
            </button>
        </div>

    </div>
</div>