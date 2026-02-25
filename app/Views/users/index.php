<!-- hero for Users List -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-users"></i> <?= e($title) ?></h1>
            <p class="subtitle is-6 has-text-white">Manage your users</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Users</a></li>
            </ul>
        </nav>
        
        <div class="level is-mobile" style="margin-top: 0.5rem;">
            <div class="level-right">
                <div class="level-item">
                    <a href="/admin/users/create" class="button is-primary">
                        <span class="icon">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        <span>Add User</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search and Filter Bar -->
        <div class="box">
            <div class="columns is-multiline">
                <div class="column is-half-tablet is-two-thirds-desktop">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by name or email...">
                            <span class="icon is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-half-tablet is-one-third-desktop">
                    <div class="field">
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select id="roleFilter">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <span class="icon is-left">
                                <i class="fas fa-filter"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users Grid -->
        <div id="usersGrid" class="columns is-multiline">
            <?php foreach ($users as $user): ?>
            <div class="column is-12-mobile is-6-tablet is-4-desktop user-card" 
                 data-name="<?= strtolower(e($user['name'])) ?>" 
                 data-email="<?= strtolower(e($user['email'])) ?>"
                 data-role="<?= strtolower($user['role']) ?>">
                <div class="card">
                    <div class="card-content">
                        <div class="media">
                            <div class="media-left">
                                <figure class="image is-48x48">
                                    <div class="has-background-primary has-text-white is-flex is-justify-content-center is-align-items-center" 
                                         style="width: 48px; height: 48px; border-radius: 50%; font-size: 1.5rem; font-weight: bold;">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                </figure>
                            </div>
                            <div class="media-content">
                                <p class="title is-5"><?= e($user['name']) ?></p>
                                <p class="subtitle is-7 has-text-grey">
                                    <i class="fas fa-envelope"></i> <?= e($user['email']) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="content">
                            <div class="tags">
                                <span class="tag <?= $user['role'] === 'Admin' ? 'is-danger' : 'is-info' ?>">
                                    <i class="fas <?= $user['role'] === 'Admin' ? 'fa-crown' : 'fa-user' ?>"></i>
                                    <span class="ml-1"><?= e($user['role']) ?></span>
                                </span>
                                <span class="tag is-light">
                                    <i class="fas fa-id-badge"></i>
                                    <span class="ml-1">ID: <?= $user['id'] ?></span>
                                </span>
                            </div>
                            
                            <div class="buttons are-small">
                                <a href="/admin/users/<?= $user['id'] ?>/edit" class="button is-warning is-fullwidth">
                                    <span class="icon"><i class="fas fa-edit"></i></span>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- No Results Message -->
        <div id="noResults" class="notification is-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i> No users found matching your search criteria.
        </div>
    </div>
</section>

<script>
// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const userCards = document.querySelectorAll('.user-card');
    const noResults = document.getElementById('noResults');
    
    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();
        let visibleCount = 0;
        
        userCards.forEach(card => {
            const name = card.dataset.name;
            const email = card.dataset.email;
            const role = card.dataset.role;
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = roleValue === '' || role === roleValue;
            
            if (matchesSearch && matchesRole) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    
    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
});
</script>
