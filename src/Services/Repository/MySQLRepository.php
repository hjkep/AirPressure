<?php

namespace AirPressure\Services\Repository;

use AirPressure\Model\AirPressurePoint;
use AirPressure\Model\EnvironmentEnum;
use DateTimeZone;
use PDO;
use DateTimeImmutable;
use DateTimeInterface;

class MySQLRepository implements RepositoryInterface {

    /**
     * @var array<mixed, mixed>
     */
    private array $configArray;
    private PDO $pdo;

    private const string DATE_FORMAT_TIMESTAMP = 'Y-m-d H:i:s';

    public function __construct() {
        $this->configArray = include __DIR__ . '/../../../config/phinx.php';
    }

    private function getPdo(): PDO {
        if (isset($this->pdo)) {
            return $this->pdo;
        }

        $environment = EnvironmentEnum::getConfigured();

        if (empty($this->configArray['environments']) || empty($this->configArray['environments'][$environment->value])) {
            throw new \Exception('Config for environment ' . $environment->value . ' not found');
        }

        $environmentConfig = $this->configArray['environments'][$environment->value];

        $host = $environmentConfig['host'];
        $db = $environmentConfig['name'];

        $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=UTF8",$environmentConfig['user'],$environmentConfig['pass']);
        return $this->pdo;
    }

    public function get(int $id): AirPressurePoint {
        $statement = $this->getPdo()->prepare('SELECT * FROM airpressure WHERE id = :id LIMIT 1');
        $statement->execute([
            'id' => $id
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($result)) {
            throw new \Exception('Airpressure with id '. $id .' not found');
        }

        return $this->_mapRowToModel($result);
    }

    public function save(AirPressurePoint $model): AirPressurePoint {
        if (!$model->getId()) {
            return $this->_insert($model);
        }

        return $this->_update($model);
    }

    public function delete(int $id): void {
        $sql = 'DELETE FROM airpressure WHERE id = :id';
        $statement = $this->getPdo()->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new \Exception('Could not delete pressure with id '. $id);
        }
    }

    /**
     * 
     * @return AirPressurePoint[]
     */
    public function list(int $limit = 100, int $offset = 0, string $sort = 'id:ASC'): array {
        [$sortingField, $sortingDirection] = explode(":", $sort);

        $sql = "SELECT * FROM airpressure ORDER BY $sortingField $sortingDirection LIMIT :offset, :limit";

        $statement = $this->getPdo()->prepare($sql);

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(
            fn ($result) => $this->_mapRowToModel($result), 
            $result
        );
    }

    public function listWithRange(DateTimeImmutable $start, DateTimeImmutable $end): array {
        $sql = "SELECT * FROM airpressure WHERE created >= :created_start AND created <= :created_end ORDER BY id ASC limit 1000";

        $statement = $this->getPdo()->prepare($sql);

        $statement->execute([
            'created_start' => $start->format(self::DATE_FORMAT_TIMESTAMP),
            'created_end' => $end->modify('+1 day -1 microsecond')->format(self::DATE_FORMAT_TIMESTAMP)
        ]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(
            fn ($result) => $this->_mapRowToModel($result), 
            $result
        );
    }

    private function _insert(AirPressurePoint $model): AirPressurePoint {
        $sql = 'INSERT INTO airpressure (created, last_modified, pressure) VALUES (:created, :last_modified, :pressure)';
        $statement = $this->getPdo()->prepare($sql);

        $result = $statement->execute([
            'created' => $model->getCreated()->format(self::DATE_FORMAT_TIMESTAMP),
            'last_modified' => $model->getLastModified()->format(self::DATE_FORMAT_TIMESTAMP),
            'pressure' => $model->getPressure()
        ]);

        $id = $this->pdo->lastInsertId();

        if (!$result || !is_string($id)) {
            throw new \Exception('Airpressure not created');
        }

        return new AirPressurePoint(
            last_modified: $model->getLastModified(), 
            pressure: $model->getPressure(), 
            created: $model->getCreated(), 
            id: (int)$id
        );
    }

    private function _update(AirPressurePoint $model): AirPressurePoint {
        $sql = 'UPDATE airpressure
        SET last_modified = :last_modified
        SET pressure = :pressure
        WHERE id = :id';

        $statement = $this->getPdo()->prepare($sql);

        $result = $statement->execute([
            'last_modified' => $model->getLastModified()->format(self::DATE_FORMAT_TIMESTAMP),
            'pressure' => $model->getPressure(),
            'id' => $model->getId()
        ]);

        if (!$result) {
            throw new \Exception('Airpressure not updated');
        }

        return $model;
    }

    /**
     * @param string[] $row
     * @return AirPressurePoint
     */
    private function _mapRowToModel(array $row): AirPressurePoint {
        return new AirPressurePoint(
            id: (int)$row['id'],
            pressure: (float)$row['pressure'],
            created: (new DateTimeImmutable($row['created'], new DateTimeZone('UTC'))),
            last_modified: (new DateTimeImmutable($row['last_modified'], new DateTimeZone('UTC')))
        );
    }

    public function getWithLastModifiedAndPressure(DateTimeImmutable $lastModified, float $pressure): ?AirPressurePoint {
        $statement = $this->getPdo()->prepare('SELECT * FROM airpressure WHERE last_modified = :last_modified AND pressure = :pressure LIMIT 1');
        $statement->execute([
            'last_modified' => $lastModified->format(self::DATE_FORMAT_TIMESTAMP),
            'pressure' => $pressure
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return null;
        }

        return $this->_mapRowToModel($result);
    }
}