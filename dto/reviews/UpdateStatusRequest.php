<?php

namespace dto\reviews;


use enums\reviews\ReviewStatus;

class UpdateStatusRequest
{
    public int $id;
    public ReviewStatus $status;
}