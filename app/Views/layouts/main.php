<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Weather App') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS styling -->
    <style>
        body {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1f2937;
        }

        .app-header {
            text-align: center;
            padding: 2.5rem 0 1.25rem;
            color: white;
        }

        .app-header a {
            color: white;
            text-decoration: none;
        }

        .app-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .app-header p {
            opacity: 0.9;
            font-size: 1.05rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.6);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control-custom {
            border-radius: 14px;
            padding: 0.85rem 1.25rem;
            font-size: 1.05rem;
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .btn-gradient {
            border-radius: 14px;
            padding: 0.85rem 1.75rem;
            font-size: 1.05rem;
            font-weight: 600;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .btn-gradient:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .attribution {
            text-align: center;
            padding: 2rem 0;
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.9rem;
        }

        .attribution a {
            color: white;
            font-weight: 500;
            text-decoration: underline;
        }

        /* Subtle animations */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Header Section with Home Link -->
    <header class="app-header">
        <h1>
            <a href="<?= site_url('/') ?>">🌤️ Weather App</a>
        </h1>
        <p>Live forecasts powered by CodeIgniter 4 and Open-Meteo</p>
    </header>

    <div class="container fade-in" style="max-width: 820px;">

        <!-- Flash Message Alerts -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <strong>⚠️ Note:</strong> <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <?= esc(session()->getFlashdata('message')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search Card -->
        <div class="glass-card">
            <form id="searchForm" action="<?= site_url('weather/search') ?>" method="get">
                <div class="row g-2">
                    <div class="col-sm-9">
                        <input
                            type="text"
                            name="city"
                            id="cityInput"
                            class="form-control form-control-custom"
                            placeholder="Search by city name (e.g. Guwahati, London, Tokyo)..."
                            value="<?= esc($city ?? '') ?>"
                            autocomplete="off"
                            required
                        >
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" id="searchBtn" class="btn btn-gradient w-100">
                            <span id="btnText">🔍 Search</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Dynamic Content Slot -->
        <div class="content-area">
            <?= $this->renderSection('content') ?>
        </div>

    </div>

    <!-- Attribution -->
    <footer class="attribution">
        Weather data provided by <a href="https://open-meteo.com/" target="_blank" rel="noopener">Open-Meteo.com</a>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Client-side Loading State Handler -->
    <script>
        const searchForm = document.getElementById('searchForm');
        const searchBtn  = document.getElementById('searchBtn');
        const btnText    = document.getElementById('btnText');
        const cityInput  = document.getElementById('cityInput');

        if (searchForm && searchBtn) {
            searchForm.addEventListener('submit', function (e) {
                if (cityInput.value.trim() !== '') {
                    // Show spinner and disable button to prevent multiple submissions
                    btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Searching...';
                    searchBtn.setAttribute('disabled', 'disabled');
                }
            });
        }
    </script>
</body>
</html>
