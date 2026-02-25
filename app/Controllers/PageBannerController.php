<?php

namespace App\Controllers;

use App\Models\PageBanner;
use App\Services\LogService;
use Core\Validator;

class PageBannerController extends Controller
{
    private LogService $logService;

    public function __construct()
    {
        $this->logService = new LogService();
    }

    public function index(): void
    {
        // order by sort_order ascending
        $banners = PageBanner::all(['*'], ['sort_order' => 'ASC']);
        $this->view('banners/admin', [
            'title' => 'Manage Banners',
            'banners' => $banners,
        ]);
    }

    public function create(): void
    {
        // include list of non-admin pages for dropdown suggestions
        $pages = \App\Models\MenuItem::getNonAdminUrls();

        $this->view('banners/create', [
            'title' => 'Create Banner',
            'pages' => $pages,
        ]);
    }

    public function store(): void
    {
        $validator = new Validator([
            'page' => $this->input('page'),
            'position' => $this->input('position'),
            'text' => $this->input('text'),
            'colour' => $this->input('colour'),
            'dismissable' => $this->input('dismissable'),
            'sort_order' => $this->input('sort_order'),
            'active' => $this->input('active'),
            'start_at' => $this->input('start_at'),
            'end_at' => $this->input('end_at'),
        ],[
            'page' => 'required|max:50',
            'position' => 'required|in:top,bottom',
            'text' => 'required',
            'colour' => 'required|in:info,warning,danger,none',
            'dismissable' => 'in:0,1',
            'sort_order' => 'numeric',
            'active' => 'in:0,1',
            'start_at' => 'date',
            'end_at' => 'date',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors() as $fieldErrors) {
                $errors = array_merge($errors, $fieldErrors);
            }
            $this->flash('danger', 'Validation failed: ' . implode(', ', $errors));
            flash_old_input($_POST);
            $this->redirect('/admin/banners/create');
            return;
        }

        $banner = PageBanner::create([
            'page' => $this->input('page'),
            'position' => $this->input('position'),
            'text' => $this->input('text'),
            'colour' => $this->input('colour'),
            'dismissable' => $this->input('dismissable') === '1' ? 1 : 0,
            'sort_order' => (int) $this->input('sort_order'),
            'active' => $this->input('active') === '1' ? 1 : 0,
            'start_at' => $this->input('start_at') ?: null,
            'end_at' => $this->input('end_at') ?: null,
        ]);

        $this->logService->add('info','Banner created',['id'=>$banner['id']]);
        $this->flash('success','Banner created');
        $this->redirect('/admin/banners');
    }

    public function edit(string $id): void
    {
        $bid = (int) $id;
        $banner = PageBanner::find($bid);
        if (!$banner) {
            $this->flash('danger','Banner not found');
            $this->redirect('/admin/banners');
            return;
        }

        $pages = \App\Models\MenuItem::getNonAdminUrls();

        $this->view('banners/edit', [
            'title'=>'Edit Banner',
            'banner'=>$banner,
            'pages' => $pages,
        ]);
    }

    public function update(string $id): void
    {
        $bid = (int) $id;
        $banner = PageBanner::find($bid);
        if (!$banner) {
            $this->flash('danger','Banner not found');
            $this->redirect('/admin/banners');
            return;
        }
        $validator = new Validator([
            'page' => $this->input('page'),
            'position' => $this->input('position'),
            'text' => $this->input('text'),
            'colour' => $this->input('colour'),
            'dismissable' => $this->input('dismissable'),
            'sort_order' => $this->input('sort_order'),
            'active' => $this->input('active'),
            'start_at' => $this->input('start_at'),
            'end_at' => $this->input('end_at'),
        ],[
            'page' => 'required|max:50',
            'position' => 'required|in:top,bottom',
            'text' => 'required',
            'colour' => 'required|in:info,warning,danger,none',
            'dismissable' => 'in:0,1',
            'sort_order' => 'numeric',
            'active' => 'in:0,1',
            'start_at' => 'date',
            'end_at' => 'date',
        ]);
        if ($validator->fails()) {
            $errors=[];
            foreach($validator->errors() as $fieldErrors){$errors=array_merge($errors,$fieldErrors);}
            $this->flash('danger','Validation failed: '.implode(', ',$errors));
            flash_old_input($_POST);
            $this->redirect("/admin/banners/{$bid}/edit");
            return;
        }
        PageBanner::update($bid,[
            'page'=>$this->input('page'),
            'position'=>$this->input('position'),
            'text'=>$this->input('text'),
            'colour'=>$this->input('colour'),
            'dismissable'=>$this->input('dismissable')==='1'?1:0,
            'sort_order'=>(int)$this->input('sort_order'),
            'active'=>$this->input('active')==='1'?1:0,
            'start_at'=>$this->input('start_at')?:null,
            'end_at'=>$this->input('end_at')?:null,
        ]);
        $this->logService->add('info','Banner updated',['id'=>$bid]);
        $this->flash('success','Banner updated');
        $this->redirect('/admin/banners');
    }

    public function destroy(string $id): void
    {
        $bid=(int)$id;
        $banner=PageBanner::find($bid);
        if(!$banner){
            $this->flash('danger','Banner not found');
            $this->redirect('/admin/banners');
            return;
        }
        PageBanner::delete($bid);
        $this->logService->add('info','Banner deleted',['id'=>$bid]);
        $this->flash('success','Banner deleted');
        $this->redirect('/admin/banners');
    }
}
