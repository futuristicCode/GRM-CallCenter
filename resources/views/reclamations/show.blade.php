@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.app')

@section('title', $reclamation->reference)

@section('content')
    @php
        $sColors = ['en_attente'=>'badge-yellow','en_cours'=>'badge-blue','attente_client'=>'badge-orange','resolu'=>'badge-green','rejete'=>'badge-red','archive'=>'badge-gray'];
        $sLabels = ['en_attente'=>'En attente','en_cours'=>'En cours','attente_client'=>'Attente client','resolu'=>'Résolu','rejete'=>'Rejeté','archive'=>'Archivé'];
        $pColors = ['haute'=>'badge-red','moyenne'=>'badge-yellow','basse'=>'badge-green'];
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('reclamations.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L7.5 12l7.5-7.5"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $reclamation->reference }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $reclamation->sujet }}</p>
            </div>
            <span class="{{ $sColors[$reclamation->statut] ?? 'badge-gray' }}">{{ $sLabels[$reclamation->statut] ?? $reclamation->statut }}</span>
            <span class="{{ $pColors[$reclamation->priorite] ?? 'badge-gray' }}">{{ ucfirst($reclamation->priorite) }}</span>
        </div>
        <div class="flex gap-2">
            @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
                <a href="{{ route('reclamations.edit', $reclamation) }}" class="btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    Modifier
                </a>
            @endif
        </div>
    </div>

    @if(!empty($transitions) && count($transitions) > 0)
        <div class="card mb-6" x-data="{ showModal: false, modalAction: '' }">
            <div class="card-header">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                <h3>Actions rapides</h3>
            </div>
            <div class="card-body">
                <div class="flex flex-wrap gap-2">
                    @if(in_array('en_cours', $transitions))
                        <form method="POST" action="{{ route('reclamations.changer-statut', $reclamation) }}" class="inline">
                            @csrf
                            <input type="hidden" name="statut" value="en_cours">
                            <input type="hidden" name="commentaire" value="Reprise du traitement">
                            <button type="submit" class="btn-sm bg-blue-600 text-white hover:bg-blue-700 rounded-xl px-4 py-2 font-medium transition-colors">Reprendre</button>
                        </form>
                    @endif
                    @if(in_array('attente_client', $transitions))
                        <button @click="showModal=true; modalAction='attente_client'" class="btn-sm bg-orange-500 text-white hover:bg-orange-600 rounded-xl px-4 py-2 font-medium transition-colors">Demander info client</button>
                    @endif
                    @if(in_array('resolu', $transitions))
                        <button @click="showModal=true; modalAction='resolu'" class="btn-sm bg-green-600 text-white hover:bg-green-700 rounded-xl px-4 py-2 font-medium transition-colors">Résoudre</button>
                    @endif
                    @if(in_array('rejete', $transitions))
                        <button @click="showModal=true; modalAction='rejete'" class="btn-sm bg-red-600 text-white hover:bg-red-700 rounded-xl px-4 py-2 font-medium transition-colors">Rejeter</button>
                    @endif
                    @if(in_array('archive', $transitions))
                        <form method="POST" action="{{ route('reclamations.changer-statut', $reclamation) }}" class="inline">
                            @csrf
                            <input type="hidden" name="statut" value="archive">
                            <button type="submit" class="btn-sm bg-gray-500 text-white hover:bg-gray-600 rounded-xl px-4 py-2 font-medium transition-colors">Archiver</button>
                        </form>
                    @endif
                </div>

                <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none" @click.self="showModal=false">
                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
                    <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10" @click.stop>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <template x-if="modalAction==='attente_client'"><span>Demander des informations</span></template>
                            <template x-if="modalAction==='resolu'"><span>Résoudre la réclamation</span></template>
                            <template x-if="modalAction==='rejete'"><span>Rejeter la réclamation</span></template>
                        </h3>
                        <form method="POST" action="{{ route('reclamations.changer-statut', $reclamation) }}">
                            @csrf
                            <input type="hidden" name="statut" :value="modalAction">
                            <div class="mb-4">
                                <label class="label">Commentaire</label>
                                <textarea name="commentaire" rows="3" class="input" placeholder="Décrivez le motif..."></textarea>
                            </div>
                            <div x-show="modalAction==='rejete'" class="mb-4">
                                <label class="label">Motif du rejet <span class="text-red-500">*</span></label>
                                <textarea name="motif_rejet" rows="2" class="input" placeholder="Motif obligatoire..."></textarea>
                            </div>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="showModal=false" class="btn-secondary btn-sm">Annuler</button>
                                <button type="submit" class="btn-primary btn-sm">Confirmer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div x-data="{ activeTab: 'details' }">
        <div class="flex gap-1 border-b border-gray-200 mb-6">
            <button @click="activeTab='details'" :class="activeTab==='details' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="px-4 py-2.5 border-b-2 text-sm font-medium rounded-t-xl transition-colors">Détails</button>
            <button @click="activeTab='historique'" :class="activeTab==='historique' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="px-4 py-2.5 border-b-2 text-sm font-medium rounded-t-xl transition-colors">Historique</button>
            <button @click="activeTab='messages'" :class="activeTab==='messages' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="px-4 py-2.5 border-b-2 text-sm font-medium rounded-t-xl transition-colors">Messagerie</button>
            <button @click="activeTab='fichiers'" :class="activeTab==='fichiers' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" class="px-4 py-2.5 border-b-2 text-sm font-medium rounded-t-xl transition-colors">Pièces jointes</button>
        </div>

        <div x-show="activeTab==='details'" class="card">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Client</h4>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $reclamation->client->prenom ?? '' }} {{ $reclamation->client->nom ?? '' }}</p>
                            <p class="text-sm text-gray-600">{{ $reclamation->client->email ?? '' }}</p>
                            <p class="text-sm text-gray-600">{{ $reclamation->client->telephone ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Classification</h4>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-600">Type: <span class="font-semibold text-gray-900">{{ $reclamation->type->libelle ?? '' }}</span></p>
                            <p class="text-sm text-gray-600">Sous-type: <span class="text-gray-900">{{ $reclamation->sousType->libelle ?? '-' }}</span></p>
                            <p class="text-sm text-gray-600">Réf. externe: <span class="text-gray-900">{{ $reclamation->reference_externe ?? '-' }}</span></p>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sujet</h4>
                        <p class="text-sm text-gray-900">{{ $reclamation->sujet }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Description</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reclamation->description }}</p>
                    </div>
                    @if($reclamation->motif_rejet)
                        <div class="md:col-span-2 bg-red-50 border border-red-200 p-4 rounded-xl">
                            <h4 class="text-sm font-bold text-red-700 mb-1">Motif de rejet</h4>
                            <p class="text-sm text-red-600">{{ $reclamation->motif_rejet }}</p>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Assigné à</h4>
                        <p class="text-sm text-gray-900">{{ $reclamation->assigne->name ?? 'Non assigné' }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Dates</h4>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-600">Créé le: <span class="text-gray-900">{{ ($reclamation->date_creation ?? $reclamation->created_at)->format('d/m/Y à H:i') }}</span></p>
                            @if($reclamation->date_cloture)
                                <p class="text-sm text-gray-600">Clôturé le: <span class="text-gray-900">{{ $reclamation->date_cloture->format('d/m/Y à H:i') }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

                @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']) && $agents->count())
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <form method="POST" action="{{ route('reclamations.assigner', $reclamation) }}" class="flex items-center gap-3">
                            @csrf
                            <label class="text-sm font-medium text-gray-700">Réassigner à:</label>
                            <select name="assigne_a" class="select flex-1">
                                @foreach($agents as $a)
                                    <option value="{{ $a->id }}" {{ $reclamation->assigne_a == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-secondary btn-sm">Assigner</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div x-show="activeTab==='historique'" class="card" style="display:none">
            <div class="card-body">
                <div class="relative">
                    @forelse($reclamation->historiqueStatuts->sortByDesc('date_changement') as $h)
                        <div class="relative flex gap-4 pb-6 last:pb-0">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-indigo-500 ring-4 ring-indigo-100 flex-shrink-0"></div>
                                @if(!$loop->last)
                                    <div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>
                                @endif
                            </div>
                            <div class="flex-1 -mt-1">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $h->nouveau_statut ? ucfirst(str_replace('_', ' ', $h->nouveau_statut)) : 'Création' }}
                                    @if($h->ancien_statut)
                                        <span class="text-gray-400 font-normal">← {{ ucfirst(str_replace('_', ' ', $h->ancien_statut)) }}</span>
                                    @endif
                                </div>
                                @if($h->commentaire)
                                    <p class="text-sm text-gray-600 mt-1">{{ $h->commentaire }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $h->utilisateur->name ?? 'Système' }} — {{ $h->date_changement->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-8">Aucun historique</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div x-show="activeTab==='messages'" class="card" style="display:none">
            <div class="card-body">
                <div class="space-y-3 mb-6">
                    @forelse($reclamation->messages->sortByDesc('created_at') as $msg)
                        <div class="p-4 rounded-xl {{ $msg->est_interne ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-200' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-gray-900">{{ $msg->expediteur->name ?? 'Inconnu' }}</span>
                                <span class="text-xs text-gray-400">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $msg->contenu }}</p>
                            @if($msg->est_interne)
                                <span class="inline-block mt-1.5 text-xs text-amber-600 italic font-medium">Note interne</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-8">Aucun message</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <form method="POST" action="{{ route('messages.store', $reclamation) }}">
                        @csrf
                        <textarea name="contenu" rows="3" required placeholder="Écrire un message..." class="input mb-3"></textarea>
                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center text-sm text-gray-600 gap-2 cursor-pointer">
                                <input type="checkbox" name="est_interne" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                Note interne
                            </label>
                            <button type="submit" class="btn-primary btn-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="activeTab==='fichiers'" class="card" style="display:none">
            <div class="card-body">
                @forelse($reclamation->piecesJointes as $pj)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $pj->nom_fichier }}</p>
                                <p class="text-xs text-gray-400">{{ $pj->type_mime }} — {{ round($pj->taille_octets / 1024, 1) }} Ko</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($pj->chemin_stockage) }}" target="_blank" class="btn-secondary btn-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            Télécharger
                        </a>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-8">Aucune pièce jointe</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
