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

