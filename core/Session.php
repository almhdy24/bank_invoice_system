<?php

class Session {
  public static function start() {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
      self::checkTimeout();
    }
  }

  public static function set($key, $value) {
    $_SESSION[$key] = $value;
  }

  public static function get($key, $default = null) {
    return $_SESSION[$key] ?? $default;
  }

  public static function delete($key) {
    unset($_SESSION[$key]);
  }

  public static function destroy() {
    session_destroy();
  }

  public static function regenerate() {
    session_regenerate_id(true);
  }

  private static function checkTimeout() {
    $lastActivity = self::get('last_activity');

    if ($lastActivity && (time() - $lastActivity > Config::SESSION_TIMEOUT)) {
      self::destroy();
      Response::redirect('/login');
    }

    self::set('last_activity', time());
  }

  public static function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
  }

  public static function getFlash($key) {
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
  }
}