<?php

namespace App\Controllers;

use App\Models\RateGroup;
use App\Services\LogService;
use Core\Validator;

class RateGroupController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    /**
     * List all rate groups (admin)
     */
    public function index(): void
    {
        $groups = RateGroup::allGroups();
        $this->view('rates/admin', [
            'title' => 'Manage Rate Groups',
            'groups' => $groups,
        ]);
    }

    /**
     * Show form to create a new group
     */
    public function create(): void
    {
        $this->view('rates/create_group', [
            'title' => 'Create Rate Group',
        ]);
    }

    /**
     * Store new group
     */
    public function store(): void
    {
        $validator = new Validator(
            [
                'slug' => $this->input('slug'),
                'title' => $this->input('title'),
                'subtitle' => $this->input('subtitle'),
                'note' => $this->input('note'),
                'sort_order' => $this->input('sort_order'),
                'active' => $this->input('active'),
            ],
            [
                'slug' => 'required|alpha_dash|max:60',
                'title' => 'required|max:100',
                'subtitle' => 'max:150',
                'note' => 'max:500',
                'sort_order' => 'numeric',
                'active' => 'in:0,1',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/rates/create');
            return;
        }

        $group = RateGroup::create([
            'slug' => $this->input('slug'),
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'note' => $this->input('note'),
            'sort_order' => (int) $this->input('sort_order'),
            'active' => $this->input('active') === '1' ? 1 : 0,
        ]);

        $this->logService->add('info', 'Rate group created', ['group_id' => $group['id'], 'slug' => $group['slug']]);
        $this->flash('success', 'Rate group created successfully!');
        $this->redirect('/admin/rates');
    }

    /**
     * Show edit form for a group
     */
    public function edit(string $id): void
    {
        $groupId = (int) $id;
        $group = RateGroup::find($groupId);
        if (!$group) {
            $this->flash('danger', 'Rate group not found');
            $this->redirect('/admin/rates');
            return;
        }

        $this->view('rates/edit_group', [
            'title' => 'Edit Rate Group',
            'group' => $group,
        ]);
    }

    /**
     * Update a group
     */
    public function update(string $id): void
    {
        $groupId = (int) $id;
        $group = RateGroup::find($groupId);
        if (!$group) {
            $this->flash('danger', 'Rate group not found');
            $this->redirect('/admin/rates');
            return;
        }

        $validator = new Validator(
            [
                'slug' => $this->input('slug'),
                'title' => $this->input('title'),
                'subtitle' => $this->input('subtitle'),
                'note' => $this->input('note'),
                'sort_order' => $this->input('sort_order'),
                'active' => $this->input('active'),
            ],
            [
                'slug' => 'required|alpha_dash|max:60',
                'title' => 'required|max:100',
                'subtitle' => 'max:150',
                'note' => 'max:500',
                'sort_order' => 'numeric',
                'active' => 'in:0,1',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect("/admin/rates/{$groupId}/edit");
            return;
        }

        RateGroup::update($groupId, [
            'slug' => $this->input('slug'),
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'note' => $this->input('note'),
            'sort_order' => (int) $this->input('sort_order'),
            'active' => $this->input('active') === '1' ? 1 : 0,
        ]);

        $this->logService->add('info', 'Rate group updated', ['group_id' => $groupId]);
        $this->flash('success', 'Rate group updated successfully!');
        $this->redirect('/admin/rates');
    }

    /**
     * Delete a group
     */
    public function destroy(string $id): void
    {
        $groupId = (int) $id;
        $group = RateGroup::find($groupId);
        if (!$group) {
            $this->flash('danger', 'Rate group not found');
            $this->redirect('/admin/rates');
            return;
        }

        RateGroup::delete($groupId);
        $this->logService->add('info', 'Rate group deleted', ['group_id' => $groupId]);
        $this->flash('success', 'Rate group deleted');
        $this->redirect('/admin/rates');
    }
}
