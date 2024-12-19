<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Cache;

class TableParserController extends Controller
{
    /**
     * Parse the website URL and return the tables in JSON format.
     * 
     * @param Request $request The HTTP request object
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response containing the parsed tables or an error message
     */
    public function parse(Request $request)
    {
        // Validate the input URL
        $request->validate([
            'url' => 'required|url',  // Ensures the URL is provided and is valid
        ]);
    
        try {
            $url = $request->input('url');
    
            // Check if the cached data for the given URL already exists
            $cachedData = Cache::get('tables_' . md5($url));
    
            if ($cachedData) {
                // Return the cached data if it exists
                return response()->json([
                    'message' => 'Returned cached data.',
                    'tables' => $cachedData,
                ]);
            }
    
            // Make an HTTP request to fetch the website content
            $response = \Http::get($url);
    
            if ($response->successful()) {
                $htmlContent = $response->body();  // Extract the body content of the website
    
                // Use Symfony DomCrawler to parse the HTML and find tables
                $crawler = new Crawler($htmlContent);
                $tables = $crawler->filter('table');
                
                if ($tables->count() === 0) {
                    // If no tables are found, return a 404 response
                    return response()->json([
                        'message' => 'No tables found on the provided website.',
                    ], 404);
                }
    
                // Parse each table found and format the data into a structured array
                $parsedTables = [];
                $tables->each(function (Crawler $tableNode) use (&$parsedTables) {
                    $tableData = [];
                    $tableNode->filter('tr')->each(function (Crawler $rowNode) use (&$tableData) {
                        $rowData = [];
                        $rowNode->filter('th, td')->each(function (Crawler $cellNode) use (&$rowData) {
                            // Collect the cell data and trim extra spaces
                            $rowData[] = trim($cellNode->text());
                        });
                        $tableData[] = $rowData;  // Add the row data to the table data
                    });
                    $parsedTables[] = $tableData;  // Add the formatted table data to the final response
                });
    
                // Cache the parsed tables for 10 minutes to optimize subsequent requests
                Cache::put('tables_' . md5($url), $parsedTables, now()->addMinutes(10));
    
                return response()->json([
                    'message' => 'Tables parsed successfully!',
                    'tables' => $parsedTables,
                ]);
            }
    
            // If the website content fetch fails, return a 502 response with the status code
            return response()->json([
                'message' => 'Failed to fetch website content.',
                'status' => $response->status(),
            ], 502);
    
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Handle errors when making the HTTP request
            return response()->json([
                'message' => 'Error fetching the website.',
                'error' => 'Request failed: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Catch any other exceptions that occur during the process
            return response()->json([
                'message' => 'An error occurred while processing the website.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
