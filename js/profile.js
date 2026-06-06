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

            $("#profileName").text(response.name);
            $("#profileEmail").text(response.email);
            $("#age").val(response.age);
            $("#dob").val(response.dob);
            $("#contact").val(response.contact);
        },
        error: function (err) {
            console.log("AJAX error:", err);
        }
    });

    $("#updateBtn").click(function () {
        let age     = $("#age").val().trim();
        let dob     = $("#dob").val().trim();
        let contact = $("#contact").val().trim();

        if (age === "" || dob === "" || contact === "") {
            alert("Please fill all fields before updating");
            return;
        }

        $.ajax({
            url: "php/update_profile.php",
            type: "POST",
            data: { token: token, age: age, dob: dob, contact: contact },
            success: function (response) {
                if (typeof response === "string") {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.log("Parse error:", e);
                        return;
                    }
                }
                if (response.success) {
                    alert("Profile updated successfully!");
                } else {
                    alert("Error: " + response.error);
                }
            },
            error: function (err) {
                console.log("Update error:", err);
            }
        });
    });

    $("#logoutBtn").click(function () {
        localStorage.removeItem("token");
        window.location.href = "login.html";
    });
});