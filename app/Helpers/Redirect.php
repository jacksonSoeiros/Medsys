<?php

namespace App\Helpers;

class Redirect
{
    private string $url;

    private function __construct(string $url)
    {
        $this->url = $url;
    }

    public static function to(string $path): self
    {
        $url = ViewHelper::url($path);
        return new self($url);
    }

    public static function back(): self
    {
        $url = $_SERVER['HTTP_REFERER'] ?? ViewHelper::url('/');
        return new self($url);
    }

    public function with(string $key, mixed $value): self
    {
        Session::flash($key, $value);
        return $this;
    }

    public function withErrors(array $errors): self
    {
        Session::flash('errors', $errors);
        return $this;
    }

    public function withInput(): self
    {
        Session::flash('old', $_POST);
        return $this;
    }

    public function __destruct()
    {
        header("Location: {$this->url}");
        exit;
    }
}

