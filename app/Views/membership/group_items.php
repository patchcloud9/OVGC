<?php
$layout = 'main';
?>

<section class="section">
    <div class="container">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/membership">Membership</a></li>
                <li class="is-active"><a href="#" aria-current="page"><?= e($group['title']) ?></a></li>
            </ul>
        </nav>

        <h1 class="title">Items for <?= e($group['title']) ?></h1>
        <?php if (!empty($group['subtitle'])): ?>
            <p class="subtitle"><?= e($group['subtitle']) ?></p>
        <?php endif; ?>
        <?php if (!empty($group['note'])): ?>
            <p class="has-text-grey"><?= e($group['note']) ?></p>
        <?php endif; ?>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="mb-4">
            <a href="/admin/membership/<?= e($group['id']) ?>/items/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Add Item</span>
            </a>
        </div>

        <?php if (empty($items)): ?>
            <div class="notification is-info">No items defined for this group.</div>
        <?php else: ?>
            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= e($item['sort_order']) ?></td>
                            <td><?= e($item['name']) ?></td>
                            <td>$<?= number_format($item['price'],2) ?></td>
                            <td><?= e($item['notes']) ?></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="/admin/membership/<?= e($group['id']) ?>/items/<?= e($item['id']) ?>/edit" class="button is-small is-info">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                        <span>Edit</span>
                                    </a>
                                    <a href="#" class="button is-small is-danger" onclick="if(confirm('Delete this item?')){document.getElementById('delete-item-<?= e($item['id']) ?>').submit();}return false;">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                        <span>Delete</span>
                                    </a>
                                    <form id="delete-item-<?= e($item['id']) ?>" method="POST" action="/admin/membership/<?= e($group['id']) ?>/items/<?= e($item['id']) ?>" style="display:none;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
