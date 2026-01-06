function getBaseUrl() {
  let path = window.location.pathname;
  let baseDir = path.substring(0, path.lastIndexOf('/') + 1);
  return baseDir + "php/";}
function inscrireUtilisateur(nom, email, password, type) {
      $.ajax({  url: getBaseUrl() + "inscription.php",
                      type: "POST",
                      data: { nom, email, password, type },
                      success: function (response) {
                                    if (response.success) {
               alert("Inscription réussie! Vous pouvez maintenant vous connecter.");
                  $(".wrapper").removeClass("show active");
                                  } else {
               alert("Erreur: " + response.message);         
                                }
        },
                      error: function () {
                                    alert("Erreur de connexion au serveur");       }
    });
} 
function connecterUtilisateur(email, password) {
      $.ajax({ 
                     url: getBaseUrl() + "connexion.php",
                type: "POST",
                data: { email, password },
                success: function (response) {
                              if (response.success) {
                                                localStorage.setItem("user", JSON.stringify(response.user));
                                                alert("Bienvenue " + response.user.nom + "!");
                                                $(".wrapper").removeClass("show");
                                                location.reload();
                                            } else {alert("Erreur: " + response.message); }
        },
                error: function () {
                              alert("Erreur de connexion au serveur");
                    }
    });
}
function chargerAnnonces(recherche, wilaya) {
                                      $.ajax({  
                                        url: getBaseUrl() + "get_annonces.php",
                                                type: "GET",
                                                data: { recherche: recherche  "", wilaya: wilaya  "" },
                                                success: function (response) {
                                                              if (response.success) {
                                                                                afficherAnnonces(response.annonces);
                                                                            } else {
                                                                                $(".annonces-grid").html("<p style='text-align:center; padding:50px;'>Erreur de chargement</p>"); }
        },
                                                error: function () {
                                                              $(".annonces-grid").html("<p style='text-align:center; padding:50px;'>Erreur de connexion</p>");
                                                  }
    });
}
function afficherAnnonces(annonces) {
      let html = "";
      let user = JSON.parse(localStorage.getItem("user"));
      if (annonces.length === 0) {
                html = "<p style='text-align:center; padding:50px;'>Aucune annonce trouvée</p>";
            } else {
                annonces.forEach(function (annonce) {
                              let boutonReserver = "";
                              if (!user || user.type === "client") {
                                                boutonReserver = <button class="reserve-btn" onclick="reserverAnnonce(${annonce.id})">Réserver</button>;
                                            }
                              html += `
                                              <div class="card">
                                                                  <div class="card-img">
                                                                              <img src="${annonce.image_url}" alt="${annonce.titre}">
                                                                              <span class="status-badge">Disponible</span>
                                                                              </div>
                                                                              <div class="card-body">
                                                                               <span class="category">${annonce.categorie}</span>
                                                                               <h3>${annonce.titre}</h3>
                   <p class="res-name"><ion-icon name="business-outline"></ion-icon> ${annonce.restaurant_nom}</p>
                   <p class="res-loc"><ion-icon name="location-outline"></ion-icon> ${annonce.wilaya}, ${annonce.adresse}</p>
                   <div class="card-footer">
                           <span class="date">${formaterDate(annonce.date_creation)}</span>
                            ${boutonReserver}
                        </div>
                    </div>
                </div>
            `;
        });
    }
    $(".annonces-grid").html(html);}
function reserverAnnonce(annonceId) {
      let user = JSON.parse(localStorage.getItem("user"));
      if (!user) {
                alert("Vous devez être connecté pour réserver.");
                window.location.href = "index.html";
                return;}
      if (user.type !== "client") {
                alert("Seuls les clients peuvent réserver des plats.");
                return;}
      if (!confirm("Voulez-vous réserver ce plat?")) return;
  $.ajax({url: getBaseUrl() + "reserver.php",type: "POST",data: { user_id: user.id, annonce_id: annonceId }
     success: function (response) {
    if (response.success) { alert("Réservation réussie!");
                           chargerAnnonces();
                           } else {alert("Erreur: " + response.message);   }
        },
  error: function () { alert("Erreur de connexion");}
    });
}
function chargerMesReservations() {let user = JSON.parse(localStorage.getItem("user"));
                                    if (!user) return;
            $.ajax({ url: getBaseUrl() + "mes_reservations.php", type: "GET", data: { user_id: user.id },
                      success: function (response) {
                     if (response.success) {afficherReservations(response.reservations);}
                                                    },

