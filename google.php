<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Google Map</title>
    <style>
        /* Style to make the map visible */
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

<h2>My Google Map</h2>
<div id="map"></div>

<!-- Google Maps JavaScript API -->
<script>
    function initMap() {
        const center = { lat: 14.5995, lng: 120.9842 }; // Manila coordinates
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: center
        });

        // Example marker
        const marker = new google.maps.Marker({
            position: center,
            map: map,
            title: "Manila, Philippines"
        });
    }
</script>

<!-- Replace YOUR_API_KEY with your actual API key -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZLFEgzAEJ70iCS3z9i7fZOQFcjpd8zmA&callback=initMap" async defer></script>

</body>
</html>
