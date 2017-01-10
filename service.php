<?php

use CapMousse\ReactRestify\Http\Request;
use CapMousse\ReactRestify\Http\Response;
use CapMousse\ReactRestify\Runner;
use CapMousse\ReactRestify\Server;

require 'vendor/autoload.php';

$server = new Server("BookService", "0.1");
$faker = Faker\Factory::create();

$server->get('/book/last', function (Request $request, Response $response, $next) use ($faker) {
    usleep(rand(0, 200 * 1000));
    $response->writeJson([
        'id' => $faker->uuid,
        'author' => $faker->name,
        'title' => substr($faker->sentence(5), 0, -1),
        'summary' => $faker->text,
        'price' => $faker->randomNumber(2),
        'isbn' => $faker->ean13
    ]);
    $next();
});

$server->get('/review/last', function (Request $request, Response $response, $next) use ($faker) {
    usleep(rand(0, 200 * 1000));
    $response->writeJson([
        'id' => $faker->uuid,
        'reviewer' => $faker->name,
        'book' => substr($faker->sentence(5), 0, -1),
        'review' => $faker->text,
        'date' => $faker->date(),
    ]);
    $next();
});

$runner = new Runner($server);
$runner->listen(1337);
