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

        // load editable content
        $pageContent = \App\Models\RatePageContent::getContent();
        $bulletList = [];
        if (!empty($pageContent['rules_text'])) {
            $lines = preg_split('/\r?\n/', trim($pageContent['rules_text']));
            foreach ($lines as $l) {
                if (trim($l) !== '') {
                    $bulletList[] = $l;
                }
            }
        }

        $this->view('rates/index', [
            'title' => 'Rates',
            'groups' => $groups,
            'pageContent' => $pageContent,
            'bulletList' => $bulletList,
        ]);
    }
}
