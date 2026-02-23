<?php
/**
 * Menu Management View
 *
 * List, create, edit, delete, and reorder menu items (admin only).
 */
$layout = 'main';
?>

<section class="section">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Menu</a></li>
            </ul>
        </nav>

        <h1 class="title">
            <span class="icon-text">
                <span class="icon has-text-primary">
                    <i class="fas fa-bars"></i>
                </span>
                <span>Manage Menu</span>
            </span>
        </h1>
        <p class="subtitle">Create and organize navigation menu items</p>

        <!-- Flash Messages -->
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <!-- Create Button -->
        <div class="mb-4">
            <a href="/admin/menu/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Create Menu Item</span>
            </a>
        </div>

        <!-- Menu Items List -->
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-list"></i></span>
                    <span>Menu Items (<?= count($menuItems) ?>)</span>
                </p>
            </header>
            <div class="card-content">

                <?php if (empty($menuItems)): ?>
                    <div class="notification is-info">
                        <p class="has-text-centered">
                            <i class="fas fa-bars fa-3x mb-3"></i>
                            <br>
                            <strong>No menu items yet</strong>
                            <br>
                            Create your first menu item using the button above!
                        </p>
                    </div>
                <?php else: ?>

                    <div class="table-container">
                        <table class="table is-fullwidth is-striped is-hoverable">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>URL</th>
                                    <th>Icon</th>
                                    <th>Parent</th>
                                    <th>Visibility</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Build children lookup array for hierarchical display
                                $childrenByParent = [];
                                foreach ($menuItems as $item) {
                                    if ($item['parent_id'] !== null) {
                                        $parentId = (int) $item['parent_id'];
                                        if (!isset($childrenByParent[$parentId])) {
                                            $childrenByParent[$parentId] = [];
                                        }
                                        $childrenByParent[$parentId][] = $item;
                                    }
                                }

                                // Display top-level items first, followed by their children
                                foreach ($topLevel as $item):
                                ?>
                                <tr id="menu-<?= e($item['id']) ?>">
                                    <td>
                                        <div class="buttons are-small">
                                            <a href="#" class="button is-small" onclick="moveMenuItem(<?= e($item['id']) ?>, 'up'); return false;" title="Move Up">
                                                <span class="icon is-small"><i class="fas fa-arrow-up"></i></span>
                                            </a>
                                            <a href="#" class="button is-small" onclick="moveMenuItem(<?= e($item['id']) ?>, 'down'); return false;" title="Move Down">
                                                <span class="icon is-small"><i class="fas fa-arrow-down"></i></span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= e($item['title']) ?></strong>
                                        <?php if ($item['is_system']): ?>
                                            <span class="tag is-warning is-small ml-2">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code class="is-size-7"><?= e($item['url']) ?></code></td>
                                    <td>
                                        <?php if ($item['icon']): ?>
                                            <span class="icon"><i class="<?= e($item['icon']) ?>"></i></span>
                                            <code class="is-size-7"><?= e($item['icon']) ?></code>
                                        <?php else: ?>
                                            <span class="has-text-grey-light">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="has-text-grey-light">Top Level</span>
                                    </td>
                                    <td>
                                        <span class="tag <?= $item['visibility'] === 'admin' ? 'is-danger' : ($item['visibility'] === 'authenticated' ? 'is-warning' : 'is-info') ?>">
                                            <?= e(ucfirst($item['visibility'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($item['is_active']): ?>
                                            <span class="tag is-success">Active</span>
                                        <?php else: ?>
                                            <span class="tag is-light">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="buttons are-small">
                                            <a href="/admin/menu/<?= e($item['id']) ?>/edit" class="button is-small is-info">
                                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                                <span>Edit</span>
                                            </a>
                                            <?php if (!$item['is_system']): ?>
                                            <a href="#" class="button is-small is-danger" onclick="deleteMenuItem(<?= e($item['id']) ?>, '<?= e($item['title']) ?>'); return false;">
                                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                                <span>Delete</span>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                // Display children of this parent immediately after
                                if (isset($childrenByParent[$item['id']])):
                                    foreach ($childrenByParent[$item['id']] as $child):
                                ?>
                                <tr id="menu-<?= e($child['id']) ?>" class="has-background-light">
                                    <td>
                                        <div class="buttons are-small">
                                            <a href="#" class="button is-small" onclick="moveMenuItem(<?= e($child['id']) ?>, 'up'); return false;" title="Move Up">
                                                <span class="icon is-small"><i class="fas fa-arrow-up"></i></span>
                                            </a>
                                            <a href="#" class="button is-small" onclick="moveMenuItem(<?= e($child['id']) ?>, 'down'); return false;" title="Move Down">
                                                <span class="icon is-small"><i class="fas fa-arrow-down"></i></span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="icon has-text-grey-light ml-3"><i class="fas fa-level-up-alt fa-rotate-90"></i></span>
                                        <strong><?= e($child['title']) ?></strong>
                                        <?php if ($child['is_system']): ?>
                                            <span class="tag is-warning is-small ml-2">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code class="is-size-7"><?= e($child['url']) ?></code></td>
                                    <td>
                                        <?php if ($child['icon']): ?>
                                            <span class="icon"><i class="<?= e($child['icon']) ?>"></i></span>
                                            <code class="is-size-7"><?= e($child['icon']) ?></code>
                                        <?php else: ?>
                                            <span class="has-text-grey-light">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= e($item['title']) ?>
                                    </td>
                                    <td>
                                        <span class="tag <?= $child['visibility'] === 'admin' ? 'is-danger' : ($child['visibility'] === 'authenticated' ? 'is-warning' : 'is-info') ?>">
                                            <?= e(ucfirst($child['visibility'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($child['is_active']): ?>
                                            <span class="tag is-success">Active</span>
                                        <?php else: ?>
                                            <span class="tag is-light">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="buttons are-small">
                                            <a href="/admin/menu/<?= e($child['id']) ?>/edit" class="button is-small is-info">
                                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                                <span>Edit</span>
                                            </a>
                                            <?php if (!$child['is_system']): ?>
                                            <a href="#" class="button is-small is-danger" onclick="deleteMenuItem(<?= e($child['id']) ?>, '<?= e($child['title']) ?>'); return false;">
                                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                                <span>Delete</span>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    endforeach;
                                endif;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>
</section>

<!-- Hidden delete form -->
<form id="delete-form" method="POST" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Hidden reorder form -->
<form id="reorder-form" method="POST" action="/admin/menu/reorder" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="menu_item_id" id="reorder-menu-item-id">
    <input type="hidden" name="direction" id="reorder-direction">
</form>

<script>
    // Delete menu item with confirmation
    function deleteMenuItem(menuItemId, menuTitle) {
        if (!confirm(`Are you sure you want to delete "${menuTitle}"?\n\nThis will also delete any child menu items.\n\nThis action cannot be undone.`)) {
            return;
        }

        const form = document.getElementById('delete-form');
        form.action = `/admin/menu/${menuItemId}`;
        form.submit();
    }

    // Move menu item up or down
    function moveMenuItem(menuItemId, direction) {
        sessionStorage.setItem('scrollToMenuItem', menuItemId);

        document.getElementById('reorder-menu-item-id').value = menuItemId;
        document.getElementById('reorder-direction').value = direction;
        document.getElementById('reorder-form').submit();
    }

    // Scroll back to moved item after page reload
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToMenuItemId = sessionStorage.getItem('scrollToMenuItem');

        if (scrollToMenuItemId) {
            const menuElement = document.getElementById('menu-' + scrollToMenuItemId);

            if (menuElement) {
                setTimeout(() => {
                    menuElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    menuElement.style.transition = 'background-color 0.5s';
                    menuElement.style.backgroundColor = 'rgba(72, 199, 142, 0.2)';

                    setTimeout(() => {
                        menuElement.style.backgroundColor = '';
                    }, 1000);
                }, 100);
            }

            sessionStorage.removeItem('scrollToMenuItem');
        }
    });
</script>
