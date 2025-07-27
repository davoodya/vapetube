<?php
// Create placeholder images for products

$imageDir = 'assets/images/';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

$products = [
    'smok-nord4.jpg' => 'SMOK Nord 4',
    'voopoo-dragx.jpg' => 'VOOPOO Drag X',
    'nasty-mango.jpg' => 'Nasty Juice',
    'juul-mint.jpg' => 'JUUL Pod',
    'puff-bar.jpg' => 'Puff Bar',
    'mesh-coil.jpg' => 'Mesh Coil',
    'vape-case.jpg' => 'Vape Case',
    'iqos-iluma.jpg' => 'IQOS ILUMA',
    'placeholder.jpg' => 'Product'
];

foreach ($products as $filename => $productName) {
    $width = 400;
    $height = 300;
    
    // Create image
    $image = imagecreate($width, $height);
    
    // Colors
    $bg_color = imagecolorallocate($image, 240, 240, 240);
    $text_color = imagecolorallocate($image, 100, 100, 100);
    $border_color = imagecolorallocate($image, 200, 200, 200);
    
    // Fill background
    imagefill($image, 0, 0, $bg_color);
    
    // Draw border
    imagerectangle($image, 0, 0, $width-1, $height-1, $border_color);
    
    // Add text
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($productName);
    $text_height = imagefontheight($font_size);
    
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $productName, $text_color);
    
    // Save image
    imagejpeg($image, $imageDir . $filename, 80);
    imagedestroy($image);
    
    echo "Created: " . $filename . "\n";
}

echo "All placeholder images created successfully!\n";
?>

