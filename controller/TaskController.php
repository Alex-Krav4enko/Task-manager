<?php

require_once 'functions.php';

class TaskController
{
  public static $taskId = null;
  public static $description = null;
  public static $userId = null;
  public static $delegateUserId = null;
  public static $delegateTaskId = null;
  private $model = null;

  function __construct($db)
  {
    include 'model/Task.php';
    $this->model = new Task($db);
  }

  /**
   * Добавление новой задачи
   */
  function addTask()
  {
    if (isset($_POST['description']) && empty($_GET['action'] == 'edit')) {
      $this->model->newTask(self::$description, self::$userId);
    }
  }

  /**
   * Действие
   */
  function action()
  {
    if (!empty($_GET['action'])) {
      if ($_GET['action'] == 'delete') {
        $this->model->delete(self::$taskId);
      } else if ($_GET['action'] == 'done') {
        $this->model->done(self::$taskId);
      } else if ($_GET['action'] == 'edit' && isset($_POST['description'])) {
        $this->model->edit(self::$description, self::$taskId);
        return null;
      } else if ($_GET ['action'] == 'edit') {
        return $this->model->getDescription(self::$taskId);
      }
    }
  }

  /**
   * Сортировка
   */
  function sort()
  {
    if (empty($_POST['sort_by'])) {
      return $this->model->getTasks(self::$userId);
    } else {
      if ($_POST['sort_by'] == 'date_added') {
        return $this->model->sortBy('date_added', self::$userId);
      } else if ($_POST['sort_by'] == 'is_done') {
        return $this->model->sortBy('is_done', self::$userId);
      } else if ($_POST['sort_by'] == 'description') {
        return $this->model->sortBy('description', self::$userId);
      }
    }
  }

  /**
   * Список пользователей
   */
  function getUsers()
  {
    return $this->model->users();
  }

  /**
   * Определить исполнителя
   */
  function setExecutor()
  {
    $this->model->delegateTask(self::$delegateUserId, self::$delegateTaskId);
  }

  /**
   * Cписок авторов задач
   */
  function authors()
  {
    return $this->model->authors(self::$userId);
  }

  /**
   * Cписок исполнителей задач
   */
  function executors()
  {
    return $this->model->executors(self::$userId);
  }

  /**
   * Получение задач зарегистрированного пользователя
   */
  function getTasksForUser()
  {
    return $this->model->userTasks(self::$userId);
  }

  /**
   * Cписок авторов делегированных задач
   */
  function authorsForUser()
  {
    return $this->model->userAuthorOrExecutor('t.user_id', self::$userId);
  }

  /**
   * Cписок исполнителей делегированных задач
   */
  function executorsForUser()
  {
    return $this->model->userAuthorOrExecutor('t.assigned_user_id', self::$userId);
  }

}

TaskController::$taskId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
TaskController::$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
TaskController::$delegateUserId = filter_input(INPUT_POST, 'assigned_user_id', FILTER_SANITIZE_NUMBER_INT);
TaskController::$delegateTaskId = filter_input(INPUT_POST, 'assign', FILTER_SANITIZE_NUMBER_INT);
TaskController::$userId = $_SESSION['user']['id'];

$tasksObj = new TaskController($db);

$tasksObj->addTask();
$selectDescription = $tasksObj->action();
$tasksObj->setExecutor();
$users = $tasksObj->getUsers();
$tasks = $tasksObj->sort();
$tasks['author'] = $tasksObj->authors();
$tasks['responsible'] = $tasksObj->executors();
$tasksForUser = $tasksObj->getTasksForUser();
$tasksForUser['author_second_table'] = $tasksObj->authorsForUser();
$tasksForUser['responsible_second_table'] = $tasksObj->executorsForUser();
