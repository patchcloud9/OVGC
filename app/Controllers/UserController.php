<?php

namespace App\Controllers;

use App\Models\User;

/**
 * User Controller
 * 
 * Manages user CRUD operations using the User model.
 */
class UserController extends Controller
{
    /**
     * List all users
     * Route: GET /users
     */
    public function index(): void
    {
        $users = User::all();
        
        $this->view('users/index', [
            'title' => 'All Users',
            'users' => $users,
        ]);
    }
    
    /**
     * Show edit form for a user
     * Route: GET /users/(\d+)/edit
     */
    public function edit(string $id): void
    {
        $userId = (int) $id;
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->flash('error', "User {$id} not found");
            $this->redirect('/admin/users');
            return;
        }
        
        $this->view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
        ]);
    }
    
    /**
     * Show form to create a new user
     * Route: GET /users/create
     */
    public function create(): void
    {
        $this->view('users/create', [
            'title' => 'Create New User',
        ]);
    }
    
    /**
     * Create a new user (handle form submission)
     * Route: POST /users
     * Middleware: csrf, rate-limit:user-creation,3,300
     */
    public function store(): void
    {
        // Validate input
        $validator = new \Core\Validator($_POST, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|max:255',
            'role' => 'required|in:user,admin',
        ]);
        
        if ($validator->fails()) {
            flash_old_input($_POST);
            foreach ($validator->errors() as $field => $errors) {
                $this->flash('error', $errors[0]);
                break; // Show only first error
            }
            $this->redirect('/admin/users/create');
            return;
        }
        
        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');
        $role = $this->input('role');
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);
        
        $this->flash('success', "User '{$name}' created successfully!");
        $this->redirect('/admin/users');
    }
    
    /**
     * Update an existing user
     * Route: POST /users/(\d+)
     * Middleware: csrf
     */
    public function update(string $id): void
    {
        $userId = (int) $id;
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->json([
                'success' => false,
                'message' => "User {$id} not found",
            ], 404);
            return;
        }
        
        // Build validation rules dynamically based on what's being updated
        $rules = [];
        $postData = [];
        
        if ($this->input('name') !== null) {
            $rules['name'] = 'required|min:2|max:100';
            $postData['name'] = $this->input('name');
        }
        
        if ($this->input('email') !== null) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $userId;
            $postData['email'] = $this->input('email');
        }
        
        if ($this->input('password') !== null && $this->input('password') !== '') {
            $rules['password'] = 'required|min:8|max:255';
            $postData['password'] = $this->input('password');
        }
        
        if ($this->input('role') !== null) {
            $rules['role'] = 'required|in:admin,user,guest';
            $postData['role'] = $this->input('role');
        }
        
        // Validate if there are any rules
        if (!empty($rules)) {
            $validator = new \Core\Validator($postData, $rules);
            
            if ($validator->fails()) {
                flash_old_input($_POST);
                foreach ($validator->errors() as $field => $errors) {
                    $this->flash('error', $errors[0]);
                    break;
                }
                $this->redirect('/admin/users/' . $userId . '/edit');
                return;
            }
        }
        
        // Get update data (filter out password if empty)
        $data = [];
        if ($name = $this->input('name')) {
            $data['name'] = $name;
        }
        if ($email = $this->input('email')) {
            $data['email'] = $email;
        }
        if ($password = $this->input('password')) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        if ($role = $this->input('role')) {
            $data['role'] = $role;
        }
        
        User::update($userId, $data);
        
        $this->flash('success', 'User updated successfully!');
        $this->redirect('/admin/users');
    }
    
    /**
     * Delete a user
     * Route: DELETE /users/(\d+)
     * Middleware: csrf
     */
    public function destroy(string $id): void
    {
        $userId = (int) $id;
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->flash('error', "User not found");
            $this->redirect('/admin/users');
            return;
        }
        
        $userName = $user['name'];
        User::delete($userId);
        
        $this->flash('success', "User '{$userName}' deleted successfully");
        $this->redirect('/admin/users');
    }
}
