function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const url = `https://wsmindland-production.up.railway.app/login.php?user=${encodeURIComponent(username)}&pass=${encodeURIComponent(password)}`;


    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Muestra la respuesta del servidor
            const responseDiv = document.getElementById("response");
            if (data.login === "y") {
                console.log(data.token);
            } else {
                console.log(data.login);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("response").innerHTML = `<p>Error de conexión.</p>`;
        });
}
