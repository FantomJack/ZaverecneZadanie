<?php
$method = $_SERVER['REQUEST_METHOD'];

echo "Test " . $method;

// Question.add()
// if (qrcode == null) http_response_code(404)
//then Batch.add(question_id) return id;
//if closed question then for each {question responses} : Response.add(batch.id, answer) {votes = 0}