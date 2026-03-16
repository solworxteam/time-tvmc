<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' - ' : ''; ?>Prayer Times - UK Mosques</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            corePlugins: { preflight: false }
        };
    </script>
    <script src="/js/lucide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            background: #f3f4f6;
            color: #111827;
        }

        .dark body {
            background: #0f172a;
            color: #f8fafc;
        }

        .pt-site-nav {
            background: linear-gradient(to right, #16a34a, #15803d) !important;
            box-shadow: 0 10px 30px rgba(22, 163, 74, 0.18);
        }

        .dark .pt-site-nav {
            background: linear-gradient(to right, #166534, #14532d) !important;
            box-shadow: 0 10px 30px rgba(5, 46, 22, 0.4);
        }

        .pt-site-nav a {
            color: #fff;
            text-decoration: none;
        }

        .pt-brand {
            align-items: center;
            color: #fff;
            display: inline-flex;
            font-size: 1.1rem;
            font-weight: 700;
            gap: 0.6rem;
        }

        .pt-brand-mark {
            align-items: center;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            display: inline-flex;
            height: 2rem;
            justify-content: center;
            width: 2rem;
        }

        .pt-header-link {
            border-radius: 999px;
            color: rgba(255,255,255,.9);
            font-size: .92rem;
            font-weight: 600;
            padding: .45rem .8rem;
            text-decoration: none;
            transition: background-color .18s ease, color .18s ease;
        }

        .pt-header-link:hover,
        .pt-header-link.is-active {
            background: rgba(255,255,255,.16);
            color: #fff;
        }

        .pt-dark-toggle {
            align-items: center;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            height: 2.4rem;
            justify-content: center;
            transition: background-color .18s ease, transform .18s ease;
            width: 2.4rem;
        }

        .pt-dark-toggle:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-1px);
        }

        .pt-page-shell {
            min-height: calc(100vh - 220px);
        }

        .pt-page-shell .container {
            max-width: 1200px;
        }

        .pt-inner-header {
            background: linear-gradient(to right, #16a34a, #15803d);
            color: #fff;
            margin-bottom: 2rem;
            padding: 2rem 0 1.75rem;
        }

        .dark .pt-inner-header {
            background: linear-gradient(to right, #166534, #14532d);
        }

        .pt-inner-header h1 {
            font-size: clamp(1.8rem, 2vw, 2.4rem);
            font-weight: 700;
            margin: 0;
        }

        .pt-inner-header p {
            color: rgba(255, 255, 255, 0.82);
            margin: 0.35rem 0 0;
        }

        .pt-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
        }

        .dark .pt-panel {
            background: #111827;
            border-color: #334155;
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.36);
        }

        .pt-soft-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
        }

        .dark .pt-soft-panel {
            background: #1e293b;
            border-color: #334155;
        }

        .pt-btn-primary {
            align-items: center;
            background: linear-gradient(to right, #16a34a, #15803d);
            border: none;
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-weight: 600;
            gap: 0.45rem;
            justify-content: center;
            padding: 0.7rem 1.15rem;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .pt-btn-primary:hover {
            box-shadow: 0 10px 20px rgba(22, 163, 74, 0.22);
            color: #fff;
            transform: translateY(-1px);
        }

        .pt-btn-secondary {
            align-items: center;
            background: #fff;
            border: 1px solid #86efac;
            border-radius: 999px;
            color: #166534;
            display: inline-flex;
            font-weight: 600;
            gap: 0.45rem;
            justify-content: center;
            padding: 0.7rem 1.15rem;
            text-decoration: none;
            transition: background-color .18s ease, transform .18s ease;
        }

        .pt-btn-secondary:hover {
            background: #f0fdf4;
            color: #166534;
            transform: translateY(-1px);
        }

        .dark .pt-btn-secondary {
            background: #0f172a;
            border-color: #166534;
            color: #86efac;
        }

        .dark .pt-btn-secondary:hover {
            background: #14532d;
            color: #dcfce7;
        }

        .pt-map-card {
            color: inherit;
            display: block;
            text-decoration: none;
        }

        .pt-map-card:hover {
            color: inherit;
        }

        .pt-footer {
            background: linear-gradient(to right, #0f172a, #111827);
            border-top: 1px solid rgba(148, 163, 184, 0.16);
            color: #e5e7eb;
        }

        .dark .pt-footer {
            background: linear-gradient(to right, #020617, #0f172a);
            color: #f8fafc;
            border-top-color: rgba(148, 163, 184, 0.22);
        }

        .pt-footer p {
            color: inherit;
            font-weight: 500;
            margin: 0;
            opacity: 0.95;
        }

        .pt-footer small {
            color: #cbd5e1;
            display: block;
            margin-top: 0.35rem;
        }

        .pt-inline-icon {
            display: inline-flex;
            height: 1rem;
            vertical-align: middle;
            width: 1rem;
        }
    </style>
</head>
<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$navItems = [
    ['href' => '/', 'label' => 'Home'],
    ['href' => '/mosques.php', 'label' => 'Mosques'],
    ['href' => '/nearest.php', 'label' => 'Find Nearest'],
];
$showShellNav = ($pageTitle ?? '') !== 'Home';
?>
<body>
<?php if ($showShellNav): ?>
<header class="pt-site-nav">
    <div class="container py-3">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <a class="pt-brand" href="/">
                <span class="pt-brand-mark" aria-hidden="true">
                    <i data-lucide="landmark" style="width:16px;height:16px"></i>
                </span>
                <span>Local Prayer Times</span>
            </a>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php foreach ($navItems as $item): ?>
                <?php $active = $requestPath === $item['href'] || ($item['href'] !== '/' && strpos($requestPath, $item['href']) === 0); ?>
                <a class="pt-header-link<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo $item['href']; ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>><?php echo sanitize($item['label']); ?></a>
                <?php endforeach; ?>
                <button id="pt-dark-btn" class="pt-dark-toggle" type="button" aria-label="Toggle dark mode">
                    <i id="pt-dark-icon" data-lucide="moon" style="width:18px;height:18px"></i>
                </button>
            </div>
        </div>
    </div>
</header>
<?php endif; ?>

<main class="pt-page-shell">
    <?php if (isset($content)): ?>
        <?php echo $content; ?>
    <?php endif; ?>
</main>

<footer class="pt-footer py-4 mt-5">
    <div class="container text-center">
        <p>&copy; <?php echo date('Y'); ?> Local Prayer Times. All rights reserved.</p>
        <small>Prayer times, mosque locations, and nearest-mosque tools for UK communities.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>
