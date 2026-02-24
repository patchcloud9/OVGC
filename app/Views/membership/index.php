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

        <!-- benefits text above cards; heading moved above everything -->

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <p>
                As a member of OVGC you'll join a welcoming community of golfers who share
                a passion for the game and the beautiful course we call home. Your
                membership supports course maintenance, club events, and junior programs,
                keeping the greens in great shape for everyone.
            </p>
            <ul>
                <li>10% off Clubhouse purchases (excluding alcohol)</li>
                <li>(3)&nbsp;Free 18‑Hole Rounds for a guest of a full Single or Couples member ($105 value)</li>
                <li>(1)&nbsp;Free 18‑Hole Round for a guest of all other members</li>
                <li>Juniors golf free when accompanied by a Single, Couples, or Young Adult member</li>
                <li>Free 18‑Hole Round at Bear Creek Golf Course in Winthrop, WA</li>
            </ul>
        </div>

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
                                        <td><?= e($item['name']) ?></td>
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
                        (<?= e($carts['subtitle']) ?>)
                    <?php endif; ?>
                </h2>
                <table class="table is-fullwidth is-narrow is-striped">
                    <tbody>
                        <?php foreach ($carts['items'] as $item): ?>
                            <tr>
                                <td><?= e($item['name']) ?></td>
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

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <p class="has-text-weight-semibold">
                MEMBERSHIP PROMOTION: For every new member you recruit in 2025 you receive a $50 reduction on your 2026 dues; all other memberships receive $25 reduction (excluding Junior and College).
            </p>
        </div>
    </div>
</section>
