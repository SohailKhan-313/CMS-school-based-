<?php

test('the application redirects unauthenticated guest to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
