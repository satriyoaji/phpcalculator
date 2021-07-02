<?php

namespace Jakmall\Recruitment\Calculator\Containers;

use Illuminate\Contracts\Container\Container;

interface ContainerServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return void
     */
    public function register(Container $container): void;
}