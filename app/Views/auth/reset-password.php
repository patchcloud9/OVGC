<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <div class="columns is-centered">
            <div class="column is-5-tablet is-4-desktop">
                <div class="box">
                    <h1 class="title has-text-centered">Reset Password</h1>
                    <p class="has-text-grey has-text-centered mb-4">
                        Choose a new password for <strong><?= e($email) ?></strong>.
                    </p>

                    <form method="POST" action="/password/reset">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= e($token) ?>">
                        <input type="hidden" name="email" value="<?= e($email) ?>">

                        <div class="field">
                            <label class="label" for="password">New Password</label>
                            <div class="control has-icons-left">
                                <input
                                    class="input"
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="At least 8 characters"
                                    minlength="8"
                                    required
                                    autofocus
                                >
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="password_confirmation">Confirm New Password</label>
                            <div class="control has-icons-left">
                                <input
                                    class="input"
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="Repeat your new password"
                                    minlength="8"
                                    required
                                >
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-primary is-fullwidth">
                                    Reset Password
                                </button>
                            </div>
                        </div>

                        <hr>

                        <p class="has-text-centered">
                            <a href="/password/forgot">Request a new link</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
