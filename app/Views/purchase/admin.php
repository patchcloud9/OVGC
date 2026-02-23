<?php
/**
 * Purchase Page Admin - Edit Content
 */
$layout = 'main';
?>

<section class="section">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Purchase Page</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <span>Edit Purchase Page</span>
            </span>
        </h1>
        <p class="subtitle">Customize the Purchase page content</p>

        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/purchase">
            <?= csrf_field() ?>

            <div class="box">
                <div class="field">
                    <label class="label">Page Title</label>
                    <div class="control">
                        <input type="text" name="page_title" class="input" value="<?= e($content['page_title'] ?? 'Purchase') ?>" required maxlength="100">
                    </div>
                    <p class="help">Main heading displayed at the top of the purchase page</p>
                </div>

                <div class="field mt-4">
                    <label class="label">Subtitle</label>
                    <div class="control">
                        <input type="text" name="page_subtitle" class="input" value="<?= e($content['page_subtitle'] ?? '') ?>" maxlength="255">
                    </div>
                    <p class="help">Optional subtitle displayed under the main heading</p>
                </div>

                <div class="field">
                    <label class="label">Content Text</label>
                    <div class="control">
                        <textarea name="content_text" class="textarea" rows="6"><?= e($content['content_text'] ?? '') ?></textarea>
                    </div>
                    <p class="help">Use {email} to insert the contact email from Theme Settings. Manage it in <a href="/admin/theme">Theme Settings</a>. Use line breaks for paragraphs.</p>
                </div>

                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <label class="label">Button Text</label>
                            <div class="control">
                                <input type="text" name="button_text" class="input" value="<?= e($content['button_text'] ?? 'Fine Art America') ?>" maxlength="100">
                            </div>
                            <p class="help">Text displayed on the call-to-action button</p>
                        </div>
                        <div class="field">
                            <label class="label">Button URL</label>
                            <div class="control">
                                <input type="url" name="button_url" class="input" value="<?= e($content['button_url'] ?? '') ?>" maxlength="255">
                            </div>
                            <p class="help">Full URL to purchase prints/store (include https://)</p>
                        </div>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-primary">
                            <span class="icon"><i class="fas fa-save"></i></span>
                            <span>Save Changes</span>
                        </button>
                    </div>
                    <div class="control">
                        <a href="/admin" class="button is-light">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Back to Admin</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>

    </div>
</section>
