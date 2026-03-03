<?php
$layout = 'main';
?>

<!-- Hero -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">
                <span class="icon-text">
                    <span class="icon has-text-info"><i class="fas fa-envelope"></i></span>
                    <span>Test Email</span>
                </span>
            </h1>
            <p class="subtitle is-6 has-text-white">Send a test message to verify SMTP configuration</p>
        </div>
    </div>
</section>

<div class="section">
    <div class="container" style="max-width:700px;">
        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Test Email</a></li>
            </ul>
        </nav>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <!-- SMTP Configuration Status -->
        <div class="box">
            <h2 class="title is-5"><i class="fas fa-cog"></i> SMTP Configuration</h2>
            <?php if ($configured): ?>
                <table class="table is-fullwidth is-narrow">
                    <tbody>
                        <tr>
                            <th style="width:160px;">Host</th>
                            <td><?= e($mailHost) ?>:<?= e($mailPort) ?></td>
                        </tr>
                        <tr>
                            <th>Encryption</th>
                            <td><?= e(strtoupper($mailEnc)) ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?= e($mailUser) ?></td>
                        </tr>
                        <tr>
                            <th>From Address</th>
                            <td><?= e($mailFrom) ?></td>
                        </tr>
                        <tr>
                            <th>Password</th>
                            <td><span class="has-text-grey">••••••••</span></td>
                        </tr>
                    </tbody>
                </table>
                <p class="has-text-success"><span class="icon"><i class="fas fa-check-circle"></i></span> SMTP constants are defined.</p>
            <?php else: ?>
                <div class="notification is-warning">
                    <p><strong>SMTP is not configured.</strong> Define <code>MAIL_HOST</code>, <code>MAIL_USERNAME</code>, and <code>MAIL_PASSWORD</code> in <code>config/config.php</code> (or the server-side <code>config.php</code>) before sending email.</p>
                    <p class="mt-2">See the ROADMAP for the required constants.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Send Test Email Form -->
        <div class="box">
            <h2 class="title is-5"><i class="fas fa-paper-plane"></i> Send Test Email</h2>
            <form method="POST" action="/admin/test-email">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="label" for="to">Recipient Email Address</label>
                    <div class="control has-icons-left">
                        <input class="input" type="email" id="to" name="to"
                               placeholder="you@example.com"
                               value="<?= e(old('to')) ?>"
                               required>
                        <span class="icon is-left"><i class="fas fa-envelope"></i></span>
                    </div>
                    <p class="help">The test message will be delivered to this address.</p>
                </div>
                <div class="field">
                    <div class="control">
                        <button type="submit" class="button is-primary" <?= $configured ? '' : 'disabled' ?>>
                            <span class="icon"><i class="fas fa-paper-plane"></i></span>
                            <span>Send Test Email</span>
                        </button>
                        <a href="/admin" class="button is-light ml-2">Back to Admin</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
