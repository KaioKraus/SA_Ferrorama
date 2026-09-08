
// quando o banco de dados for criado (api) é só
// trocar a fonte das linhas (ex: renderizar via fetch) que a
// parte de pesquisa continua funcionando igual.
 
const input_busca_sensor = document.getElementById("input_busca_sensor");
const btn_busca_sensor = document.getElementById("btn_busca_sensor");
const tbody_sensores = document.getElementById("tbody_sensores");
 
if (input_busca_sensor && tbody_sensores) {
    const linhas_sensores = Array.from(tbody_sensores.querySelectorAll("tr"));
 
    function filtrar_sensores() {
        const termo = input_busca_sensor.value.trim().toLowerCase();
        let algum_visivel = false;
 
        linhas_sensores.forEach(linha => {
            const id_sensor = linha.children[0].textContent.trim().toLowerCase();
            const nome_sensor = linha.children[1].textContent.trim().toLowerCase();
 
            const corresponde = termo === "" || id_sensor.includes(termo) || nome_sensor.includes(termo);
 
            linha.style.display = corresponde ? "" : "none";
            if (corresponde) algum_visivel = true;
        });
 
        let linha_vazia = tbody_sensores.querySelector("#linha_sensor_vazia");
 
        if (!algum_visivel) {
            if (!linha_vazia) {
                linha_vazia = document.createElement("tr");
                linha_vazia.id = "linha_sensor_vazia";
                linha_vazia.innerHTML = "<td colspan='5' class='text-center text-muted'>Nenhum sensor encontrado.</td>";
                tbody_sensores.appendChild(linha_vazia);
            }
        } else if (linha_vazia) {
            linha_vazia.remove();
        }
    }
 
    input_busca_sensor.addEventListener("input", filtrar_sensores);
 
    if (btn_busca_sensor) {
        btn_busca_sensor.addEventListener("click", filtrar_sensores);
    }
}