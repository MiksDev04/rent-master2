<?php
// Database configuration
$host = '127.0.0.1';
$dbname = 'rentsystem';
$username = 'root'; // Change to your database username
$password = ''; // Change to your database password

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get all properties with their amenities and images
function getProperties($pdo, $filters = []) {
    $sql = "SELECT p.*, 
                   GROUP_CONCAT(DISTINCT a.amenity_name) as amenities,
                   pi.image1, pi.image2, pi.image3
            FROM properties p
            LEFT JOIN property_amenities pa ON p.property_id = pa.property_id
            LEFT JOIN amenities a ON pa.amenity_id = a.amenity_id
            LEFT JOIN property_images pi ON p.property_id = pi.property_id
            WHERE p.property_status = 'available'";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['price'])) {
        if ($filters['price'] == '5000') {
            $sql .= " AND p.property_rental_price < 5000";
        } elseif ($filters['price'] == '10000') {
            $sql .= " AND p.property_rental_price BETWEEN 5000 AND 10000";
        } elseif ($filters['price'] == '20000') {
            $sql .= " AND p.property_rental_price BETWEEN 10000 AND 20000";
        } elseif ($filters['price'] == '20001') {
            $sql .= " AND p.property_rental_price > 20000";
        }
    }
    
    if (!empty($filters['type'])) {
        // This assumes property_name contains the type - adjust based on your actual DB structure
        $sql .= " AND p.property_name LIKE :type";
        $params[':type'] = '%' . $filters['type'] . '%';
    }
    
    if (!empty($filters['location'])) {
        $sql .= " AND p.property_location LIKE :location";
        $params[':location'] = '%' . $filters['location'] . '%';
    }
    
    $sql .= " GROUP BY p.property_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $filters = [
        'price' => $_GET['price'] ?? '',
        'type' => $_GET['type'] ?? '',
        'location' => $_GET['location'] ?? ''
    ];
    
    $properties = getProperties($pdo, $filters);
    
    echo json_encode($properties);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Philippines House Rental Finder</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Basic styles - you should move these to a separate CSS file */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .search-container {
            margin-top: 1rem;
        }
        
        #search-input {
            padding: 0.5rem;
            width: 60%;
            max-width: 500px;
        }
        
        #search-btn {
            padding: 0.5rem 1rem;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .filters {
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
        }
        
        .filters select {
            padding: 0.5rem;
        }
        
        main {
            padding: 2rem 0;
        }
        
        .map-container {
            height: 400px;
            margin-bottom: 2rem;
            position: relative;
        }
        
        #map {
            height: 100%;
        }
        
        .map-legend {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .legend-icon {
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border-radius: 50%;
        }
        
        .legend-icon.available {
            background-color: #2ecc71;
        }
        
        .legend-icon.rented {
            background-color: #e74c3c;
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .property-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .property-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }
        
        .property-info {
            padding: 1rem;
        }
        
        .property-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .property-location {
            color: #7f8c8d;
            margin: 0.5rem 0;
        }
        
        .property-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .amenity-tag {
            background-color: #ecf0f1;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Philippines House Rental Finder</h1>
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Search by location, price, or features...">
                <button id="search-btn"><i class="fas fa-search"></i> Search</button>
                <div class="filters">
                    <select id="price-filter">
                        <option value="">Any Price</option>
                        <option value="5000">Below ₱5,000</option>
                        <option value="10000">₱5,000 - ₱10,000</option>
                        <option value="20000">₱10,000 - ₱20,000</option>
                        <option value="20001">Above ₱20,000</option>
                    </select>
                    <select id="type-filter">
                        <option value="">Any Type</option>
                        <option value="house">House</option>
                        <option value="apartment">Apartment</option>
                        <option value="condo">Condo</option>
                        <option value="townhouse">Townhouse</option>
                    </select>
                    <select id="bedrooms-filter">
                        <option value="">Any Bedrooms</option>
                        <option value="1">1 Bedroom</option>
                        <option value="2">2 Bedrooms</option>
                        <option value="3">3+ Bedrooms</option>
                    </select>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="map-container">
            <div id="map"></div>
            <div class="map-legend">
                <div><span class="legend-icon available"></span> Available</div>
                <div><span class="legend-icon rented"></span> Rented</div>
            </div>
        </div>

        <div class="property-listings">
            <h2>Available Properties</h2>
            <div id="properties-container" class="properties-grid">
                <!-- Properties will be loaded here via JavaScript -->
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        const map = L.map('map').setView([12.8797, 121.7740], 6); // Center on Philippines
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Function to load properties from the server
        async function loadProperties() {
            const priceFilter = document.getElementById('price-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            const locationFilter = document.getElementById('search-input').value;
            
            try {
                const response = await fetch('?action=get_properties&price=' + priceFilter + 
                                           '&type=' + typeFilter + '&location=' + encodeURIComponent(locationFilter));
                const properties = await response.json();
                
                displayProperties(properties);
                updateMapMarkers(properties);
            } catch (error) {
                console.error('Error loading properties:', error);
            }
        }
        
        // Function to display properties in the grid
        function displayProperties(properties) {
            const container = document.getElementById('properties-container');
            container.innerHTML = '';
            
            if (properties.length === 0) {
                container.innerHTML = '<p>No properties found matching your criteria.</p>';
                return;
            }
            
            properties.forEach(property => {
                const propertyCard = document.createElement('div');
                propertyCard.className = 'property-card';
                
                // Use the first available image or a placeholder if none exists
                const imageUrl = property.image1 || property.image2 || property.image3 || 'https://via.placeholder.com/300x200';
                
                propertyCard.innerHTML = `
                    <div class="property-image" style="background-image: url('${imageUrl}')"></div>
                    <div class="property-info">
                        <h3>${property.property_name}</h3>
                        <div class="property-price">₱${parseInt(property.property_rental_price).toLocaleString()}</div>
                        <div class="property-location">${property.property_location}</div>
                        <p>${property.property_description.substring(0, 100)}...</p>
                        ${property.amenities ? `
                        <div class="property-amenities">
                            ${property.amenities.split(',').map(amenity => `
                                <span class="amenity-tag">${amenity.trim()}</span>
                            `).join('')}
                        </div>
                        ` : ''}
                    </div>
                `;
                
                container.appendChild(propertyCard);
            });
        }
        
        // Function to update map markers based on properties
        function updateMapMarkers(properties) {
            // Clear existing markers
            map.eachLayer(layer => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
            
            // For demo purposes, we'll use random coordinates within the Philippines
            // In a real app, you would geocode the addresses or store coordinates in your database
            const phBounds = {
                north: 21.2,
                south: 4.6,
                west: 116.9,
                east: 126.6
            };
            
            properties.forEach((property, index) => {
                // Generate random coordinates within Philippines bounds
                const lat = phBounds.south + Math.random() * (phBounds.north - phBounds.south);
                const lng = phBounds.west + Math.random() * (phBounds.east - phBounds.west);
                
                const marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(`<b>${property.property_name}</b><br>₱${property.property_rental_price}`);
                
                // Use different colors for available properties
                const markerIcon = L.divIcon({
                    className: 'property-marker',
                    html: `<div style="background-color: #2ecc71; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                marker.setIcon(markerIcon);
            });
            
            // If we have properties, fit the map bounds to show all markers
            if (properties.length > 0) {
                const group = new L.featureGroup(Array.from(map._layers).filter(l => l instanceof L.Marker));
                map.fitBounds(group.getBounds().pad(0.2));
            }
        }
        
        // Event listeners for search and filters
        document.getElementById('search-btn').addEventListener('click', loadProperties);
        document.getElementById('price-filter').addEventListener('change', loadProperties);
        document.getElementById('type-filter').addEventListener('change', loadProperties);
        document.getElementById('search-input').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                loadProperties();
            }
        });
        
        // Load properties when page loads
        document.addEventListener('DOMContentLoaded', loadProperties);
    </script>
</body>
</html>