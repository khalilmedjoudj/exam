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
                                                    
