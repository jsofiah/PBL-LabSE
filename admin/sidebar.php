<?php
require '../config.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

$id_admin = $_SESSION['id_admin'];

$queryAdmin = "SELECT username, foto_admin FROM vw_admin_user WHERE id = $id_admin";
$resultAdmin = pg_query($conn, $queryAdmin);
$admin = pg_fetch_assoc($resultAdmin);

$namaAdmin = $admin['username'];
$fotoAdmin = $admin['foto_admin'];
$initial = strtoupper(substr($namaAdmin, 0, 2));
?>
<div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo"><img src="../img/logo_footer.png" alt="logo" style="height: 70px;"></div>
            <h4>LAB SE</h4>
        </div>

        <a href="dashboard.php" class="dashboard-label" style="text-decoration:none; display:block;">
            <i class="fas fa-gauge"></i>  Dashboard
        </a>

        <div class="sidebar-menu">
            <div class="menu-item">
                <div class="menu-link" data-toggle="home">
                    <span><i class="fas fa-home"></i><span class="menu-text">Home</span></span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
                <div class="submenu" id="home">
                    <a href="kelola_nav.php" class="submenu-link"><i class="fas fa-bars"></i> Nav & Subnav</a>
                    <a href="kelola_footer.php" class="submenu-link"><i class="fas fa-shoe-prints"></i> Footer</a>
                    <a href="kelola_logoCTA.php" class="submenu-link"><i class="fas fa-bullhorn"></i> Logo CTA</a>
                    <a href="kelola_admin.php" class="submenu-link"><i class="fas fa-circle-user"></i> Admin</a>
                </div>
            </div>

            <div class="menu-item">
                <div class="menu-link" data-toggle="profil">
                    <span><i class="fas fa-building"></i><span class="menu-text">Profil Laboratorium</span></span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
                <div class="submenu" id="profil">
                    <a href="kelola_konten.php" class="submenu-link"><i class="fas fa-info-circle"></i> Konten Lab</a>
                    <a href="kelola_peran.php" class="submenu-link"><i class="fas fa-lightbulb"></i> Peran Lab</a>
                    <a href="kelola_roadmap.php" class="submenu-link"><i class="fas fa-road"></i> Roadmap</a>
                    <a href="kelola_fasilitas.php" class="submenu-link"><i class="fas fa-door-open"></i> Fasilitas</a>
                    <a href="kelola_mitra.php" class="submenu-link"><i class="fas fa-handshake"></i> Mitra</a>
                    <a href="kelola_galeri.php" class="submenu-link"><i class="fas fa-images"></i> Galeri</a>
                    <a href="kelola_jenisFasilitas.php" class="submenu-link"><i class="fas fa-list-ul"></i> Jenis Fasilitas</a>
                    <a href="kelola_jenisMitra.php" class="submenu-link"><i class="fas fa-list-alt"></i> Jenis Mitra</a>
                </div>
            </div>

            <div class="menu-item">
                <div class="menu-link" data-toggle="dosen">
                    <span><i class="fas fa-chalkboard-teacher"></i><span class="menu-text">Data Dosen</span></span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
                <div class="submenu" id="dosen">
                    <a href="kelola_profil.php" class="submenu-link"><i class="fas fa-user-tie"></i> Profil</a>
                    <a href="kelola_pendidikan.php" class="submenu-link"><i class="fas fa-user-graduate"></i> Riwayat Pendidikan</a>
                    <a href="kelola_sertifikasi.php" class="submenu-link"><i class="fas fa-certificate"></i> Sertifikasi</a>
                    <a href="kelola_penelitian.php" class="submenu-link"><i class="fas fa-flask"></i> Penelitian</a>
                    <a href="kelola_publikasi.php" class="submenu-link"><i class="fas fa-book"></i> Publikasi</a>
                    <a href="kelola_jenisPublikasi.php" class="submenu-link"><i class="fas fa-book-open"></i> Jenis Publikasi</a>
                </div>
            </div>

            <div class="menu-item">
                <div class="menu-link" data-toggle="bidang">
                    <span><i class="fas fa-graduation-cap"></i><span class="menu-text">Bidang Keahlian</span></span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
                <div class="submenu" id="bidang">
                    <a href="kelola_keahlian.php" class="submenu-link"><i class="fas fa-clipboard-list"></i> Daftar Bidang Keahlian</a>
                    <a href="kelola_dosenKeahlian.php" class="submenu-link"><i class="fas fa-chalkboard"></i> Dosen per Bidang</a>
                    <a href="kelola_mhsKeahlian.php" class="submenu-link"><i class="fas fa-user-friends"></i> Mahasiswa per Bidang</a>
                </div>
            </div>

            <div class="menu-item">
                <div class="menu-link" data-toggle="geeks">
                    <span><i class="fas fa-users"></i><span class="menu-text">SE Geeks</span></span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
                <div class="submenu" id="geeks">
                    <a href="kelola_mhs.php" class="submenu-link"><i class="fas fa-user-graduate"></i> Daftar Mahasiswa</a>
                    <a href="kelola_pendaftaran.php" class="submenu-link"><i class="fas fa-user-plus"></i> Pendaftaran SE Geeks</a>
                    <a href="kelola_proyek.php" class="submenu-link"><i class="fas fa-project-diagram"></i> Proyek SE Geeks</a>
                    <a href="kelola_statusPendaftaran.php" class="submenu-link"><i class="fas fa-tasks"></i> Status Pendaftaran</a>
                </div>
            </div>

            <div class="menu-item">
                <div class="menu-link" data-toggle="artikel">
                    <span><i class="fas fa-newspaper"></i><span class="menu-text">Artikel</span></span>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
                <div class="submenu" id="artikel">
                    <a href="kelola_artikel.php" class="submenu-link"><i class="fas fa-newspaper"></i> Artikel</a>
                    <a href="kelola_jenisArtikel.php" class="submenu-link"><i class="fas fa-tag"></i> Jenis Artikel</a>
                </div>
            </div>
        </div>

        <div style="height: 100px;"></div>

        <div class="admin-profile">
            <div class="profile-dropdown" id="profileDropdown">
                <a href="kelola_profil.php" class="profile-item">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="profile-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            <div class="profile-content" id="profileBtn">
                <?php if (!empty($fotoAdmin)): ?>
                    <img src=<?php echo $fotoAdmin; ?> class="profile-img" style="object-fit:cover;">
                <?php else: ?>
                    <div class="profile-img"><?php echo $initial; ?></div>
                <?php endif; ?>

                <div class="profile-info">
                    <div class="profile-name"><?php echo $namaAdmin; ?></div>
                </div>
            </div>

        </div>
        <div class="submenu-panel" id="submenuPanel"></div>
    </div>
