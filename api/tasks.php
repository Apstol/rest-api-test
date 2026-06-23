<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once '../repository/tasks-repository.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            getTask();
        } else {
            getTasks();
        }
        break;
    case 'POST':
        createTask();
        break;
    case 'PUT':
        updateTask();
        break;
    case 'DELETE':
        deleteTask();
        break;
    default:
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Некорректный запрос'
        ]);
        break;
}

exit;

function getTask(): void {
    $tasksRepository = new TasksRepository();
    try {
        $task = $tasksRepository->getTask($_GET['id']);
        http_response_code(200);
        echo json_encode($task);
    } catch (PDOException $exception) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Внутренняя ошибка сервера',
        ]);
    } catch (Exception $exception) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => $exception->getMessage(),
        ]);
    }
}

function getTasks(): void {
    try {
        $tasksRepository = new TasksRepository();
        $tasks = $tasksRepository->getTasks();
        http_response_code(200);
        echo json_encode($tasks);
    } catch (PDOException $exception) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Внутренняя ошибка сервера',
        ]);
    }
}

function createTask() {
    $tasksRepository = new TasksRepository();
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['title'], $data['description'], $data['status'])) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Переданы не все параметры',
        ]);

        return;
    }

    try {
        $task = $tasksRepository->createTask($data['title'], $data['description'], $data['status']);
        http_response_code(201);
        echo json_encode($task);
    } catch (PDOException $exception) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Внутренняя ошибка сервера',
        ]);
    } catch (Exception $exception) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => $exception->getMessage(),
        ]);
    }
}

function updateTask(): void {
    $tasksRepository = new TasksRepository();
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['title'], $data['description'], $data['status']) || empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => 'Переданы не все параметры',
        ]);
        
        return;
    }

    try {
        $task = $tasksRepository->updateTask($_GET['id'], $data['title'], $data['description'], $data['status']);
        echo json_encode($task);
    } catch (PDOException $exception) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Внутренняя ошибка сервера',
        ]);
    } catch (Exception $exception) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => $exception->getMessage(),
        ]);
    }
}

function deleteTask(): void {
    if (empty($_GET['id'])) {
        echo json_encode([
            'error' => true,
            'message' => 'Некорректный запрос'
        ]);

        return;
    }

    $tasksRepository = new TasksRepository();
    try {
        $tasksRepository->deleteTask($_GET['id']);
        http_response_code(204);
    } catch (PDOException $exception) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Внутренняя ошибка сервера',
        ]);
    } catch (Exception $exception) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'message' => $exception->getMessage(),
        ]);
    }
}
