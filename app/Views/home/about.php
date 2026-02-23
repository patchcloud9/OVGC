<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <h1 class="title"><?= e($title) ?></h1>
        
        <div class="content">
            <p>This is a simple PHP MVC framework demonstration.</p>
            
            <h2>How Routing Works</h2>
            <p>When you visit <code>/about</code>, here's what happens:</p>
            
            <pre><code>1. Browser requests: GET /about

2. Apache receives request
   - Checks: Does /about exist as a file? No
   - Checks: Does /about exist as a directory? No
   - Applies .htaccess rule: Rewrite to index.php

3. index.php runs:
   $uri = '/about'
   $method = 'GET'
   $router->dispatch('GET', '/about')

4. Router searches routes:
   '/about' matches pattern '/about'
   Handler: ['HomeController', 'about']

5. Router creates controller:
   $controller = new HomeController()
   $controller->about()

6. Controller renders view:
   $this->view('home/about', ['title' => 'About Us'])</code></pre>
            
            <h2>Try These URLs</h2>
            <ul>
                <li><a href="/users/1">/users/1</a> - Shows user with ID 1</li>
                <li><a href="/users/42">/users/42</a> - Shows user with ID 42</li>
                <li><a href="/users/999">/users/999</a> - User doesn't exist</li>
                <li><a href="/users/abc">/users/abc</a> - Won't match (pattern requires digits)</li>
                <li><a href="/nonexistent">/nonexistent</a> - No route matches, shows 404</li>
            </ul>
        </div>
    </div>
</section>
