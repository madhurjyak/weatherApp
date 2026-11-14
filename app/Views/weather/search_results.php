<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if (empty($cities)): ?>

    <!-- Empty State -->
    <div class="glass-card text-center py-5">
        <div style="font-size: 3.5rem; margin-bottom: 1rem;">🔍</div>
        <h4 class="fw-bold mb-2">No matching cities found</h4>
        <p class="text-muted mb-4">
            We couldn't find any locations matching "<strong><?= esc($city) ?></strong>".
        </p>
        <a href="<?= site_url('/') ?>" class="btn btn-outline-primary rounded-pill px-4">
            ← Try Another Search
        </a>
    </div>

<?php else: ?>

    <!-- Results Listing -->
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <h5 class="fw-bold m-0">
                📍 Matching Locations
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <?= count($cities) ?> result<?= count($cities) > 1 ? 's' : '' ?>
            </span>
        </div>

        <p class="text-muted small mb-3">
            Multiple places may share this name. Click the correct one below to view its weather:
        </p>

        <div class="list-group list-group-flush gap-2">
            <?php foreach ($cities as $city_item): ?>
                <a
                    href="<?= site_url('weather/forecast') ?>?lat=<?= esc($city_item['latitude']) ?>&lon=<?= esc($city_item['longitude']) ?>&name=<?= urlencode($city_item['name']) ?>&country=<?= urlencode($city_item['country'] ?? '') ?>"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 rounded-3 border"
                    style="transition: all 0.2s ease;"
                >
                    <div>
                        <div class="fw-bold fs-5 text-dark">
                            <?= esc($city_item['name']) ?>
                        </div>
                        <div class="text-muted small">
                            <?php
                                $details = [];
                                if (!empty($city_item['admin1'])) {
                                    $details[] = esc($city_item['admin1']);
                                }
                                if (!empty($city_item['country'])) {
                                    $details[] = esc($city_item['country']);
                                }
                                echo implode(', ', $details);
                            ?>
                            <span class="ms-2 text-secondary" style="font-size: 0.75rem;">
                                (<?= round($city_item['latitude'], 2) ?>°, <?= round($city_item['longitude'], 2) ?>°)
                            </span>
                        </div>
                    </div>

                    <span class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; color: #4f46e5;">
                        ➔
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
