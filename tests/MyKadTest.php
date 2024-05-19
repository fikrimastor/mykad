<?php

use FikriMastor\MyKad\Facades\MyKad;

const TEST_MYKAD = '010101-01-0101';

it('can test', function () {
    expect(true)->toBeTrue();
});

it('can test mykad length is valid', function () {

    $mykad = MyKad::lengthIsValid(TEST_MYKAD);

    expect($mykad)->toBeTrue(TEST_MYKAD.' length is valid');
});

it('can test mykad length is invalid', function () {

    $number = str()->random(13);
    $mykad = MyKad::lengthIsValid($number);

    expect($mykad)->toBeFalse($number.' length is invalid');
});

it('can test mykad input is valid', function () {

    $mykad = MyKad::extractMyKad(TEST_MYKAD);

    expect($mykad)->toBeArray(TEST_MYKAD.' input is valid');
});

it('can test mykad input is invalid', function () {

    $number = str()->random(13);
    $mykad = MyKad::extractMyKad($number);

    expect($mykad)->toBeFalse($number.' input is invalid');
});
