<?php
// Unsplash API Access Key - replace with your actual API key
$UNSPLASH_ACCESS_KEY = 'o5fOYq8ttjb_RBO8IZZEpRhh0z8rgy-jzHX7Eq4hkmE';

// Directory to save the downloaded images
$saveDir = 'unsplash_images';

// Create directory if it doesn't exist
if (!file_exists($saveDir)) {
    mkdir($saveDir, 0755, true);
}

// Function to search Unsplash for images
function searchUnsplash($query, $perPage = 30, $page = 1) {
    global $UNSPLASH_ACCESS_KEY;
    
    $url = 'https://api.unsplash.com/search/photos';
    $headers = [
        'Authorization: Client-ID ' . $UNSPLASH_ACCESS_KEY
    ];
    
    // Build query parameters
    $params = http_build_query([
        'query' => $query,
        'per_page' => $perPage,
        'page' => $page
    ]);
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Execute cURL request
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true); // Return the response as an array
}

// Function to download images from Unsplash
function downloadImage($url, $imageId) {
    global $saveDir;
    
    // Image file path
    $imageFile = $saveDir . '/' . $imageId . '.jpg';
    
    // Download the image and save to the file
    $imgData = file_get_contents($url);
    file_put_contents($imageFile, $imgData);
    
    echo "Downloaded image: " . $imageFile . "\n";
}

// Main function to search and download house images
function downloadHouseImages($query = 'front house', $totalImages = 100) {
    $imagesDownloaded = 0;
    $page = 1;

    while ($imagesDownloaded < $totalImages) {
        // Fetch search results from Unsplash API
        $data = searchUnsplash($query, 30, $page);
        
        foreach ($data['results'] as $result) {
            $imageUrl = $result['urls']['regular'];  // Get the regular image URL
            $imageId = $result['id'];               // Get the image ID

            downloadImage($imageUrl, $imageId);
            $imagesDownloaded++;

            // Stop if we reach the required total images
            if ($imagesDownloaded >= $totalImages) {
                break;
            }
        }

        $page++;  // Move to the next page
    }

    echo "Downloaded $imagesDownloaded images.\n";
}

// Download 50 images of houses
downloadHouseImages();
