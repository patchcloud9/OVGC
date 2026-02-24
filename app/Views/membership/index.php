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
            // grouped membership data for card layout (tax only)
            $groups = [
                'Standard' => [
                    ['Single', '$750'],
                    ['Couple', '$1,200'],
                    ['Reduced Single *', '$400'],
                    ['Reduced Couple *', '$750'],
                ],
                'Lifetime' => [
                    ['Lifetime Single', '$7,250'],
                    ['Lifetime Couple', '$11,500'],
                ],
                'Under 30' => [
                    ['Junior (Under 18)', '$65'],
                    ['College (18–24)', '$120'],
                    ['Young Adult (19–30)', '$400'],
                ],
            ];
        ?>

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <div class="columns is-multiline">
            <?php foreach ($groups as $title => $items): ?>
                <div class="column is-one-third">
                    <div class="box has-text-centered">
                        <h3 class="title is-4 mt-3"><?= e($title) ?></h3>
                        <p class="subtitle is-6 has-text-grey">*rates + tax</p>
                        <table class="table is-fullwidth is-narrow is-striped">
                            <tbody>
                                <?php foreach ($items as $row): ?>
                                    <tr>
                                        <td><?= e($row[0]) ?></td>
                                        <td><?= e($row[1]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> <!-- close card container -->

        <!-- additional pricing table inserted below cards -->
        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <h2 class="title is-4">Other Prices (including tax)</h2>
            <table class="table is-fullwidth is-narrow is-striped">
                <tbody>
                    <tr>
                        <td>Yearly Cart Storage – Electric</td>
                        <td>$300</td>
                    </tr>
                    <tr>
                        <td>Yearly Cart Storage – Gas</td>
                        <td>$250</td>
                    </tr>
                    <tr>
                        <td>Yearly Trail Fee (Carts from home)</td>
                        <td>$60</td>
                    </tr>
                    <tr>
                        <td>Daily Trail Fee (Carts from home)</td>
                        <td>$12</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <p class="has-text-weight-semibold">
                MEMBERSHIP PROMOTION: For every new member you recruit in 2025 you receive a $50 reduction on your 2026 dues; all other memberships receive $25 reduction (excluding Junior and College).
            </p>
        </div>
    </div>
</section>
