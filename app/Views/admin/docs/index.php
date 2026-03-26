<link rel="stylesheet" href="/assets/css/docs.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/docs.css') ?>">

<!-- Hero -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <div class="is-flex is-justify-content-space-between is-align-items-center is-flex-wrap-wrap" style="gap:0.75rem;">
                <div>
                    <h1 class="title is-3"><i class="fas fa-book-open"></i> Documentation</h1>
                    <p class="subtitle is-6 has-text-white">Admin guides for managing the website</p>
                </div>
                <a href="/admin" class="button is-light is-small">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Admin Panel</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns">
            <!-- Sidebar -->
            <div class="column is-3-desktop is-4-tablet docs-sidebar">
                <div class="box p-3 mb-4">
                    <form action="/admin/docs" method="GET">
                        <div class="field has-addons mb-0">
                            <div class="control is-expanded">
                                <input class="input" type="text" name="q"
                                       placeholder="Search docs…"
                                       value="<?= e($searchQuery) ?>"
                                       autofocus>
                            </div>
                            <div class="control">
                                <button class="button is-primary" type="submit">
                                    <span class="icon"><i class="fas fa-search"></i></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box p-3">
                    <div class="menu">
                        <p class="menu-label">Guides</p>
                        <ul class="menu-list">
                            <?php foreach ($docs as $slug => $doc): ?>
                            <li>
                                <a href="/admin/docs/<?= e($slug) ?>">
                                    <span class="icon is-small"><i class="fas <?= e($doc['icon']) ?>"></i></span>
                                    <?= e($doc['title']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="column">
                <?php if ($searchQuery !== ''): ?>
                    <h2 class="title is-5 mb-3">
                        <?php if (empty($results)): ?>
                            No results for &ldquo;<?= e($searchQuery) ?>&rdquo;
                        <?php else: ?>
                            <?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?> for &ldquo;<?= e($searchQuery) ?>&rdquo;
                        <?php endif; ?>
                    </h2>

                    <?php if (empty($results)): ?>
                        <div class="notification is-light">
                            <p>Try different keywords, or browse a guide from the sidebar.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($results as $r): ?>
                        <div class="doc-search-result">
                            <div class="result-meta">
                                <?= e($r['doc_title']) ?>
                                <?php if ($r['section'] !== $r['doc_title']): ?>
                                    &rsaquo; <?= e($r['section']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="result-title">
                                <a href="/admin/docs/<?= e($r['slug']) ?><?= $r['anchor'] ? '#' . e($r['anchor']) : '' ?>">
                                    <?= e($r['section']) ?>
                                    <i class="fas fa-arrow-right fa-xs ml-1"></i>
                                </a>
                            </div>
                            <div class="result-excerpt"><?= e($r['excerpt']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Welcome / landing state -->
                    <div class="box has-text-centered py-6">
                        <span class="icon is-large mb-3" style="font-size:3rem; color:#ccc;">
                            <i class="fas fa-book-open"></i>
                        </span>
                        <h2 class="title is-4 mt-3">Admin Documentation</h2>
                        <p class="subtitle is-6 has-text-grey">Step-by-step guides for managing the Okanogan Valley Golf Club website.</p>
                        <div class="buttons is-centered mt-4 is-flex-wrap-wrap">
                            <?php foreach ($docs as $slug => $doc): ?>
                            <a href="/admin/docs/<?= e($slug) ?>" class="button is-primary">
                                <span class="icon"><i class="fas <?= e($doc['icon']) ?>"></i></span>
                                <span><?= e($doc['title']) ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
