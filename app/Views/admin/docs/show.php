<link rel="stylesheet" href="/assets/css/docs.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/docs.css') ?>">

<!-- Hero -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <div class="is-flex is-justify-content-space-between is-align-items-center is-flex-wrap-wrap" style="gap:0.75rem;">
                <div>
                    <h1 class="title is-3"><i class="fas fa-book-open"></i> Documentation</h1>
                    <p class="subtitle is-6 has-text-white"><?= e($doc['title']) ?></p>
                </div>
                <div class="buttons mb-0">
                    <a href="/admin/docs" class="button is-light is-small">
                        <span class="icon"><i class="fas fa-search"></i></span>
                        <span>Search</span>
                    </a>
                    <a href="/admin" class="button is-light is-small">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Admin Panel</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:1.5rem;">
    <div class="container">
        <div class="columns">
            <!-- Sidebar -->
            <div class="column is-3-desktop is-4-tablet docs-sidebar">
                <div class="box p-3 mb-4">
                    <form action="/admin/docs" method="GET">
                        <div class="field has-addons mb-0">
                            <div class="control is-expanded">
                                <input class="input" type="text" name="q"
                                       placeholder="Search docs…">
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
                            <?php foreach ($docs as $slug => $d): ?>
                            <li>
                                <a href="/admin/docs/<?= e($slug) ?>"
                                   class="<?= $currentSlug === $slug ? 'is-active' : '' ?>">
                                    <span class="icon is-small"><i class="fas <?= e($d['icon']) ?>"></i></span>
                                    <?= e($d['title']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Doc content -->
            <div class="column">
                <div class="box doc-content">
                    <?= $docHtml ?>
                </div>
            </div>
        </div>
    </div>
</section>
