<?php declare(strict_types=1);

namespace DistanceRateShipping\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1690895435DistanceRatesTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1690895435;
    }

    public function update(Connection $connection): void
    {
        // implement update
        $query = <<<SQL
           CREATE TABLE IF NOT EXISTS `distance_rate` (
               `id` BINARY(16) NOT NULL,
               `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
               `range_from` INTEGER COLLATE utf8mb4_unicode_ci,
               `range_to` INTEGER COLLATE utf8mb4_unicode_ci,
               `price` FLOAT COLLATE utf8mb4_unicode_ci,
               `status` TINYINT COLLATE utf8mb4_unicode_ci,
               `created_at` DATETIME(3) NOT NULL,
               `updated_at` DATETIME(3),
               PRIMARY KEY (`id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
       $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
