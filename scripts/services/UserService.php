<?php

class UserService {
    public static function checkAuth($withRedirect = false) {
        session_start();

        $isAuth = !empty($_SESSION["isAuth"]) && $_SESSION["isAuth"];

        if (!$isAuth && $withRedirect) {
            header("Location: /");
        }

        return $isAuth;
    }

    public static function logout() {
        session_start();
        session_destroy();

        header("Location: /");
    }
}
