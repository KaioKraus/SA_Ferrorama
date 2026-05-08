const login = document.querySelector(".form1");
const elMensagem = document.getElementById("mensagem");

const toggleSenha = document.getElementById("toggleSenha");
const senha = document.getElementById("inputSenha");
const iconSenha = toggleSenha.querySelector("i");

if (toggleSenha) {
    toggleSenha.onclick = () => {
        if (senha.type === "password") {
            senha.type = "text";
            iconSenha.className = "bi bi-eye-slash";
        } else {
            senha.type = "password";
            iconSenha.className = "bi bi-eye";
        }
    };
}

if (login) {

    login.onsubmit = function (event) {
        event.preventDefault();

        const email = document.getElementById("inputEmail").value;
        const senhaValue = inputSenha.value;

        const emailDefinido = "admin@teste.com";
        const senhaDefinida = "adm1n123";

        elMensagem.innerHTML = "";

        if (email === "" || senhaValue === "") {
            elMensagem.innerHTML = "<div class='text-danger fw-bold'>Preencha todos os campos</div>";
            return;
        }

        if (!email.includes("@") || !email.includes(".")) {
            elMensagem.innerHTML = "<div class='text-danger fw-bold'>Email Inválido!</div>";
            return;
        }

        if (senha.length < 4) {
            elMensagem.innerHTML = "<div class='text-danger fw-bold'>Senha muito curta!</div>";
            return
        }

        if (email === emailDefinido && senhaValue === senhaDefinida) {
            window.location.href = "dashboard.html";
        } else {
            elMensagem.innerHTML = "<div class='text-danger fw-bold'>Dados incorretos!</div>";
        }
    };
}
