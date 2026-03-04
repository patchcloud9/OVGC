<?php
/**
 * Debug toolbar partial.
 * Only rendered when \Core\DebugBar::isVisible() is true (called from main.php).
 */
$dbg      = \Core\DebugBar::getInstance();
$queries  = $dbg->getQueries();
$views    = $dbg->getViews();
$route    = $dbg->getRoute();
$excepts  = $dbg->getExceptions();
$qCount   = count($queries);
$qMs      = $dbg->getTotalQueryMs();
$elapsed  = $dbg->getElapsedMs();
$memMb    = $dbg->getMemoryMb();
$peakMb   = $dbg->getPeakMemoryMb();

// Mask sensitive session / request keys
$sensitiveKeys = ['password', 'pass', 'token', 'key', 'secret', 'hash', 'auth', 'credential'];
$maskValue = function (string $k, $v) use ($sensitiveKeys): string {
    foreach ($sensitiveKeys as $word) {
        if (str_contains(strtolower($k), $word)) {
            return '***';
        }
    }
    return is_array($v) ? json_encode($v) : (string) $v;
};
?>
<div id="dbg-bar" class="dbg-bar dbg-collapsed" role="region" aria-label="Debug toolbar">

    <!-- Summary strip (always visible) -->
    <button id="dbg-toggle" class="dbg-summary" type="button" aria-expanded="false" aria-controls="dbg-panel">
        <span class="dbg-brand">&#128030; Debug</span>
        <span class="dbg-stat <?= $elapsed > 500 ? 'dbg-warn' : '' ?>">
            &#9201; <?= e((string) $elapsed) ?> ms
        </span>
        <span class="dbg-stat <?= $qCount > 20 ? 'dbg-warn' : '' ?>">
            &#128200; <?= e((string) $qCount) ?> quer<?= $qCount === 1 ? 'y' : 'ies' ?>
            <?php if ($qCount): ?>
                <span class="dbg-muted">(<?= e((string) $qMs) ?> ms)</span>
            <?php endif; ?>
        </span>
        <span class="dbg-stat">&#128190; <?= e($memMb) ?> MB</span>
        <?php if ($route): ?>
            <span class="dbg-stat dbg-route">
                <span class="dbg-method dbg-method-<?= e(strtolower($route['method'])) ?>"><?= e($route['method']) ?></span>
                <?= e($route['controller']) ?>::<?= e($route['action']) ?>
            </span>
        <?php endif; ?>
        <?php if ($excepts): ?>
            <span class="dbg-stat dbg-error">&#9888; <?= count($excepts) ?> exception<?= count($excepts) > 1 ? 's' : '' ?></span>
        <?php endif; ?>
        <span class="dbg-chevron" aria-hidden="true">&#9650;</span>
    </button>

    <!-- Expandable panel -->
    <div id="dbg-panel" class="dbg-panel" hidden>

        <!-- Tabs -->
        <div class="dbg-tabs" role="tablist">
            <button class="dbg-tab dbg-tab-active" role="tab" data-panel="dbg-pane-queries" aria-selected="true">
                Queries <span class="dbg-badge"><?= $qCount ?></span>
            </button>
            <button class="dbg-tab" role="tab" data-panel="dbg-pane-route" aria-selected="false">Route</button>
            <button class="dbg-tab" role="tab" data-panel="dbg-pane-views" aria-selected="false">
                Views <span class="dbg-badge"><?= count($views) ?></span>
            </button>
            <button class="dbg-tab" role="tab" data-panel="dbg-pane-session" aria-selected="false">Session</button>
            <button class="dbg-tab" role="tab" data-panel="dbg-pane-request" aria-selected="false">Request</button>
            <?php if ($excepts): ?>
                <button class="dbg-tab dbg-tab-error" role="tab" data-panel="dbg-pane-exceptions" aria-selected="false">
                    Exceptions <span class="dbg-badge"><?= count($excepts) ?></span>
                </button>
            <?php endif; ?>
        </div>

        <!-- Queries pane -->
        <div id="dbg-pane-queries" class="dbg-pane dbg-pane-active" role="tabpanel">
            <?php if (!$queries): ?>
                <p class="dbg-empty">No queries this request.</p>
            <?php else: ?>
                <table class="dbg-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>SQL</th>
                            <th>Params</th>
                            <th>ms</th>
                            <th>Rows</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queries as $i => $q): ?>
                            <tr class="<?= $q['duration_ms'] > 50 ? 'dbg-slow' : '' ?>">
                                <td class="dbg-num"><?= $i + 1 ?></td>
                                <td class="dbg-sql">
                                    <span class="dbg-sql-short" title="<?= e($q['sql']) ?>">
                                        <?= e(mb_strimwidth($q['sql'], 0, 120, '…')) ?>
                                    </span>
                                </td>
                                <td class="dbg-params">
                                    <?php if ($q['params']): ?>
                                        <code><?= e(json_encode(array_values($q['params']))) ?></code>
                                    <?php else: ?>
                                        <span class="dbg-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="dbg-num <?= $q['duration_ms'] > 50 ? 'dbg-warn' : '' ?>">
                                    <?= e((string) $q['duration_ms']) ?>
                                </td>
                                <td class="dbg-num"><?= (int) $q['rows'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Route pane -->
        <div id="dbg-pane-route" class="dbg-pane" role="tabpanel" hidden>
            <?php if (!$route): ?>
                <p class="dbg-empty">No route captured.</p>
            <?php else: ?>
                <table class="dbg-table">
                    <tbody>
                        <tr><th>Method</th><td><span class="dbg-method dbg-method-<?= e(strtolower($route['method'])) ?>"><?= e($route['method']) ?></span></td></tr>
                        <tr><th>URI</th><td><code><?= e($route['uri']) ?></code></td></tr>
                        <tr><th>Pattern</th><td><code><?= e($route['pattern']) ?></code></td></tr>
                        <tr><th>Controller</th><td><code><?= e($route['controller']) ?></code></td></tr>
                        <tr><th>Action</th><td><code><?= e($route['action']) ?></code></td></tr>
                        <tr>
                            <th>Middleware</th>
                            <td>
                                <?php if ($route['middleware']): ?>
                                    <?php foreach ($route['middleware'] as $mw): ?>
                                        <span class="dbg-badge dbg-badge-mw"><?= e($mw) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="dbg-muted">none</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($route['params']): ?>
                            <tr>
                                <th>URL params</th>
                                <td><code><?= e(json_encode($route['params'])) ?></code></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Views pane -->
        <div id="dbg-pane-views" class="dbg-pane" role="tabpanel" hidden>
            <?php if (!$views): ?>
                <p class="dbg-empty">No views recorded.</p>
            <?php else: ?>
                <table class="dbg-table">
                    <thead><tr><th>#</th><th>View</th><th>ms</th></tr></thead>
                    <tbody>
                        <?php foreach ($views as $i => $v): ?>
                            <tr>
                                <td class="dbg-num"><?= $i + 1 ?></td>
                                <td><code><?= e($v['path']) ?></code></td>
                                <td class="dbg-num"><?= e((string) $v['duration_ms']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Session pane -->
        <div id="dbg-pane-session" class="dbg-pane" role="tabpanel" hidden>
            <?php $sess = $_SESSION ?? []; ?>
            <?php if (!$sess): ?>
                <p class="dbg-empty">Session is empty.</p>
            <?php else: ?>
                <table class="dbg-table">
                    <thead><tr><th>Key</th><th>Value</th></tr></thead>
                    <tbody>
                        <?php foreach ($sess as $k => $v): ?>
                            <tr>
                                <td><code><?= e((string) $k) ?></code></td>
                                <td><code><?= e($maskValue((string) $k, $v)) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Request pane -->
        <div id="dbg-pane-request" class="dbg-pane" role="tabpanel" hidden>
            <table class="dbg-table">
                <tbody>
                    <tr><th>Method</th><td><?= e($_SERVER['REQUEST_METHOD'] ?? '') ?></td></tr>
                    <tr><th>URI</th><td><code><?= e($_SERVER['REQUEST_URI'] ?? '') ?></code></td></tr>
                    <tr><th>IP</th><td><?= e($_SERVER['REMOTE_ADDR'] ?? '') ?></td></tr>
                    <tr><th>User-Agent</th><td class="dbg-ua"><?= e($_SERVER['HTTP_USER_AGENT'] ?? '') ?></td></tr>
                    <tr><th>Peak Memory</th><td><?= e($peakMb) ?> MB</td></tr>
                    <tr><th>PHP</th><td><?= e(PHP_VERSION) ?></td></tr>
                </tbody>
            </table>
            <?php if ($_GET): ?>
                <h4 class="dbg-subheading">GET</h4>
                <table class="dbg-table">
                    <thead><tr><th>Key</th><th>Value</th></tr></thead>
                    <tbody>
                        <?php foreach ($_GET as $k => $v): ?>
                            <tr>
                                <td><code><?= e((string) $k) ?></code></td>
                                <td><code><?= e($maskValue((string) $k, $v)) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <?php if ($_POST): ?>
                <h4 class="dbg-subheading">POST</h4>
                <table class="dbg-table">
                    <thead><tr><th>Key</th><th>Value</th></tr></thead>
                    <tbody>
                        <?php foreach ($_POST as $k => $v): ?>
                            <tr>
                                <td><code><?= e((string) $k) ?></code></td>
                                <td><code><?= e($maskValue((string) $k, $v)) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <?php if ($excepts): ?>
        <!-- Exceptions pane -->
        <div id="dbg-pane-exceptions" class="dbg-pane" role="tabpanel" hidden>
            <?php foreach ($excepts as $ex): ?>
                <div class="dbg-exception">
                    <div class="dbg-exception-class"><?= e($ex['class']) ?></div>
                    <div class="dbg-exception-msg"><?= e($ex['message']) ?></div>
                    <div class="dbg-exception-loc"><?= e($ex['file']) ?>:<?= (int) $ex['line'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div><!-- /.dbg-panel -->
</div><!-- /.dbg-bar -->
