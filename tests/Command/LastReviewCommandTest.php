<?php

namespace Demo\Command;

use Demo\Model\Book;
use Demo\Model\Review;
use Faker\Factory;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class LastReviewCommandTest extends CommandTest
{
    protected $commandClass = LastReviewCommand::class;

    public function testShouldRequestWhenCircuitBreakerIsOpen()
    {
        $expectedReview = new Review();
        $expectedReview->fromGenerator(Factory::create());

        $this->circuitBreaker->expects($this->once())
            ->method('allowRequest')
            ->willReturn(true);
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], \json_encode($expectedReview)));
        
        $actualReview = $this->command->execute();
        
        self::assertSame((array) $expectedReview, $actualReview, $this->requestLog->getExecutedCommandsAsString());
    }

    public function testShouldFallbackWhenCircuitBreakerIsOpenButRequestFails()
    {
        $this->circuitBreaker->expects($this->once())
            ->method('allowRequest')
            ->willReturn(true);

        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException(new BadResponseException("timed out", new Request("GET", "/")));

        $actualReview = $this->command->execute();

        self::assertSame([], $actualReview, $this->requestLog->getExecutedCommandsAsString());
    }

    public function testShouldFallbackWhenCircuitBreakerIsClosed()
    {
        $this->circuitBreaker->expects($this->once())
            ->method('allowRequest')
            ->willReturn(false);

        $this->client->expects($this->never())
            ->method('request');

        $actualReview = $this->command->execute();

        self::assertSame([], $actualReview, $this->requestLog->getExecutedCommandsAsString());
    }
}
