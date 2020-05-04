<?php
namespace Arek2k\ElasticApmSymfony\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestProcessor
{
    public static function getTransactionName(Request $request): string
    {
        $routeName = $request->get('_route');
        $controllerName = $request->get('_controller');

        return sprintf(
            "%s %s",
            $routeName,
            $controllerName
        );
    }
}
