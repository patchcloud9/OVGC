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
            // card data for rates layout (three columns)
            $cards = [
                'Green Fees' => [
                    ['Membership', '$25.00'],
                    ['18 Holes', '$40.00'],
                    ['All Day', '$50.00'],
                    ['Juniors (Under 18 Years Old)', '$5/9 holes — $10/18 holes'],
                ],
                'Reduced Membership' => [
                    ['Membership', '$12.00'],
                    ['18 Holes', '$20.00'],
                    ['All Day', '$25.00'],
                ],
                'Cart Rentals' => [
                    ['9 Holes', '$10.00'],
                    ['18 Holes', '$18.00'],
                    ['All Day', '$30.00'],
                ],
            ];
        ?>

        <div class="columns is-multiline" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <?php foreach ($cards as $cardTitle => $rows): ?>
                <div class="column is-one-third">
                    <div class="box has-text-centered">
                        <h3 class="title is-4 mt-3"><?= e($cardTitle) ?></h3>
                        <table class="table is-fullwidth is-narrow is-striped">
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= e($row[0]) ?></td>
                                        <td><?= e($row[1]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if ($cardTitle === 'Reduced Membership'): ?>
                            <p class="subtitle is-6 has-text-grey">
                                *Requires a valid reduced‑rate membership card.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
