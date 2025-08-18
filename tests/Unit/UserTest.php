<?php

test('it can create a user', function () {
    $user = \App\Models\User::factory()->make();
    expect($user)->toBeInstanceOf(\App\Models\User::class);
});
