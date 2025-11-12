<?php

namespace App\Livewire\Concerns;

use App\Models\UserFavorite;
use Illuminate\Support\Facades\Auth;

trait HandlesFavorites
{
    public string $favoriteName = '';

    /** @var array<int, array{id:int,name:string}> */
    public array $favoritos = [];

    public function carregarFavoritos(): void
    {
        $this->favoritos = Auth::user()?->favorites()
            ->where('context', $this->favoriteContext())
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (UserFavorite $fav) => ['id' => $fav->id, 'name' => $fav->name])
            ->all() ?? [];
    }

    public function salvarFavoritoAtual(): void
    {
        $this->validate([
            'favoriteName' => ['required', 'string', 'max:60'],
        ], [
            'favoriteName.required' => 'Informe um nome para o favorito.',
        ]);

        $payload = $this->favoritePayload();

        $favorite = UserFavorite::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'context' => $this->favoriteContext(),
                'name' => $this->favoriteName,
            ],
            [
                'payload' => $payload,
            ]
        );

        if (property_exists($this, 'favoriteSelecionado')) {
            $this->favoriteSelecionado = $favorite->id;
        }

        $this->favoriteName = '';
        $this->carregarFavoritos();
        $this->dispatch('app:toast', message: 'Favorito salvo.', type: 'success');
        $this->aplicarFavorito($favorite->id);
    }

    public function aplicarFavorito(int|string $favoriteId): void
    {
        if (!$favoriteId) {
            return;
        }

        $favorite = Auth::user()?->favorites()
            ->where('context', $this->favoriteContext())
            ->findOrFail($favoriteId);

        if (property_exists($this, 'favoriteSelecionado')) {
            $this->favoriteSelecionado = $favoriteId;
        }

        $this->applyFavoritePayload($favorite->payload ?? []);
        $this->dispatch('app:toast', message: 'Favorito aplicado.', type: 'info');
    }

    public function excluirFavorito(int|string $favoriteId): void
    {
        if (!$favoriteId) {
            return;
        }

        $favorite = Auth::user()?->favorites()
            ->where('context', $this->favoriteContext())
            ->findOrFail($favoriteId);

        $favorite->delete();
        $this->carregarFavoritos();

        if (property_exists($this, 'favoriteSelecionado') && $this->favoriteSelecionado === $favoriteId) {
            $this->favoriteSelecionado = null;
        }

        $this->dispatch('app:toast', message: 'Favorito removido.', type: 'success');
    }

    abstract protected function favoriteContext(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function favoritePayload(): array;

    /**
     * @param array<string, mixed> $payload
     */
    abstract protected function applyFavoritePayload(array $payload): void;
}
