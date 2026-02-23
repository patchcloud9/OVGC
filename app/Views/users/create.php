<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li class="is-active"><a href="#" aria-current="page">Create New User</a></li>
            </ul>
        </nav>
        
        <div class="columns is-centered">
            <div class="column is-8-tablet is-6-desktop">
                <div class="box">
                    <h1 class="title is-4">
                        <i class="fas fa-user-plus"></i> <?= e($title) ?>
                    </h1>
                    <p class="subtitle is-6">Add a new user to the system</p>
                    
                    <form method="POST" action="/admin/users">
                        <?= csrf_field() ?>
                        
                        <!-- Name Field -->
                        <div class="field">
                            <label class="label">
                                <i class="fas fa-user"></i> Name
                            </label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="text" 
                                    name="name" 
                                    placeholder="John Doe"
                                    value="<?= old('name') ?>"
                                    required
                                    minlength="2"
                                    maxlength="100">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <p class="help">Minimum 2 characters, maximum 100</p>
                        </div>
                        
                        <!-- Email Field -->
                        <div class="field">
                            <label class="label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="email" 
                                    name="email" 
                                    placeholder="john@example.com"
                                    value="<?= old('email') ?>"
                                    required
                                    maxlength="255">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <p class="help">Must be a valid email address</p>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="field">
                            <label class="label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="password" 
                                    name="password" 
                                    placeholder="••••••••"
                                    required
                                    minlength="8"
                                    maxlength="255">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <p class="help">Minimum 8 characters</p>
                        </div>
                        
                        <!-- Role Field -->
                        <div class="field">
                            <label class="label">
                                <i class="fas fa-user-shield"></i> Role
                            </label>
                            <div class="control has-icons-left">
                                <div class="select is-fullwidth">
                                    <select name="role" required>
                                        <option value="user" <?= old('role') === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                            </div>
                            <p class="help">Select user role and permissions</p>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="field is-grouped">
                            <div class="control">
                                <button type="submit" class="button is-primary">
                                    <span class="icon">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span>Create User</span>
                                </button>
                            </div>
                            <div class="control">
                                <a href="/admin/users" class="button is-light">
                                    <span class="icon">
                                        <i class="fas fa-times"></i>
                                    </span>
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <div class="notification is-info is-light mt-5">
                        <p class="has-text-weight-bold">
                            <i class="fas fa-info-circle"></i> Security Notice
                        </p>
                        <p class="is-size-7 mt-2">
                            This form is protected by CSRF tokens and rate limiting (3 attempts per 5 minutes).
                            Passwords are hashed using bcrypt before storage.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
