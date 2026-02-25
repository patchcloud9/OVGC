<?php
/**
 * Edit Menu Item View
 */
$layout = 'main';
?>

<!-- hero for editing menu item -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-edit"></i> Edit Menu Item</h1>
            <p class="subtitle is-6 has-text-white">Modify navigation entry</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:1000px;">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/menu">Menu</a></li>
                <li class="is-active"><a href="#" aria-current="page">Edit</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary">
                    <i class="fas fa-edit"></i>
                </span>
                <span>Edit Menu Item</span>
            </span>
        </h1>

        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php if ($menuItem['is_system']): ?>
        <div class="notification is-warning">
            <strong>Note:</strong> This is a system menu item and cannot be edited or deleted.
        </div>
        <?php endif; ?>

        <div class="columns">
            <div class="column is-8">
                <div class="card">
                    <div class="card-content">
                        <form action="/admin/menu/<?= e($menuItem['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <div class="field">
                                <label class="label">Title *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="title" value="<?= e($menuItem['title']) ?>" required minlength="1" maxlength="100" <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-heading"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">URL *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="url" value="<?= e($menuItem['url']) ?>" required maxlength="255" <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-link"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Icon (Font Awesome)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="icon" value="<?= e($menuItem['icon'] ?? '') ?>" maxlength="50" <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-icons"></i>
                                    </span>
                                </div>
                                <p class="help">Optional. Example: fas fa-home, fas fa-user, fas fa-cog</p>
                            </div>

                            <div class="field">
                                <label class="label">Parent Menu Item</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="parent_id" <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                            <option value="">None (Top Level)</option>
                                            <?php foreach ($topLevelItems as $item): ?>
                                                <?php if ($item['id'] != $menuItem['id']): // Can't be own parent ?>
                                                <option value="<?= e($item['id']) ?>" <?= $menuItem['parent_id'] == $item['id'] ? 'selected' : '' ?>>
                                                    <?= e($item['title']) ?>
                                                </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Visibility *</label>
                                <div class="control">
                                    <label class="radio">
                                        <input type="radio" name="visibility" value="public" <?= $menuItem['visibility'] === 'public' ? 'checked' : '' ?> <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                        Public (everyone can see)
                                    </label>
                                    <br>
                                    <label class="radio">
                                        <input type="radio" name="visibility" value="authenticated" <?= $menuItem['visibility'] === 'authenticated' ? 'checked' : '' ?> <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                        Authenticated (logged in users only)
                                    </label>
                                    <br>
                                    <label class="radio">
                                        <input type="radio" name="visibility" value="admin" <?= $menuItem['visibility'] === 'admin' ? 'checked' : '' ?> <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                        Admin (admin users only)
                                    </label>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Options</label>
                                <div class="control">
                                    <label class="checkbox">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" <?= $menuItem['is_active'] ? 'checked' : '' ?> <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                        Active (visible in menu)
                                    </label>
                                    <br>
                                    <label class="checkbox">
                                        <input type="hidden" name="open_new_tab" value="0">
                                        <input type="checkbox" name="open_new_tab" value="1" <?= $menuItem['open_new_tab'] ? 'checked' : '' ?> <?= $menuItem['is_system'] ? 'disabled' : '' ?>>
                                        Open in new tab
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="field is-grouped">
                                <?php if (!$menuItem['is_system']): ?>
                                <div class="control">
                                    <button type="submit" class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span>Save Changes</span>
                                    </button>
                                </div>
                                <?php endif; ?>
                                <div class="control">
                                    <a href="/admin/menu" class="button is-light">
                                        <span class="icon">
                                            <i class="fas fa-arrow-left"></i>
                                        </span>
                                        <span>Back to Menu</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
