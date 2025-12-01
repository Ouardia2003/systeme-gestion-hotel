import socket
import json
import sys
import re

class HotelClient:
    ADRESSE_PAR_DEFAUT = "localhost"
    PORT_PAR_DEFAUT = 8080
    
    def __init__(self, host=ADRESSE_PAR_DEFAUT, port=PORT_PAR_DEFAUT):
        self.host = host
        self.port = port
        self.sock = None
    
    def connect(self):
        try:
            self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.sock.settimeout(15.0) # Robustesse timeout
            self.sock.connect((self.host, self.port))
            print(f"Connecté au serveur {self.host}:{self.port}")
        except Exception as e:
            print(f"Erreur de connexion: {e}")
            sys.exit(1)
    
    def validate_data(self, data: dict) -> tuple:
        # 1. D'abord on vérifie si le champ "type" existe
        if "type" not in data:
            return False, "Le champ 'type' est obligatoire"
        
        # 2. Ensuite on récupère le type
        request_type = data["type"]
        
        # 3. Maintenant on peut tester le type
        if request_type == "scanIncident":
            if "id_chambre" not in data:
                return False, "Il manque l'ID de la chambre"
            if not isinstance(data["id_chambre"], int):
                return False, "L'ID chambre doit être un nombre entier"

        elif request_type == "updateReservation":
            if "id" not in data or "statut" not in data:
                return False, "Champs 'id' et 'statut' obligatoires"
            if data["statut"] not in ["confirmee", "confirmée", "en_attente", "annulee", "annulée"]:
                return False, "Statut invalide (choix: confirmée, en_attente, annulée)"
        
        elif request_type == "insertFacture":
            champs = ["date_emission", "montant_total", "tva", "statut_paiement", "id_reservation", "id_paiement"]
            for c in champs:
                if c not in data: return False, f"Champ '{c}' manquant"
            if not re.match(r'^\d{4}-\d{2}-\d{2}$', data["date_emission"]):
                return False, "Date invalide (YYYY-MM-DD)"
                
        elif request_type == "deleteChambre":
            if "id_chambre" not in data: return False, "id_chambre manquant"
            
        elif request_type in ["countClients", "BYE"]:
            pass
        else:
            return False, f"Type inconnu: {request_type}"
            
        return True, ""
    
    def send_json(self, data: dict):
        is_valid, error_message = self.validate_data(data)
        if not is_valid:
            print(f"Validation échouée: {error_message}")
            return {"status": "error", "message": error_message}
        
        try:
            message = json.dumps(data) + "\n"
            self.sock.sendall(message.encode())
            response = self.sock.recv(4096).decode().strip()
            print(f"Réponse serveur: {response}\n")
            return json.loads(response)
        except socket.timeout:
            print("❌ Timeout serveur.")
            return {"status": "error", "message": "Timeout"}
        except Exception as e:
            print(f"❌ Erreur : {e}")
            return {"status": "error", "message": str(e)}
    
    def close(self):
        if self.sock:
            self.sock.close()
            print("Connexion fermée")

# --- MAIN ---
# --- MENU COMPLET ---
if __name__ == "__main__":
    # Récupération des arguments de la ligne de commande
    if len(sys.argv) >= 2:
        host = sys.argv[1]
    else:
        host = HotelClient.ADRESSE_PAR_DEFAUT
    
    if len(sys.argv) >= 3:
        port = int(sys.argv[2])
    else:
        port = HotelClient.PORT_PAR_DEFAUT
    
    client = HotelClient(host, port)
    client.connect()
    
    while True:
        print("\n" + "="*10 + " GESTION HÔTEL " + "="*10)
        print("1. 🗑️  Supprimer une chambre (Scan ou ID)")
        print("2. 🔄 Mettre à jour une Réservation (Scan ou ID)")
        print("3. 💶 Créer une Facture")
        print("4. 📊 Compter les clients (Info)")
        print("5. 🚪 Quitter")
        
        mode = input("\n👉 Choisis une option (1-5) : ")

        try:
            # OPTION 1 : DELETE
            if mode == "1":
                code = input(">> Entrez l'ID Chambre à supprimer : ")
                client.send_json({"type": "deleteChambre", "id_chambre": int(code)})

            # OPTION 2 : UPDATE (C'est ça ton Update !)
            elif mode == "2":
                code = input(">> ID de la Réservation : ")
                # Rappel : accepte 'confirmée', 'annulée' (avec accents grâce à ta modif précédente)
                statut = input(">> Nouveau statut (confirmée/annulée/en_attente) : ")
                
                req = {
                    "type": "updateReservation",
                    "id": int(code),
                    "statut": statut
                }
                client.send_json(req)

            # OPTION 3 : FACTURE (Le nouveau truc !)
            elif mode == "3":
                print("\n--- CRÉATION FACTURE ---")
                # On demande les infos une par une
                id_res = input("   ID Réservation : ")
                montant = input("   Montant Total : ")
                tva = input("   Dont TVA : ")
                date_em = input("   Date (YYYY-MM-DD) : ")
                statut = input("   Statut (payee/en_attente) : ")
                id_pai = input("   ID Paiement (ex: 601) : ")

                req = {
                    "type": "insertFacture",
                    "date_emission": date_em,
                    "montant_total": float(montant),
                    "tva": float(tva),
                    "statut_paiement": statut,
                    "id_reservation": int(id_res),
                    "id_paiement": int(id_pai)
                }
                client.send_json(req)

            # OPTION 4 : COUNT
            elif mode == "4":
                reponse = client.send_json({"type": "countClients"})
                if reponse.get("status") == "ok":
                    print(f"📊 RÉSULTAT : {reponse.get('resultat')} clients trouvés.")

            # OPTION 5 : QUITTER
            elif mode == "5":
                client.send_json({"type": "BYE"})
                break
            
        except ValueError:
            print("❌ Erreur de saisie : Il faut entrer un nombre quand c'est demandé !")
        except Exception as e:
            print(f"❌ Erreur : {e}")

    client.close()