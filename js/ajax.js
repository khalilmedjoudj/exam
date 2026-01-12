function getBaseUrl() {
    let path = window.location.pathname;
    let baseDir = path.substring(0, path.lastIndexOf('/') + 1);
    return baseDir + "php/";
}
function inscrireUtilisateur(nom, email, password, type) {
    $.ajax({
        url: getBaseUrl() + "inscription.php",
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
            alert("Erreur de connexion au serveur");
        }
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
            } else {
                alert("Erreur: " + response.message);
            }
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
        data: { recherche: recherche || "", wilaya: wilaya || "" },
        success: function (response) {
            if (response.success) {
                afficherAnnonces(response.annonces);
            } else {
                $(".annonces-grid").html("<p style='text-align:center; padding:50px;'>Erreur de chargement</p>");
            }
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
                boutonReserver = `<button class="reserve-btn" onclick="reserverAnnonce(${annonce.id})">Réserver</button>`;
            }
            html += `
                <div class="card">
                    <div class="card-img">
                        <img src="${annonce.image_url}" alt="${annonce.titre}" onerror="this.onerror=null; this.src='pic/no-image.svg';">
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
    $(".annonces-grid").html(html);
}
function reserverAnnonce(annonceId) {
    let user = JSON.parse(localStorage.getItem("user"));
    if (!user) {
        alert("Vous devez être connecté pour réserver.");
        window.location.href = "index.html";
        return;
    }
    if (user.type !== "client") {
        alert("Seuls les clients peuvent réserver des plats.");
        return;
    }
    if (!confirm("Voulez-vous réserver ce plat?")) return;
    $.ajax({
        url: getBaseUrl() + "reserver.php",
        type: "POST",
        data: { user_id: user.id, annonce_id: annonceId },
        success: function (response) {
            if (response.success) {
                alert("Réservation réussie!");
                chargerAnnonces();
            } else {
                alert("Erreur: " + response.message);
            }
        },
        error: function () {
            alert("Erreur de connexion");
        }
    });
}
function chargerMesReservations() {
    let user = JSON.parse(localStorage.getItem("user"));
    if (!user) return;
    $.ajax({
        url: getBaseUrl() + "mes_reservations.php",
        type: "GET",
        data: { user_id: user.id },
        success: function (response) {
            if (response.success) {
                afficherReservations(response.reservations);
            }
        },
        error: function () {
            $(".reservations-grid").html("<p>Erreur de chargement</p>");
        }
    });
}
function afficherReservations(reservations) {
    let html = "";
    if (reservations.length === 0) {
        html = `<div style="text-align:center; padding:50px; grid-column:1/-1;">
            <ion-icon name="basket-outline" style="font-size:4rem; color:#ccc;"></ion-icon>
            <h3 style="margin:20px 0;">Aucune réservation</h3>
            <a href="annonces.html" class="btn" style="display:inline-block; text-decoration:none;">Voir les annonces</a>
        </div>`;
    } else {
        reservations.forEach(function (res) {
            html += `
                <div class="card">
                    <div class="card-img">
                        <img src="${res.image_url}" alt="${res.titre}" onerror="this.onerror=null; this.src='pic/no-image.svg';">
                    </div>
                    <div class="card-body">
                        <h3>${res.titre}</h3>
                        <p class="res-name"><ion-icon name="business-outline"></ion-icon> ${res.restaurant_nom}</p>
                        <p class="res-loc"><ion-icon name="location-outline"></ion-icon> ${res.wilaya}</p>
                        <div class="card-footer">
                            <span class="date">Réservé le ${formaterDateComplete(res.date_reservation)}</span>
                            <button class="cancel-btn" onclick="annulerCommande(${res.id})">Annuler la commande</button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    $(".reservations-grid").html(html);
}
function annulerCommande(reservationId) {
    let user = JSON.parse(localStorage.getItem("user"));
    if (!user) {
        alert("Vous devez être connecté.");
        return;
    }
    if (!confirm("Voulez-vous vraiment annuler cette commande?")) return;
    $.ajax({
        url: getBaseUrl() + "annuler_reservation.php",
        type: "POST",
        data: { user_id: user.id, reservation_id: reservationId },
        success: function (response) {
            if (response.success) {
                alert("Commande annulée avec succès!");
                chargerMesReservations();
            } else {
                alert("Erreur: " + response.message);
            }
        },
        error: function () {
            alert("Erreur de connexion");
        }
    });
}
function mettreAJourNavbar() {
    let user = JSON.parse(localStorage.getItem("user"));
    let menuLinks = `
        <li><a href="index.html">Accueil</a></li>
        <li><a href="annonces.html">Annonces</a></li>
    `;
    if (user) {
        if (user.type === 'client') {
            menuLinks += `<li><a href="mes-reservations.html">Mes Réservations</a></li>`;
        } else if (user.type === 'restaurant') {
            menuLinks += `
                <li><a href="mes-annonces.html">Mes Annonces</a></li>
                <li><a href="ajouter.html">Publier</a></li>
            `;
        }
    }
    $(".nav-links ul").html(menuLinks + '<li class="nav-account"></li>');
    if (user) {
        let profilHtml = `
            <div class="profil-dropdown">
                <button class="login-btn profil-btn">
                    <ion-icon name="person-circle-outline"></ion-icon> ${user.nom}
                </button>
                <div class="profil-menu">
                    <div class="profil-header">
                        <ion-icon name="person-circle" style="font-size:3rem; color:forestgreen;"></ion-icon>
                        <div class="profil-info">
                            <strong>${user.nom}</strong>
                            <span>${user.email}</span>
                            <span class="badge-${user.type}">${user.type === 'restaurant' ? 'Restaurant' : 'Client'}</span>
                        </div>
                    </div>
                    <button class="btn-deconnexion" onclick="deconnexion()">
                        <ion-icon name="log-out-outline"></ion-icon> Se déconnecter
                    </button>
                </div>
            </div>
        `;
        $(".nav-account").html(profilHtml);
        $(document).off("click", ".profil-btn").on("click", ".profil-btn", function (e) {
            e.stopPropagation();
            $(".profil-menu").toggleClass("show");
        });
        $(document).off("click.profil").on("click.profil", function () {
            $(".profil-menu").removeClass("show");
        });
    } else {
        $(".nav-account").html('<button type="button" class="login-btn">Login</button>');
        $(document).off("click", ".nav-account .login-btn").on("click", ".nav-account .login-btn", function (e) {
            e.preventDefault();
            $(".wrapper").addClass("show");
            $(".wrapper-overlay").addClass("show");
        });
    }
}
function deconnexion() {
    localStorage.removeItem("user");
    alert("Déconnexion réussie");
    window.location.href = "index.html";
}
function formaterDate(dateString) {
    let date = new Date(dateString);
    let maintenant = new Date();
    let diff = Math.floor((maintenant - date) / 1000 / 60);
    if (diff < 60) return "Il y a " + diff + " min";
    if (diff < 1440) return "Il y a " + Math.floor(diff / 60) + "h";
    return "Il y a " + Math.floor(diff / 1440) + " jour(s)";
}
function formaterDateComplete(dateString) {
    let date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
$(document).ready(function () {
    mettreAJourNavbar();
    if ($(".reservations-grid").length > 0) {
        chargerMesReservations();
    }
});