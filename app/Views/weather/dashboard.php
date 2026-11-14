<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Navigation & Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= site_url('/') ?>" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm border text-decoration-none">
        ← Search Another City
    </a>

    <!-- Day or Night Status Pill -->
    <span class="badge rounded-pill px-3 py-2 <?= ($current['is_day'] ?? 1) == 1 ? 'bg-warning text-dark' : 'bg-dark text-white' ?> shadow-sm">
        <?= ($current['is_day'] ?? 1) == 1 ? '☀️ Daytime' : '🌙 Nighttime' ?>
    </span>
</div>

<!-- City Title -->
<div class="text-center mb-4 text-white">
    <h2 class="fw-bold mb-1 display-6">
        <?= esc($city) ?>
    </h2>
    <?php if (!empty($country)): ?>
        <p class="fs-5 opacity-75 m-0">
            <?= esc($country) ?>
        </p>
    <?php endif; ?>
</div>

<!-- Current Weather Card -->
<div class="glass-card text-center mb-4">
    <div class="py-2">
        <div style="font-size: 4.5rem; line-height: 1.1;">
            <?= esc($current['weather_icon']) ?>
        </div>
        <div class="display-3 fw-bold text-dark my-1">
            <?= esc($current['temperature']) ?>°C
        </div>
        <div class="fs-5 text-secondary fw-medium">
            <?= esc($current['weather_description']) ?>
        </div>
    </div>

    <!-- Weather Details Grid -->
    <div class="row g-3 mt-3 pt-3 border-top">
        <!-- Feels Like -->
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <div class="fs-4 mb-1">🌡️</div>
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.75rem;">Feels Like</div>
                <div class="fs-5 fw-bold text-dark">
                    <?= esc($current['feels_like']) ?>°C
                </div>
            </div>
        </div>

        <!-- Humidity -->
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <div class="fs-4 mb-1">💧</div>
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.75rem;">Humidity</div>
                <div class="fs-5 fw-bold text-dark">
                    <?= esc($current['humidity']) ?>%
                </div>
            </div>
        </div>

        <!-- Wind Speed -->
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <div class="fs-4 mb-1">💨</div>
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.75rem;">Wind Speed</div>
                <div class="fs-5 fw-bold text-dark">
                    <?= esc($current['wind_speed']) ?> <span class="small fs-6 fw-normal text-muted">km/h</span>
                </div>
            </div>
        </div>

        <!-- Precipitation -->
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded-4 text-center border">
                <div class="fs-4 mb-1">🌧️</div>
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.75rem;">Precipitation</div>
                <div class="fs-5 fw-bold text-dark">
                    <?= esc($current['precipitation']) ?> <span class="small fs-6 fw-normal text-muted">mm</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 7-Day Forecast Section -->
<?php if (!empty($forecast)): ?>
<div class="glass-card mb-4">
    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <span>📅</span> 7-Day Forecast
    </h5>

    <div class="row g-2">
        <?php foreach ($forecast as $day): ?>
            <div class="col-6 col-md">
                <div class="p-3 bg-light rounded-4 text-center h-100 d-flex flex-column justify-content-between border">
                    <div>
                        <!-- Day name -->
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                            <?= esc($day['day_name']) ?>
                        </div>

                        <!-- Date -->
                        <div class="text-muted small mb-2" style="font-size: 0.75rem;">
                            <?= esc($day['date']) ?>
                        </div>

                        <!-- Icon -->
                        <div style="font-size: 2.2rem; margin: 0.2rem 0;">
                            <?= esc($day['icon']) ?>
                        </div>

                        <!-- Condition Text -->
                        <div class="text-secondary small fw-medium" style="font-size: 0.75rem; min-height: 2.2rem; display: flex; align-items: center; justify-content: center;">
                            <?= esc($day['condition']) ?>
                        </div>
                    </div>

                    <div class="mt-2 pt-2 border-top">
                        <!-- High and Low Temperatures -->
                        <div class="d-flex justify-content-center align-items-center gap-1" style="font-size: 0.95rem;">
                            <span class="fw-bold text-danger">
                                <?= esc($day['temp_max']) ?>°
                            </span>
                            <span class="text-muted small">/</span>
                            <span class="fw-semibold text-primary">
                                <?= esc($day['temp_min']) ?>°
                            </span>
                        </div>

                        <!-- UV Index Badge -->
                        <div class="mt-2">
                            <span class="badge bg-white text-secondary border rounded-pill" style="font-size: 0.65rem;">
                                UV <?= esc($day['uv_index']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Sunrise and Sunset Card -->
<?php if ($todaySunrise !== '--' || $todaySunset !== '--'): ?>
<div class="glass-card">
    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <span>☀️</span> Sun & Daylight Schedule
    </h5>
    <div class="row g-3">
        <div class="col-6 text-center">
            <div class="p-3 bg-light rounded-4 border">
                <div class="fs-2 mb-1">🌅</div>
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.75rem;">Sunrise</div>
                <div class="fs-5 fw-bold text-dark">
                    <?= esc($todaySunrise) ?>
                </div>
            </div>
        </div>
        <div class="col-6 text-center">
            <div class="p-3 bg-light rounded-4 border">
                <div class="fs-2 mb-1">🌇</div>
                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.75rem;">Sunset</div>
                <div class="fs-5 fw-bold text-dark">
                    <?= esc($todaySunset) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
