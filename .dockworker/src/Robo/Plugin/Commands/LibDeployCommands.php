<?php

namespace Dockworker\Robo\Plugin\Commands;

use Dockworker\DockworkerDrupalCommands;

/**
 * Provides commands for building and deploying the Drupal application locally.
 */
class LibDeployCommands extends DockworkerDrupalCommands
{
    /**
     * The following function curently does nothing, but provides an example.
     *
     * @hook on-event dockworker-logs-errors-exceptions
     *
     * @return mixed[]
     *   The error log exceptions.
     */
    public function provideErrorLogConfiguration(): array
    {
        return [
            [],
            array_values(
                [
                    'Expected in Local.' => ' Unable to authenticate Google API Client',
                ]
            ),
        ];
    }
}
