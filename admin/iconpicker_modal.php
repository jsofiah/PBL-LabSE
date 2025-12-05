<div class="icon-picker-modal" id="iconPickerModal">
    <div class="icon-picker-content">
        <div class="icon-picker-header">
            <h3><i class="fas fa-icons"></i>  Pilih Icon Font Awesome</h3>
            <button type="button" class="close-modal" id="closeModal">&times;</button>
        </div>

        <div class="icon-picker-search">
            <div style="position: relative;">
                <input type="text" class="search-input" id="searchInput" 
                       placeholder="Cari icon... contoh: user, home, star">
            </div>
        </div>

        <div class="icon-picker-body" id="iconPickerBody">
            <div class="loading">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
                <p class="mt-3">Memuat icon Font Awesome...</p>
            </div>
        </div>

        <div class="icon-picker-footer">
            <button type="button" class="btn-cancel" id="cancelBtn">Batal</button>
            <button type="button" class="btn-select" id="selectBtn">
                <i class="fas fa-check"></i> Gunakan Icon Ini
            </button>
        </div>
    </div>
</div>