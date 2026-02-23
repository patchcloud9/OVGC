<?php
// Get flash messages if any
$flashMessages = $_SESSION['flash'] ?? null;
if ($flashMessages) {
    unset($_SESSION['flash']);
    
    // Ensure it's an array (backwards compatibility)
    if (!is_array($flashMessages) || isset($flashMessages['type'])) {
        // Old format: single message object
        $flashMessages = [$flashMessages];
    }
    
    // Map our flash types to Bulma notification classes
    $typeClasses = [
        'success' => 'is-success',
        'error'   => 'is-danger',
        'warning' => 'is-warning',
        'info'    => 'is-info',
    ];
?>
<div class="container mt-4 mb-5">
    <?php foreach ($flashMessages as $flash): ?>
        <?php $bulmaClass = $typeClasses[$flash['type']] ?? 'is-info'; ?>
        <div class="notification <?= $bulmaClass ?> mb-2">
            <button class="delete" onclick="this.parentElement.remove()"></button>
            <?= e($flash['message']) ?>
        </div>
    <?php endforeach; ?>
</div>
<?php } ?>
