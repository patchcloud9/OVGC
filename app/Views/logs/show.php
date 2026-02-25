<!-- hero for log entry -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-file-alt"></i> Log #<?= $log['id'] ?></h1>
            <p class="subtitle is-6 has-text-white">Detailed log entry</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/logs">Logs</a></li>
                <li class="is-active"><a href="#" aria-current="page">Log #<?= $log['id'] ?></a></li>
            </ul>
        </nav>
        
        <!-- back button -->
        <div class="buttons" style="margin-top:0.5rem; margin-bottom:1.5rem;">
            <a href="/logs" class="button is-light is-small">
                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                <span>Back to Logs</span>
            </a>
        </div>
        
        <!-- Log Level Badge -->
        <div class="mb-4">
            <?php
            $levelIcons = [
                'info'    => 'fa-info-circle',
                'warning' => 'fa-exclamation-triangle',
                'error'   => 'fa-times-circle',
                'debug'   => 'fa-bug',
            ];
            $levelColors = [
                'info'    => 'is-info',
                'warning' => 'is-warning',
                'error'   => 'is-danger',
                'debug'   => 'is-dark',
            ];
            $icon = $levelIcons[$log['level']] ?? 'fa-file-alt';
            $color = $levelColors[$log['level']] ?? 'is-light';
            ?>
            <span class="tag is-large <?= $color ?>">
                <span class="icon"><i class="fas <?= $icon ?>"></i></span>
                <span class="ml-2"><?= e(strtoupper($log['level'])) ?></span>
            </span>
        </div>
        
        <!-- Log Message Card -->
        <div class="card mb-4">
            <header class="card-header has-background-light">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-comment-alt"></i></span>
                    <span class="ml-2">Message</span>
                </p>
            </header>
            <div class="card-content">
                <div class="content">
                    <p class="is-size-5 has-text-weight-medium"><?= e($log['message']) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Log Metadata Card -->
        <div class="card mb-4">
            <header class="card-header has-background-light">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-info-circle"></i></span>
                    <span class="ml-2">Metadata</span>
                </p>
            </header>
            <div class="card-content">
                <div class="content">
                    <div class="columns is-mobile">
                        <div class="column">
                            <p class="heading">Log ID</p>
                            <p class="title is-6"><?= $log['id'] ?></p>
                        </div>
                        <div class="column">
                            <p class="heading">Timestamp</p>
                            <p class="title is-6"><?= e($log['created_at'] ?? $log['timestamp'] ?? '') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Context Card -->
        <?php 
        $context = null;
        if (!empty($log['context'])) {
            $context = is_string($log['context']) ? json_decode($log['context'], true) : $log['context'];
        }
        ?>
        
        <?php if ($context && is_array($context) && count($context) > 0): ?>
            <div class="card mb-4">
                <header class="card-header has-background-light">
                    <p class="card-header-title">
                        <span class="icon"><i class="fas fa-tags"></i></span>
                        <span class="ml-2">Context Data</span>
                    </p>
                </header>
                <div class="card-content">
                    <div class="content">
                        <?php foreach ($context as $key => $value): ?>
                            <div class="box is-shadowless" style="border: 1px solid #dbdbdb; margin-bottom: 0.75rem;">
                                <div class="columns is-mobile is-multiline">
                                    <div class="column is-full-mobile is-one-third-tablet">
                                        <p class="heading">
                                            <span class="icon-text">
                                                <span class="icon has-text-info"><i class="fas fa-tag"></i></span>
                                                <span><?= e(ucfirst($key)) ?></span>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="column is-full-mobile is-two-thirds-tablet">
                                        <p class="has-text-grey-dark" style="word-break: break-word;">
                                            <?php if (is_array($value)): ?>
                                                <code style="white-space: pre-wrap; word-break: break-word; background: #f5f5f5; padding: 0.5rem; display: block; border-radius: 4px;"><?= e(json_encode($value, JSON_PRETTY_PRINT)) ?></code>
                                            <?php else: ?>
                                                <?= e($value) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="notification is-light">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-info-circle"></i></span>
                    <span>No context data available for this log entry.</span>
                </span>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="buttons">
            <a href="/logs" class="button is-primary">
                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                <span>Back to Logs</span>
            </a>
        </div>
    </div>
</section>