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

        <div class="columns is-multiline">
            <div class="column is-one-third">
                <div class="card">
                    <div class="card-content">
                        <p class="title is-5">Individual</p>
                        <p class="subtitle is-6">$50 &ndash; per year</p>
                        <ul>
                            <li>Access to all club events</li>
                            <li>Monthly newsletter</li>
                            <li>10% discount on supplies</li>
                            <li>Voting rights</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="column is-one-third">
                <div class="card">
                    <div class="card-content">
                        <p class="title is-5">Family</p>
                        <p class="subtitle is-6">$120 &ndash; per year</p>
                        <ul>
                            <li>All Individual benefits for two adults</li>
                            <li>Children under 18 free</li>
                            <li>Priority registration for workshops</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="column is-one-third">
                <div class="card">
                    <div class="card-content">
                        <p class="title is-5">Corporate</p>
                        <p class="subtitle is-6">$300 &ndash; per year</p>
                        <ul>
                            <li>All Family benefits</li>
                            <li>Company listing on website</li>
                            <li>Complimentary event space</li>
                        </ul>
                    </div>
                </div>
            </div>
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
