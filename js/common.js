window.addEventListener("load", function () {
    bindFormSubmit("signup-form", "api/signup_submit.php", signup_success);
    bindFormSubmit("login-form", "api/login_submit.php", login_success);
});

function bindFormSubmit(formId, actionUrl, successCallback) {
    var form = document.getElementById(formId);
    if (!form) {
        return;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        var XHR = new XMLHttpRequest();
        var form_data = new FormData(form);

        XHR.addEventListener("load", successCallback);
        XHR.addEventListener("error", on_error);
        XHR.open("POST", actionUrl);
        XHR.send(form_data);

        var loading = document.getElementById("loading");
        if (loading) {
            loading.style.display = 'block';
        }
    });
}


function parseJsonResponse(text) {
    try {
        return JSON.parse(text);
    } catch (e) {
        return { success: false, message: 'Invalid server response.' };
    }
}

var signup_success = function (event) {
    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'none';
    }

    var response = parseJsonResponse(event.target.responseText);
    if (response.success) {
        if (window.$) {
            window.$('#signup-modal').modal('hide');
        }
        alert(response.message);
        window.location.href = "index.php";
    } else {
        alert(response.message);
    }
};

var login_success = function (event) {
    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'none';
    }

    var response = parseJsonResponse(event.target.responseText);
    if (response.success) {
        if (window.$) {
            window.$('#login-modal').modal('hide');
        }
        alert(response.message);
        window.location.reload();
    } else {
        alert(response.message);
    }
};

var on_error = function (event) {
    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'none';
    }

    alert('Oops! Something went wrong.');
};