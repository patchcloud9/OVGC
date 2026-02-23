<?php
$layout = 'main';
?>

<section class="hero is-link">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-users"></i> <?= e($title ?? 'Membership') ?>
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

        <h2 class="title is-4">Membership Levels &amp; Dues</h2>

        <?php
            // grouped membership data for card layout
            $groups = [
                'Standard' => [
                    ['Single', '$698.88', '$750'],
                    ['Couple', '$1,107.01', '$1,200'],
                    ['Reduced Single *', '$369', '$400'],
                    ['Reduced Couple *', '$691.88', '$750'],
                ],
                'Lifetime' => [
                    ['Lifetime Single', '$6,688', '$7,250'],
                    ['Lifetime Couple', '$10,609', '$11,500'],
                ],
                'Under 30' => [
                    ['Junior (Under 18)', '$59.96', '$65'],
                    ['College (18–24)', '$110.70', '$120'],
                    ['Young Adult (19–30)', '$369', '$400'],
                ],
            ];
        ?>

        <div class="columns is-multiline">
            <?php foreach ($groups as $title => $items): ?>
                <div class="column is-one-third">
                    <div class="box has-text-centered">
                        <h3 class="title is-4 mt-3"><?= e($title) ?></h3>
                        <table class="table is-fullwidth is-narrow is-striped">
                            <tbody>
                                <?php foreach ($items as $row): ?>
                                    <tr>
                                        <td><?= e($row[0]) ?></td>
                                        <td><?= e($row[1]) ?></td>
                                        <td><?= e($row[2]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="content" style="max-width:820px;margin:2rem auto;text-align:left;">
            <h2 class="title is-4">Why Join?</h2>

            <h3 class="subtitle is-5">All members enjoy:</h3>
            <ul>
                <li>10% off Clubhouse purchases (excluding alcohol)</li>
                <li>(3)&nbsp;Free 18‑Hole Rounds for a guest of a full Single or Couples member ($105 value)</li>
                <li>(1)&nbsp;Free 18‑Hole Round for a guest of all other members</li>
                <li>Juniors golf free when accompanied by a Single, Couples, or Young Adult member</li>
                <li>Free 18‑Hole Round at Bear Creek Golf Course in Winthrop, WA</li>
                <li>MEMBERSHIP PROMOTION: For every new member you recruit in 2025 you receive a $50 reduction on your 2026 dues; all other memberships receive $25 reduction (excluding Junior and College)</li>
            </ul>

            <p>
                As a member of OVGC you'll join a welcoming community of golfers who share
                a passion for the game and the beautiful course we call home. Your
                membership supports course maintenance, club events, and junior programs,
                keeping the greens in great shape for everyone.
            </p>
            <p>
                Ready to become a member? <a href="/contact">Contact us</a> or visit our
                next meeting for more information.
            </p>
        </div>
    </div>
</section>
