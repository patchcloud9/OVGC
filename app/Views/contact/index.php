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

        <div class="columns is-variable is-8">
            <div class="column is-half">
                <div class="box has-background-light">
                    <h2 class="title is-4 has-text-centered">CONTACT INFO</h2>
                    <div class="content has-text-centered">
                        <?php $name = theme_setting('site_name'); ?>
                        <?php $addr1 = theme_setting('address1'); ?>
                        <?php $addr2 = theme_setting('address2'); ?>
                        <?php $cityzip = theme_setting('city_state_zip'); ?>
                        <?php $email = theme_setting('contact_email'); ?>
                        <?php $phone = theme_setting('phone_number'); ?>

                        <?php if (!empty($name)): ?>
                            <p><strong><?= e($name) ?></strong></p>
                        <?php endif; ?>
                        <?php if (!empty($addr1)): ?>
                            <p><?= nl2br(e($addr1)) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($addr2)): ?>
                            <p><?= nl2br(e($addr2)) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($cityzip)): ?>
                            <p><?= nl2br(e($cityzip)) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="notification is-light has-text-centered">
                        <?php if (!empty($email)): ?>
                            <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                        <?php endif; ?>
                        <?php if (!empty($phone)): ?>
                            <br>
                            <span class="icon"><i class="fas fa-phone"></i></span> <a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="column is-half">
                <div class="box has-background-light">
                    <h2 class="title is-4 has-text-centered">Send a Message</h2>
                    <form method="POST" action="/contact" style="max-width:600px;margin:0 auto;">
                    <div class="field">
                        <label class="label">Name</label>
                        <div class="control">
                            <input class="input" type="text" name="name" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Phone Number</label>
                        <div class="control">
                            <input class="input" type="tel" name="phone">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input class="input" type="email" name="email" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Comment</label>
                        <div class="control">
                            <textarea class="textarea" name="comment" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="field is-grouped is-grouped-centered">
                        <div class="control">
                            <button type="submit" class="button is-primary">Submit</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>
