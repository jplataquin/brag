<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$router = app('router');
$routes = collect($router->getRoutes()->getRoutesByName())->keys()->toArray();

$output = shell_exec('grep -rnoE "route\([\'\"][^\'\"]+[\'\"]" resources/views app routes');
$matches = [];
preg_match_all("/route\(['\"]([^'\"]+)['\"]/", $output, $matches);

$usedRoutes = array_unique($matches[1]);
$missingRoutes = [];
foreach($usedRoutes as $used) {
    if(!in_array($used, $routes)) {
        $missingRoutes[] = $used;
    }
}

echo "=== USED ROUTES NOT DEFINED ===\n";
if (empty($missingRoutes)) {
    echo "None found!\n";
} else {
    print_r($missingRoutes);
}

echo "\n=== CONTROLLER METHODS MISSING ===\n";
$missingMethods = [];
foreach ($router->getRoutes() as $route) {
    $action = $route->getAction();
    if (isset($action['controller'])) {
        $parts = explode('@', $action['controller']);
        if (count($parts) == 2) {
            $class = $parts[0];
            $method = $parts[1];
            if (!method_exists($class, $method)) {
                $missingMethods[] = "$class @ $method";
            }
        }
    }
}

if (empty($missingMethods)) {
    echo "None found!\n";
} else {
    print_r($missingMethods);
}

