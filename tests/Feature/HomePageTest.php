<?php

test('home redirects to the kaiju catalogue', function () {
    $this->get(route('home'))
        ->assertRedirect(route('kaijus.index'));
});
