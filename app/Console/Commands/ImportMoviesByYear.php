<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Kind;
use App\Models\Language;
use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMoviesByYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:movies-by-year';

    /**
     * The console command gets all movies by a certain year.
     *
     * @var string
     */
    protected $description = 'The console command gets all movies by a certain year.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $movies = $this->getMoviesByYears();
        $this->putMoviesWithGenresIntoDB($movies);
    }

    protected function getMoviesByYears()
    {
        $client = new \GuzzleHttp\Client();
        $uri = config('services.tmdb.base_uri') . 'discover/movie';
        $oldMoviesYears = [1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989, 1990, 1991, 1992, 1993, 1994,
            1995, 1996, 1997, 1998];
        $years = [1999, 2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015,
            2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023];
        $yearsTest = [1999, 2000];
        $movies = [];
        //get 5 pages of movies for each year
        foreach ($oldMoviesYears as $year) {
            for ($i = 1; $i <= 10; $i++) {
                $response = $client->request('GET', $uri, [
                    'query' => [
                        'api_key' => config('services.tmdb.token'),
                        'language' => 'en-US',
                        'sort_by' => 'popularity.desc',
                        'include_adult' => 'true',
                        'include_video' => 'false',
                        'page' => $i,
                        'primary_release_year' => $year,
                    ],
                ]);
                $movies[] = json_decode($response->getBody()->getContents());
            }
        }

        return $movies;
    }

    /**
     * @throws \Exception
     */
    protected function putMoviesWithGenresIntoDB($movies)
    {
        //it gives me pages of movies, but I need to get movies from each page
        //and put them into DB
        DB::beginTransaction();

        try {
            foreach ($movies as $onePage) {
                foreach ($onePage->results as $movie) {
                    //we need to check if this movie already exists
                    if (Movie::where('tmdb_id', $movie->id)->exists()) {
                        //how to log this info into console?
                        echo 'Movie with id ' . $movie->id . ' already exists' . PHP_EOL;
                        continue;
                    }
                    $newMovie = new Movie();

                    //redo to object

                    $newMovie->title = $movie->title;
                    $newMovie->description = $movie->overview;
                    $newMovie->release_date = $movie->release_date;
                    $newMovie->vote_average = $movie->vote_average;
                    $newMovie->vote_count = $movie->vote_count;

                    if (!Language::where('id', $movie->original_language)->exists()) {
                        $language = new Language();
                        $language->id = $movie->original_language;
                        //don't have name yet
                        //get name of language from iso_639_1

                        $language->name = $movie->original_language;
                        $language->save();
                    }
                    $newMovie->original_language_id = $movie->original_language;

                    //don't have runtime yet
                    //$newMovie->runtime = $movie->runtime;
                    $newMovie->tmdb_id = $movie->id;
                    $newMovie->save();

                    //put genres into DB for every movie
                    $this->putGenresIntoDB($movie, $newMovie);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function putGenresIntoDB($movie, $newMovie): void
    {
        //loop through genres and put them into DB if they don't exist
        foreach ($movie->genre_ids as $genreId) {
            //if genre is already exists, add it to the MovieGenre table,
            //otherwise create it in Genre table
            //and add it to the MovieGenre table
            $kindId = Kind::where('name', 'movie')->first()->id;
            $newGenre = new Genre();

            if (Genre::where('tmdb_id', $genreId)->where('kind_id', $kindId)->exists()) {
                $genre = Genre::where('tmdb_id', $genreId)->where('kind_id', $kindId)->first();
                $this->putMovieGenresIntoDB($newMovie->id, $genre->id);
            } else {
                //else genre doesn't exist, create it
                //actually, we need to get a genre name from another request,
                // because it's not in the first request, so we need to make another request

                $newGenre->name = $this->getGenreNameById($genreId);
                $newGenre->tmdb_id = $genreId;
                $newGenre->kind_id = $kindId;
                $newGenre->save();

                //and add it to the MovieGenre table
                //dd($newMovie, $newGenre);
                $this->putMovieGenresIntoDB($newMovie->id, $newGenre->id);
            }


        }
    }

    protected function getGenreNameById($genreId)
    {
        $client = new \GuzzleHttp\Client();
        $uri = config('services.tmdb.base_uri') . 'genre/movie/list';
        $response = $client->request('GET', $uri, [
            'query' => [
                'api_key' => config('services.tmdb.token'),
                'language' => 'en-US',
            ],
        ]);
        $genres = json_decode($response->getBody()->getContents());
        $genreName = '';
        foreach ($genres->genres as $genre) {
            if ($genre->id == $genreId) {
                $genreName = $genre->name;
            }
        }

        return $genreName;
    }

    protected function putMovieGenresIntoDB($movieId, $genreId)
    {
        $movieGenre = new MovieGenre();
        $movieGenre->movie_id = $movieId;
        $movieGenre->genre_id = $genreId;
        $movieGenre->save();
    }
}
