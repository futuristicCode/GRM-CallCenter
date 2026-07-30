<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\Message;
use App\Services\ReclamationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private ReclamationService $service)
    {
    }

    public function store(Request $request, Reclamation $reclamation)
    {
        $validated = $request->validate([
            'contenu' => 'required|string',
            'est_interne' => 'nullable|boolean',
        ]);

        $estInterne = (bool) ($validated['est_interne'] ?? false);

        Message::create([
            'reclamation_id' => $reclamation->id,
            'expediteur_id' => $request->user()->id,
            'contenu' => $validated['contenu'],
            'est_interne' => $estInterne,
        ]);

        if (!$estInterne && $reclamation->assigne_a && $reclamation->assigne_a !== $request->user()->id) {
            $this->service->creerNotification(
                $reclamation->assigne_a,
                __('Nouveau message'),
                __("Un nouveau message a été ajouté sur la réclamation") . " {$reclamation->reference} " . __('par') . " {$request->user()->name}.",
                'message',
                $reclamation->id
            );
        }

        if (!$estInterne && $reclamation->client) {
            $this->service->creerNotification(
                $request->user()->id,
                __('Message envoyé'),
                __("Votre message sur la réclamation") . " {$reclamation->reference} " . __("a été envoyé."),
                'message',
                $reclamation->id
            );
        }

        return back()->with('success', __('Message envoyé.'));
    }
}
