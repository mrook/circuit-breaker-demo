<?php

namespace Demo\Command;

use Demo\Phystrix\RequestLog;
use GuzzleHttp\Client;
use Odesk\Phystrix\AbstractCommand;
use Odesk\Phystrix\ArrayStateStorage;
use Odesk\Phystrix\CircuitBreaker;
use Odesk\Phystrix\CircuitBreakerFactory;
use Odesk\Phystrix\CommandMetricsFactory;
use PHPUnit\Framework\TestCase;
use Zend\Config\Config;

abstract class CommandTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected $client;

    /**
     * @var AbstractCommand
     */
    protected $command;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CircuitBreaker
     */
    protected $circuitBreaker;

    /**
     * @var RequestLog
     */
    protected $requestLog;

    /**
     * @var string
     */
    protected $commandClass = null;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->getMock();

        $this->circuitBreaker = $this->getMockBuilder(CircuitBreaker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMetricsFactory = new CommandMetricsFactory(new ArrayStateStorage());

        $circuitBreakerFactory = $this->getMockBuilder(CircuitBreakerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $circuitBreakerFactory->expects($this->once())
            ->method('get')
            ->willReturn($this->circuitBreaker);

        $this->command = new $this->commandClass($this->client);
        $this->command->setCommandMetricsFactory($commandMetricsFactory);
        $this->command->setCircuitBreakerFactory($circuitBreakerFactory);
        $this->command->setConfig(
            new Config(
                [
                    'fallback' => [
                        'enabled' => true,
                    ],
                    'metrics' => [
                        'healthSnapshotIntervalInMilliseconds' => 1000,
                        'rollingStatisticalWindowInMilliseconds' => 1000,
                        'rollingStatisticalWindowBuckets' => 10,
                    ],
                    'requestCache' => [
                        'enabled' => true,
                    ],
                    'requestLog' => [
                        'enabled' => true,
                    ],
                ], true
            )
        );
        $this->requestLog = new RequestLog();
        $this->command->setRequestLog($this->requestLog);
    }
}
