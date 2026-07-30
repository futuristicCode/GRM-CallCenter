<section>
    <div class="flex items-start gap-4">
        <div class="flex-1">
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Avant de supprimer votre compte, veuillez télécharger toutes les données que vous souhaitez conserver.') }}
            </p>
        </div>
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="btn-danger flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
            {{ __('Supprimer le compte') }}
        </button>
    </div>

    <div x-data="{ show: @json($errors->userDeletion->isNotEmpty()) }" x-show="show" x-transition
         class="mt-6 p-5 bg-red-50 border border-red-200 rounded-xl" x-cloak>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <h4 class="text-sm font-semibold text-red-800">{{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}</h4>
            <p class="text-sm text-red-700">
                {{ __('Cette action est irréversible. Toutes vos données seront définitivement supprimées. Veuillez entrer votre mot de passe pour confirmer.') }}
            </p>

            <div>
                <label for="password" class="sr-only">{{ __('Mot de passe') }}</label>
                <input id="password" name="password" type="password" placeholder="{{ __('Votre mot de passe') }}"
                       class="input !border-red-300 focus:!ring-red-500 focus:!border-red-500" autocomplete="current-password">
                @error('userDeletion.password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" x-on:click="show = false" class="btn-secondary">{{ __('Annuler') }}</button>
                <button type="submit" class="btn-danger">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    {{ __('Supprimer définitivement') }}
                </button>
            </div>
        </form>
    </div>
</section>
