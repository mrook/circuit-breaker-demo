<?php

set_time_limit(0);

require 'vendor/autoload.php';

use Odesk\Phystrix\ArrayStateStorage;
use Zend\Config\Config;
use Odesk\Phystrix\CircuitBreakerFactory;
use Odesk\Phystrix\CommandMetricsFactory;
use Odesk\Phystrix\CommandFactory;
use CapMousse\ReactRestify\Http\Request;
use CapMousse\ReactRestify\Http\Response;
use CapMousse\ReactRestify\Runner;
use CapMousse\ReactRestify\Server;

require 'LastBookCommand.php';
require 'LastReviewCommand.php';

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

function getExecutedCommandsAsString(array $executedCommands)
{
    $output = "";
    $aggregatedCommandsExecuted = [];
    $aggregatedCommandExecutionTime = [];
    
    /** @var \Odesk\Phystrix\AbstractCommand $executedCommand */
    foreach ($executedCommands as $executedCommand) {
        $display = $executedCommand->getCommandKey() . "[";
        $events = $executedCommand->getExecutionEvents();
        
        if (count($events) > 0) {
            foreach ($events as $event) {
                $display .= "{$event}, ";
            }
            $display = substr($display, 0, strlen($display) - 2);
        } else {
            $display .= "Executed";
        }
        
        $display .= "]";
        
        if (!isset($aggregatedCommandsExecuted[$display])) {
            $aggregatedCommandsExecuted[$display] = 0; 
        }
        
        $aggregatedCommandsExecuted[$display] = $aggregatedCommandsExecuted[$display] + 1;
        
        $executionTime = $executedCommand->getExecutionTimeInMilliseconds();
        
        if ($executionTime < 0) {
            $executionTime = 0;
        }
        
        if (isset($aggregatedCommandExecutionTime[$display]) && $executionTime > 0) {
            $aggregatedCommandExecutionTime[$display] = $aggregatedCommandExecutionTime[$display] + $executionTime;
        } else {
            $aggregatedCommandExecutionTime[$display] = $executionTime;
        }
    }

    foreach ($aggregatedCommandsExecuted as $display => $count) {
        if (strlen($output) > 0) {
            $output .= ", ";
        }
        
        $output .= "{$display}";

        $output .= "[" . $aggregatedCommandExecutionTime[$display] . "ms]";

        if ($count > 1) {
            $output .= "x{$count}";
        }
    }
    
    return $output;
}

$server->get('/', function (Request $request, Response $response, $next) use ($phystrix, $twig) {
    $requestLog = new \Odesk\Phystrix\RequestLog();
    
    $lastBookCommand = $phystrix->getCommand(LastBookCommand::class);
    $lastBookCommand->setRequestLog($requestLog);
    $book = $lastBookCommand->execute();

    $lastReviewCommand = $phystrix->getCommand(LastReviewCommand::class);
    $lastReviewCommand->setRequestLog($requestLog);
    $review = $lastReviewCommand->execute();
    
    $response
        ->addHeader('Content-Type', 'text/html')
        ->write($twig->render("frontend.html.twig", ['book' => $book, 'review' => $review]));
    $next();
    
    echo "Request => " . getExecutedCommandsAsString($requestLog->getExecutedCommands()) . "\n";
});

$runner = new Runner($server);
$runner->listen(8080);
