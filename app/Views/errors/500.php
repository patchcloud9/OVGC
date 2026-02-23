<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .error-hero {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .error-icon {
            font-size: 8rem;
            animation: shake 5s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            10%, 30%, 50%, 70%, 90% { transform: rotate(-5deg); }
            20%, 40%, 60%, 80% { transform: rotate(5deg); }
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
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        
                        <h1 class="title is-1 has-text-white">500</h1>
                        <h2 class="subtitle is-3 has-text-white-ter mb-5">Internal Server Error</h2>
                        
                        <p class="has-text-white is-size-5 mb-6" style="max-width: 600px; margin: 0 auto;">
                            <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($message)): ?>
                                <?= htmlspecialchars($message) ?>
                            <?php else: ?>
                                We're sorry! Something went wrong on our end. Our team has been notified and we're working to fix it.
                            <?php endif; ?>
                        </p>
                        
                        <!-- Helpful Actions -->
                        <div class="buttons is-centered mb-6">
                            <a href="/" class="button is-white is-medium">
                                <span class="icon"><i class="fas fa-home"></i></span>
                                <span>Go Home</span>
                            </a>
                            <button onclick="location.reload()" class="button is-light is-medium">
                                <span class="icon"><i class="fas fa-redo"></i></span>
                                <span>Try Again</span>
                            </button>
                        </div>
                        
                        <!-- What to do next -->
                        <div class="columns is-centered mt-6">
                            <div class="column is-8">
                                <div class="box" style="background: rgba(255, 255, 255, 0.95);">
                                    <h3 class="title is-5 has-text-centered mb-4">
                                        <span class="icon-text">
                                            <span class="icon has-text-info"><i class="fas fa-question-circle"></i></span>
                                            <span>What You Can Do</span>
                                        </span>
                                    </h3>
                                    <div class="content">
                                        <ul class="has-text-centered" style="max-width: 500px; margin: 0 auto; list-style-position: inside;">
                                            <li>
                                                <span class="icon-text">
                                                    <span class="icon has-text-success"><i class="fas fa-check"></i></span>
                                                    <span>Refresh the page</span>
                                                </span>
                                            </li>
                                            <li>
                                                <span class="icon-text">
                                                    <span class="icon has-text-success"><i class="fas fa-check"></i></span>
                                                    <span>Go to <a href="/">home page</a></span>
                                                </span>
                                            </li>
                                            <li>
                                                <span class="icon-text">
                                                    <span class="icon has-text-success"><i class="fas fa-check"></i></span>
                                                    <span><a href="/contact">Contact support</a></span>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Debug Info (Only in Debug Mode) -->
                        <?php if (defined('APP_DEBUG') && APP_DEBUG): ?>
                        <div class="columns is-centered mt-5">
                            <div class="column is-10">
                                <div class="message is-danger">
                                    <div class="message-header">
                                        <p>
                                            <span class="icon"><i class="fas fa-bug"></i></span>
                                            <span>Debug Information (Only visible in development)</span>
                                        </p>
                                    </div>
                                    <div class="message-body">
                                        <?php if (isset($message)): ?>
                                            <div class="notification is-danger is-light">
                                                <p class="has-text-weight-bold mb-2">Error Message:</p>
                                                <pre style="white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars($message) ?></pre>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($trace)): ?>
                                            <div class="notification is-warning is-light mt-3">
                                                <p class="has-text-weight-bold mb-2">Stack Trace:</p>
                                                <pre style="white-space: pre-wrap; word-break: break-word; font-size: 0.8rem; max-height: 300px; overflow-y: auto;"><?= htmlspecialchars($trace) ?></pre>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <table class="table is-fullwidth is-hoverable mt-3">
                                            <tr>
                                                <td class="has-text-weight-bold" style="width: 150px;">Request Method</td>
                                                <td><code><?= $_SERVER['REQUEST_METHOD'] ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">Request URI</td>
                                                <td><code><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">Timestamp</td>
                                                <td><code><?= date('Y-m-d H:i:s') ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="has-text-weight-bold">PHP Version</td>
                                                <td><code><?= PHP_VERSION ?></code></td>
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
