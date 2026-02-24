<?php
$layout = 'main';
/** @var array $banner */
?>

<section class="section">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/banners">Banners</a></li>
                <li class="is-active"><a href="#" aria-current="page">Edit Banner</a></li>
            </ul>
        </nav>

        <h1 class="title">Edit Banner</h1>
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/banners/<?= e($banner['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="field">
                <label class="label">Page</label>
                <div class="control">
                    <input class="input" type="text" name="page" value="<?= e(old('page', $banner['page'])) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Position</label>
                <div class="control">
                    <div class="select">
                        <select name="position">
                            <option value="top" <?= old('position', $banner['position'])=='top'?'selected':'' ?>>Top</option>
                            <option value="bottom" <?= old('position', $banner['position'])=='bottom'?'selected':'' ?>>Bottom</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Colour</label>
                <div class="control">
                    <div class="select">
                        <select name="colour">
                            <option value="info" <?= old('colour', $banner['colour'])=='info'?'selected':'' ?>>Info (blue)</option>
                            <option value="warning" <?= old('colour', $banner['colour'])=='warning'?'selected':'' ?>>Warning (yellow)</option>
                            <option value="danger" <?= old('colour', $banner['colour'])=='danger'?'selected':'' ?>>Danger (red)</option>
                            <option value="none" <?= old('colour', $banner['colour'])=='none'?'selected':'' ?>>None (transparent)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Text</label>
                <div class="control">
                    <textarea class="textarea" name="text" rows="3"><?= e(old('text', $banner['text'])) ?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Start At</label>
                <div class="control">
                    <input class="input" type="datetime-local" name="start_at" value="<?= e(old('start_at', $banner['start_at'] ? date('Y-m-d\TH:i', strtotime($banner['start_at'])) : '')) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">End At</label>
                <div class="control">
                    <input class="input" type="datetime-local" name="end_at" value="<?= e(old('end_at', $banner['end_at'] ? date('Y-m-d\TH:i', strtotime($banner['end_at'])) : '')) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order', $banner['sort_order'])) ?>">
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="dismissable" value="1" <?= old('dismissable', $banner['dismissable']) ? 'checked' : '' ?>> Dismissable
                    </label>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="active" value="1" <?= old('active', $banner['active']) ? 'checked' : '' ?>> Active
                    </label>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">Update Banner</button>
                </div>
                <div class="control">
                    <a href="/admin/banners" class="button is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>
