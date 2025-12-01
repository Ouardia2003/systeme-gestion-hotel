package server;

import java.sql.Connection;
import java.sql.Date;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.Statement;
import java.sql.SQLException;
import java.sql.PreparedStatement;

public class DatabaseServer {

    // Paramètres de connexion
	    private static final String HOST = "postgresql-achabouardia.alwaysdata.net";
	    private static final String PORT = "5432";
	    private static final String DATABASE = "achabouardia_hotel_db";
	    private static final String USER = "achabouardia";
	    private static final String PASSWORD = "Nouveau2021!";


    private Connection connection = null;

    public void connectToDatabase() {
        try {
                  // ✅ AJOUTE CETTE LIGNE
            Class.forName("org.postgresql.Driver");
            String url = "jdbc:postgresql://" + HOST + ":" + PORT + "/" + DATABASE;
            connection = DriverManager.getConnection(url, USER, PASSWORD);
            System.out.println("Connexion réussie !");
        } catch (ClassNotFoundException e) { //✅l'exeception ajoutée
            System.err.println("Erreur : Driver PostgreSQL non trouvé : " + e.getMessage());
        } catch (SQLException e) {
            System.err.println("Erreur de connexion : " + e.getMessage());}
    }
       
 // NOUVELLE MÉTHODE : Compter les clients distincts avec réservation
 // Modifie cette méthode pour qu'elle renvoie un INT (le nombre)
    public int countClientsAvecReservation() {
        if (connection == null) return -1; // -1 indique une erreur
        
        String sql = "SELECT COUNT(DISTINCT id_client) AS nombre FROM Reservation";

        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            if (rs.next()) {
                // On retourne le nombre trouvé
                return rs.getInt("nombre");
            }

        } catch (SQLException e) {
            System.err.println("Erreur SQL count : " + e.getMessage());
        }
        return 0; // Si rien trouvé ou erreur
    }

    // Mise à jour du statut d'une réservation
    public void updateStatutReservation(int idReservation, String nouveauStatut) {
        String sql = "UPDATE Reservation SET statut = ? WHERE id_reservation = ?";

        try (PreparedStatement pstmt = connection.prepareStatement(sql)) {
            pstmt.setString(1, nouveauStatut);
            pstmt.setInt(2, idReservation);

            int rows = pstmt.executeUpdate();
            if (rows > 0) {
                System.out.println("Statut de la réservation mis à jour ! Lignes modifiées : " + rows);
            } else {
                System.out.println("Aucune réservation trouvée avec l'ID : " + idReservation);
            }

        } catch (SQLException e) {
            System.err.println("Erreur UPDATE Reservation : " + e.getMessage());
            e.printStackTrace();
        }
    }
 // Méthode SÉCURISÉE (renvoie boolean)
    public boolean insertFacture(Date dateEmission, double montantTotal, double tva, 
            String statutPaiement, int idReservation, int idPaiement) {

        if (connection == null) {
            System.err.println("Erreur : Pas de connexion BDD");
            return false;
        }

        String sql = "INSERT INTO Facture (date_emission, montant_total, tva, statut_paiement, id_reservation, id_paiement) " +
                     "VALUES (?, ?, ?, ?, ?, ?)";
        
        try (PreparedStatement pstmt = connection.prepareStatement(sql)) {
        
            java.sql.Date sqlDateEmission = new java.sql.Date(dateEmission.getTime());
            
            pstmt.setDate(1, sqlDateEmission);
            pstmt.setDouble(2, montantTotal);
            pstmt.setDouble(3, tva);
            pstmt.setString(4, statutPaiement);
            pstmt.setInt(5, idReservation);
            pstmt.setInt(6, idPaiement);
            
            int rows = pstmt.executeUpdate();
            
            if (rows > 0) {
                System.out.println("Facture créée pour réservation " + idReservation);
                return true;
            }
            return false;
            
        } catch (SQLException e) {
            // C'est ici que ça plante si l'ID 604 n'existe pas
            System.err.println(" ÉCHEC INSERTION FACTURE : " + e.getMessage());
            // On affiche le message dans la console noire pour t'aider
            return false;
        }
    }
    //delete 
 // Suppression d'une réservation par son ID
 // Suppression d'une chambre ET de toutes ses réservations
    public void deleteChambreAvecReservations(int idChambre) {

        if (connection == null) {
            System.out.println("Pas connecté à la base.");
            return;
        }

        try {
            // 1. Supprimer les consommations liées aux réservations de cette chambre
            String sqlConsomme = "DELETE FROM consomme WHERE id_reservation IN (SELECT id_reservation FROM reservation WHERE id_chambre = ?)";
            PreparedStatement psConsomme = connection.prepareStatement(sqlConsomme);
            psConsomme.setInt(1, idChambre);
            psConsomme.executeUpdate();
            psConsomme.close();

            // 2. Supprimer les factures liées aux réservations de cette chambre
            String sqlFactures = "DELETE FROM facture WHERE id_reservation IN (SELECT id_reservation FROM reservation WHERE id_chambre = ?)";
            PreparedStatement psF = connection.prepareStatement(sqlFactures);
            psF.setInt(1, idChambre);
            psF.executeUpdate();
            psF.close();

            // 3. Supprimer les réservations
            String sqlRes = "DELETE FROM reservation WHERE id_chambre = ?";
            PreparedStatement psR = connection.prepareStatement(sqlRes);
            psR.setInt(1, idChambre);
            psR.executeUpdate();
            psR.close();

            // 4. Supprimer la chambre
            String sqlCh = "DELETE FROM chambre WHERE id_chambre = ?";
            PreparedStatement psC = connection.prepareStatement(sqlCh);
            psC.setInt(1, idChambre);
            int rows = psC.executeUpdate();
            psC.close();

            if (rows > 0) {
                System.out.println("Chambre supprimée avec succès !");
            } else {
                System.out.println("Aucune chambre trouvée.");
            }

        } catch (SQLException e) {
            System.out.println("Erreur : " + e.getMessage());
        }
    }

    public void closeConnection() {
        try {
            if (connection != null)
                connection.close();
            System.out.println("Connexion fermée.");
        } catch (SQLException e) {
            System.err.println("Erreur fermeture : " + e.getMessage());
        }
    }

    public boolean isConnected() {
        try {
            return connection != null && !connection.isClosed();
        } catch (SQLException e) {
            return false;
        }
    }

    public static void main(String[] args) {

        DatabaseServer server = new DatabaseServer();
        server.connectToDatabase();

        if (server.isConnected()) {
                  // APPEL DE LA NOUVELLE MÉTHODE
            server.countClientsAvecReservation();
            // TEST UPDATE - Changer le statut d'une réservation
            server.updateStatutReservation(4, "en_attente");  // OK (pas d'accent)
            server.insertFacture(Date.valueOf("2025-10-22"), 350.50, 70.00, "payee", 1, 601);
            server.deleteChambreAvecReservations(202);
            server.closeConnection();
        }
    }
}