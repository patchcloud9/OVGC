<?php

namespace App\Controllers;

use App\Models\BoardMember;
use App\Models\BoardMinute;

class BoardController extends Controller
{
    /**
     * Public page showing board members and minutes.
     */
    public function index(): void
    {
        $members = BoardMember::allOrdered();

        $page = (int) ($this->query('page') ?? 1);
        $pagination = BoardMinute::paginate($page, 10);

        $this->view('board/index', [
            'title' => 'Board Members & Minutes',
            'members' => $members,
            'minutes' => $pagination['minutes'],
            'currentPage' => $pagination['page'],
            'totalPages' => $pagination['totalPages'],
        ]);
    }
}
