<?php

namespace App\Controllers;

use App\Models\Rate;
use App\Models\RateGroup;
use App\Services\LogService;
use Core\Validator;

class RateController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    /**
     * List rates for a group
     */
    public function index(string $groupId): void
    {
        $id = (int) $groupId;
        $group = RateGroup::find($id);
        if (!$group) {
            $this->flash('danger', 'Rate group not found');
            $this->redirect('/admin/rates');
            return;
        }

        $rates = Rate::where(['group_id' => $id]);

        $this->view('rates/group_rates', [
            'title' => 'Rates for ' . $group['title'],
            'group' => $group,
            'rates' => $rates,
        ]);
    }

    /**
     * Show form to create rate under group
     */
    public function create(string $groupId): void
    {
        $id = (int) $groupId;
        $group = RateGroup::find($id);
        if (!$group) {
            $this->flash('danger', 'Rate group not found');
            $this->redirect('/admin/rates');
            return;
        }
        $this->view('rates/create_rate', [
            'title' => 'Create Rate',
            'group' => $group,
        ]);
    }

    /**
     * Store new rate
     */
    public function store(string $groupId): void
    {
        $id = (int) $groupId;
        $group = RateGroup::find($id);
        if (!$group) {
            $this->flash('danger', 'Rate group not found');
            $this->redirect('/admin/rates');
            return;
        }

        $validator = new Validator(
            [
                'description' => $this->input('description'),
                'price' => $this->input('price'),
                'sort_order' => $this->input('sort_order'),
                'notes' => $this->input('notes'),
            ],
            [
                'description' => 'required|max:100',
                'price' => 'required|numeric',
                'sort_order' => 'numeric',
                'notes' => 'max:150',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect("/admin/rates/{$id}/rates/create");
            return;
        }

        $rate = Rate::create([
            'group_id' => $id,
            'description' => $this->input('description'),
            'price' => (float) $this->input('price'),
            'sort_order' => (int) $this->input('sort_order'),
            'notes' => $this->input('notes'),
        ]);

        $this->logService->add('info', 'Rate created', ['rate_id' => $rate['id'], 'group_id' => $id]);
        $this->flash('success', 'Rate created successfully!');
        $this->redirect("/admin/rates/{$id}/rates");
    }

    /**
     * Show edit form for a rate
     */
    public function edit(string $groupId, string $rateId): void
    {
        $gid = (int) $groupId;
        $rid = (int) $rateId;
        $group = RateGroup::find($gid);
        $rate = Rate::find($rid);
        if (!$group || !$rate) {
            $this->flash('danger', 'Rate or group not found');
            $this->redirect('/admin/rates');
            return;
        }
        $this->view('rates/edit_rate', [
            'title' => 'Edit Rate',
            'group' => $group,
            'rate' => $rate,
        ]);
    }

    /**
     * Update a rate
     */
    public function update(string $groupId, string $rateId): void
    {
        $gid = (int) $groupId;
        $rid = (int) $rateId;
        $group = RateGroup::find($gid);
        $rate = Rate::find($rid);
        if (!$group || !$rate) {
            $this->flash('danger', 'Rate or group not found');
            $this->redirect('/admin/rates');
            return;
        }

        $validator = new Validator(
            [
                'description' => $this->input('description'),
                'price' => $this->input('price'),
                'sort_order' => $this->input('sort_order'),
                'notes' => $this->input('notes'),
            ],
            [
                'description' => 'required|max:100',
                'price' => 'required|numeric',
                'sort_order' => 'numeric',
                'notes' => 'max:150',
            ]
        );

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect("/admin/rates/{$gid}/rates/{$rid}/edit");
            return;
        }

        Rate::update($rid, [
            'description' => $this->input('description'),
            'price' => (float) $this->input('price'),
            'sort_order' => (int) $this->input('sort_order'),
            'notes' => $this->input('notes'),
        ]);

        $this->logService->add('info', 'Rate updated', ['rate_id' => $rid]);
        $this->flash('success', 'Rate updated successfully!');
        $this->redirect("/admin/rates/{$gid}/rates");
    }

    /**
     * Delete a rate
     */
    public function destroy(string $groupId, string $rateId): void
    {
        $gid = (int) $groupId;
        $rid = (int) $rateId;
        $rate = Rate::find($rid);
        if (!$rate) {
            $this->flash('danger', 'Rate not found');
            $this->redirect('/admin/rates');
            return;
        }
        Rate::delete($rid);
        $this->logService->add('info', 'Rate deleted', ['rate_id' => $rid]);
        $this->flash('success', 'Rate deleted');
        $this->redirect("/admin/rates/{$gid}/rates");
    }
}
