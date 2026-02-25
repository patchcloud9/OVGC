<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-users"></i> <?= e($title ?? 'Membership & Dues') ?>
            </h1>
            <p class="subtitle">
                Join the OVGC community and enjoy exclusive benefits!
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <!-- dynamic page content provided via admin settings -->
        <?php if (!empty($pageContent['top_text'])): ?>
            <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
                <?= nl2br(e($pageContent['top_text'])) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($bulletList)): ?>
            <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
                <ul>
                    <?php foreach ($bulletList as $bullet): ?>
                        <li><?= e($bullet) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <?php
            $groups = $groups ?? [];
            $carts = null;
            foreach ($groups as $i => $g) {
                // match 'cart' or 'carts' case‑insensitive
                if (preg_match('/^carts?$/i', $g['slug'])) {
                    $carts = $g;
                    unset($groups[$i]);
                    break;
                }
            }
        ?>

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <div class="columns is-multiline">
            <?php foreach ($groups as $group): ?>
                <div class="column is-one-third">
                    <div class="box has-text-centered">
                        <h3 class="title is-4 mt-3"><?= e($group['title']) ?></h3>
                        <?php if (!empty($group['subtitle'])): ?>
                            <p class="subtitle is-6 has-text-grey"><?= e($group['subtitle']) ?></p>
                        <?php endif; ?>
                        <table class="table is-fullwidth is-narrow is-striped">
                            <tbody>
                                <?php foreach ($group['items'] as $item): ?>
                                    <tr>
                                        <td>
                                        <?= e($item['name']) ?>
                                        <?php if (!empty($item['notes'])): ?>
                                            <span class="is-size-7">(<?= e($item['notes']) ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                        <td>$<?= number_format($item['price'],2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (!empty($group['note'])): ?>
                            <p class="subtitle is-6 has-text-grey"><?= e($group['note']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> <!-- close card container -->

        <?php if ($carts): ?>
            <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
                <h2 class="title is-4">
                    <?= e($carts['title']) ?>
                    <?php if (!empty($carts['subtitle'])): ?>
                        <span class="is-size-7 has-text-weight-normal">(<?= e($carts['subtitle']) ?>)</span>
                    <?php endif; ?>
                </h2>
                <table class="table is-fullwidth is-narrow is-striped">
                    <tbody>
                        <?php foreach ($carts['items'] as $item): ?>
                            <tr>
                                <td>
                                    <?= e($item['name']) ?>
                                    <?php if (!empty($item['notes'])): ?>
                                        <span class="is-size-7">(<?= e($item['notes']) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?= number_format($item['price'],2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!empty($carts['note'])): ?>
                    <p class="subtitle is-6 has-text-grey"><?= e($carts['note']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($pageContent['bottom_text'])): ?>
            <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
                <?= nl2br(e($pageContent['bottom_text'])) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
