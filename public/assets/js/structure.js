function addPanier(event, id) {
  event.preventDefault(); // Empêche le comportement par défaut du formulaire ou du lien

  fetch("/cart/add", {
    method: "POST", // Méthode POST pour ajouter un produit
    headers: {
      "Content-Type": "application/json", // Spécifie que les données envoyées sont en JSON
    },
    body: JSON.stringify({ id: id }), // Le corps de la requête contenant l'ID du produit
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur lors de l'ajout au panier."); // Gère une réponse non OK
      }
      return response.json(); // Retourne la réponse JSON
    })
    .then((result) => {
      // Vérifie que le backend renvoie un objet result valide
      if (!result || typeof result !== "object") {
        throw new Error("La réponse du serveur est invalide.");
      }

      // Affiche un message de succès
      const message = document.getElementById(`message-${id}`);
      if (message) {
        message.style.display = "block";
        setTimeout(() => {
          message.style.display = "none"; // Cache le message après 1 seconde
        }, 1000);
      }

      // Met à jour la quantité pour le produit spécifique
      const quantityElement = document.querySelector(`#quantity-${id}`);
      if (quantityElement && result.cart) {
        // Cherche la quantité mise à jour pour le produit
        const updatedItem = result.cart.find((item) => item.produit.id === id);
        if (updatedItem) {
          quantityElement.textContent = updatedItem.quantite; // Met à jour la quantité affichée
        }
      }

      // Met à jour le total général
      const totalPriceElement = document.querySelector("#total-price");
      if (totalPriceElement && typeof result.totalPrice === "number") {
        totalPriceElement.textContent = result.totalPrice.toFixed(2) + " €";
      }
    })
    .catch((error) => {
      console.error("Erreur lors de l'ajout au panier :", error);
      alert("Une erreur est survenue. Veuillez réessayer.");
    });
}

function deleteArticle(event, id) {
  let button = event.target;
  fetch("/cart/remove", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id }),
  })
    .then((result) => {
      return result.json();
    })
    .then((result) => {
      console.log();
      button.closest("tr").remove();

      let totalPrice = document.querySelector("#total-price");
      let produits = document.querySelector("#produits-list");

      if (result.totalItems == 0) {
        produits.innerHTML =
          "<h2>Panier</h2><div id='cart'><p>Votre panier est vide.</p></div>";
      }

      totalPrice.textContent = result.totalPrice;
    })
    .catch((error) => {
      console.log(error);
    });
}



document.addEventListener('DOMContentLoaded', function () {

  // ICI C EST POUR CHANGER LA QUANTITE DES PRODUITS 
  // On récupère tous les SELECT
  const QUANTITES = document.querySelectorAll(".quantity-select");

  // Pour chacun des selects
  QUANTITES.forEach((quantite) => {
    // On ajoute un ecouteur d'evenement qui se déclanchera à chaque changement (quand tu vas choisir une autre quantité)
    quantite.addEventListener('change', function () {
      // On récupère l'index des options choisies dans le select
      const CHOICE = quantite.selectedIndex
      const INDEXQUANTITE = quantite[CHOICE].value;
      console.log(INDEXQUANTITE);
      // On récupère l'id du produit concerné
      const ID = quantite.getAttribute('data-product-id');
      // On récupère l'URL où la requête sera envoyée

      const URL = quantite.getAttribute('data-url');
      // Méthode AJAX
      fetch(URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ quantite: INDEXQUANTITE, id: ID })
      }).then((result) => result.json()).then((result) => {
        const UNITPRICE = parseFloat(document.querySelector('#unit-price-' + result.itemId).textContent);
        const TOTALITEMPRICE = INDEXQUANTITE * UNITPRICE;

        const TOTALPRICE = parseFloat(result.totalPrice);

        // Vérifier si le total est un entier (sans décimale)
        const formattedTotalItemPrice = TOTALITEMPRICE % 1 === 0
          ? TOTALITEMPRICE.toFixed(2).replace('.00', ',00')  // Si entier, ajouter ",00"
          : TOTALITEMPRICE.toFixed(2).replace('.', ',');    // Sinon, remplacer le point par une virgule

        const totalPRICE = TOTALPRICE % 1 === 0 ? TOTALPRICE.toFixed(2).replace('.00', ',00') : TOTALPRICE.toFixed(2).replace('.00', ',00');

        document.querySelector('#total-price').textContent = totalPRICE;
        document.querySelector('#total-item-price-' + result.itemId).textContent = formattedTotalItemPrice + '€';
      }).catch((error) => console.log(error));
    })
  })

  // ICI C EST POUR SUPPRIMER TOUT LE PANIER

  const SUPP = document.querySelector('#total-empty-cart');

  SUPP.addEventListener('click', function () {
    const URL = this.getAttribute('data-url');
    fetch(URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ supp: true })
    }).then((result) => result.json()).then((result) => {
      if (result.success) {
        document.querySelector('#produits-list').innerHTML = "<h2>Panier</h2><div id='cart'><p>Votre panier est vide.</p></div>"
      }
    }).catch((error) => console.log(error));
  })
})
