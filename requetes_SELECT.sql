--1-Nombre de clients distincts ayant au moins une réservation
SELECT COUNT(DISTINCT id_client) AS nombre_clients_avec_reservation
FROM Reservation;

--2-Nombre de réservations faites pour une chambre donnée (ex : 201)
SELECT COUNT(id_reservation) AS nombre_reservations_chambre_201
FROM Reservation
WHERE id_chambre = 201;

--3-Montant total facturé par jour
SELECT date_emission::date AS date_facturation_jour,
       SUM(montant_total) AS total_facture_jour
FROM Facture
GROUP BY date_emission::date
ORDER BY date_emission::date;

--4-Prix moyen des chambres par étage
SELECT etage,
       ROUND(AVG(prix_base), 2) AS prix_moyen
FROM Chambre
GROUP BY etage
ORDER BY etage;

--5-Clients ayant une réservation incluant la date du 3 octobre 2025
SELECT C.nom,C.prenom,C.mail
FROM Client C
JOIN Reservation R ON C.id_client = R.id_client
WHERE '2025-10-03' BETWEEN R.date_debut AND R.date_fin;

--6-Chambres disponibles (statut = 'libre') avec leur prix
SELECT id_chambre, etage, prix_base, superficie
FROM Chambre
WHERE statut = 'libre';

--7-Factures impayées + nom du client + montant
SELECT F.id_facture,
       F.montant_total,
       C.nom,
       C.prenom
FROM Facture F
JOIN Reservation R ON F.id_reservation = R.id_reservation
JOIN Client C ON R.id_client = C.id_client
WHERE F.statut_paiement = 'impayee';

-- 8- Les interventions de maintenance de priorité moyenne à élevée (priorité 1-3) 
-- qui sont en cours ou planifiées, avec l'employé affecté
SELECT 
    m.id_maintenance,
    m.type_intervention,
    m.description,
    m.priorite,
    m.statut,
    m.date_prevue,
    e.nom AS employe_nom,
    e.prenom AS employe_prenom,
    e.poste
FROM Maintenance m
LEFT JOIN Employe e ON e.id_maintenance = m.id_maintenance
WHERE m.priorite <= 3 
  AND m.statut IN ('en_cours', 'planifiee')
ORDER BY m.priorite ASC, m.date_prevue ASC;

--9. Scénario Check-in : enregistrement d'un client pour sa réservation (ex : réservation 2)
-- DML 1 : Mettre à jour le statut de la réservation en confirmée
UPDATE Reservation
SET statut = 'confirmée'
WHERE id_reservation = 2;

-- DML 2 : Mettre à jour le statut de la chambre en occupée
UPDATE Chambre
SET statut = 'occupée'
WHERE id_chambre = (SELECT id_chambre FROM Reservation WHERE id_reservation = 2);

SELECT 'CHECKIN_SUCCESS' AS code_reponse, 'Le check-in pour la réservation 2 est effectué.' AS message;

-- 10. Liste de toutes les réservations (confirmées et en attente) pour un client (ex. : ID 101)
-- avec les détails de la chambre et le montant total facturé
SELECT
    R.id_reservation,
    R.date_debut,
    R.date_fin,
    R.nombre_personne,
    R.statut AS statut_reservation,
    Ch.id_chambre,
    TC.nom AS type_chambre,
    Ch.prix_base,
    F.montant_total,
    F.statut_paiement
FROM Reservation R
JOIN Chambre Ch ON R.id_chambre = Ch.id_chambre
JOIN Type_Chambre TC ON Ch.id_type = TC.id_type
LEFT JOIN Facture F ON R.id_reservation = F.id_reservation
WHERE R.id_client = 101
  AND R.statut IN ('confirmée', 'en_attente')
ORDER BY R.date_debut DESC;

