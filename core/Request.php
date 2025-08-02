<?php
class Request {
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        return strtok($path, '?'); // More efficient way to get path without query string
    }

    public function getBody() {
        $body = [];
        $filter = FILTER_SANITIZE_SPECIAL_CHARS;

        if ($this->getMethod() === 'GET') {
            $body = filter_input_array(INPUT_GET, $filter) ?? [];
        }

        if ($this->getMethod() === 'POST') {
            $body = filter_input_array(INPUT_POST, $filter) ?? [];
        }

        return $body;
    }

    public function getFile($name) {
        return $_FILES[$name] ?? null;
    }

    public function isAuthenticated() {
        return !empty(Session::get('user_id'));
    }

    public function isAdmin() {
        // Debug output (remove after testing)
        error_log('User Role: ' . Session::get('user_role'));
        error_log('Config Admin Role: ' . Config::ROLE_ADMIN);
        
        // Case-insensitive comparison and trim whitespace
        return strtolower(trim(Session::get('user_role'))) === strtolower(trim(Config::ROLE_ADMIN));
    }

    public function getUserId() {
        return Session::get('user_id');
    }

    public function getUserRole() {
        return Session::get('user_role');
    }
}