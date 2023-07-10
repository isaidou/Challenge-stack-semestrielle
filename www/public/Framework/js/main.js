// Écouteur d'événement pour les clics sur la page
document.addEventListener("click", function ($event) {
  const parent = $event.target.parentNode;

  // Vérifie si l'élément cliqué a la classe "sidebar-toggler"
  if ($event.target.classList.contains("sidebar-toggler")) {
    expandSidebar($event.target); // Appelle la fonction pour étendre la barre latérale
  } else {
    // Vérifie si l'élément parent a la classe "sidebar-toggler"
    if (parent.classList.contains("sidebar-toggler")) {
      expandSidebar(parent); // Appelle la fonction pour étendre la barre latérale
    }
  }

  // Gestion du clic sur le bouton de navigation
  if ($event.target.classList.contains("nav-toggler")) {
    expandNavbar($event.target); // Appelle la fonction pour étendre le menu de navigation
  } else {
    // Vérifie si l'élément parent a la classe "nav-toggler"
    if (parent.classList.contains("nav-toggler")) {
      expandNavbar(parent); // Appelle la fonction pour étendre le menu de navigation
    }
  }

  // Gestion du clic sur un élément avec la classe "modal-trigger"
  if ($event.target.classList.contains("modal-trigger")) {
    showModal($event.target); // Appelle la fonction pour afficher le modal
  } else {
    // Vérifie si l'élément parent a la classe "modal-trigger"
    if (parent.classList.contains("modal-trigger")) {
      showModal(parent); // Appelle la fonction pour afficher le modal
    }
  }

  // Gestion du clic sur un élément avec la classe "expandible-trigger"
  if ($event.target.classList.contains("expandible-trigger")) {
    expandContent($event.target); // Appelle la fonction pour étendre le contenu
  } else {
    // Vérifie si l'élément parent a la classe "expandible-trigger"
    if (parent.classList.contains("expandible-trigge")) {
      expandContent(parent); // Appelle la fonction pour étendre le contenu
    }
  }
});

// Fonction pour étendre le menu de navigation
function expandNavbar(element) {
  let menuId = element.dataset.target;
  let menu = document.getElementById(menuId);
  let content = menu.getElementsByClassName("nav")[0];

  // Vérifie si le menu a déjà une hauteur définie
  if (menu.style.height) {
    menu.style.height = null; // Réinitialise la hauteur pour cacher le menu
  } else {
    menu.style.height = content.scrollHeight + "px"; // Définit la hauteur du menu en fonction de la hauteur du contenu
  }
}

// Fonction pour étendre le contenu
function expandContent(element) {
  const target = element.getAttribute("data-target");
  const expandible = document.getElementById(target);
  const content = expandible.firstElementChild;

  expandible.style.display = "block"; // Affiche l'élément conteneur
  if (expandible.style.height) {
    expandible.style.height = null; // Réinitialise la hauteur pour réduire le contenu
    setTimeout(() => {
      expandible.style.display = ""; // Masque l'élément après la transition
    }, 300);
  } else {
    expandible.style.height = content.scrollHeight + "px"; // Définit la hauteur du contenu pour l'étendre
  }
}

// Fonction pour étendre la barre latérale
function expandSidebar(element) {
  const target = element.dataset ? element.dataset.target : false;
  if (target) {
    const sidebar = document.getElementById(target);

    // Vérifie si la barre latérale est déjà étendue
    if (sidebar.classList.contains("expanded")) {
      sidebar.classList.remove("expanded"); // Réduit la barre latérale
    } else {
      sidebar.classList.add("expanded"); // Étend la barre latérale
    }
  }
}

// Fonction pour afficher ou masquer le modal
function showModal(trigger) {
  const target = document.getElementById(trigger.dataset.target);

  if (target.style.display == "none" || target.style.display === "") {
    target.style.display = "block"; // Affiche le modal
    setTimeout(() => {
      target.classList.add("show"); // Ajoute la classe pour afficher le modal avec une transition
    }, 100);
  } else {
    if (target.classList.contains("modal-animated")) {
      target.classList.remove("show"); // Masque le modal avec une transition
      setTimeout(() => {
        target.style.display = "none"; // Masque le modal après la transition
      }, 300);
    } else {
      target.classList.remove("show"); // Masque le modal sans transition
      target.style.display = "none"; // Masque le modal
    }
  }
}
