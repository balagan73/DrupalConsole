<?php

/**
 * @file
 * Contains \Drupal\Console\Command\Database\ConnectTrait.
 */

namespace Drupal\Console\Command\Database;

use Drupal\Console\Style\DrupalStyle;

trait ConnectTrait
{
    public function resolveConnection(DrupalStyle $io, $database = 'default')
    {
        $connectionInfo = $this->getConnectionInfo();

        if (!$connectionInfo || !isset($connectionInfo[$database])) {
            $io->error(
                sprintf(
                    $this->trans('commands.database.connect.messages.database-not-found'),
                    $database
                )
            );

            return null;
        }

        $databaseConnection = $connectionInfo[$database];
        if ($databaseConnection['driver'] !== 'mysql') {
            $io->error(
                sprintf(
                    $this->trans('commands.database.connect.messages.database-not-supported'),
                    $databaseConnection['driver']
                )
            );

            return null;
        }

        return $databaseConnection;
    }

    public function getRedBeanConnection($database = 'default')
    {
        $redBean = $this->getContainerHelper()->get('redbean');

        $connectionInfo = $this->getConnectionInfo();
        $databaseConnection = $connectionInfo[$database];
        if ($databaseConnection['driver'] == 'mysql') {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s',
                $databaseConnection['host'],
                $databaseConnection['database']
            );

            $redBean->setup(
                $dsn,
                $databaseConnection['username'],
                $databaseConnection['password'],
                true
            );

            return $redBean;
        }

        return null;
    }
}
