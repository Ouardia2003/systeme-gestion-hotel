package server;

import java.io.*;
import java.net.*;
import org.json.JSONObject;

public class ServerTCP {
    public static final int PORT_PAR_DEFAULT = 8080;
    private static final int MAX_MESSAGE_SIZE = 1024 * 1024; // 1 Mo
    private static final int TIMEOUT_CLIENT = 600000; //  secondes

    public static void main(String[] args) {

        int port = PORT_PAR_DEFAULT;
        if (args.length > 0) {
            try {
                port = Integer.parseInt(args[0]);
            } catch (NumberFormatException e) {
                port = PORT_PAR_DEFAULT;
            }
        }

        // Le bloc TRY commence ici
        try (ServerSocket serveur = new ServerSocket(port)) {

            System.out.println("Serveur en attente sur le port " + port + "...");
            
            
            DatabaseServer db = new DatabaseServer();
            db.connectToDatabase(); // On se connecte une seule fois au début

            // On attend un client
            Socket client = serveur.accept();
            System.out.println("🔌 Client connecté : " + client.getInetAddress());

            client.setSoTimeout(TIMEOUT_CLIENT);

            // Création du reader sécurisé
            BufferedReader in = new BufferedReader(new InputStreamReader(client.getInputStream())) {
                @Override
                public String readLine() throws IOException {
                    String line = super.readLine();
                    if (line != null && line.length() > MAX_MESSAGE_SIZE) {
                        throw new IOException("Message trop long");
                    }
                    return line;
                }
            };

            PrintWriter out = new PrintWriter(client.getOutputStream(), true);
            String message;

            try {
                // Boucle de lecture infinie (tant que le client envoie des messages)
                while ((message = in.readLine()) != null) {
                    
                    JSONObject req = new JSONObject(message);
                    JSONObject rep = new JSONObject();
                    
                    // On récupère le type de commande envoyé par le Python
                    String type = req.optString("type");

                    System.out.println("Commande reçue : " + type); // Log serveur
                    System.out.println("📋 Traitement de la requête : " + type);

                    switch (type) {
                        // --- CAS 1 : SUPPRESSION (Via Scan mode 1) ---
                        case "deleteChambre":
                             if (!req.has("id_chambre")) {
                                rep.put("status", "error"); 
                                rep.put("message", "ID chambre manquant");
                                break;
                             }
                             int idToDelete = req.getInt("id_chambre");
                             
                             // Appel de ta méthode de suppression
                             db.deleteChambreAvecReservations(idToDelete);
                             
                             rep.put("status", "ok");
                             rep.put("message", "Commande de suppression exécutée pour la chambre " + idToDelete);
                             break;

                        // --- CAS 2 : MISE À JOUR (Via Scan mode 2) ---
                        case "updateReservation":
                            if (!req.has("id") || !req.has("statut")) {
                                rep.put("status", "error");
                                rep.put("message", "Paramètres manquants (id ou statut)");
                                break;
                            }
                            int idRes = req.getInt("id");
                            String statut = req.getString("statut");
                            
                         // NOUVEAU CODE (Accepte les accents comme dans ta base de données)
                            if (idRes <= 0 || !statut.matches("^(confirmee|confirmée|en_attente|annulee|annulée)$")) {
                                rep.put("status", "error");
                                // J'affiche le statut reçu dans le message d'erreur pour t'aider à débugger si besoin
                                rep.put("message", "Statut refusé par le serveur : " + statut);
                                break;
                            }
                            
                            // Appel de ta méthode d'update
                            db.updateStatutReservation(idRes, statut);
                            
                            rep.put("status", "ok");
                            rep.put("message", "Statut mis à jour pour la réservation " + idRes);
                            break;

                        // --- CAS 3 : COMPTER LES CLIENTS ---

                        case "countClients":
                            int nombreClients = db.countClientsAvecReservation();
                            
                            // 2. On prépare la réponse JSON
                            rep.put("status", "ok");
                            rep.put("resultat", nombreClients); // On met le chiffre dans le JSON
                            rep.put("message", "Succès : " + nombreClients + " clients trouvés.");
                            break;
                        // --- CAS 4 : FACTURE (Optionnel si tu veux l'utiliser plus tard) ---
                         // --- CAS 4 : CRÉER UNE FACTURE ---
                         // --- CAS 4 : CRÉER UNE FACTURE (Sécurisé) ---
                        case "insertFacture":
                             // 1. Vérif données
                             if(!req.has("id_reservation") || !req.has("montant_total")) {
                                 rep.put("status", "error");
                                 rep.put("message", "Données manquantes");
                                 break;
                             }

                             try {
                                 // 2. Conversion
                            	 String dateStr = req.getString("date_emission").trim();
                                 java.sql.Date dateEmission = java.sql.Date.valueOf(dateStr);
                                 
                                 double montant = req.getDouble("montant_total");
                                 double tva = req.getDouble("tva");
                                 String statutPaiement = req.getString("statut_paiement");
                                 int idResa = req.getInt("id_reservation");
                                 int idPaiement = req.getInt("id_paiement");

                                 // 3. Appel de la méthode BDD (qui ne crashe plus)
                                 boolean succes = db.insertFacture(dateEmission, montant, tva, statutPaiement, idResa, idPaiement);
                                 
                                 if (succes) {
                                     rep.put("status", "ok");
                                     rep.put("message", "Facture de " + montant + "€ créée avec succès !");
                                 } else {
                                     rep.put("status", "error");
                                     // Ce message s'affichera dans ton Python
                                     rep.put("message", "Erreur SQL (Vérifie que l'ID Paiement " + idPaiement + " existe !)");
                                 }

                             } catch (IllegalArgumentException e) {
                                 rep.put("status", "error");
                                 rep.put("message", "Format de date invalide (Attendu: YYYY-MM-DD)");
                             } catch (Exception e) {
                                 // Filet de sécurité ultime pour ne jamais crash
                                 rep.put("status", "error");
                                 rep.put("message", "Erreur technique : " + e.getMessage());
                             }
                             break;
                        // --- CAS 5 : QUITTER ---
                        case "BYE":
                            rep.put("status", "bye");
                            out.println(rep.toString());
                            System.out.println("Déconnexion demandée par le client.");
                            client.close();
                            db.closeConnection();
                            return;

                        default:
                            rep.put("status", "error");
                            rep.put("message", "Type de commande inconnu : " + type);
                            break;
                    }
                    
                    // AJOUT DES LOGS ICI
                    System.out.println("✅ Réponse préparée : " + rep.toString());
                    
                    // Envoi de la réponse au client Python
                    out.println(rep.toString());
                    
                    System.out.println("📤 Réponse envoyée au client !");
         
                }
            
            } catch (SocketTimeoutException e) {
                System.err.println("ALERTE ROBUSTESSE : Le client a mis trop de temps à répondre (Timeout).");
                System.err.println("   -> Déconnexion forcée.");
            }

            client.close();
            db.closeConnection();

        } catch (BindException e) {
            System.err.println("ERREUR : Le port " + port + " est déjà utilisé !");
        } catch (Exception e) {
            System.err.println("Erreur générale : " + e.getMessage());
            e.printStackTrace();
        }
    }
}