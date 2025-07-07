<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function shareView()
    {
        $route = Route::getCurrentRoute();
        $routeName = $route ? $route->getName() : null;
        
        if ($routeName) {
            $route_arr = explode('.', $routeName);
        if (count($route_arr) < 2) {
                $first_part = $routeName;
            $second_part = null;
        } else {
            $first_part = $route_arr[0];
            $second_part = $route_arr[1];
            }
        } else {
            $first_part = null;
            $second_part = null;
        }

        View::share([
            'first_url_part' => $first_part,
            'second_url_part' => $second_part,
        ]);
    }
}
