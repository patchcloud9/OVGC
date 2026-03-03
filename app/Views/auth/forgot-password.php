<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns is-centered">
            <div class="column is-5-tablet is-4-desktop">
                <div class="box">
                    <h1 class="title has-text-centered">Forgot Password</h1>
                    <p class="has-text-grey has-text-centered mb-4">
                        Enter your email address and we will send you a password reset link.
                    </p>

                    <form method="POST" action="/password/forgot">
                        <?= csrf_field() ?>

                        <div class="field">
                            <label class="label" for="email">Email Address</label>
                            <div class="control has-icons-left">
                                <input
                                    class="input"
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?= e(old('email')) ?>"
                                    placeholder="you@example.com"
                                    required
                                    autofocus
                                >
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-primary is-fullwidth">
                                    Send Reset Link
                                </button>
                            </div>
                        </div>

                        <hr>

                        <p class="has-text-centered">
                            Remember your password? <a href="/login">Back to login</a>
                        </p>
                    </form>
                </div>

                <div class="content has-text-centered">
                    <p class="help">
                        <strong>Security:</strong> CSRF protected, rate limited (3 requests per 10 minutes)
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
