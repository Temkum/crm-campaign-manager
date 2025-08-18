<?php

test('it can create an operator', function () {
    $operator = \App\Models\Operator::factory()->make();
    expect($operator)->toBeInstanceOf(\App\Models\Operator::class);
});
