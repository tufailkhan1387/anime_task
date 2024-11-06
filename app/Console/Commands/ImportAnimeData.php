<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Anime;

class ImportAnimeData extends Command
{
    protected $signature = 'anime:import';
    protected $description = 'Import top 100 anime from Jikan API';

    public function handle()
    {
        $response = Http::get('https://api.jikan.moe/v4/top/anime');
        if ($response->failed()) {
            $this->error('Failed to fetch anime data.');
            return;
        }

        $animeList = $response->json()['data'];

        foreach (array_slice($animeList, 0, 100) as $anime) {
            Anime::updateOrCreate(
                ['mal_id' => $anime['mal_id']],
                [
                    'titles' => json_encode(['en' => $anime['title'], 'pl' => $anime['title']], JSON_UNESCAPED_UNICODE),
                    'slug' => json_encode(['en' => strtolower(str_replace(' ', '-', $anime['title'])), 'pl' => strtolower(str_replace(' ', '-', $anime['title']))], JSON_UNESCAPED_UNICODE),
                    'synopsis' => $anime['synopsis'],
                ]
            );
        }

        $this->info('Anime data imported successfully.');
    }
}
