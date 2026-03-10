<?php
require_once("./vendor/autoload.php");
use App\Views\BaseTemplate;

$template = BaseTemplate::getTemplate();
$resultTemplate =  sprintf($template, 
    "Основная страница", 
    "<p>Пиццерия ИС-231 - это вкусная пицца, которую вам доставят прямо на занятия в 409 кабинет!</p>");
echo $resultTemplate;