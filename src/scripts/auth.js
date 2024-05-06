script = document.createElement('script');
script.src = 'https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js';
document.head.appendChild(script);


function login(url, api, login, password, errEl){
    let data = {login: login, password: password};
    postData(url, api, data, errEl);
}

function auth_register(url, api, login, email, password, errEl){
    let data = {login: login, email: email, password: password};
    postData(url, api, data, errEl);
}

function postData(url, api, data, errEL){
    axios.post(url + api, data,
        {headers: {'Content-Type': 'multipart/form-data'}})
        .then(function (result) {
            window.location.href = url;
        })
        .catch(error => {
            if (error.response) {
                // The request was made but no response was received
                errEL.textContent = error.response.data.message;
            } else if (error.request) {
                console.error('No response received:', error.request);
            } else {
                console.error('Error during request setup:', error.message);
            }
        });


}