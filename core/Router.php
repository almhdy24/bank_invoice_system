<?php
class Router {
    private $routes = [];
    private $request;
    
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    public function addRoute($method, $path, $handler, $middlewares = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }
    
    public function dispatch() {
        $requestMethod = $this->request->getMethod();
        $requestPath = $this->request->getPath();
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestPath)) {
                try {
                    // Execute middlewares
                    foreach ($route['middlewares'] as $middleware) {
                        $this->executeMiddleware($middleware);
                    }
                    
                    // Extract parameters from dynamic routes
                    $params = $this->extractParams($route['path'], $requestPath);
                    
                    return $this->executeHandler($route['handler'], $params);
                } catch (Exception $e) {
                    error_log("Router Error: " . $e->getMessage());
                    Session::setFlash('error', 'حدث خطأ في النظام');
                    Response::redirect('/');
                }
            }
        }
        
        Response::sendNotFound();
    }
    
    private function executeMiddleware($middleware) {
        if (is_callable($middleware)) {
            return call_user_func($middleware, $this->request);
        }
        
        if (is_string($middleware)) {
            $middlewareClass = new $middleware();
            if (method_exists($middlewareClass, 'handle')) {
                return $middlewareClass->handle($this->request);
            }
        }
        
        throw new Exception("Invalid middleware");
    }
    
    private function executeHandler($handler, $params = []) {
        if ($handler instanceof Closure) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler)) {
            list($controllerName, $methodName) = explode('@', $handler);
            
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller file not found");
            }
            
            require_once $controllerFile;
            
            $controller = new $controllerName($this->request);
            return call_user_func_array([$controller, $methodName], $params);
        }
        
        throw new Exception("Invalid handler type");
    }
    
    private function matchRoute($route, $requestMethod, $requestPath) {
        if ($route['method'] !== $requestMethod) {
            return false;
        }
        
        $routePath = trim($route['path'], '/');
        $requestPath = trim($requestPath, '/');
        
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        return preg_match("#^$pattern$#", $requestPath);
    }
    
    private function extractParams($routePath, $requestPath) {
        $params = [];
        
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        
        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $matches)) {
                $params[$matches[1]] = $requestParts[$index] ?? null;
            }
        }
        
        return $params;
    }
}