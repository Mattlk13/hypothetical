<?php

use App\Providers\AppServiceProvider;
use Spatie\Newsletter\NewsletterServiceProvider;
use Intervention\Image\ImageServiceProvider;

return [
    AppServiceProvider::class,
    NewsletterServiceProvider::class,
    ImageServiceProvider::class,
];
