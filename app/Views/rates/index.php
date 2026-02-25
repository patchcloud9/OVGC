<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-dollar-sign"></i> <?= e($title ?? 'Rates') ?>
            </h1>
            <p class="subtitle">
                Current green fees and cart rental pricing
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php
            // use data loaded from controller
            $groups = $groups ?? [];
        ?>

        <!-- split 60/40: rules on left, scorecard on right -->
        <div class="columns" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <div class="column is-two-thirds">
                <h2 class="title is-4">Golf Course Rules</h2>
                <ul>
                    <li>Dress Code: Golf attire.</li>
                    <li>Soft Spikes ONLY.</li>
                    <li>All alcoholic beverages must be purchased from Bear Creek Golf Course. Washington State Law prohibits people from bringing their own alcoholic beverages onto the golf course!</li>
                    <li>Our pace of play is 4.5 hours for 18 holes.</li>
                    <li>Each player must have a set of clubs in their own bag.</li>
                    <li>No more than 4 players per group without starter approval.</li>
                    <li>Groups not able to keep pace with the group ahead shall allow faster groups to play through.</li>
                    <li>BCGC reserves the right to rescind Daily Passes and Carts from anyone who does not adhere to standard golf and common courtesy to others.</li>
                </ul>
            </div>
            <div class="column is-one-third">
                <h2 class="title is-4">Scorecard</h2>
                <p>
                    <a href="/assets/scorecard.pdf" download class="button is-primary">
                        <i class="fas fa-file-download"></i> Download Scorecard
                    </a>
                </p>
                <p class="is-size-7 has-text-grey">(Place your scorecard PDF at <code>public/assets/scorecard.pdf</code> or update the link above.)</p>
            </div>
        </div>

        <div class="columns is-multiline" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <?php foreach ($groups as $group): ?>
                <div class="column is-one-third">
                    <div class="box has-text-centered">
                        <h3 class="title is-4 mt-3"><?= e($group['title']) ?></h3>
                        <?php if (!empty($group['subtitle'])): ?>
                            <p class="subtitle is-6 has-text-grey"><?= e($group['subtitle']) ?></p>
                        <?php endif; ?>
                        <table class="table is-fullwidth is-narrow is-striped">
                            <tbody>
                                <?php foreach ($group['rates'] as $rate): ?>
                                    <tr>
                                        <td><?= e($rate['description']) ?></td>
                                        <td>$<?= number_format($rate['price'],2) ?></td>
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
        </div>
    </div>
</section>
