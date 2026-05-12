$(document).ready(function () {

    console.log("JS loaded");

    $("#registerBtn").click(function () {

        let name = $("#name").val().trim();
        let email = $("#email").val().trim();
        let password = $("#password").val().trim();

        if (name === "" || email === "" || password === "") {
            alert("All fields required");
            return;
        }

        $.ajax({
            url: "php/register.php",
            type: "POST",
            data: { name, email, password },

            success: function (response) {
                let res = response.trim();

                if (res === "success") {
                    alert("Registered successfully!");
                    window.location.href = "login.html";
                } else {
                    alert("Error: " + res);
                }
            },

            error: function (err) {
                console.log("Register error:", err);
            }
        });
    });
});
