
# Anime Importer Project

A Laravel 11 application that imports and stores data on the 100 most popular anime from the Jikan API (an unofficial MyAnimeList API). The project includes a basic API endpoint to fetch anime data in multiple languages.

## Prerequisites

Ensure you have the following installed:
- PHP 8.3
- Composer
- MySQL 8

## Project Setup

### Step 1: Clone the Repository

Clone this repository to your local machine and navigate into the project folder.

```bash
git clone <your-repo-url>
cd animeApp
```

### Step 2: Set Up Environment Variables

Create a copy of the `.env` file and configure it with your database credentials.

```plaintext
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anime_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Create the Model and Migration

Generate the model and migration for the `Anime` table.

```bash
php artisan make:model Anime -m
```

### Step 4: Define Database Structure

Open the generated migration file in `database/migrations/` and define the database structure as follows:

```php
Schema::create('animes', function (Blueprint $table) {
    $table->id();
    $table->integer('mal_id')->unique();
    $table->json('titles');
    $table->json('slug');
    $table->text('synopsis')->nullable();
    $table->timestamps();
});
```

### Step 5: Run Migrations

Run the migrations to create the `animes` table in your database.

```bash
php artisan migrate
```

## Data Import

### Step 6: Create Import Command

Create a custom Artisan command to import data from the Jikan API.

```bash
php artisan make:command ImportAnimeData
```

### Step 7: Implement Data Fetching and Storing Logic

In `app/Console/Commands/ImportAnimeData.php`, update the `handle` method with the following code to fetch data from the Jikan API and store it in the database.

```php
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
                    'slug' => json_encode([
                        'en' => strtolower(str_replace(' ', '-', $anime['title'])),
                        'pl' => strtolower(str_replace(' ', '-', $anime['title']))
                    ], JSON_UNESCAPED_UNICODE),
                    'synopsis' => $anime['synopsis'],
                ]
            );
        }

        $this->info('Anime data imported successfully.');
    }
}
```

### Step 8: Run the Import Command

To fetch and store the top 100 anime from the Jikan API, use the following command:

```bash
php artisan anime:import
```

### Step 9: Define the Route

In routes/web.php, add a route to create an API endpoint for fetching anime data by slug and language.

```bash
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;

Route::get('/api/anime/{slug}', [AnimeController::class, 'show']);

```

### Step 10: Create the Anime Controller
Step 10: Create the Anime Controller

```bash
php artisan make:controller AnimeController

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function show(Request $request, $slug)
    {
        $lang = $request->query('lang', 'en');
        $anime = Anime::where("slug->$lang", $slug)->first();

        if (!$anime) {
            return response()->json(['error' => 'Anime not found or language mismatch'], 404);
        }

        return response()->json($anime);
    }
}

```
### Step 11: Test the API
You can now test the API endpoint by accessing:

```
http://your-app-url/api/anime/{slug}?lang=pl
```

---

With these steps, you should be able to set up and populate your database with anime data. Adjust `<your-repo-url>` to the actual Git repository URL if you are sharing this project.
