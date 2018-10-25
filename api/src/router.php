<?php
$app->group('/books', function () use ($app) {
    $app->get('/', 'book.controller:loadPage');
    $app->get('/list', 'book.controller:listBooks');
    $app->post('/add', 'book.controller:addBook');
    $app->group('/{id}', function () use ($app) {
        $app->put('/edit', 'book.controller:editBook');
        $app->delete('/delete', 'book.controller:deleteBook');
    });
});
