window.onload = checkLocalStorage;

const nom = document.getElementById("sortie_nom");
const dateHeureDebut = document.getElementById("sortie_dateHeureDebut");
const dateLimiteInscription = document.getElementById("sortie_dateLimiteInscription");
const nbInscriptionsMax = document.getElementById("sortie_nbInscriptionsMax");
const duree = document.getElementById("sortie_duree");
const infosSortie = document.getElementById("sortie_infosSortie");
const lieu = document.getElementById("sortie_lieu");

function checkLocalStorage() {
    if (
        localStorage.getItem("nom") !== null ||
        localStorage.getItem("dateHeureDebut") !== null ||
        localStorage.getItem("dateLimiteInscription") !== null ||
        localStorage.getItem("nbInscriptionsMax") !== null ||
        localStorage.getItem("duree") !== null ||
        localStorage.getItem("infosSortie") !== null ||
        localStorage.getItem("lieu") !== null
    ) {
        restore();
    }
}

function restore() {

    nom.value = localStorage.getItem("nom");

    dateHeureDebut.value = localStorage.getItem("dateHeureDebut");

    dateLimiteInscription.value = localStorage.getItem("dateLimiteInscription");

    nbInscriptionsMax.value = localStorage.getItem("nbInscriptionsMax");

    duree.value = localStorage.getItem("duree");

    infosSortie.value = localStorage.getItem("infosSortie");

    lieu.value = localStorage.getItem("lieu");

    emptyLocalStorage()

}

function store() {

    localStorage.setItem("nom", nom.value);

    localStorage.setItem("dateHeureDebut", dateHeureDebut.value);

    localStorage.setItem("dateLimiteInscription", dateLimiteInscription.value);

    localStorage.setItem("nbInscriptionsMax", nbInscriptionsMax.value);

    localStorage.setItem("duree", duree.value);

    localStorage.setItem("infosSortie", infosSortie.value);

    localStorage.setItem("lieu", lieu.value);

}

function emptyLocalStorage() {

    localStorage.removeItem("nom");

    localStorage.removeItem("dateHeureDebut");

    localStorage.removeItem("dateLimiteInscription");

    localStorage.removeItem("nbInscriptionsMax");

    localStorage.removeItem("duree");

    localStorage.removeItem("infosSortie");

    localStorage.removeItem("lieu");

}