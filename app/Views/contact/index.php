<?php
$layout = 'main';
?>

<!-- top map (replace coordinates with actual club location) -->
<section class="map-container">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.019611501056!2d-120.34046858468103!3d48.469225679266834!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x540b2de9b5f3aef3%3A0x123456789abcdef!2sOkanogan%20Valley%20Golf%20Course!5e0!3m2!1sen!2sus!4v1698000000000"
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
