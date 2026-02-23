<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .error-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .error-icon {
            font-size: 8rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .suggestion-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <section class="hero error-hero is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-vcentered">
                    <div class="column has-text-centered">
                        <div class="error-icon has-text-white mb-5">
                            <i class="fas fa-compass"></i>
                        </div>
                        
                        <h1 class="title is-1 has-text-white">404</h1>
                        <h2 class="subtitle is-3 has-text-white-ter mb-5">Page Not Found</h2>
                        
                        <p class="has-text-white is-size-5 mb-6" style="max-width: 600px; margin: 0 auto;">
                            <?php if (isset($message)): ?>
                                <?= htmlspecialchars($message) ?>
                            <?php else: ?>
                                Oops! We couldn't find the page you're looking for. It may have been moved or doesn't exist.
                            <?php endif; ?>
                        </p>
                        
                        <!-- Helpful Actions -->
                        <div class="buttons is-centered mb-6">
                            <a href="/" class="button is-white is-medium">
                                <span class="icon"><i class="fas fa-home"></i></span>
                                <span>Go Home</span>
                            </a>
                            <button onclick="history.back()" class="button is-light is-medium">
                                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                                <span>Go Back</span>
                            </button>
                        </div>
                        
                        <!-- Suggestions -->
                        <div class="columns is-centered mt-6">
                            <div class="column is-8">
                                <div class="box" style="background: rgba(255, 255, 255, 0.95);">
                                    <h3 class="title is-5 has-text-centered mb-4">
                                        <span class="icon-text">
                                            <span class="icon has-text-info"><i class="fas fa-lightbulb"></i></span>
                                            <span>Try These Pages</span>
                                        </span>
                                    </h3>
                                    <div class="columns is-multiline is-centered">
                                        <div class="column is-full-mobile is-one-third-tablet">
                                            <a href="/" class="box suggestion-card has-text-centered">
                                                <span class="icon is-large has-text-primary">
                                                    <i class="fas fa-home fa-2x"></i>
                                                </span>
                                                <p class="has-text-weight-semibold mt-2">Home</p>
                                            </a>
                                        </div>
                                        <div class="column is-full-mobile is-one-third-tablet">
                                            <a href="/about" class="box suggestion-card has-text-centered">
                                                <span class="icon is-large has-text-info">
                                                    <i class="fas fa-info-circle fa-2x"></i>
                                                </span>
                                                <p class="has-text-weight-semibold mt-2">About</p>
                                            </a>
                                        </div>
                                        <div class="column is-full-mobile is-one-third-tablet">
                                            <a href="/contact" class="box suggestion-card has-text-centered">
                                                <span class="icon is-large has-text-success">
                                                    <i class="fas fa-envelope fa-2x"></i>
                                                </span>
                                                <p class="has-text-weight-semibold mt-2">Contact</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Debug Info -->
                        <?php if (defined('APP_DEBUG') && APP_DEBUG): ?>
                        <div class="columns is-centered mt-5">
                            <div class="column is-8">
                                <div class="message is-dark">
                                    <div class="message-header">
                                        <p>
                                            <span class="icon"><i class="fas fa-bug"></i></span>
                                            <span>Debug Information</span>
                                        </p>
                                    </div>
                                    <div class="message-body">
                                        <table class="table is-fullwidth is-hoverable">
                                            <tr>
                                                <td class="has-text-weight-bold" style="width: 150px;">Request Method</td>
                                                <td><code><?= $_SERVER['REQUEST_METHOD'] ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">Request URI</td>
                                                <td><code><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">Referer</td>
                                                <td><code><?= htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'None') ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">IP Address</td>
                                                <td><code><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown') ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">User Agent</td>
                                                <td><code style="font-size: 0.8rem;"><?= htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') ?></code></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
