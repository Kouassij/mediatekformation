<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
 /**
     * @Route("/", name="index")
     * @return Response
     */
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
