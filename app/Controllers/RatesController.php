<?php

namespace App\Controllers;

class RatesController extends Controller
{
    /**
     * Display the green fees & cart rentals page.
     * Route: GET /rates
     */
    public function index(): void
    {
        // load active groups and their rates
        $groups = \App\Models\RateGroup::allGroups(true);
        foreach ($groups as &$g) {
            $g['rates'] = \App\Models\Rate::where(['group_id' => $g['id']]);
            // sort by sort_order just in case
            usort($g['rates'], fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);
        }

        $this->view('rates/index', [
            'title' => 'Rates',
            'groups' => $groups,
        ]);
    }
}
