<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <div class="columns">
            <div class="column is-6 is-offset-3">
                <h1 class="title"><?= e($title) ?></h1>
                
                <div class="box">
                    <form method="POST" action="/contact">
                        <?= csrf_field() ?>
                        <div class="field">
                            <label class="label">Name</label>
                            <div class="control">
                                <input class="input" type="text" name="name" placeholder="Your name" 
                                       value="<?= e(old('name')) ?>" required>
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control">
                                <input class="input" type="email" name="email" placeholder="your@email.com" 
                                       value="<?= e(old('email')) ?>" required>
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">Message</label>
                            <div class="control">
                                <textarea class="textarea" name="message" placeholder="Your message" required><?= e(old('message')) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="notification is-info is-light">
                    <strong>Protected:</strong> This form has CSRF protection, rate limiting (5 per minute), 
                    and input validation. Try submitting invalid data or too many times!
                </div>
            </div>
        </div>
    </div>
</section>
