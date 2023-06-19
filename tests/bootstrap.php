<?php

use Symfony\Component\Dotenv\Dotenv;

include __DIR__."/../vendor/autoload.php";

if (file_exists(__DIR__."/../ibexa/config/bootstrap.php")) {
    include __DIR__."/../ibexa/config/bootstrap.php";
} else {
    include __DIR__."/../ibexa/vendor/autoload.php";
    (new Dotenv())->loadEnv(dirname(__DIR__).'/ibexa/.env');
}

