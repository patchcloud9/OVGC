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

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <h2 class="title is-4">Green Fees</h2>
            <table class="table is-fullwidth is-narrow is-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Price</th>
                        <th>*Price w/ Reduced Membership</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Membership</td>
                        <td>$25.00</td>
                        <td>$12.00</td>
                    </tr>
                    <tr>
                        <td>18 Holes</td>
                        <td>$40.00</td>
                        <td>$20.00</td>
                    </tr>
                    <tr>
                        <td>All Day</td>
                        <td>$50.00</td>
                        <td>$25.00</td>
                    </tr>
                    <tr>
                        <td>Juniors (Under 18 Years Old)</td>
                        <td colspan="2">$5/9 holes &mdash; $10/18 holes</td>
                    </tr>
                </tbody>
            </table>
            <p class="subtitle is-6 has-text-grey">*Reduced membership pricing requires a valid reduced‐rate membership card.</p>
        </div>

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <h2 class="title is-4">Cart Rentals</h2>
            <table class="table is-fullwidth is-narrow is-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Price‑Per‑Person</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>9 Holes</td>
                        <td>$10.00</td>
                    </tr>
                    <tr>
                        <td>18 Holes</td>
                        <td>$18.00</td>
                    </tr>
                    <tr>
                        <td>All Day</td>
                        <td>$30.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
