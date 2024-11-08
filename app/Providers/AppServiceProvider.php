<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('base64_image_size', function ($attribute, $value, $parameters, $validator) {
            $decodedImage = base64_decode($value);
            $imageSize = strlen($decodedImage) / 1024;
          
            return $imageSize <= $parameters[0]; 
          
        });

        Validator::extend('is_image', function ($attribute, $value, $parameters, $validator) {
            $acceptedExtensions = [
                "jpeg",
                "jpg",
                "png",
            ];
            
            $header = explode(',', $value)[0];
            preg_match("/\/(.*?);/", $header, $extension);
          

            return in_array($extension[1], $acceptedExtensions);
          
        });
          

        Validator::replacer('base64_image_size', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':attribute', ':max'], [$attribute, $parameters[0]], $message);
        });
          
        Validator::replacer('is_image', function ($message, $attribute, $rule, $parameters) {
            $types = implode(', ', $parameters);
            return str_replace([':attribute', ':types'], [$attribute, $types], $message);
        });
          
    }
}
