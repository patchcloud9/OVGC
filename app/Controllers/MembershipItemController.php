<?php

namespace App\Controllers;

use App\Models\MembershipItem;
use App\Models\MembershipGroup;
use App\Services\LogService;
use Core\Validator;

class MembershipItemController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    public function index(string $groupId): void
    {
        $gid = (int) $groupId;
        $group = MembershipGroup::find($gid);
        if (!$group) {
            $this->flash('danger', 'Membership group not found');
            $this->redirect('/admin/membership');
            return;
        }
        $items = MembershipItem::where(['group_id' => $gid]);
        $this->view('membership/group_items', [
            'title' => 'Items for ' . $group['title'],
            'group' => $group,
            'items' => $items,
        ]);
    }

    public function create(string $groupId): void
    {
        $gid = (int) $groupId;
        $group = MembershipGroup::find($gid);
        if (!$group) {
            $this->flash('danger', 'Membership group not found');
            $this->redirect('/admin/membership');
            return;
        }
        $this->view('membership/create_item', [
            'title' => 'Create Item',
            'group' => $group,
        ]);
    }

    public function store(string $groupId): void
    {
        $gid = (int) $groupId;
        $group = MembershipGroup::find($gid);
        if (!$group) {
            $this->flash('danger', 'Membership group not found');
            $this->redirect('/admin/membership');
            return;
        }
        $validator = new Validator([
            'name' => $this->input('name'),
            'price' => $this->input('price'),
            'sort_order' => $this->input('sort_order'),
            'notes' => $this->input('notes'),
        ], [
            'name' => 'required|max:100',
            'price' => 'required|numeric',
            'sort_order' => 'numeric',
            'notes' => 'max:150',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect("/admin/membership/{$gid}/items/create");
            return;
        }
        $item = MembershipItem::create([
            'group_id' => $gid,
            'name' => $this->input('name'),
            'price' => (float) $this->input('price'),
            'sort_order' => (int) $this->input('sort_order'),
            'notes' => $this->input('notes'),
        ]);
        $this->logService->add('info', 'Membership item created', ['item_id' => $item['id'], 'group_id' => $gid]);
        $this->flash('success', 'Item created successfully!');
        $this->redirect("/admin/membership/{$gid}/items");
    }

    public function edit(string $groupId, string $itemId): void
    {
        $gid = (int) $groupId;
        $rid = (int) $itemId;
        $group = MembershipGroup::find($gid);
        $item = MembershipItem::find($rid);
        if (!$group || !$item) {
            $this->flash('danger', 'Group or item not found');
            $this->redirect('/admin/membership');
            return;
        }
        $this->view('membership/edit_item', [
            'title' => 'Edit Item',
            'group' => $group,
            'item' => $item,
        ]);
    }

    public function update(string $groupId, string $itemId): void
    {
        $gid = (int) $groupId;
        $rid = (int) $itemId;
        $group = MembershipGroup::find($gid);
        $item = MembershipItem::find($rid);
        if (!$group || !$item) {
            $this->flash('danger', 'Group or item not found');
            $this->redirect('/admin/membership');
            return;
        }
        $validator = new Validator([
            'name' => $this->input('name'),
            'price' => $this->input('price'),
            'sort_order' => $this->input('sort_order'),
            'notes' => $this->input('notes'),
        ], [
            'name' => 'required|max:100',
            'price' => 'required|numeric',
            'sort_order' => 'numeric',
            'notes' => 'max:150',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect("/admin/membership/{$gid}/items/{$rid}/edit");
            return;
        }
        MembershipItem::update($rid, [
            'name' => $this->input('name'),
            'price' => (float) $this->input('price'),
            'sort_order' => (int) $this->input('sort_order'),
            'notes' => $this->input('notes'),
        ]);
        $this->logService->add('info', 'Membership item updated', ['item_id' => $rid]);
        $this->flash('success', 'Item updated successfully!');
        $this->redirect("/admin/membership/{$gid}/items");
    }

    public function destroy(string $groupId, string $itemId): void
    {
        $gid = (int) $groupId;
        $rid = (int) $itemId;
        $item = MembershipItem::find($rid);
        if (!$item) {
            $this->flash('danger', 'Item not found');
            $this->redirect('/admin/membership');
            return;
        }
        MembershipItem::delete($rid);
        $this->logService->add('info', 'Membership item deleted', ['item_id' => $rid]);
        $this->flash('success', 'Item deleted');
        $this->redirect("/admin/membership/{$gid}/items");
    }
}
