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

        // load editable page text
        $content = \App\Models\MembershipPageContent::getContent();

        // convert bullets into array for view convenience
        $bulletList = [];
        if (!empty($content['bullets'])) {
            $lines = preg_split('/\r\n|\r|\n/', $content['bullets']);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $bulletList[] = $line;
                }
            }
        }

        $this->view('membership/index', [
            'title' => 'Membership',
            'groups' => $groups,
            'pageContent' => $content,
            'bulletList' => $bulletList,
        ]);
    }
}
