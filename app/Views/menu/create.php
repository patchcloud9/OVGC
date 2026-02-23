<?php
/**
 * Create Menu Item View
 */
$layout = 'main';
?>

<section class="section">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/menu">Menu</a></li>
                <li class="is-active"><a href="#" aria-current="page">Create</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Create Menu Item</span>
            </span>
        </h1>

        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns">
            <div class="column is-8">
                <div class="card">
                    <div class="card-content">
                        <form action="/admin/menu" method="POST">
                            <?= csrf_field() ?>

                            <div class="field">
                                <label class="label">Title *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="title" placeholder="e.g., Blog" required minlength="1" maxlength="100" value="<?= e(old('title')) ?>">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-heading"></i>
                                    </span>
                                </div>
                                <p class="help">Display text for the menu item</p>
                            </div>

                            <div class="field">
                                <label class="label">URL *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="url" placeholder="e.g., /blog" required maxlength="255" value="<?= e(old('url')) ?>">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-link"></i>
                                    </span>
                                </div>
                                <p class="help">Link destination (use # for dropdown parents)</p>
                            </div>

                            <div class="field">
                                <label class="label">Icon (Font Awesome)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="icon" placeholder="e.g., fas fa-home" maxlength="50" value="<?= e(old('icon')) ?>">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-icons"></i>
                                    </span>
                                </div>
                                <p class="help">Optional. Example: fas fa-home, fas fa-user, fas fa-cog (see <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a>)</p>
                            </div>

                            <div class="field">
                                <label class="label">Parent Menu Item</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="parent_id">
                                            <option value="">None (Top Level)</option>
                                            <?php foreach ($topLevelItems as $item): ?>
                                                <option value="<?= e($item['id']) ?>" <?= old('parent_id') == $item['id'] ? 'selected' : '' ?>>
                                                    <?= e($item['title']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <p class="help">Select a parent to create a dropdown menu item</p>
                            </div>

                            <div class="field">
                                <label class="label">Visibility *</label>
                                <div class="control">
                                    <label class="radio">
                                        <input type="radio" name="visibility" value="public" <?= old('visibility', 'public') === 'public' ? 'checked' : '' ?>>
                                        Public (everyone can see)
                                    </label>
                                    <br>
                                    <label class="radio">
                                        <input type="radio" name="visibility" value="authenticated" <?= old('visibility') === 'authenticated' ? 'checked' : '' ?>>
                                        Authenticated (logged in users only)
                                    </label>
                                    <br>
                                    <label class="radio">
                                        <input type="radio" name="visibility" value="admin" <?= old('visibility') === 'admin' ? 'checked' : '' ?>>
                                        Admin (admin users only)
                                    </label>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Options</label>
                                <div class="control">
                                    <label class="checkbox">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" <?= old('is_active', '1') === '1' ? 'checked' : '' ?>>
                                        Active (visible in menu)
                                    </label>
                                    <br>
                                    <label class="checkbox">
                                        <input type="hidden" name="open_new_tab" value="0">
                                        <input type="checkbox" name="open_new_tab" value="1" <?= old('open_new_tab') === '1' ? 'checked' : '' ?>>
                                        Open in new tab
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="field is-grouped">
                                <div class="control">
                                    <button type="submit" class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span>Create Menu Item</span>
                                    </button>
                                </div>
                                <div class="control">
                                    <a href="/admin/menu" class="button is-light">
                                        <span class="icon">
                                            <i class="fas fa-times"></i>
                                        </span>
                                        <span>Cancel</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="column is-4">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <span class="icon"><i class="fas fa-question-circle"></i></span>
                            <span>Help</span>
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <h4>Creating Dropdown Menus</h4>
                            <p>To create a dropdown menu:</p>
                            <ol>
                                <li>Create a parent item with URL set to <code>#</code></li>
                                <li>Create child items and select the parent</li>
                            </ol>

                            <h4>Icon Examples</h4>
                            <ul class="is-size-7">
                                <li><i class="fas fa-home"></i> fas fa-home</li>
                                <li><i class="fas fa-user"></i> fas fa-user</li>
                                <li><i class="fas fa-envelope"></i> fas fa-envelope</li>
                                <li><i class="fas fa-images"></i> fas fa-images</li>
                                <li><i class="fas fa-cog"></i> fas fa-cog</li>
                            </ul>

                            <a href="https://fontawesome.com/icons" target="_blank" class="button is-small is-info mt-3">
                                Browse All Icons
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
