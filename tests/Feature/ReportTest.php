<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reclamation;
use App\Models\Client;
use App\Models\TypeReclamation;
use App\Models\SousType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private TypeReclamation $type;
    private SousType $sousType;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('fr');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->type = TypeReclamation::create([
            'code' => 'TEST',
            'libelle' => 'Test Type',
            'delai_traitement_sla' => 48,
            'actif' => true,
        ]);

        $this->sousType = SousType::create([
            'type_id' => $this->type->id,
            'libelle' => 'Test Sous-Type',
            'actif' => true,
        ]);

        $client1 = Client::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean@test.com',
        ]);

        $client2 = Client::create([
            'nom' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie@test.com',
        ]);

        Reclamation::create([
            'reference' => 'R-2026-0001',
            'client_id' => $client1->id,
            'type_id' => $this->type->id,
            'sous_type_id' => $this->sousType->id,
            'sujet' => 'Problème de connexion',
            'description' => 'Impossible de se connecter',
            'priorite' => 'haute',
            'statut' => 'en_cours',
            'date_creation' => now(),
        ]);

        Reclamation::create([
            'reference' => 'R-2026-0002',
            'client_id' => $client2->id,
            'type_id' => $this->type->id,
            'sous_type_id' => $this->sousType->id,
            'sujet' => 'Facture incorrecte',
            'description' => 'Montant facturé erroné',
            'priorite' => 'moyenne',
            'statut' => 'en_attente',
            'date_creation' => now(),
        ]);
    }

    public function test_guest_cannot_access_reports()
    {
        $this->get('/rapports')->assertRedirect('/login');
        $this->get('/rapports/reclamations')->assertRedirect('/login');
        $this->get('/rapports/statistiques')->assertRedirect('/login');
    }

    public function test_reports_index_page()
    {
        $this->actingAs($this->admin)
            ->get('/rapports')
            ->assertOk()
            ->assertSee('Rapports');
    }

    public function test_reclamations_list_page()
    {
        $this->actingAs($this->admin)
            ->get('/rapports/reclamations')
            ->assertOk()
            ->assertSee('Réclamation')
            ->assertSee('R-2026-0001')
            ->assertSee('R-2026-0002');
    }

    public function test_single_reclamation_page()
    {
        $rec = Reclamation::first();
        $this->actingAs($this->admin)
            ->get("/rapports/reclamations/{$rec->id}")
            ->assertOk()
            ->assertSee($rec->reference)
            ->assertSee($rec->sujet);
    }

    public function test_statistiques_page()
    {
        $this->actingAs($this->admin)
            ->get('/rapports/statistiques')
            ->assertOk()
            ->assertSee('Statistiques');
    }

    public function test_csv_export()
    {
        $this->actingAs($this->admin)
            ->get('/rapports/reclamations?format=csv')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    public function test_agent_only_sees_assigned_reclamations()
    {
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $this->actingAs($agent)
            ->get('/rapports/reclamations')
            ->assertOk();
    }

    public function test_agent_cannot_access_others_reclamation()
    {
        $agent = User::factory()->create(['role' => 'agent', 'is_active' => true]);
        $rec = Reclamation::first();
        $this->actingAs($agent)
            ->get("/rapports/reclamations/{$rec->id}")
            ->assertStatus(403);
    }

    public function test_filters_work()
    {
        $this->actingAs($this->admin);
        $this->get('/rapports/reclamations?type_id=' . $this->type->id)->assertOk();
        $this->get('/rapports/reclamations?priorite=haute')->assertOk();
        $this->get('/rapports/reclamations?statut=en_cours')->assertOk();
    }
}
