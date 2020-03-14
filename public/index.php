<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Controller\ApiController;
use App\Entity\Order;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

$definitionBuilder = new DefinitionBuilder();
$definition = $definitionBuilder->addPlaces([
    Order::NEW_STATUS,
    Order::PAID_STATUS,
])
    ->addTransition(new Transition(Order::PAY_TRANSITION, Order::NEW_STATUS, Order::PAID_STATUS))
    ->build();

$singleState = true;
$property = 'status';
$marking = new MethodMarkingStore($singleState, $property);
$workflow = new Workflow($definition, $marking);

$entityManager = require_once PROJECT_DIR.'/entityManager.php';
$request = Request::createFromGlobals();
$client = HttpClient::create();
$controller = new ApiController($request, $entityManager, $workflow, $client);
$routes = new RouteCollection();

foreach ($controller->getRoutes() as $route) {
    $routes->add($route->getPath(), $route);
}

$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

try {
    $response = $kernel->handle($request);
} catch (HttpException $exception) {
    $response = new JsonResponse([
        'message' => $exception->getMessage(),
    ], $exception->getStatusCode());
}

$response->send();
$kernel->terminate($request, $response);
