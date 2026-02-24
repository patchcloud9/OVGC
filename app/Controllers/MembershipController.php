<?php

namespace App\Controllers;

class MembershipController extends Controller
{
    /**
     * Display the membership information page.
     * Route: GET /membership
     */
    public function index(): void
    {
        // pull active membership groups and their items
        $groups = \App\Models\MembershipGroup::allGroups(true);
        foreach ($groups as &$g) {
            $g['items'] = \App\Models\MembershipItem::where(['group_id' => $g['id']]);
            usort($g['items'], fn($a,$b)=> $a['sort_order']<=>$b['sort_order']);
        }

        $this->view('membership/index', [
            'title' => 'Membership',
            'groups' => $groups,
        ]);
    }
}
