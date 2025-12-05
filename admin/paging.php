<?php if ($total_pages > 1): ?>
<div class="pagination-wrapper">
    <div class="d-flex justify-content-center">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous" <?= ($page <= 1) ? 'tabindex="-1"' : ''; ?>>
                        <span aria-hidden="true">&laquo; Sebelumnya</span>
                    </a>
                </li>

                <?php 
                if ($page > 3): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1">1</a>
                    </li>
                    <?php if ($page > 4): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): 
                ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php 
                if ($page < $total_pages - 2): ?>
                    <?php if ($page < $total_pages - 3): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $total_pages; ?>"><?= $total_pages; ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next" <?= ($page >= $total_pages) ? 'tabindex="-1"' : ''; ?>>
                        <span aria-hidden="true">Berikutnya &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    
    <div class="text-center">
        <div class="pagination-info">
            Menampilkan data <strong><?= $offset + 1; ?></strong> - <strong><?= min($offset + $limit, $total_records); ?></strong> dari total <strong><?= $total_records; ?></strong> data
        </div>
    </div>
</div>
<?php endif; ?>