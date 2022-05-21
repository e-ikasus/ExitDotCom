function toggleHtmlElementVisibility(elementId) {
    var element = document.getElementById(elementId);
    if (element.classList.value === '') {
        element.classList.add("invisible");
    } else {
        element.classList.remove("invisible");
    }
}