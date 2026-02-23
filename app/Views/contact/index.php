<?php
$layout = 'main';
?>

<!-- top map (replace coordinates with actual club location) -->
<section class="map-container">
    <iframe
        src="https://www.google.com/maps/place/Okanogan+Valley+Golf+Club/@48.3685257,-119.5831589,13z/data=!4m6!3m5!1s0x549cea2b0d7cf30f:0x3a7f09b5cf8f42c4!8m2!3d48.3952102!4d-119.5804748!16s%2Fg%2F1tzlypct?entry=ttu&g_ep=EgoyMDI2MDIxOC4wIKXMDSoASAFQAw%3D%3D"
        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="content" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <!-- replace with contact-specific content later -->
            <p>Use this page to display contact information or a form.</p>
        </div>
    </div>
</section>
