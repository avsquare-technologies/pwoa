<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --laravel-red: #FF2D20;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #111827;
            /* Deep dark gray */
            color: #e5e7eb;
        }

        .hero-logo {
            color: var(--laravel-red);
            filter: drop-shadow(0 0 15px rgba(255, 45, 32, 0.4));
        }

        .card-custom {
            background-color: #1f2937;
            border: 1px solid #374151;
            transition: transform 0.2s, border-color 0.2s;
        }

        .card-custom:hover {
            transform: translateY(-3px);
            border-color: var(--laravel-red);
        }

        .icon-box {
            background: rgba(255, 45, 32, 0.1);
            color: var(--laravel-red);
            padding: 10px;
            border-radius: 10px;
        }

        .version-info {
            font-size: 0.85rem;
            color: #6b7280;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 justify-content-center align-items-center">

    <div
        style="position: fixed; top: -20%; right: -10%; width: 50%; height: 50%; background: radial-gradient(circle, rgba(255,45,32,0.15) 0%, rgba(0,0,0,0) 70%); z-index: -1;">
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="text-center mb-5">
                    <svg class="hero-logo mb-4" width="60" height="60" viewBox="0 0 512 512" fill="currentColor">
                        <path
                            d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zM240 144c0-8.8 7.2-16 16-16s16 7.2 16 16v138.3l66.3 38.2c7.6 4.4 10.2 14.2 5.8 21.8s-14.2 10.2-21.8 5.8L240 200.7V144z" />
                    </svg>
                    <h1 class="display-6 fw-bold">Welcome to Laravel</h1>
                    <p class="text-secondary">The PHP Framework for Web Artisans</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <a href="https://laravel.com/docs" class="text-decoration-none">
                            <div class="card card-custom h-100 p-4 rounded-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box me-3">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                    </div>
                                    <h5 class="fw-bold text-white mb-0">Documentation</h5>
                                </div>
                                <p class="text-secondary mb-0 small">
                                    Everything you need to know about Laravel. Comprehensive and easy to read.
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="https://laracasts.com" class="text-decoration-none">
                            <div class="card card-custom h-100 p-4 rounded-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box me-3">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                            </path>
                                            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h5 class="fw-bold text-white mb-0">Laracasts</h5>
                                </div>
                                <p class="text-secondary mb-0 small">
                                    Thousands of video tutorials. It's like Netflix for your career.
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="https://laravel-news.com" class="text-decoration-none">
                            <div class="card card-custom h-100 p-4 rounded-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box me-3">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h5 class="fw-bold text-white mb-0">Laravel News</h5>
                                </div>
                                <p class="text-secondary mb-0 small">
                                    Latest news, ecosystem highlights, and tutorials.
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-custom h-100 p-4 rounded-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064">
                                        </path>
                                    </svg>
                                </div>
                                <h5 class="fw-bold text-white mb-0">Vibrant Ecosystem</h5>
                            </div>
                            <p class="text-secondary mb-0 small">
                                Tools like Forge, Vapor, Nova, and Envoyer help you take your projects to the next
                                level.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top border-secondary">
                    <div class="version-info">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                            Built with Bootstrap
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
