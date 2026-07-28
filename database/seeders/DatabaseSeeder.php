<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\TypeReclamation;
use App\Models\SousType;
use App\Models\Reclamation;
use App\Models\HistoriqueStatut;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin GRM',
            'email' => 'admin@grm.com',
            'phone' => '+243 81 000 0001',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // Create gestionnaires
        $gestionnaires = [];
        $gestionnaireNames = [
            ['name' => 'Malamine', 'email' => 'malamine@grm.com'],
        ];
        foreach ($gestionnaireNames as $g) {
            $gestionnaires[] = User::create([
                'name' => $g['name'],
                'email' => $g['email'],
                'role' => 'gestionnaire',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Create agents
        $agents = [];
        $agentNames = [
            ['name' => 'Mohamed', 'email' => 'mohamed@grm.com'],
        ];
        foreach ($agentNames as $a) {
            $agents[] = User::create([
                'name' => $a['name'],
                'email' => $a['email'],
                'role' => 'agent',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Create types de réclamation
        $types = [];
        $typeData = [
            ['code' => 'BILL', 'libelle' => 'Billeterie', 'delai_traitement_sla' => 48],
            ['code' => 'MESS', 'libelle' => 'Messagerie', 'delai_traitement_sla' => 72],
            ['code' => 'FAUX', 'libelle' => 'Faux chargement', 'delai_traitement_sla' => 96],
            ['code' => 'RETA', 'libelle' => 'Retard / Annulation', 'delai_traitement_sla' => 24],
            ['code' => 'AUTO', 'libelle' => 'Autre', 'delai_traitement_sla' => 72],
        ];
        foreach ($typeData as $td) {
            $types[$td['code']] = TypeReclamation::create($td);
        }

        // Create sous-types
        $sousTypes = [];
        $sousTypesData = [
            'BILL' => ['Billet perdu', 'Erreur de tarif', 'Annulation de billet', 'Remboursement'],
            'MESS' => ['Colis perdu', 'Colis endommagé', 'Livraison partielle', 'Retard de livraison'],
            'FAUX' => ['Erreur de marchandise', 'Quantité incorrecte', 'Marchandise endommagée', 'Mauvais destinataire'],
            'RETA' => ['Retard de bus', 'Retard de train', 'Annulation de trajet', 'Cheminement modifié'],
            'AUTO' => ['Service client', 'Plainte générale', 'Suggestion', 'Réclamation divers'],
        ];
        foreach ($sousTypesData as $code => $items) {
            foreach ($items as $item) {
                $sousTypes[$item] = SousType::create([
                    'type_id' => $types[$code]->id,
                    'libelle' => $item,
                ]);
            }
        }

        // Create sample clients
        $clientsData = [
            ['nom' => 'Kabongo', 'prenom' => 'Patrick', 'email' => 'patrick.kabongo@email.com', 'telephone' => '+243 82 123 4567', 'type' => 'particulier'],
            ['nom' => 'Lubala', 'prenom' => 'Françoise', 'email' => 'f.lubala@email.com', 'telephone' => '+243 81 987 6543', 'type' => 'particulier'],
            ['nom' => 'Mutombo', 'prenom' => 'David', 'email' => 'd.mutombo@entreprise.cd', 'telephone' => '+243 83 456 7890', 'type' => 'entreprise'],
            ['nom' => 'Ngoy', 'prenom' => 'Aimée', 'email' => 'a.ngoy@email.com', 'telephone' => '+243 84 321 0987', 'type' => 'particulier'],
            ['nom' => 'Tshimanga', 'prenom' => 'Samuel', 'email' => 's.tshimanga@email.com', 'telephone' => '+243 85 654 3210', 'type' => 'particulier'],
            ['nom' => 'Kasongo Transport', 'prenom' => '', 'email' => 'info@kasongo.cd', 'telephone' => '+243 81 111 2222', 'type' => 'entreprise'],
            ['nom' => 'Mballo', 'prenom' => 'Ibrahim', 'email' => 'i.mballo@email.com', 'telephone' => '+243 82 333 4444', 'type' => 'particulier'],
            ['nom' => 'Ilunga', 'prenom' => 'Béatrice', 'email' => 'b.ilunga@email.com', 'telephone' => '+243 83 555 6666', 'type' => 'particulier'],
        ];

        $clients = [];
        foreach ($clientsData as $cd) {
            $clients[] = Client::create($cd);
        }

        // Create sample reclamations
        $statuts = ['en_attente', 'en_cours', 'attente_client', 'resolu', 'rejete'];
        $priorites = ['haute', 'moyenne', 'basse'];

        $sujets = [
            'BILL' => ['Billet non remboursé', 'Prix incorrect au guichet', 'Annulation sans préavis'],
            'MESS' => ['Colis jamais reçu', 'Colis arrivé cassé', 'Livraison partielle - manque 2 colis'],
            'FAUX' => ['Marchandise échangée', 'Trop-perçu de 15 cartons', 'Fragile non signalé'],
            'RETA' => ['Bus en retard de 3 heures', 'Train annulé sans info', 'Vol raté à cause du retard'],
            'AUTO' => ['Accueil très décevant', 'Agent impoli au guichet', 'Suggestions d\'amélioration'],
        ];

        $descriptions = [
            'Billet non remboursé' => 'J\'ai acheté un billet le 15 juillet pour un voyage le 20 juillet. Le bus a été annulé mais je n\'ai toujours pas reçu mon remboursement malgré plusieurs relances.',
            'Prix incorrect au guichet' => 'On m\'a facturé 50$ au lieu du tarif affiché de 35$ pour le trajet Kinshasa-Lubumbashi.',
            'Annulation sans préavis' => 'Mon vol a été annulé 2 heures avant le départ sans aucune notification préalable.',
            'Colis jamais reçu' => 'Mon colis (code suivi: MSG-2026-0456) expédié le 10 juillet n\'est toujours pas arrivé à destination.',
            'Colis arrivé cassé' => 'Le colis que j\'ai reçu contenait un appareil électronique complètement endommagé. Le carton était ouvert et mal refermé.',
            'Livraison partielle - manque 2 colis' => 'Sur les 10 colis envoyés, je n\'en ai reçu que 8. Les 2 manquants contenaient du matériel informatique.',
            'Marchandise échangée' => 'La marchandise livrée ne correspond pas à celle commandée. J\'ai commandé du ciment mais reçu des sacs de riz.',
            'Trop-perçu de 15 cartons' => 'La facture indique 50 cartons mais j\'en ai reçu seulement 35. Le poids ne correspond pas non plus.',
            'Fragile non signalé' => 'Des articles fragiles ont été expédiés sans marquage "fragile", résultat cassés à la réception.',
            'Bus en retard de 3 heures' => 'Le bus-programmé à 8h00 est finalement arrivé à 11h15 sans aucune explication.',
            'Train annulé sans info' => 'Le train de 14h00 a été annulé. Aucune information n\'a été donnée aux passagers.',
            'Vol raté à cause du retard' => 'À cause d\'un retard de 5h du vol précédent, j\'ai raté ma connexion.',
            'Accueil très décevant' => 'Le personnel de l\'accueil était désagréable et ne voulait pas répondre à mes questions.',
            'Agent impoli au guichet' => 'L\'agent au guichet n°3 m\'a parlé de manière très désagréable et refusé de traiter mon dossier.',
            'Suggestions d\'amélioration' => 'Il serait bien d\'avoir un comptoir dédié aux réclamations pour éviter les files d\'attente.',
        ];

        for ($i = 0; $i < 20; $i++) {
            $typeCode = array_rand($types);
            $type = $types[$typeCode];
            $sujetOptions = $sujets[$typeCode];
            $sujet = $sujetOptions[array_rand($sujetOptions)];
            $description = $descriptions[$sujet] ?? 'Description de la réclamation pour: ' . $sujet;
            $statut = $statuts[array_rand($statuts)];
            $client = $clients[array_rand($clients)];
            $gestionnaire = $gestionnaires[array_rand($gestionnaires)];
            $jourOffset = rand(0, 29);

            $reclamation = Reclamation::create([
                'reference' => Reclamation::genererReference(),
                'client_id' => $client->id,
                'type_id' => $type->id,
                'sous_type_id' => $sousTypes[array_rand($sousTypes)]->id,
                'sujet' => $sujet,
                'description' => $description,
                'priorite' => $priorites[array_rand($priorites)],
                'reference_externe' => strtoupper(substr($typeCode, 0, 3)) . '-' . rand(10000, 99999),
                'statut' => $statut,
                'assigne_a' => in_array($statut, ['en_cours', 'resolu', 'rejete']) ? $gestionnaire->id : null,
                'date_creation' => now()->subDays($jourOffset)->subHours(rand(0, 23)),
                'date_cloture' => in_array($statut, ['resolu', 'rejete']) ? now()->subDays(max(0, $jourOffset - rand(1, 5)))->toDateTimeString() : null,
                'motif_rejet' => $statut === 'rejete' ? 'Réclamation non fondée après vérification.' : null,
            ]);

            // Create history entry
            HistoriqueStatut::create([
                'reclamation_id' => $reclamation->id,
                'ancien_statut' => null,
                'nouveau_statut' => 'en_attente',
                'utilisateur_id' => $agents[array_rand($agents)]->id,
                'commentaire' => 'Réclamation créée',
                'date_changement' => $reclamation->date_creation,
            ]);

            if ($statut !== 'en_attente') {
                HistoriqueStatut::create([
                    'reclamation_id' => $reclamation->id,
                    'ancien_statut' => 'en_attente',
                    'nouveau_statut' => 'en_cours',
                    'utilisateur_id' => $gestionnaire->id,
                    'commentaire' => 'Prise en charge',
                    'date_changement' => $reclamation->date_creation->addHours(rand(1, 6)),
                ]);
            }

            if (in_array($statut, ['resolu', 'rejete'])) {
                HistoriqueStatut::create([
                    'reclamation_id' => $reclamation->id,
                    'ancien_statut' => 'en_cours',
                    'nouveau_statut' => $statut,
                    'utilisateur_id' => $gestionnaire->id,
                    'commentaire' => $statut === 'resolu' ? 'Problème résolu, client satisfait.' : 'Réclamation rejetée.',
                    'date_changement' => $reclamation->date_cloture,
                ]);
            }
        }
    }
}
