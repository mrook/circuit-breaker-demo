<?php

use CapMousse\ReactRestify\Http\Request;
use CapMousse\ReactRestify\Http\Response;
use CapMousse\ReactRestify\Runner;
use CapMousse\ReactRestify\Server;
use Demo\Model\Book;
use Demo\Model\Review;

require '../vendor/autoload.php';

$server = new Server("BookService", "0.1");
$faker = Faker\Factory::create();

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="localhost:1337",
 *     basePath="/",
 *     @SWG\Info(
 *         version="0.1",
 *         title="Demo Book and Review Service",
 *         description="This is a Demo Book and Review Service.",
 *         @SWG\Contact(
 *             email="mrook@php.net"
 *         )
 *     ),
 *     @SWG\ExternalDocumentation(
 *         description="Find out more about Swagger",
 *         url="http://swagger.io"
 *     )
 * )
 */


/**
 * @SWG\Get(
 *     path="/book/last",
 *     summary="Retrieve the last book",
 *     tags={"book"},
 *     operationId="getLastBook",
 *     produces={"application/json"},
 *     @SWG\Response(
 *         response=200,
 *         description="successful operation",
 *         @SWG\Schema(ref="#/definitions/Book")
 *     ),
 * )
 */
$server->get('/book/last', function (Request $request, Response $response, $next) use ($faker) {
    usleep(rand(0, 200 * 1000));

    $book = new Book();
    $book->fromGenerator($faker);

    $response->writeJson($book);
    $next();
});

/**
 * @SWG\Get(
 *     path="/review/last",
 *     summary="Retrieve the last review",
 *     tags={"review"},
 *     operationId="getLastReview",
 *     produces={"application/json"},
 *     @SWG\Response(
 *         response=200,
 *         description="successful operation",
 *         @SWG\Schema(ref="#/definitions/Review")
 *     ),
 * )
 */
$server->get('/review/last', function (Request $request, Response $response, $next) use ($faker) {
    usleep(rand(0, 200 * 1000));

    $review = new Review();
    $review->fromGenerator($faker);

    $response->writeJson($review);
    $next();
});

$server->get('/swagger.json', function (Request $request, Response $response, $next) {
    $response->write(\Swagger\scan(__DIR__));
    $response->addHeader("Content-Type", "application/json");
    $next();
});

$runner = new Runner($server);
$runner->listen(1337);
