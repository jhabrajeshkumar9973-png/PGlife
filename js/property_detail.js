window.addEventListener("load", function () {
    const propertyId = window.PG_LIFE_PROPERTY_ID || null;
    const propertyName = window.PG_LIFE_PROPERTY_NAME || null;
    const isInterestedImage = document.getElementsByClassName("is-interested-image")[0];
    const bookNowButton = document.querySelector('.book-now-btn');

    if (isInterestedImage && propertyId) {
        isInterestedImage.addEventListener("click", function (event) {
            event.preventDefault();
            if (!window.PG_LIFE_LOGGED_IN) {
                if (window.$) {
                    window.$('#login-modal').modal('show');
                } else {
                    alert('Please log in to mark interest.');
                }
                return;
            }

            sendToggleInterest(propertyId);
        });
    }

    if (bookNowButton) {
        bookNowButton.addEventListener('click', function (event) {
            event.preventDefault();
            if (!window.PG_LIFE_LOGGED_IN) {
                if (window.$) {
                    window.$('#login-modal').modal('show');
                } else {
                    alert('Please log in to book this PG.');
                }
                return;
            }

            var message = 'Thanks for your interest! We have recorded your booking request for ' + (propertyName || 'this PG') + '.';
            alert(message);
        });
    }
});

function sendToggleInterest(propertyId) {
    var XHR = new XMLHttpRequest();
    XHR.addEventListener("load", toggle_interested_success);
    XHR.addEventListener("error", on_error);
    XHR.open("GET", "api/toggle_interested.php?property_id=" + encodeURIComponent(propertyId));
    XHR.send();

    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'block';
    }
}

var toggle_interested_success = function (event) {
    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'none';
    }

    var response;
    try {
        response = JSON.parse(event.target.responseText);
    } catch (e) {
        alert('Invalid server response.');
        return;
    }

    if (response.success) {
        var isInterestedImage = document.getElementsByClassName("is-interested-image")[0];
        var interestedUserCount = document.getElementsByClassName("interested-user-count")[0];
        if (!isInterestedImage || !interestedUserCount) {
            return;
        }

        if (response.is_interested) {
            isInterestedImage.classList.add("fas");
            isInterestedImage.classList.remove("far");
            interestedUserCount.innerHTML = parseFloat(interestedUserCount.innerHTML) + 1;
        } else {
            isInterestedImage.classList.add("far");
            isInterestedImage.classList.remove("fas");
            interestedUserCount.innerHTML = Math.max(0, parseFloat(interestedUserCount.innerHTML) - 1);
        }
    } else if (response.is_logged_in === false) {
        if (window.$) {
            window.$("#login-modal").modal("show");
        } else {
            alert("Please log in to mark interest.");
        }
    } else {
        alert(response.message || 'Something went wrong.');
    }
};

var on_error = function (event) {
    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'none';
    }
    alert('Oops! Something went wrong.');
};
