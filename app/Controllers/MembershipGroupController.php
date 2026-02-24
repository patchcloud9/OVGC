<?php

namespace App\Controllers;

use App\Models\MembershipGroup;
use App\Services\LogService;
use Core\Validator;

class MembershipGroupController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    public function index(): void
    {
        $groups = MembershipGroup::allGroups();
        $this->view('membership/admin', [
            'title' => 'Manage Membership Groups',
            'groups' => $groups,
        ]);
    }

    public function create(): void
    {
        $this->view('membership/create_group', [
            'title' => 'Create Membership Group',
        ]);
    }

    public function store(): void
    {
        $validator = new Validator([
            'slug' => $this->input('slug'),
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'note' => $this->input('note'),
            'sort_order' => $this->input('sort_order'),
            'active' => $this->input('active'),
        ], [
            'slug' => 'required|alpha_dash|max:60',
            'title' => 'required|max:100',
            'subtitle' => 'max:150',
            'note' => 'max:500',
            'sort_order' => 'numeric',
            'active' => 'in:0,1',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/membership/create');
            return;
        }

        $group = MembershipGroup::create([
            'slug' => $this->input('slug'),
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'note' => $this->input('note'),
            'sort_order' => (int) $this->input('sort_order'),
            'active' => $this->input('active') === '1' ? 1 : 0,
        ]);

        $this->logService->add('info', 'Membership group created', ['group_id' => $group['id']]);
        $this->flash('success', 'Membership group created successfully!');
        $this->redirect('/admin/membership');
    }

    public function edit(string $id): void
    {
        $groupId = (int) $id;
        $group = MembershipGroup::find($groupId);
        if (!$group) {
            $this->flash('danger', 'Membership group not found');
            $this->redirect('/admin/membership');
            return;
        }
        $this->view('membership/edit_group', [
            'title' => 'Edit Membership Group',
            'group' => $group,
        ]);
    }

    public function update(string $id): void
    {
        $groupId = (int) $id;
        $group = MembershipGroup::find($groupId);
        if (!$group) {
            $this->flash('danger', 'Membership group not found');
            $this->redirect('/admin/membership');
            return;
        }

        $validator = new Validator([
            'slug' => $this->input('slug'),
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'note' => $this->input('note'),
            'sort_order' => $this->input('sort_order'),
            'active' => $this->input('active'),
        ], [
            'slug' => 'required|alpha_dash|max:60',
            'title' => 'required|max:100',
            'subtitle' => 'max:150',
            'note' => 'max:500',
            'sort_order' => 'numeric',
            'active' => 'in:0,1',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect("/admin/membership/{$groupId}/edit");
            return;
        }

        MembershipGroup::update($groupId, [
            'slug' => $this->input('slug'),
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'note' => $this->input('note'),
            'sort_order' => (int) $this->input('sort_order'),
            'active' => $this->input('active') === '1' ? 1 : 0,
        ]);

        $this->logService->add('info', 'Membership group updated', ['group_id' => $groupId]);
        $this->flash('success', 'Membership group updated successfully!');
        $this->redirect('/admin/membership');
    }

    public function destroy(string $id): void
    {
        $groupId = (int) $id;
        $group = MembershipGroup::find($groupId);
        if (!$group) {
            $this->flash('danger', 'Membership group not found');
            $this->redirect('/admin/membership');
            return;
        }
        MembershipGroup::delete($groupId);
        $this->logService->add('info', 'Membership group deleted', ['group_id' => $groupId]);
        $this->flash('success', 'Membership group deleted');
        $this->redirect('/admin/membership');
    }
}
