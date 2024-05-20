<?php
// Get the full request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Split the URI into parts
$uri_parts = explode('/', $request_uri);

// Get the last part of the URI, which should be your code
$code = end($uri_parts);

// Do something with the code, like display it or process it
//echo "The code is: " . htmlspecialchars($code);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js"></script>
    <script src=".config.js"></script>
    <link rel="stylesheet" href="styles/main.css">
    <script src="scripts/cookieHandler.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
    <title>Hlasovanie</title>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<h2>Hlasovanie za otázku</h2>
<div id="questionContainer">
    <div id="questionText"></div>
    <div id="textFieldContainer" class="hidden">
        <label for="responseText">Vaša odpoveď:</label><br>
        <input type="text" id="responseText" name="responseText"><br>
    </div>
    <div id="responseContainer" class="hidden">
        <label for="responseOptions">Vyberte jednu z možností:</label><br>
        <select id="responseOptions">

        </select>
    </div>
    <div id="checkboxContainer" class="hidden">
        <label>Vyberte svoju odpoveď:</label><br>
    </div>
</div>
<div id="messageContainer"></div>
<button id="submitButton">Potvrdiť hlasovanie</button>
<button id="redirectButton">Spať na úvodnú stranu</button>

<script>
    const code = '<?php echo $code; ?>';
    const questionApiUrl = 'https://node57.webte.fei.stuba.sk:739/BE/api/questions.php?qrcode=%22' + code + '%22';
    let batchId = null;
    let questionId = null;
    let wordmap = null;

    axios.get(questionApiUrl)
        .then(response => {
            if (response.status === 200) {
                const question = response.data;
                console.log(response.data);
                const questionTextDiv = document.getElementById('questionText');
                questionTextDiv.textContent = `Otázka: ${question.text}`;

                questionId = question.id;
                wordmap = question.is_wordmap;

                if (question.type === 'OPEN') {
                    const textFieldContainer = document.getElementById('textFieldContainer');
                    textFieldContainer.classList.remove('hidden');
                } else if (question.type === 'CLOSED' && question.multiple_answers === 'N') {
                    const responseContainer = document.getElementById('responseContainer');
                    responseContainer.classList.remove('hidden');
                } else if (question.type === 'CLOSED' && question.multiple_answers === 'Y') {
                    const checkboxContainer = document.getElementById('checkboxContainer');
                    checkboxContainer.classList.remove('hidden');
                }

                fetchLastBatchInfo(question.id, question.type, question.multiple_answers);
            } else {
                console.error('Failed to fetch question');
            }
        })
        .catch(error => {
            console.error('Error:', error.message);
        });

    function fetchLastBatchInfo(questionId, questionType, questionMA) {
        const batchApiUrl = 'https://node57.webte.fei.stuba.sk:739/BE/api/batches.php?question_id=' + questionId;

        axios.get(batchApiUrl)
            .then(response => {
                if (response.status === 200) {
                    const batches = response.data;
                    if (batches.length > 0) {
                        const lastBatch = batches[batches.length - 1];
                        batchId = lastBatch.id;
                        fetchResponses(lastBatch.id, questionType, questionMA);
                    } else {
                        console.error('No batches found for question ID:', questionId);
                    }
                } else {
                    console.error('Failed to fetch batches');
                }
            })
            .catch(error => {
                console.error('Error:', error.message);
            });
    }

    function fetchResponses(batchId, questionType, questionMA) {
        const responseApiUrl = 'https://node57.webte.fei.stuba.sk:739/BE/api/responses.php?batch_id=' + batchId;

        axios.get(responseApiUrl)
            .then(response => {
                if (response.status === 200) {
                    const responses = response.data;
                    console.log('Responses:', responses);

                    if (questionType === 'OPEN') {
                        if (responses.length > 0 && responses[0].answer !== undefined) {
                            document.getElementById('responseText').value = responses[0].answer;
                        }
                    } else if (questionType === 'CLOSED') {
                        if (questionMA === 'N') {
                            if (responses.length > 0 && responses[0].answer !== undefined) {
                                const responseOptions = document.getElementById('responseOptions');
                                responses.forEach(response => {
                                    const option = document.createElement('option');
                                    option.value = response.id;
                                    option.textContent = response.answer;
                                    responseOptions.appendChild(option);
                                });
                            }
                        } else if (questionMA === 'Y') {
                            if (responses.length > 0 && responses[0].answer !== undefined) {
                                const checkboxContainer = document.getElementById('checkboxContainer');
                                responses.forEach(response => {
                                    const label = document.createElement('label');
                                    const checkbox = document.createElement('input');
                                    checkbox.type = 'checkbox';
                                    checkbox.name = 'responseCheckbox';
                                    checkbox.value = response.id;
                                    label.appendChild(checkbox);
                                    label.appendChild(document.createTextNode(response.answer));
                                    label.appendChild(document.createElement('br'));
                                    checkboxContainer.appendChild(label);
                                });
                            }
                        }
                    }
                } else {
                    console.error('Failed to fetch responses');
                }
            })
            .catch(error => {
                console.error('Error:', error.message);
            });
    }

    document.getElementById('submitButton').addEventListener('click', function() {
        const responseText = document.getElementById('responseText').value.trim();

        const responseOptions = document.getElementById('responseOptions');
        let selectedOptionIds = [];

        if (responseText) {
            if (!batchId) {
                displayMessage('No batch selected.', true);
                return;
            }

            const postData = `batch_id=${encodeURIComponent(batchId)}&question_id=${encodeURIComponent(questionId)}&answer=${encodeURIComponent(responseText)}`;

            axios.post('https://node57.webte.fei.stuba.sk:739/BE/api/responses.php', postData, {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => {
                    console.log(response.data);
                    increaseVoteCount(response.data.id);
                    document.getElementById('responseText').value = '';
                    window.location.href = `results.html?batchId=${batchId}&wordmap=${wordmap}`;

                })
                .catch(error => {
                    console.error('Error:', error.message);
                    displayMessage('Nepodarilo sa zahlasovať.', true);
                });

            return;
        }

        if (responseOptions) {
            const selectedOptionId = responseOptions.value;
            if (selectedOptionId) {
                selectedOptionIds.push(selectedOptionId);
            }
        }

        const checkboxes = document.querySelectorAll('input[name="responseCheckbox"]:checked');
        if (checkboxes.length > 0) {
            checkboxes.forEach(checkbox => {
                selectedOptionIds.push(checkbox.value);
            });
        }


        if (selectedOptionIds.length === 0) {
            displayMessage('Prosím vyberte možnosť', true);
            return;
        }

        selectedOptionIds.forEach(optionId => {
            axios.put('https://node57.webte.fei.stuba.sk:739/BE/api/responses.php', {
                id: optionId,
                action: 'vote'
            }, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    console.log(response.data);
                    window.location.href = `results.html?batchId=${batchId}&wordmap=${wordmap}`;

                })
                .catch(error => {
                    console.error('Error:', error.message);
                    displayMessage('Nepodarilo sa zahlasovať.', true);
                });
        });
    });
    function increaseVoteCount(responseId) {
        axios.put('https://node57.webte.fei.stuba.sk:739/BE/api/responses.php', {
            id: responseId,
            action: 'vote'
        }, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                console.log(response.data);
                window.location.href = `results.html?batchId=${batchId}&wordmap=${wordmap}`;

            })
            .catch(error => {
                console.error('Error:', error.message);
                displayMessage('Nepodarilo sa zahlasovať.', true);
            });
    }

    document.getElementById('redirectButton').addEventListener('click', function() {
        window.location.href = 'index.html';
    });

    function displayMessage(message, isError) {
        const messageContainer = document.getElementById('messageContainer');
        messageContainer.textContent = message;
        if (isError) {
            messageContainer.style.color = 'red';
        } else {
            messageContainer.style.color = 'green';
        }
    }

</script>

</body>
</html>
