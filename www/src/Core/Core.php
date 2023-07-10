<?php

declare(strict_types=1);


class Core
{
    private $currentController = 'Info';
    private $currentMethod = 'index';
    private $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        if (isset($url[0])) {
            $url[0] = strtolower($url[0]);
            $controller = $this->convertToCamelCase($url[0]);

            $controller = ucwords($controller);

            if (!file_exists(APPROOT . '/Controllers/ViewControllers/' . $controller . '.php')) Server::die_404();

            $this->currentController = $controller;
            unset($url[0]);
        }

        require_once(APPROOT . "/Controllers/ViewControllers/" . $this->currentController . '.php');

        $this->currentController = new $this->currentController;

        if (isset($url[1])) {
            $url[1] = strtolower($url[1]);
            $this->currentMethod = $url[1];
            unset($url[1]);
        }

        $this->currentMethod = $this->convertToCamelCase($this->currentMethod);

        if (method_exists($this->currentController, $this->currentMethod)) {
            if ($url) $this->params = $this->castUrlParams(array_values($url));
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        } else {
            Server::die_404();
        }
    }

    private function getUrl(): array
    {
        $query = Server::getRequestUrl();

        $arr = explode('/', $query);
        $arr = Utils::unsetNullArray($arr);

        return $arr;
    }

    private function castUrlParams(array $arr): array
    {
        foreach ($arr as $key => $value) {
            if (is_numeric($value)) {
                if (ctype_digit($value)) {
                    $arr[$key] = (int) $value;
                } else if (is_float($value + 0)) {
                    $arr[$key] = (float) $value;
                }
            } else {
                if ($value === 'true') {
                    $arr[$key] = true;
                } else if ($value === 'false') {
                    $arr[$key] = false;
                } else {
                    $arr[$key] = (string) $value;
                }
            }
        }
        return $arr;
    }

    private function convertToCamelCase(string $str): string
    {
        return trim(str_replace(' ', '', lcfirst(ucwords(implode(" ", explode("-", $str))))));
    }
}
