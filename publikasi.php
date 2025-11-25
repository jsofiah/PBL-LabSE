<?php
    require 'config.php';

    $qNav = "SELECT * FROM vw_nav";
    $rNav = pg_query($conn, $qNav);

    $navItems = [];
    while ($rowNav = pg_fetch_assoc($rNav)) {
        $id_nav = $rowNav['id_nav'];
        
        if (!isset($navItems[$id_nav])) {
            $navItems[$id_nav] = [
                'nama_nav' => $rowNav['nama_nav'],
                'url_nav' => $rowNav['url_nav'],
                'subnav' => []
            ];
        }
        
        if ($rowNav['id_subnav']) {
            $navItems[$id_nav]['subnav'][] = [
                'nama_subnav' => $rowNav['nama_subnav'],
                'url_subnav' => $rowNav['url_subnav']
            ];
        }
    }
    $qDosen = "SELECT id_dosen, nama_dosen FROM vw_detail_dosen ORDER BY nama_dosen";
    $rDosen = pg_query($conn, $qDosen);

    while ($d = pg_fetch_assoc($rDosen)) {
        $url = "dosen_detail.php?id=" . $d['id_dosen'];
        $navItems[3]['subnav'][] = [
            'nama_subnav' => $d['nama_dosen'],
            'url_subnav' => $url
        ];
    }
    
    $qLogo = "SELECT * FROM vw_logo_cta";
    $rLogo = pg_query($conn, $qLogo);
    $rowLogo = pg_fetch_assoc($rLogo);

    // --- Ambil ID dosen dari query string (A = single dosen) ---
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        // jika id tidak valid, arahkan ke daftar dosen atau tampilkan pesan
        header('Location: daftar_dosen.php'); // sesuaikan jika ada halaman daftar
        exit;
    }

    // Ambil data dosen
    $qDosenDetail = "SELECT * FROM vw_detail_dosen WHERE id_dosen = $id";
    $rDosenDetail = pg_query($conn, $qDosenDetail);
    $rowDosen = pg_fetch_assoc($rDosenDetail);

    // Ambil publikasi dosen (gunakan view vw_publikasi_dosen)
    $qPublikasi = "SELECT id_publikasi, judul_publikasi, tahun_publikasi, nama_jenispublikasi
                     FROM vw_publikasi_dosen
                     WHERE id_dosen = $id
                     ORDER BY nama_jenispublikasi, tahun_publikasi DESC, judul_publikasi";
    $rPublikasi = pg_query($conn, $qPublikasi);

    // Kelompokkan publikasi menurut jenis (Jurnal, Buku, Media, Lainnya)
    $publikasiGroup = [];
    if ($rPublikasi) { // Pastikan query berhasil
        while ($p = pg_fetch_assoc($rPublikasi)) {
            $jenis = $p['nama_jenispublikasi'] ? $p['nama_jenispublikasi'] : 'Lainnya';
            if (!isset($publikasiGroup[$jenis])) $publikasiGroup[$jenis] = [];
            $publikasiGroup[$jenis][] = $p;
        }
    } else {
        // DEBUG: Tampilkan error jika query gagal
        // echo "<script>console.error('Publikasi Query Error: " . pg_last_error($conn) . "');</script>";
        // Anda harus memastikan view vw_publikasi_dosen sudah didefinisikan dengan benar.
    }

    // Default avatar (pakai file yang kamu upload).
    $default_avatar = 'img/default_dosen.png'; 
    // Pastikan path ini benar di folder img/ Anda, atau ganti ke path yang benar.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Publikasi Dosen - Laboratorium SE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/styleRoot.css">
    <link rel="stylesheet" href="css/stylePublikasi.css">
    <link rel="stylesheet" href="css/styleFooter.css">
    <style>
      /* kecilkan spacing untuk tampilan lebih mirip desain */
      .container-main { max-width: 1400px; margin: 0 auto; padding: 20px; }
      .side-left { width: 260px; }
      .content-right { flex: 1; }
      .card-rounded { border-radius: 14px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
      .dosen-avatar-img { width: 100px; height: 100px; object-fit: cover; border-radius: 10px; }
      .back-link { display:inline-flex; align-items:center; gap:.5rem; color: #222; text-decoration:none; }
    </style>
</head>
<body>
   <div class="logo">
        <?php if ($rowLogo): ?>
            <img src="<?php echo htmlspecialchars($rowLogo['url_logo']); ?>" alt="LABSE" class="logo-img">
        <?php else: ?>
            <img src="img/logo.png" alt="LABSE" class="logo-img">
        <?php endif; ?>
    </div>

    <nav>
    <ul id="nav-list" class="nav-collapse">
        <?php foreach ($navItems as $nav): ?>
            <?php if (count($nav['subnav']) > 0): ?>
                <li class="dropdown">

                    <!-- TOMBOL UTAMA — TIDAK PINDAH HALAMAN -->
                    <a href="#" class="dropbtn" onclick="toggleDropdown(event)">
                        <?php echo htmlspecialchars($nav['nama_nav']); ?>
                        <i class="bi bi-chevron-down"></i>
                    </a>

                    <div class="dropdown-content">
                        <div class="dropdown-scroll overflow-auto" style="max-height: 250px;">
                            <?php foreach ($nav['subnav'] as $sub): ?>
                                <a href="<?php echo htmlspecialchars($sub['url_subnav']); ?>">
                                    <?php echo htmlspecialchars($sub['nama_subnav']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </li>
            <?php else: ?>
                <li>
                    <a href="<?php echo htmlspecialchars($nav['url_nav']); ?>">
                        <?php echo htmlspecialchars($nav['nama_nav']); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>

<script>
function toggleDropdown(event) {
    event.preventDefault();

    // Tutup semua dropdown lain dulu
    document.querySelectorAll(".dropdown").forEach(d => {
        if (d !== event.target.closest(".dropdown")) {
            d.classList.remove("show-dropdown");
        }
    });

    // Baru toggle dropdown yang diklik
    const parent = event.target.closest(".dropdown");
    parent.classList.toggle("show-dropdown");
}

// Tutup semua dropdown kalau klik di luar navbar
document.addEventListener("click", function(e) {
    if (!e.target.closest("nav")) {
        document.querySelectorAll(".dropdown").forEach(d => {
            d.classList.remove("show-dropdown");
        });
    }
});

</script>

<style>
/* Biar sama kaya index */
.dropdown-content {
    display: none;
}
.show-dropdown .dropdown-content {
    display: block;
}
</style>




    <?php if($rowLogo): ?>
        <a href="<?php echo htmlspecialchars($rowLogo['link_cta']); ?>" class="cta-button">
            <span class="cta-text"><?php echo htmlspecialchars($rowLogo['judul_cta']); ?></span>
        </a>
    <?php endif; ?>

    <div class="hero-wrapper">
        <div class="hero-container">
            <div class="hero-frame">
                <img src="img/background_index.jpg" alt="Lab Background">
                <div class="hero-overlay"></div>
                <div class="hero-content">
            <h1 class="hero-title">PROFIL DOSEN</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="container-main mt-4">
      <div class="d-flex gap-4">
        <aside class="side-left">
          <div class="profile-nav mb-3">
            <a href="dosen_detail.php?id=<?php echo $id; ?>" class="btn-nav">Profil</a>
            <a href="penelitian.php?id=<?php echo $id; ?>" class="btn-nav">Penelitian</a>
            <a href="publikasi.php?id=<?php echo $id; ?>" class="btn-nav active">Publikasi</a>
          </div>
        </aside>

        <main class="content-right">
          <a href="javascript:history.back()" class="back-link mb-3"><i class="bi bi-arrow-left-circle"></i> Kembali</a>
          <h3 class="mb-3">PUBLIKASI</h3>

          <div class="card card-rounded mb-3 dosen-info p-3 d-flex align-items-center gap-3">
            <div style="width:120px; text-align:center;">
              <?php
                // pakai foto jika tersedia, jika tidak pakai default avatar lokal
                $foto = ($rowDosen && $rowDosen['url_foto_dosen']) ? $rowDosen['url_foto_dosen'] : $default_avatar;
              ?>
              <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto Dosen" class="dosen-avatar-img">
            </div>
            <div class="flex-grow-1">
              <?php if ($rowDosen): ?>
                <div style="font-size:0.92rem;color:#2a2a2a;font-weight:600;">
                  <?php echo htmlspecialchars($rowDosen['nama_dosen']); ?>
                </div>
                <div style="font-size:0.85rem;color:#1f6fb2;font-weight:700;">
                  <?php echo htmlspecialchars($rowDosen['jabatan_lab']); ?>
                </div>
                <div style="font-size:0.85rem;color:#444;">
                  <?php echo htmlspecialchars($rowDosen['email_dosen']); ?>
                </div>
              <?php else: ?>
                <div>Dosen tidak ditemukan.</div>
              <?php endif; ?>
            </div>
          </div>

          <?php if (empty($publikasiGroup)): ?>
            <div class="card card-rounded p-3">
              Tidak ada publikasi untuk dosen ini.
            </div>
          <?php else: ?>
            <?php foreach ($publikasiGroup as $jenis => $items): ?>
              <div class="card card-rounded mb-3 p-3 content-box">
                <div class="publikasi-header mb-2"><?php echo htmlspecialchars(ucwords(strtolower($jenis))); ?></div>
                <ul class="list-unstyled publikasi-list mb-0">
                  <?php foreach ($items as $it): ?>
                    <li class="publikasi-item mb-3">
                      <div class="publikasi-icon-wrapper">
                            <img src="img/publikasi/bukulogo.png" alt="Ikon Buku Jurnal" class="publikasi-icon-img">
                        </div>
                      <div class="publikasi-title">
                        <?php echo htmlspecialchars($it['judul_publikasi']); ?>
                        <span style="color:#6c757d;font-size:.85rem;font-weight:600;margin-left:8px;">
                          (<?php echo htmlspecialchars($it['tahun_publikasi']); ?>)
                        </span>
                      </div>
                      <div class="publikasi-authors">
                        <?php
                          // Ambil kontributor publikasi
                          $idpub = intval($it['id_publikasi']);
                          
                          // Pastikan view vw_kontributor_publikasi tersedia dan benar
                          $qKontrib = "SELECT d.nama_dosen FROM vw_kontributor_publikasi vk
                                           JOIN dosen d ON d.id_dosen = vk.id_dosen
                                           WHERE vk.id_publikasi = $idpub";
                          $rKontrib = pg_query($conn, $qKontrib);
                          $authors = [];
                          
                          if ($rKontrib) {
                              while ($rowK = pg_fetch_assoc($rKontrib)) {
                                  $authors[] = $rowK['nama_dosen'];
                              }
                          }

                          if (count($authors) > 0) {
                              echo implode(' — ', array_map('htmlspecialchars', $authors));
                          } else if ($rowDosen) { 
                              // jika kontributor tidak ada di view, tampilkan nama dosen utama
                              echo htmlspecialchars($rowDosen['nama_dosen']) . ' (Penulis Tunggal/Kontributor Tidak Terdata)';
                          }
                        ?>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </main>
      </div>
    </div>

    <div id="footer-container"></div>
    <script src="js/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // highlight active nav sebelah kiri
      document.querySelectorAll('.btn-nav').forEach(b => {
        b.addEventListener('click', () => {
          document.querySelectorAll('.btn-nav').forEach(x => x.classList.remove('active'));
          b.classList.add('active');
        });
      });
    </script>
</body>
</html>