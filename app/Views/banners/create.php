<?php
$layout = 'main';
?>

<!-- hero for creating banner -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-flag"></i> Create Banner</h1>
            <p class="subtitle is-6 has-text-white">Add a new banner</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:1000px;">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/banners">Banners</a></li>
                <li class="is-active"><a href="#" aria-current="page">Create Banner</a></li>
            </ul>
        </nav>

        <h1 class="title">Create Banner</h1>
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <form method="POST" action="/admin/banners" style="max-width:700px;">
            <?= csrf_field() ?>
            <div class="field">
                <label class="label">Page</label>
                <div class="control has-icons-right">
                    <input class="input" type="text" name="page" list="page-list" value="<?= e(old('page')) ?>" required autocomplete="off" id="banner-page-input">
                    <span class="icon is-small is-right" id="clear-page" style="cursor:pointer; display:none;">
                        <i class="fas fa-times-circle"></i>
                    </span>
                    <datalist id="page-list">
                        <?php foreach (($pages ?? []) as $p): ?>
                        <option value="<?= e($p) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <p class="help">Select a non‑admin page or type a custom path.</p>
            </div>

            <div class="field">
                <label class="label">Position</label>
                <div class="control">
                    <div class="select">
                        <select name="position">
                            <option value="top" <?= old('position')=='top'?'selected':'' ?>>Top</option>
                            <option value="bottom" <?= old('position')=='bottom'?'selected':'' ?>>Bottom</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Colour</label>
                <div class="control">
                    <div class="select">
                        <select name="colour">
                            <option value="info" <?= old('colour')=='info'?'selected':'' ?>>Info (blue)</option>
                            <option value="warning" <?= old('colour')=='warning'?'selected':'' ?>>Warning (yellow)</option>
                            <option value="danger" <?= old('colour')=='danger'?'selected':'' ?>>Danger (red)</option>
                            <option value="none" <?= old('colour')=='none'?'selected':'' ?>>None (transparent)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Text</label>
                <div class="control">
                    <textarea class="textarea" name="text" rows="3"><?= e(old('text')) ?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Start At</label>
                <div class="control">
                    <input class="input" type="datetime-local" name="start_at" value="<?= e(old('start_at')) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">End At</label>
                <div class="control">
                    <input class="input" type="datetime-local" name="end_at" value="<?= e(old('end_at')) ?>">
                </div>
            </div>

            <div class="field">
                <label class="label">Sort Order</label>
                <div class="control">
                    <input class="input" type="number" name="sort_order" value="<?= e(old('sort_order',0)) ?>">
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="dismissable" value="1" <?= old('dismissable') ? 'checked' : '' ?>> Dismissable
                    </label>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="active" value="1" <?= old('active',1) ? 'checked' : '' ?>> Active
                    </label>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">Save Banner</button>
                </div>
                <div class="control">
                    <a href="/admin/banners" class="button is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var pageInput = document.getElementById('banner-page-input');
    var clearBtn = document.getElementById('clear-page');
    function toggleClear() {
        if (pageInput.value.length) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }
    }
    function normalize() {
        var v = pageInput.value;
        v = v.replace(/\/+/g, '/').replace(/#/g, '');
        if (!v.startsWith('/')) v = '/' + v;
        if (v.length > 1 && v.endsWith('/')) v = v.slice(0, -1);
        pageInput.value = v;
    }
    pageInput.addEventListener('input', toggleClear);
    pageInput.addEventListener('blur', normalize);
    clearBtn.addEventListener('click', function() {
        pageInput.value = '';
        toggleClear();
        pageInput.focus();
    });
    toggleClear();
});
</script>
