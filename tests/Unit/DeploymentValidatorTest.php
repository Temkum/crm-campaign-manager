<?php

test('it can instantiate DeploymentValidator', function () {
    $validator = app(\App\Services\DeploymentValidator::class);
    expect($validator)->toBeInstanceOf(\App\Services\DeploymentValidator::class);
});
