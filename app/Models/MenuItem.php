<?php

namespace App\Models;

/**
 * MenuItem Model
 *
 * Represents a navigation menu item with hierarchical support.
 *
 * Usage:
 *   $items = MenuItem::getMenuStructure();
 *   $item = MenuItem::find(1);
 *   MenuItem::create(['title' => 'Blog', 'url' => '/blog', ...]);
 */
class MenuItem extends Model
{
    protected string $table = 'menu_items';

    protected array $fillable = [
        'title',
        'url',
        'icon',
        'parent_id',
        'display_order',
        'visibility',
        'is_active',
        'is_system',
        'open_new_tab',
    ];

    protected bool $timestamps = true;

    /**
     * Get hierarchical menu structure (top-level items with their children)
     * Only returns active items
     *
     * @param string|null $visibility Filter by visibility ('public', 'authenticated', 'admin')
     * @return array Structured menu with 'children' key for each parent
     */
    public static function getMenuStructure(?string $visibility = null): array
    {
        $instance = new static();
        $db = $instance->getDatabase();

        // Get all active menu items
        $sql = "SELECT * FROM {$instance->table}
                WHERE is_active = 1
                ORDER BY display_order ASC, id ASC";

        $allItems = $db->fetchAll($sql);

        // Build parent-child structure
        $topLevel = [];
        $childrenByParent = [];

        foreach ($allItems as $item) {
            // Filter by visibility if specified
            if ($visibility && !static::isVisible($item, $visibility)) {
                continue;
            }

            if ($item['parent_id'] === null) {
                $topLevel[] = $item;
            } else {
                $parentId = (int) $item['parent_id'];
                if (!isset($childrenByParent[$parentId])) {
                    $childrenByParent[$parentId] = [];
                }
                $childrenByParent[$parentId][] = $item;
            }
        }

        // Attach children to parents
        foreach ($topLevel as &$parent) {
            $parentId = (int) $parent['id'];
            $parent['children'] = $childrenByParent[$parentId] ?? [];
        }

        return $topLevel;
    }

    /**
     * Check if a menu item should be visible based on visibility setting
     *
     * @param array $item Menu item data
     * @param string $userStatus 'public', 'authenticated', or 'admin'
     * @return bool
     */
    private static function isVisible(array $item, string $userStatus): bool
    {
        $visibility = $item['visibility'];

        // Public items visible to everyone
        if ($visibility === 'public') {
            return true;
        }

        // Authenticated items require logged in
        if ($visibility === 'authenticated' && in_array($userStatus, ['authenticated', 'admin'])) {
            return true;
        }

        // Admin items require admin role
        if ($visibility === 'admin' && $userStatus === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Get current user's visibility level for menu filtering
     *
     * @return string 'public', 'authenticated', or 'admin'
     */
    public static function getUserVisibilityLevel(): string
    {
        if (is_admin()) {
            return 'admin';
        }

        if (is_authenticated()) {
            return 'authenticated';
        }

        return 'public';
    }

    /**
     * Get all top-level menu items (no parent)
     */
    public static function getTopLevel(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}
                WHERE parent_id IS NULL
                ORDER BY display_order ASC, id ASC";

        return $instance->getDatabase()->fetchAll($sql);
    }

    /**
     * Retrieve a list of URLs for non-admin pages (active, not admin visibility)
     *
     * Used when building dropdowns or suggestions for page-related features
     * such as banner targeting.
     *
     * @return string[]
     */
    public static function getNonAdminUrls(): array
    {
        $instance = new static();
        // include display_order so ORDER BY works with DISTINCT; we'll discard it afterwards
        // also filter out any URL containing a '#' character and ignore pure "/" duplicates
        $sql = "SELECT DISTINCT url, display_order FROM {$instance->table} 
                WHERE is_active = 1 
                  AND visibility != 'admin' 
                  AND url NOT LIKE '/admin%' 
                  AND url NOT LIKE '%#%' 
                ORDER BY display_order ASC, url ASC";

        $rows = $instance->getDatabase()->fetchAll($sql);
        $urls = array_map(fn($r) => $r['url'], $rows);
        // normalize duplicates and strip trailing slashes on urls for consistency
        $clean = [];
        foreach ($urls as $u) {
            $norm = rtrim($u, '/');
            if ($norm === '') {
                $norm = '/';
            }
            if (!in_array($norm, $clean, true)) {
                $clean[] = $norm;
            }
        }
        return $clean;
    }

    /**
     * Get children of a specific menu item
     */
    public static function getChildren(int $parentId): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table}
                WHERE parent_id = ?
                ORDER BY display_order ASC, id ASC";

