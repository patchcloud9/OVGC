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

        <!-- desktop view: scrollable table -->
        <div class="table-container is-hidden-mobile">
            <table class="table is-fullwidth is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>Membership Type</th>
                        <th>Dues</th>
                        <th>Dues with Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Single</td>
                        <td>$698.88</td>
                        <td>$750</td>
                    </tr>
                    <tr>
                        <td>Couple</td>
                        <td>$1,107.01</td>
                        <td>$1,200</td>
                    </tr>
                    <tr>
                        <td>Reduced Single *</td>
                        <td>$369</td>
                        <td>$400</td>
                    </tr>
                    <tr>
                        <td>Reduced Couple *</td>
                        <td>$691.88</td>
                        <td>$750</td>
                    </tr>
                    <tr>
                        <td>Lifetime Single/Couple<br><small>(Only 9 remaining)</small></td>
                        <td>$6,688 / $10,609</td>
                        <td>$7,250 / $11,500</td>
                    </tr>
                    <tr>
                        <td>Junior (Under 18 years old)</td>
                        <td>$59.96</td>
                        <td>$65</td>
                    </tr>
                    <tr>
                        <td>College (18 – 24 years old)<br><small>(Must be enrolled in college)</small></td>
                        <td>$110.70</td>
                        <td>$120</td>
                    </tr>
                    <tr>
                        <td>Young Adult (19 – 30 years old)</td>
                        <td>$369</td>
                        <td>$400</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- mobile view: stacked cards -->
        <div class="columns is-multiline is-hidden-tablet">
            <?php
                // define an array to iterate for cards
                $types = [
                    ['label' => 'Single', 'dues' => '$698.88', 'tax' => '$750'],
                    ['label' => 'Couple', 'dues' => '$1,107.01', 'tax' => '$1,200'],
                    ['label' => 'Reduced Single *', 'dues' => '$369', 'tax' => '$400'],
                    ['label' => 'Reduced Couple *', 'dues' => '$691.88', 'tax' => '$750'],
                    ['label' => "Lifetime Single/Couple<br><small>(Only 9 remaining)</small>", 'dues' => '$6,688 / $10,609', 'tax' => '$7,250 / $11,500'],
                    ['label' => 'Junior (Under 18 years old)', 'dues' => '$59.96', 'tax' => '$65'],
                    ['label' => 'College (18 – 24 years old)<br><small>(Must be enrolled in college)</small>', 'dues' => '$110.70', 'tax' => '$120'],
                    ['label' => 'Young Adult (19 – 30 years old)', 'dues' => '$369', 'tax' => '$400'],
                ];
                foreach ($types as $t):
            ?>
                <div class="column is-full">
                    <div class="card membership-card mb-4">
                        <header class="card-header has-background-link">
                            <p class="card-header-title has-text-white">
                                <?= $t['label'] ?>
                            </p>
                        </header>
                        <div class="card-content">
                            <div class="content">
                                <div class="columns is-mobile">
                                    <div class="column has-text-weight-semibold">
                                        Dues<br><?= $t['dues'] ?>
                                    </div>
                                    <div class="column has-text-weight-semibold">
                                        With tax<br><?= $t['tax'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="content" style="max-width:820px;margin:2rem auto;text-align:left;">
            <h2 class="title is-4">Why Join?</h2>
            <p>
                As a member of OVGC you'll be part of a vibrant community of gardeners,
                learners, and advocates for sustainable growing. Membership helps support
                our programs, gardens, and educational outreach.
            </p>
            <p>
                Ready to become a member? <a href="/contact">Contact us</a> or visit our
                next meeting for more information.
            </p>
        </div>
    </div>
</section>
