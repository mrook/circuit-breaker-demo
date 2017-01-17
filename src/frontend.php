<?php

require '../vendor/autoload.php';

use Demo\Phystrix\RequestLog;
use GuzzleHttp\Client;
use Odesk\Phystrix\ArrayStateStorage;
use Zend\Config\Config;
use Odesk\Phystrix\CircuitBreakerFactory;
use Odesk\Phystrix\CommandMetricsFactory;
use Odesk\Phystrix\CommandFactory;
use CapMousse\ReactRestify\Http\Request;
use CapMousse\ReactRestify\Http\Response;
use CapMousse\ReactRestify\Runner;
use CapMousse\ReactRestify\Server;
use Demo\Command\LastBookCommand;
use Demo\Command\LastReviewCommand;

$loader = new Twig_Loader_Filesystem('.');
$twig = new Twig_Environment($loader);

$config = new Config(require 'phystrix-config.php');

$stateStorage = new ArrayStateStorage();
$circuitBreakerFactory = new CircuitBreakerFactory($stateStorage);
$commandMetricsFactory = new CommandMetricsFactory($stateStorage);

$phystrix = new CommandFactory(
    $config, new \Zend\Di\ServiceLocator(), $circuitBreakerFactory, $commandMetricsFactory,
    new \Odesk\Phystrix\RequestCache(), null
);
$server = new Server("Book Frontend");

$server->get('/', function (Request $request, Response $response, $next) use ($phystrix, $twig) {
    $requestLog = new RequestLog();
    $client = new Client();
    
    $lastBookCommand = $phystrix->getCommand(LastBookCommand::class, $client);
    $lastBookCommand->setRequestLog($requestLog);
    $book = $lastBookCommand->execute();

    $lastReviewCommand = $phystrix->getCommand(LastReviewCommand::class, $client);
    $lastReviewCommand->setRequestLog($requestLog);
    $review = $lastReviewCommand->execute();
    
    $response
        ->addHeader('Content-Type', 'text/html')
        ->write($twig->render("frontend.html.twig", ['book' => $book, 'review' => $review]));
    $next();
    
    echo "Request => " . $requestLog->getExecutedCommandsAsString() . "\n";
});

$runner = new Runner($server);
$runner->listen(8080);
