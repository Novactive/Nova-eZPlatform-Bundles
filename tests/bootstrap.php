<?php

use Symfony\Component\Dotenv\Dotenv;

include __DIR__."/../vendor/autoload.php";

if (file_exists(__DIR__."/../ezplatform/config/bootstrap.php")) {
    include __DIR__."/../ezplatform/config/bootstrap.php";
} else {
    include __DIR__."/../ezplatform/vendor/autoload.php";
    (new Dotenv())->loadEnv(dirname(__DIR__).'/ezplatform/.env');
}

