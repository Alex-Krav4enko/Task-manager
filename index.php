<?php
require_once 'vendor/autoload.php';
require_once 'database/DataBase.php';
require_once 'controller/TaskController.php';
require_once 'functions.php';

$loader = new Twig_Loader_Filesystem('template/');

$twig = new Twig_Environment($loader, [
  'cache' => 'tmp/cache/',
  'auto_reload' => true
]);

$filter_int = new Twig_Filter('toString', function ($string) {
  return (int)$string;
});

$twig->addFilter($filter_int);

$params = [
  'name' => $_SESSION['user']['login'],
  'description' => $selectDescription,
  'tasks' => $tasks,
  'users' => $users,
  'tasksForUser' => $tasksForUser
];

$template = $twig->loadTemplate('todo.twig');

$template->display($params);
