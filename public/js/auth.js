// auth.js

// Función para registrar al usuario y guardar el token
function registerUser(email, password, name) {
    fetch("http://localhost/proyecto/api/register", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ email, password, name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            localStorage.setItem("auth_token", data.token); // Guardar token
            console.log("Registro exitoso, token guardado.");
            window.location.href = "confirmAccount.html"; // Redirigir a la página de confirmación
        } else {
            console.log(data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Función para confirmar la cuenta automáticamente con el token guardado
function confirmAccount() {
    const token = localStorage.getItem("auth_token");

    if (!token) {
        document.getElementById("message").innerText = "No hay un token disponible.";
        return;
    }

    fetch("http://localhost/proyecto/api/confirmAccount", {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("message").innerText = data.message || data.error;
    })
    .catch(error => console.error("Error:", error));
}

// Función para enviar el token automáticamente en cada solicitud protegida
function fetchProtectedData() {
    const token = localStorage.getItem("auth_token");

    fetch("http://localhost/proyecto/api/protectedEndpoint", {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error("Error:", error));
}
