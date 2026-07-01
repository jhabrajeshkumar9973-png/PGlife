window.addEventListener("load", function () {
    var is_interested_images = document.getElementsByClassName("is-interested-image");
    if (!is_interested_images || is_interested_images.length === 0) {
        return;
    }

    Array.from(is_interested_images).forEach(element => {
        element.addEventListener("click", function (event) {
            event.preventDefault();
            var property_id = event.target.getAttribute("property_id");
            if (!property_id) {
                return;
            }

            var XHR = new XMLHttpRequest();
            XHR.addEventListener("load", remove_interested_success);
            XHR.addEventListener("error", on_error);
            XHR.open("GET", "api/toggle_interested.php?property_id=" + encodeURIComponent(property_id));
            XHR.send();

            var loading = document.getElementById("loading");
            if (loading) {
                loading.style.display = 'block';
            }
        });
    });
});

var remove_interested_success = function (event) {
    var loading = document.getElementById("loading");
    if (loading) {
        loading.style.display = 'none';
    }

    var response;
    try {
        response = JSON.parse(event.target.responseText);
    } catch (e) {
        return;
    }

    if (response.success) {
        var property_id = response.property_id;
        var property_card = document.getElementsByClassName("property-id-" + property_id)[0];
        if (property_card) {
            property_card.style.display = 'none';
        }
    }
};
