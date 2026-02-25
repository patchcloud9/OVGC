<!-- hero for editing user -->
<section class="hero is-dark subpage-hero is-small">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-user-edit"></i> <?= e($title) ?></h1>
            <p class="subtitle is-6 has-text-white">Modify existing user details</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li class="is-active"><a href="#" aria-current="page">Edit <?= e($user['name']) ?></a></li>
            </ul>
        </nav>
        
        <div class="columns is-centered">
            <div class="column is-8-tablet is-6-desktop">
                <div class="box">
                    <form method="POST" action="/admin/users/<?= $user['id'] ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
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
                                    value="<?= e($user['name']) ?>"
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
                                    value="<?= e($user['email']) ?>"
                                    required
                                    maxlength="255">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <p class="help">Must be a valid email address</p>
                        </div>
                        
                        <!-- Password Field (Optional) -->
                        <div class="field">
                            <label class="label">
                                <i class="fas fa-lock"></i> New Password
                            </label>
                            <div class="control has-icons-left">
                                <input 
                                    class="input" 
                                    type="password" 
                                    name="password" 
                                    placeholder="Leave blank to keep current password"
                                    minlength="8"
                                    maxlength="255">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <p class="help">Minimum 8 characters (optional - leave blank to keep current)</p>
                        </div>
                        
                        <!-- Role Field -->
                        <div class="field">
                            <label class="label">
                                <i class="fas fa-user-shield"></i> Role
                            </label>
                            <div class="control has-icons-left">
                                <div class="select is-fullwidth">
                                    <select name="role" required>
                                        <option value="user" <?= ($user['role'] === 'user' || $user['role'] === 'User') ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= ($user['role'] === 'admin' || $user['role'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                            </div>
                            <p class="help">Select user role and permissions</p>
                        </div>
                        
                        <hr>
                        
                        <!-- Action Buttons -->
                        <div class="field is-grouped is-grouped-multiline">
                            <div class="control">
                                <button type="submit" class="button is-primary">
                                    <span class="icon">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    <span>Save Changes</span>
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
                    
                    <hr class="mt-5">
                    
                    <!-- Danger Zone -->
                    <div class="notification is-danger is-light">
                        <p class="has-text-weight-bold mb-3">
                            <i class="fas fa-exclamation-triangle"></i> Danger Zone
                        </p>
                        <p class="mb-3">Once you delete a user, there is no going back. Please be certain.</p>
                        <form method="POST" action="/admin/users/<?= $user['id'] ?>" onsubmit="return confirm('Are you sure you want to delete <?= e($user['name']) ?>? This action cannot be undone!');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="button is-danger">
                                <span class="icon">
                                    <i class="fas fa-trash-alt"></i>
                                </span>
                                <span>Delete User</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
