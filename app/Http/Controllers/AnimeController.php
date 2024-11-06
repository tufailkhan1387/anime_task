<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function show(Request $request, $slug)
    {
        // Default to English if `lang` is not provided
        $lang = $request->query('lang', 'en');

        // Query the anime based on the slug in the specified language
        $anime = Anime::where("slug->$lang", $slug)->first();

        // Check if the anime was found with the correct slug in the specified language
        if (!$anime) {
            return response()->json(['error' => 'Anime not found or language mismatch','status'=>false], 404);
        }
        $data = [
            "mal_id" => $anime->mal_id,
            "titles" => json_decode($anime->titles),
            "slug" => json_decode($anime->slug),
            "synopsis" => $anime->synopsis,
        ];
        return response()->json(['status'=>true,'data'=>$data]);
    }
}
