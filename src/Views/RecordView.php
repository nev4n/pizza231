<?php

namespace Crud\Views;

class RecordView
{
    public function render(array $records): string
    {
        $html = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список записей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1 class="mb-4">Планеты</h1>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Название</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($records as $record) {
            $html .= '<tr>
                <th scope="row">' . htmlspecialchars($record['id']) . '</th>
                <td>' . htmlspecialchars($record['name']) . '</td>
            </tr>';
        }

        $html .= '</tbody></table></body></html>';
        return $html;
    }
}