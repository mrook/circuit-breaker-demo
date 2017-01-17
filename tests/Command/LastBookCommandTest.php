<?php

namespace Demo\Command;

use Demo\Model\Book;
use Faker\Factory;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class LastBookCommandTest extends CommandTest
{
    protected $commandClass = LastBookCommand::class;

    public function testShouldRequestWhenCircuitBreakerIsOpen()
    {
        $expectedBook = new Book();
        $expectedBook->fromGenerator(Factory::create());

        $this->circuitBreaker->expects($this->once())
            ->method('allowRequest')
            ->willReturn(true);

        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], \json_encode($expectedBook)));

        $actualBook = $this->command->execute();

        self::assertSame((array) $expectedBook, $actualBook, $this->requestLog->getExecutedCommandsAsString());
    }

    public function testShouldFallbackWhenCircuitBreakerIsOpenButRequestFails()
    {
        $this->circuitBreaker->expects($this->once())
            ->method('allowRequest')
            ->willReturn(true);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException(new BadResponseException("timed out", new Request("GET", "/")));

        $actualBook = $this->command->execute();

        self::assertSame([], $actualBook, $this->requestLog->getExecutedCommandsAsString());
    }

    public function testShouldFallbackWhenCircuitBreakerIsClosed()
    {
        $this->circuitBreaker->expects($this->once())
            ->method('allowRequest')
            ->willReturn(false);

        $this->client->expects($this->never())
            ->method('request');

        $actualBook = $this->command->execute();

        self::assertSame([], $actualBook, $this->requestLog->getExecutedCommandsAsString());
    }
}
