<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="glass-card text-center py-5">
    <div style="font-size: 3.5rem; margin-bottom: 1rem;">🌍</div>
    <h3 class="fw-bold mb-2">Explore Live Global Weather</h3>
    <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
        Type the name of any town or major metropolis above to view real-time conditions, wind speeds, UV levels, and a 7-day forecast.
    </p>

    <!-- Quick Search Suggestions -->
    <div class="mt-4 pt-3 border-top">
        <span class="text-muted d-block mb-2" style="font-size: 0.9rem; font-weight: 500;">
            Or try one of these popular cities:
        </span>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php
                $popularCities = ['Guwahati', 'London', 'Tokyo', 'New York', 'Paris', 'Sydney'];
                foreach ($popularCities as $popular):
            ?>
                <a
                    href="<?= site_url('weather/search') ?>?city=<?= urlencode($popular) ?>"
                    class="btn btn-sm btn-light border rounded-pill px-3 py-1 shadow-sm"
                    style="font-size: 0.85rem; color: #4f46e5; font-weight: 600;"
                >
                    📍 <?= esc($popular) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