        return $instance->getDatabase()->fetchAll($sql, [$parentId]);
    }

    /**
     * Get all menu items with parent info (for admin display)
     */
    public static function allWithParents(): array
    {
        $instance = new static();
        $sql = "SELECT mi.*,
                       parent.title as parent_title
                FROM {$instance->table} mi
                LEFT JOIN {$instance->table} parent ON mi.parent_id = parent.id
                ORDER BY mi.display_order ASC, mi.id ASC";

        return $instance->getDatabase()->fetchAll($sql);
    }

    /**
     * Get next display order for new items
     */
    public static function getNextDisplayOrder(?int $parentId = null): int
    {
        $instance = new static();

        if ($parentId === null) {
            $sql = "SELECT COALESCE(MAX(display_order), 0) + 1 as next_order
                    FROM {$instance->table}
                    WHERE parent_id IS NULL";
            $result = $instance->getDatabase()->fetch($sql);
        } else {
            $sql = "SELECT COALESCE(MAX(display_order), 0) + 1 as next_order
                    FROM {$instance->table}
                    WHERE parent_id = ?";
            $result = $instance->getDatabase()->fetch($sql, [$parentId]);
        }

        return (int) $result['next_order'];
    }

    /**
     * Swap display order with another menu item
     */
    public static function swapOrder(int $menuItemId, string $direction): bool
    {
        $instance = new static();
        $db = $instance->getDatabase();

        // Get current item
        $currentItem = static::find($menuItemId);
        if (!$currentItem) {
            return false;
        }

        $currentOrder = (int) $currentItem['display_order'];
        $parentId = $currentItem['parent_id'];

        // Build parent constraint for query
        $parentConstraint = $parentId === null ? 'IS NULL' : '= ' . (int) $parentId;

        // Find adjacent item
        if ($direction === 'up') {
            $sql = "SELECT * FROM {$instance->table}
                    WHERE display_order < ?
                    AND parent_id {$parentConstraint}
                    ORDER BY display_order DESC
                    LIMIT 1";
        } else {
            $sql = "SELECT * FROM {$instance->table}
                    WHERE display_order > ?
                    AND parent_id {$parentConstraint}
                    ORDER BY display_order ASC
                    LIMIT 1";
        }

        $adjacentItem = $db->fetch($sql, [$currentOrder]);

        if (!$adjacentItem) {
            return false; // Already at top/bottom
        }

        $adjacentOrder = (int) $adjacentItem['display_order'];

        // Swap the orders
        try {
            $db->beginTransaction();

            static::update($menuItemId, ['display_order' => $adjacentOrder]);
            static::update((int) $adjacentItem['id'], ['display_order' => $currentOrder]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            return false;
        }
    }

    /**
     * Validate that parent_id doesn't create circular reference
     */
    public static function validateParent(int $itemId, ?int $parentId): bool
    {
        // NULL parent is always valid
        if ($parentId === null) {
            return true;
        }

        // Can't be its own parent
        if ($itemId === $parentId) {
            return false;
        }

        // Check if parentId would create a circular reference
        // (parent can't be a child of the item being edited)
        $children = static::getChildren($itemId);
        foreach ($children as $child) {
            if ((int) $child['id'] === $parentId) {
                return false;
            }
        }

        return true;
    }
}
