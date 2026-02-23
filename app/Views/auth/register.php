<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <div class="columns is-centered">
            <div class="column is-6-tablet is-5-desktop">
                <div class="box">
                    <h1 class="title has-text-centered">Create Account</h1>
                    
                    <form method="POST" action="/register">
                        <?= csrf_field() ?>
                        
                        <!-- Name -->
                        <div class="field">
                            <label class="label" for="name">Name</label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="<?= e(old('name')) ?>"
                                    placeholder="John Doe"
                                    required
                                    autofocus
                                >
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <p class="help">Minimum 2 characters, maximum 100</p>
                        </div>
                        
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
                                    placeholder="Minimum 8 characters"
                                    required
                                >
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <p class="help">Minimum 8 characters</p>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="field">
                            <label class="label" for="password_confirmation">Confirm Password</label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Re-enter your password"
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
                                    Create Account
                                </button>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <p class="has-text-centered">
                            Already have an account? 
                            <a href="/login">Login here</a>
                        </p>
                    </form>
                </div>
                
                <div class="content has-text-centered">
                    <p class="help">
                        <strong>Security:</strong> CSRF protected, rate limited (3 registrations per 10 minutes), email uniqueness checked
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
