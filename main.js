
function addBorder(element) {
    let listItems = document.querySelectorAll("li");
    listItems.forEach(item => item.classList.remove("border-bottom"));

    element.classList.add("border-bottom");
}