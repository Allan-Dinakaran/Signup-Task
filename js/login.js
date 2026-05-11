$(document).ready(function () {

$("#loginBtn").click(function () {

        let email = $("#email").val().trim();
        let password = $("#password").val().trim();

        if (email === "" || password === "") {
            alert("All fields required");
            return;
        }

        $.ajax({
            url: "php/login.php",
            type: "POST",
            data: { email, password },

            success: function (response) {

                console.log("RAW:", response);

                let res = response.trim();

                if (res !== "error" && res !== "") {
                    localStorage.setItem("token", res);
                    alert("Login success");
                    window.location.href = "profile.html";
                } else {
                    alert("Invalid credentials");
                }
            },

            error: function (err) {
                console.log("Login error:", err);
            }
        });

    });

});

let eyeicon = document.getElementById("eye-close");
let password = document.getElementById("password");

eyeicon.onclick = function () {
    if (password.type === "password") {
        password.type = "text";
        eyeicon.src = "assets/eye-open.png";
    } else {
        password.type = "password";
        eyeicon.src = "assets/eye-close.png";
    }
};