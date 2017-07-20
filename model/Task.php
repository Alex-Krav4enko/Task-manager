<?php

class Task {

  private $db = null;

  function __construct($db)
  {
    $this->db = $db;
  }

  /**
   * Новая задача
   * @param $description string
   * @param $userId int
   */
  function newTask($description, $userId)
  {
    $sth = $this->db->prepare(
      "INSERT INTO tasks (description, user_id, assigned_user_id, date_added) VALUES (:description, :id, :id, NOW())"
    );
    $sth->bindValue(':description', $description, PDO::PARAM_STR);
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
    $sth->execute();
  }

  /**
   * Удаление задачи
   * @param $taskId int
   * @return mixed
   */
  function delete($taskId)
  {
    $sth = $this->db->prepare(
      "DELETE FROM tasks WHERE id = :id"
    );
    $sth->bindValue(':id', $taskId, PDO::PARAM_INT);
    return $sth->execute();
  }

  /**
   * Выполнение задачи
   * @param $taskId int
   * @return mixed
   */
  function done($taskId)
  {
    $sth = $this->db->prepare(
      "UPDATE tasks SET is_done = 1 WHERE id = :id"
    );
    $sth->bindValue(':id', $taskId, PDO::PARAM_INT);
    return $sth->execute();
  }

  /**
   * Редактирование описание задачи
   * @param $description string
   * @param $taskId int
   * @return mixed
   */
  function edit($description, $taskId)
  {
    $sth = $this->db->prepare(
      "UPDATE tasks SET description = :description WHERE id = :id"
    );
    $sth->bindValue(':description', $description, PDO::PARAM_STR);
    $sth->bindValue(':id', $taskId, PDO::PARAM_INT);
    return $sth->execute();
  }

  /**
   * Получение описания редактируемой задачи
   * @param $taskId int
   * @return string
   */
  function getDescription($taskId)
  {
    $sth = $this->db->prepare(
      "SELECT description FROM tasks WHERE id = :id"
    );
    $sth->bindValue(':id', $taskId, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchColumn();
  }

  /**
   * Получение всех задач
   * @param $userId int
   * @return array
   */
  function getTasks($userId)
  {
    $sth = $this->db->prepare(
      "SELECT * FROM tasks WHERE user_id = :id"
    );
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchAll();
  }

  /**
   * Получение всех задач отсортированных по входным данным
   * @param $sortType string
   * @param $userId int
   * @return array
   */
  function sortBy($sortType, $userId)
  {
    $sth = $this->db->prepare(
      "SELECT * FROM tasks WHERE user_id = :id ORDER BY {$sortType}"
//      "SELECT * FROM tasks WHERE user_id = :id ORDER BY :orderType"
    );
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
//    $sth->bindValue(':orderType', $sortType, PDO::PARAM_STR);
    $sth->execute();
    return $sth->fetchAll();
  }

  /**
   * Зарегистрированные пользователи
   * @return array
   */
  function users()
  {
    $sth = $this->db->prepare(
      "SELECT id, login FROM user"
    );
    $sth->execute();
    return $sth->fetchAll(PDO::FETCH_KEY_PAIR);
  }

  /**
   * Делегирование задачи
   * @param $delegateUserId int
   * @param $delegateTaskId int
   */
  function delegateTask($delegateUserId, $delegateTaskId)
  {
    $sth = $this->db->prepare(
      "UPDATE tasks SET assigned_user_id = :assigned_user_id WHERE id = :id"
    );
    $sth->bindValue(':assigned_user_id', $delegateUserId, PDO::PARAM_INT);
    $sth->bindValue(':id', $delegateTaskId, PDO::PARAM_INT);
    $sth->execute();
  }

  /**
   * Автор задач
   * @param $userId int
   * @return array
   */
  function authors($userId)
  {
    $sth = $this->db->prepare(
      "SELECT login FROM user AS u INNER JOIN tasks AS t ON t.user_id = u.id WHERE u.id = :id"
    );
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchAll(PDO::FETCH_COLUMN);
  }

  /**
   * Исполнитель задач
   * @param $userId int
   * @return array
   */
  function executors($userId)
  {
    $sth = $this->db->prepare(
      "SELECT login FROM user AS u INNER JOIN tasks AS t ON t.assigned_user_id = u.id WHERE t.user_id = :id"
    );
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchAll(PDO::FETCH_COLUMN);
  }

  /**
   * Задачи зарегистрированного пользователя
   * @param $userId int
   * @return array
   */
  function userTasks($userId)
  {
    $sth = $this->db->prepare(
      "SELECT * FROM tasks WHERE assigned_user_id = :id AND user_id != :id"
    );
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchAll();
  }

  /**
   * Авторы или исполнители задач
   * @param $authorOrExecutor string
   * @param $userId int
   * @return array
   */
  function userAuthorOrExecutor($authorOrExecutor, $userId)
  {
    $sth = $this->db->prepare(
      "SELECT login FROM user AS u INNER JOIN tasks AS t ON {$authorOrExecutor} = u.id WHERE t.assigned_user_id = :id AND user_id != :id"
//      "SELECT login FROM user AS u INNER JOIN tasks AS t ON :authorOrExecutor = u.id WHERE t.assigned_user_id = :id AND user_id != :id"
    );
//    $sth->bindValue(':authorOrExecutor', $authorOrExecutor, PDO::PARAM_INT);
    $sth->bindValue(':id', $userId, PDO::PARAM_INT);
    $sth->execute();
    return $sth->fetchAll(PDO::FETCH_COLUMN);
  }
}