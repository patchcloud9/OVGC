<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

class DocsController extends Controller
{
    /**
     * Ordered list of documentation files.
     * Slug => [title, icon (Font Awesome class)]
     * Change the order here to reorder the sidebar.
     */
    private const DOCS = [
        'dashboard'       => ['title' => 'Admin Dashboard',  'icon' => 'fa-tachometer-alt'],
        'homepage'        => ['title' => 'Homepage Editor',  'icon' => 'fa-home'],
        'banners'         => ['title' => 'Page Banners',     'icon' => 'fa-exclamation-circle'],
        'menu-management' => ['title' => 'Menu Management',  'icon' => 'fa-bars'],
        'user-management' => ['title' => 'User Management',  'icon' => 'fa-users-cog'],
        'rates'           => ['title' => 'Rates / Admin Rates', 'icon' => 'fa-tags'],
    ];

    /** Landing page — shows search results or a welcome message. */
    public function index(): void
    {
        $q       = trim($_GET['q'] ?? '');
        $results = $q !== '' ? $this->searchDocs($q) : [];

        $this->view('admin/docs/index', [
            'title'       => 'Documentation',
            'docs'        => self::DOCS,
            'searchQuery' => $q,
            'results'     => $results,
        ]);
    }

    /** Renders a single documentation page. */
    public function show(string $slug): void
    {
        if (!array_key_exists($slug, self::DOCS)) {
            $this->redirect('/admin/docs');
            return;
        }

        $path = BASE_PATH . '/app/Views/docs/' . $slug . '.html';
        if (!file_exists($path)) {
            $this->redirect('/admin/docs');
            return;
        }

        $this->view('admin/docs/show', [
            'title'       => self::DOCS[$slug]['title'] . ' — Docs',
            'docs'        => self::DOCS,
            'doc'         => self::DOCS[$slug],
            'currentSlug' => $slug,
            'docHtml'     => file_get_contents($path),
        ]);
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    /** Search all docs for the query; return matched sections with excerpts. */
    private function searchDocs(string $q): array
    {
        $results = [];

        foreach (self::DOCS as $slug => $meta) {
            $path = BASE_PATH . '/app/Views/docs/' . $slug . '.html';
            if (!file_exists($path)) {
                continue;
            }

            foreach ($this->splitIntoSections(file_get_contents($path), $meta['title']) as $section) {
                $searchable = $section['heading'] . ' ' . strip_tags($section['content']);
                if (stripos($searchable, $q) === false) {
                    continue;
                }

                $text  = trim(preg_replace('/\s+/', ' ', strip_tags($section['content'])));
                $pos   = max(0, (int) stripos($text, $q));
                $start = max(0, $pos - 80);
                $excerpt = ($start > 0 ? '…' : '') . substr($text, $start, 240) . '…';

                $results[] = [
                    'slug'      => $slug,
                    'doc_title' => $meta['title'],
                    'section'   => $section['heading'],
                    'anchor'    => $section['anchor'],
                    'excerpt'   => $excerpt,
                ];
            }
        }

        return $results;
    }

    /**
     * Split an HTML doc into sections keyed by <h2> headings.
     * Returns array of ['anchor', 'heading', 'content'].
     */
    private function splitIntoSections(string $html, string $docTitle): array
    {
        $sections = [];

        // Split at each <h2 boundary, keeping the h2 tag with its section
        $parts = preg_split('/(?=<h2[\s>])/i', $html);

        // First chunk is the intro (before any h2)
        $intro = array_shift($parts);
        if (trim(strip_tags($intro)) !== '') {
            $sections[] = ['anchor' => '', 'heading' => $docTitle, 'content' => $intro];
        }

        foreach ($parts as $part) {
            // Extract h2 attributes, inner text, and the remainder
            if (!preg_match('/^<h2([^>]*)>(.*?)<\/h2>(.*)/si', $part, $m)) {
                continue;
            }

            $heading = strip_tags($m[2]);
            $content = $m[3];

            $anchor = '';
            if (preg_match('/\bid="([^"]+)"/i', $m[1], $idM)) {
                $anchor = $idM[1];
            }

            $sections[] = ['anchor' => $anchor, 'heading' => $heading, 'content' => $content];
        }

        return $sections;
    }
}
