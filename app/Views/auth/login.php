<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <div class="columns is-centered">
            <div class="column is-5-tablet is-4-desktop">
                <div class="box">
                    <h1 class="title has-text-centered">Login</h1>
                    
                    <form method="POST" action="/login">
                        <?= csrf_field() ?>
                        
                        <!-- Email -->
                        <div class="field">
                            <label class="label" for="email">Email</label>
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
                        
                        <!-- Password -->
                        <div class="field">
                            <label class="label" for="password">Password</label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Your password"
                                    required
                                >
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-primary is-fullwidth">
                                    Login
                                </button>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <p class="has-text-centered">
                            Don't have an account? 
                            <a href="/register">Register here</a>
                        </p>
                    </form>
                </div>
                
                <div class="content has-text-centered">
                    <p class="help">
                        <strong>Security:</strong> CSRF protected, rate limited (5 attempts per 5 minutes)
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
