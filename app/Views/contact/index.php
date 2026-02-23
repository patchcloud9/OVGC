<?php
$layout = 'main';
?>

<!-- top map (replace coordinates with actual club location) -->
<section class="map-container">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2649.1941939107605!2d-119.58304972326505!3d48.39521373318963!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x549cea2b0d7cf30f%3A0x3a7f09b5cf8f42c4!2sOkanogan%20Valley%20Golf%20Club!5e0!3m2!1sen!2sus!4v1771885563072!5m2!1sen!2sus"
        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
