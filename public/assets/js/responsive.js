document.addEventListener("DOMContentLoaded", function () {
  const originalUl = document.querySelector("#largeXMobile");
  document.querySelector("#burger_menu").addEventListener("click", function () {
    let div = document.createElement("div");
    if (this.classList.contains("opened")) {
      this.classList.remove("opened");
      document.querySelectorAll(".list_menu").forEach((list) => {
        list.remove();
      });
    } else {
      // On ajoute la classe list_menu à la div
      div.classList.add("list_menu");
      const ulClone = originalUl.cloneNode(true);
      div.appendChild(ulClone);
      // On récupère son futur adoptant :
      this.parentNode.insertBefore(div, this.nextSibling);

      this.classList.add("opened");
    }
  });
});
