<?php
$testImage = 'images/image1.jpg';
echo "Current working directory: " . getcwd() . "<br>";
echo "Full path to image: " . realpath($testImage) . "<br>";
echo "File exists: " . (file_exists($testImage) ? 'Yes' : 'No') . "<br>";
if (file_exists($testImage)) {
    echo '<img src="' . $testImage . '" alt="Test Image">';
} else {
    echo "Image not found";
}