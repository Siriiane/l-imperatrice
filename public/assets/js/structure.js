function addPanier(event, id) {
  event.preventDefault();

  fetch("/cart/add", {
    method: "post", // La méthode utilisée pour la requête
    headers: {
      "Content-Type": "application/json", // le type de données à envoyer
    },
    body: JSON.stringify({ id: id }), // le corps de la requête (les données envoyées)
  })
    .then((result) => {
      return result.json(); // Réponse du serveur qui est retournée en json.
    })
    .then((result) => {
     const message = document.getElementById(`message-${id}`);

     // Affiche le message
     message.style.display = "block";

     // Cache le message après 3 secondes
     setTimeout(() => {
       message.style.display = "none";
     }, 3000);
      // Mettre ici ce qu'on doit faire avec ce que le serveur retourne
    })
    .catch((error) => console.log(error));
}