<?php

enum Status: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}

class TasksRepository {
    public function getTask(int $id): array
    {
        $query = 'SELECT * FROM tasks WHERE id=:id';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$task) {
            throw new Exception(sprintf('Task с id=%d не найден', $id));
        }

        return $task;
    }

    public function getTasks(): array
    {
        $query = 'SELECT * FROM tasks';
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $tasks;
    }

    public function createTask(string $title, string $description, string $status): array
    {
        $title = strip_tags($title);
        $description = strip_tags($description);
        $statusEnum = Status::tryFrom($status);
        if (!$statusEnum) {
            throw new Exception('Неверное поле "status"');
        }

        $query = 'INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status)';
        $connection = $this->getConnection();
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        $newId = (int) $connection->lastInsertId();

        return [
            'id' => $newId,
            'title' => $title,
            'description' => $description,
            'status' => $status,
        ];
    }

    public function updateTask(int $id, string $title, string $description, string $status): array
    {
        $title = strip_tags($title);
        $description = strip_tags($description);
        $statusEnum = Status::tryFrom($status);
        if (!$statusEnum) {
            throw new Exception('Неверное поле "status"');
        }

        $this->getTask($id);

        $query = 'UPDATE tasks SET title=:title, description=:description, status=:status WHERE id=:id';
        $connection = $this->getConnection();
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
        ];
    }

    public function deleteTask(int $id): void
    {
        $this->getTask($id);

        $query = 'DELETE FROM tasks WHERE id=:id';
        $connection = $this->getConnection();
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function getConnection(): PDO
    {
        $databaseConfig = require_once __DIR__ . '/../config/database.php';
        $connectionString = sprintf("mysql:dbname=%s;port=3306;host=%s", $databaseConfig['db'], $databaseConfig['host']);

        try {
            $connection = new PDO($connectionString, $databaseConfig['user'], $databaseConfig['password']);
        } catch (PDOException $exception) {
            error_log(sprintf('DB connection failed: %s', $exception->getMessage()));
            throw $exception;
        }

        return $connection;
    }
}
