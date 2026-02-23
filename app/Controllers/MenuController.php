<?php

namespace App\Controllers;

use App\Models\MenuItem;
use App\Services\LogService;
use Core\Validator;

/**
 * Menu Controller
 *
 * Handles admin management of navigation menu items.
 *
 * Admin routes:
 * - GET /admin/menu - List all menu items
 * - GET /admin/menu/create - Show create form
 * - POST /admin/menu - Store new menu item
 * - GET /admin/menu/{id}/edit - Show edit form
 * - PUT /admin/menu/{id} - Update menu item
 * - DELETE /admin/menu/{id} - Delete menu item
 * - POST /admin/menu/reorder - Reorder menu items
 */
class MenuController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    /**
     * List all menu items (admin only)
     */
    public function index(): void
    {
        $menuItems = MenuItem::allWithParents();

        // Separate top-level and children for display
        $topLevel = [];
        $children = [];

        foreach ($menuItems as $item) {
            if ($item['parent_id'] === null) {
                $topLevel[] = $item;
            } else {
                $children[] = $item;
            }
        }

        $this->view('menu/admin', [
            'title' => 'Manage Menu',
            'menuItems' => $menuItems,
            'topLevel' => $topLevel,
            'children' => $children,
        ]);
    }

    /**
     * Show create form (admin only)
     */
    public function create(): void
    {
        $topLevelItems = MenuItem::getTopLevel();

        $this->view('menu/create', [
            'title' => 'Create Menu Item',
            'topLevelItems' => $topLevelItems,
        ]);
    }

    /**
     * Store new menu item (admin only)
     */
    public function store(): void
    {
        // Validate input
        $validator = new Validator(
            [
                'title' => $this->input('title'),
                'url' => $this->input('url'),
                'icon' => $this->input('icon'),
                'parent_id' => $this->input('parent_id'),
                'visibility' => $this->input('visibility'),
            ],
            [
                'title' => 'required|min:1|max:100',
                'url' => 'required|max:255',
                'icon' => 'max:50',
                'visibility' => 'required|in:public,authenticated,admin',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/menu/create');
            return;
        }

        $parentId = $this->input('parent_id') ? (int) $this->input('parent_id') : null;

        // Create menu item
        $menuItem = MenuItem::create([
            'title' => $this->input('title'),
            'url' => $this->input('url'),
            'icon' => $this->input('icon') ?: null,
            'parent_id' => $parentId,
            'display_order' => MenuItem::getNextDisplayOrder($parentId),
            'visibility' => $this->input('visibility'),
            'is_active' => $this->input('is_active') === '1' ? 1 : 0,
            'is_system' => 0, // User-created items are not system items
            'open_new_tab' => $this->input('open_new_tab') === '1' ? 1 : 0,
        ]);

        // Log the action
        $this->logService->add('info', 'Menu item created', [
            'menu_item_id' => $menuItem['id'],
            'title' => $menuItem['title'],
            'user_id' => auth_user()['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        $this->flash('success', 'Menu item created successfully!');
        $this->redirect('/admin/menu');
    }

    /**
     * Show edit form (admin only)
     */
    public function edit(string $id): void
    {
        $menuItemId = (int) $id;
        $menuItem = MenuItem::find($menuItemId);

        if (!$menuItem) {
            $this->flash('danger', 'Menu item not found');
            $this->redirect('/admin/menu');
            return;
        }

        // Get all top-level items for parent selection
        $topLevelItems = MenuItem::getTopLevel();

        $this->view('menu/edit', [
            'title' => 'Edit Menu Item',
            'menuItem' => $menuItem,
            'topLevelItems' => $topLevelItems,
        ]);
    }

    /**
     * Update menu item (admin only)
     */
    public function update(string $id): void
    {
        $menuItemId = (int) $id;
        $menuItem = MenuItem::find($menuItemId);

        if (!$menuItem) {
            $this->flash('danger', 'Menu item not found');
            $this->redirect('/admin/menu');
            return;
        }

        // Prevent editing system menu items
        if ($menuItem['is_system']) {
            $this->flash('danger', 'System menu items cannot be edited');
            $this->redirect('/admin/menu');
            return;
        }

        // Validate input
        $validator = new Validator(
            [
                'title' => $this->input('title'),
                'url' => $this->input('url'),
                'icon' => $this->input('icon'),
                'parent_id' => $this->input('parent_id'),
                'visibility' => $this->input('visibility'),
            ],
            [
                'title' => 'required|min:1|max:100',
                'url' => 'required|max:255',
                'icon' => 'max:50',
                'visibility' => 'required|in:public,authenticated,admin',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/menu/' . $menuItemId . '/edit');
            return;
        }

        $parentId = $this->input('parent_id') ? (int) $this->input('parent_id') : null;

        // Validate parent (prevent circular references)
        if (!MenuItem::validateParent($menuItemId, $parentId)) {
            $this->flash('danger', 'Invalid parent selection. Cannot create circular references.');
            flash_old_input($_POST);
            $this->redirect('/admin/menu/' . $menuItemId . '/edit');
            return;
        }

        // Update menu item
        $success = MenuItem::update($menuItemId, [
            'title' => $this->input('title'),
            'url' => $this->input('url'),
            'icon' => $this->input('icon') ?: null,
            'parent_id' => $parentId,
            'visibility' => $this->input('visibility'),
            'is_active' => $this->input('is_active') === '1' ? 1 : 0,
            'open_new_tab' => $this->input('open_new_tab') === '1' ? 1 : 0,
        ]);

        if ($success) {
            // Log the action
            $this->logService->add('info', 'Menu item updated', [
                'menu_item_id' => $menuItemId,
                'title' => $this->input('title'),
                'user_id' => auth_user()['id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            $this->flash('success', 'Menu item updated successfully!');
        } else {
            $this->flash('warning', 'No changes were made');
        }

        $this->redirect('/admin/menu');
    }

    /**
     * Delete menu item (admin only)
     */
    public function destroy(string $id): void
    {
        $menuItemId = (int) $id;
        $menuItem = MenuItem::find($menuItemId);

        if (!$menuItem) {
            $this->flash('danger', 'Menu item not found');
            $this->redirect('/admin/menu');
            return;
        }

        // Prevent deleting system menu items
        if ($menuItem['is_system']) {
            $this->flash('danger', 'System menu items cannot be deleted');
            $this->redirect('/admin/menu');
            return;
        }

        // Delete (CASCADE will handle children)
        MenuItem::delete($menuItemId);

        // Log the action
        $this->logService->add('info', 'Menu item deleted', [
            'menu_item_id' => $menuItemId,
            'title' => $menuItem['title'],
            'user_id' => auth_user()['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        $this->flash('success', 'Menu item deleted successfully');
        $this->redirect('/admin/menu');
    }

    /**
     * Reorder menu items (admin only)
     */
    public function reorder(): void
    {
        $menuItemId = (int) $this->input('menu_item_id');
        $direction = $this->input('direction');

        if (!in_array($direction, ['up', 'down'])) {
            $this->flash('danger', 'Invalid direction');
            $this->redirect('/admin/menu');
            return;
        }

        $success = MenuItem::swapOrder($menuItemId, $direction);

        if ($success) {
            $this->flash('success', 'Menu order updated');
        } else {
            $this->flash('info', 'Item is already at the ' . ($direction === 'up' ? 'top' : 'bottom'));
        }

        // Log the action
        $this->logService->add('info', 'Menu item reordered', [
            'menu_item_id' => $menuItemId,
            'direction' => $direction,
            'user_id' => auth_user()['id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        $this->redirect('/admin/menu');
    }
}
