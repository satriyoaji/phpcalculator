<?php

namespace Jakmall\Recruitment\Calculator\History;

use Illuminate\Contracts\Container\Container;
use Jakmall\Recruitment\Calculator\Containers\ContainerServiceProviderInterface;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Jakmall\Recruitment\Calculator\History\CommandManage;

class CommandHistoryServiceProvider implements ContainerServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $container): void
    {
        $container->bind(
            CommandHistoryManagerInterface::class,
            function () {
                return new CommandManage();
            }
        );
    }
}