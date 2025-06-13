<?php

namespace dto\animals;

class UpdateAnimalRequest extends CreateAnimalRequest
{
    public bool $is_adopted = false;
}