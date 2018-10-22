<?php
$app->group('/user', function () use ($app) {
    $app->get('/profile', 'user.controller:showInfo');
    $app->put('/edit', 'user.controller:editUser');
    $app->delete('/delete', 'user.controller:deleteUser');
});

$app->group('/books', function () use ($app) {
    $app->get('/', 'book.controller:listBooks');
    $app->post('/add', 'bool.controller:addBook');
    $app->group('/{id}', function () use ($app) {
        $app->put('/edit', 'book.controller:editBook');
        $app->delete('/delete', 'book.controller:deleteBook');
    });
});
