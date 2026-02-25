<?php
$layout = 'main';
?>

<!-- hero for rate group -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3">Rates for <?= e($group['title']) ?></h1>
            <?php if (!empty($group['subtitle'])): ?>
                <p class="subtitle is-6 has-text-white"><?= e($group['subtitle']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:1000px;">
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/rates">Rates</a></li>
                <li class="is-active"><a href="#" aria-current="page"><?= e($group['title']) ?></a></li>
            </ul>
        </nav>

        <?php if (!empty($group['note'])): ?>
            <p class="has-text-grey"><?= e($group['note']) ?></p>
        <?php endif; ?>

        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="mb-4">
            <a href="/admin/rates/<?= e($group['id']) ?>/rates/create" class="button is-primary">
                <span class="icon"><i class="fas fa-plus"></i></span>
                <span>Add Rate</span>
            </a>
        </div>

        <?php if (empty($rates)): ?>
            <div class="notification is-info">No rates defined for this group.</div>
        <?php else: ?>
            <!-- desktop/tablet: table -->
            <div class="table-container is-hidden-mobile">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rates as $rate): ?>
                        <tr>
                            <td><?= e($rate['sort_order']) ?></td>
                            <td><?= e($rate['description']) ?></td>
                            <td>$<?= number_format($rate['price'],2) ?></td>
                            <td><?= e($rate['notes']) ?></td>
                            <td>
                                <div class="buttons are-small">
                                    <a href="/admin/rates/<?= e($group['id']) ?>/rates/<?= e($rate['id']) ?>/edit" class="button is-small is-info">
                                        <span class="icon is-small"><i class="fas fa-edit"></i></span>
                                        <span>Edit</span>
                                    </a>
                                    <a href="#" class="button is-small is-danger" onclick="if(confirm('Delete this rate?')){document.getElementById('delete-rate-<?= e($rate['id']) ?>').submit();}return false;">
                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                        <span>Delete</span>
                                    </a>
                                    <form id="delete-rate-<?= e($rate['id']) ?>" method="POST" action="/admin/rates/<?= e($group['id']) ?>/rates/<?= e($rate['id']) ?>" style="display:none;">
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

            <!-- mobile: cards -->
            <div class="is-hidden-tablet">
                <?php foreach ($rates as $rate): ?>
                    <div class="box">
                        <h3 class="title is-5">
                            <?= e($rate['description']) ?> <span class="is-size-6 has-text-grey">#<?= e($rate['sort_order']) ?></span>
                        </h3>
                        <p><strong>Price:</strong> $<?= number_format($rate['price'],2) ?></p>
                        <?php if (!empty($rate['notes'])): ?>
                            <p class="is-size-7"><strong>Notes:</strong> <?= e($rate['notes']) ?></p>
                        <?php endif; ?>
                        <div class="buttons is-small mt-3">
                            <a href="/admin/rates/<?= e($group['id']) ?>/rates/<?= e($rate['id']) ?>/edit" class="button is-info" title="Edit">
                                <span class="icon is-small"><i class="fas fa-edit"></i></span>
                            </a>
                            <a href="#" class="button is-danger" title="Delete" onclick="if(confirm('Delete this rate?')){document.getElementById('delete-rate-<?= e($rate['id']) ?>').submit();}return false;">
                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                            </a>
                            <form id="delete-rate-<?= e($rate['id']) ?>" method="POST" action="/admin/rates/<?= e($group['id']) ?>/rates/<?= e($rate['id']) ?>" style="display:none;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
