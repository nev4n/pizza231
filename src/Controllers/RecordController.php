<?php

namespace Crud\Controllers;

use Crud\Models\Record;
use Crud\Views\RecordView;
use PDO;

class RecordController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        // GET-запрос: получить данные и отобразить
        $model = new Record($this->pdo);
        $records = $model->getAll();

        $view = new RecordView();
        echo $view->render($records);
    }
}