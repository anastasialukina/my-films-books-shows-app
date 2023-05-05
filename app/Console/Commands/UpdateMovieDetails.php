<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Language;
use App\Models\Movie;
use App\Models\MovieProductionCompany;
use App\Models\MovieProductionDetails;
use App\Models\ProductionCompany;
use App\Models\Status;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMovieDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-movie-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws GuzzleException
     */
    public function handle()
    {
        $this->updateMovieDetails();
    }

    /**
     * @throws GuzzleException
     */
    private function updateMovieDetails(): void
    {
        $movies = Movie::where('id', '>', 8662)->get();

        //get details from TMDB API
        foreach ($movies as $movie) {
            if ($movie->id <= 8662) {
                continue;
            }
            $client = new Client();
            $uri = config('services.tmdb.base_uri') . 'movie/' . $movie->tmdb_id;
            $response = $client->request('GET', $uri, [
                'query' => [
                    'api_key' => config('services.tmdb.token'),
                    'language' => 'en-US',
                ],
            ]);
            //if status code is not 200, log error and continue
            if ($response->getStatusCode() !== 200) {
                Log::error('Error getting movie details from TMDB API for movie with id ' . $movie->tmdb_id);
                continue;
            }
            $movieDetails = json_decode($response->getBody()->getContents());

            $movie->tagline = $movieDetails->tagline;
            $movie->imdb_id = $movieDetails->imdb_id;

            $spokenLanguage = $movieDetails->spoken_languages[0] ?? null;
            $originalLanguage = $movieDetails->original_language ?? null;

            //if spoken language is not null, update the language table
            //if spoken language is null, check if original language is not null and
            //find original language in table by id, if not found, create new language

            if ($spokenLanguage) {
                $movie->original_language_id = $this->updateLanguage($spokenLanguage);
            } elseif ($originalLanguage) {
                $movie->original_language_id = $this->updateLanguage($originalLanguage);
            }
            $movie->save();

            $movieDetailsFromDB = MovieProductionDetails::where('movie_id', $movie->id)
                ->firstOrCreate(['movie_id' => $movie->id]);

            $this->updateMovieProductionDetails($movieDetails, $movieDetailsFromDB);
        }
    }

    private function updateMovieProductionDetails($movieProductionDetail, $movieDetailsFromDB): void
    {
        //dd($movieProductionDetail->production_countries[0]);
        $country = $movieProductionDetail->production_countries[0] ?? null;
        $movieDetailsFromDB->country_id = $country ? $this->getCountryId($country) : null;
        $status = $movieProductionDetail->status ?? null;
        $movieDetailsFromDB->status_id = $status ? $this->updateStatus($status) : null;
        $movieDetailsFromDB->save();

        //update movie production company info in movie_production_companies table
        //and finding the production company id in production_companies table
        //or creating a new one if it doesn't exist
        $this->updateProductionCompany($movieProductionDetail, $movieDetailsFromDB->movie_id);
    }

    private function updateStatus($status)
    {
        //example of statuses: revealed, released, post production, planned, in production, rumored, canceled
        return Status::updateOrCreate(
            ['name' => $status],
        )->id;
    }

    private function updateProductionCompany($movieProductionDetail, $movieId): void
    {
        //update production company info in production_companies table
        //or creating a new one if it doesn't exist

        $movie = Movie::find($movieId);
        $productionCompanyIds = [];

        foreach ($movieProductionDetail->production_companies as $productionCompany) {
            $productionCompanyModel = ProductionCompany::firstOrCreate([
                'name' => $productionCompany->name,
                //'origin_country_id' => $this->getCountryId($productionCompany->origin_country) ?? null
            ]);
            $productionCompanyIds[] = $productionCompanyModel->id;
        }

        //attach production company ids to movie_production_companies table
        $movie->productionCompanies()->attach($productionCompanyIds);
    }

    private function updateLanguage($language)
    {
        if ($language == null) {
            return null;
        }
        if (gettype($language) == 'string') {
            return Language::updateOrCreate(
                ['id' => $language],
                [
                    'name' => $language,
                    'id' => $language,
                ]
            )->id;
        }
        return Language::updateOrCreate(
            ['id' => $language->iso_639_1],
            [
                'name' => $language->english_name,
                'original_name' => $language->name,
                'id' => $language->iso_639_1,
            ]
        )->id;
    }

    private function getCountryId($country)
    {
        $country_id = Country::updateOrCreate(
            ['name' => $country->name],
            [
                'iso_3166_1' => $country->iso_3166_1,
                'name' => $country->name,
            ],
        )->id;

        if ($country_id == 0 || $country_id == null) {
            return null;
        } else {
            return $country_id;
        }

    }
}
