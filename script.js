function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    // Construye la URL con los parámetros de usuario y contraseña
    //const url = `http://lerimind.wuaze.com/mindland/login.php?user=${encodeURIComponent(username)}&pass=${encodeURIComponent(password)}`;
    const url = `http://localhost/mindland/login.php?user=${encodeURIComponent(username)}&pass=${encodeURIComponent(password)}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Muestra la respuesta del servidor
            const responseDiv = document.getElementById("response");
            if (data.login === "y") {
                responseDiv.innerHTML = `<p>Login exitoso. Token: ${data.token}</p>`;
            } else {
                responseDiv.innerHTML = `<p>Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("response").innerHTML = `<p>Error de conexión.</p>`;
        });
}
