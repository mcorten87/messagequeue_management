<?php

namespace mcorten87\rabbitmq_api\objects;

class QueueName extends BaseObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    public function validate($value) : bool
    {
        if (empty($value)) {
            return false;
        }
        return parent::validate($value);
    }
}
