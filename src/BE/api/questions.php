<?php
$method = $_SERVER['REQUEST_METHOD'];

require_once "../.config.php";
require_once "../services/Question.php";
$questionObj = new Question($conn);


// Question.add()
// if (qrcode == null) http_response_code(404)
//then Batch.add(question_id) return id;
//if closed question then for each {question responses} : Response.add(batch.id, answer) {votes = 0}