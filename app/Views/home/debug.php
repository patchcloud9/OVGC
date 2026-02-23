<section class="section" style="padding-top: 1.5rem;">
    <div class="container">        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Debug</a></li>
            </ul>
        </nav>
        
        <h1 class="title"><?= e($title) ?></h1>
        <p class="subtitle">See what's happening behind the scenes</p>
        
        <?php foreach ($debugInfo as $section => $items): ?>
        <div class="box">
            <h3 class="title is-5"><?= e($section) ?></h3>
            <table class="table is-fullwidth is-striped">
                <tbody>
                    <?php if (is_array($items) && !empty($items)): ?>
                        <?php foreach ($items as $key => $value): ?>
                        <tr>
                            <td style="width: 200px;"><strong><?= e($key) ?></strong></td>
                            <td>
                                <?php if (is_array($value)): ?>
                                    <pre><?= e(print_r($value, true)) ?></pre>
                                <?php else: ?>
                                    <code><?= e((string)$value) ?></code>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="has-text-grey">(empty)</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
        
        <div class="box">
            <h3 class="title is-5">Registered Routes</h3>
            <?php 
            $routes = require BASE_PATH . '/config/routes.php';
            foreach ($routes as $method => $methodRoutes): 
            ?>
            <h4 class="title is-6 has-text-info"><?= $method ?></h4>
            <table class="table is-fullwidth is-striped mb-5">
                <thead>
                    <tr>
                        <th>Pattern</th>
                        <th>Controller</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($methodRoutes as $pattern => $handler): ?>
                    <tr>
                        <td><code><?= e($pattern) ?></code></td>
                        <td><?= e($handler[0]) ?></td>
                        <td><?= e($handler[1]) ?>()</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endforeach; ?>
        </div>
    </div>
</section>
