<?php
    require 'config.php';

    $query = "SELECT * FROM vw_footer";
    $result = pg_query($conn, $query);
    $row = pg_fetch_assoc($result);
?>
<div class="footer-container">
    <div class="footer-blue">
        <div class="row align-items-start g-4">

            <div class="col-lg-7">
                <div class="d-flex gap-4">
                    <div>
                        <img src="<?= htmlspecialchars($row['url_logo_footer']); ?>" alt="Logo Footer" class="logo-img-footer">
                    </div>

                    <div class="contact-section">
                        <h2><?= htmlspecialchars($row['judul_footer']); ?></h2>

                        <div class="contact-item">
                            <div class="contact-icon"><i class="far fa-clock"></i></div>
                            <div class="contact-text">
                                <?= htmlspecialchars($row['hari_kerja']); ?><br>
                                <?= htmlspecialchars($row['jam_kerja']); ?>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-phone"></i></div>
                            <div class="contact-text">
                                <a href="tel:<?= htmlspecialchars($row['no_telepon1']); ?>">
                                    <?= htmlspecialchars($row['no_telepon1']); ?>
                                </a><br>
                                <a href="tel:<?= htmlspecialchars($row['no_telepon2']); ?>">
                                    <?= htmlspecialchars($row['no_telepon2']); ?>
                                </a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="contact-text">
                                <?= nl2br(htmlspecialchars($row['alamat'])); ?>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div class="contact-text">
                                <a href="mailto:<?= htmlspecialchars($row['email']); ?>">
                                    <?= htmlspecialchars($row['email']); ?>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-5 text-center">
                <div class="maps-box">
                    <iframe src=<?= $row['link_maps']; ?>></iframe>
                </div>

                <div class="social-links mt-3">
                    <a href="<?= htmlspecialchars($row['link_instagram']); ?>" class="social-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="<?= htmlspecialchars($row['link_youtube']); ?>" class="social-link" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="<?= htmlspecialchars($row['link_linkedin']); ?>" class="social-link" target="_blank">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="footer-yellow">
        Copyright &copy; Laboratorium Software Engineer <?= date("Y") ?>
    </div>
</div>
