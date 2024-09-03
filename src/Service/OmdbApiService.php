<?php
     namespace App\Service;

     use Symfony\Contracts\HttpClient\HttpClientInterface;

     class OmdbApiService
     {
     public $client;
     public $apiKey;

     public function __construct(HttpClientInterface $client, string $apiKey)
     {
     $this->client = $client;
     $this->apiKey = $apiKey;
     }

     public function fetchMovieData(string $title): array
     {
     $response = $this->client->request(
     'GET',
     'http://www.omdbapi.com/',
     [
     'query' => [
     'apikey' => $this->apiKey,
     't' => $title,
     ],
     ]
     );

     return $response->toArray();
     }
     } ?>