<?php

namespace Dafiti\Datajet\Exception;

class ResourceNotFound extends \Exception
{
    /**
     * Construct exception with resource name.
     *
     * @param string $resource Resource name
     */
    public function __construct($resource)
    {
        $message = sprintf('The resource %s not found.', $resource);

        parent::__construct($message);
    }
}
