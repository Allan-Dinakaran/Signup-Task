$(document).ready(function () {

    console.log("Profile JS loaded");

    let token = localStorage.getItem("token");

    if (!token) {
        window.location.href = "login.html";
        return;
    }

    console.log("TOKEN:", token);

    $.ajax({
        url: "php/profile.php",
        type: "POST",
        data: { token: token },

        success: function (response) {

            console.log("RAW RESPONSE:", response);

            if (typeof response === "string") {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.log("JSON parse error:", response);
                    return;
                }
            }

            if (response.error) {
                alert(response.error);
                localStorage.removeItem("token");
                window.location.href = "login.html";
                return;
            }

            $("#userInfo").text(
                "Name: " + response.name + "\nEmail: " + response.email
            );
        },

        error: function (err) {
            console.log("AJAX error:", err);
        }
    });

    $("#logoutBtn").click(function () {
        localStorage.removeItem("token");
        window.location.href = "login.html";
    });

});