<?php
$srcPath = 'C:/Users/Arsal/.gemini/antigravity/brain/d8698c2b-0219-4616-90ee-afa0782b92b2/.user_uploaded/media_1790679662406.png';
$im = imagecreatefrompng($srcPath);
$w = imagesx($im);
$h = imagesy($im);
echo "Original size: {$w}x{$h}\n";

// Let's create a high quality 2x version (e.g., 480x446) with crisp antialiasing
$targetH = 400;
$targetW = round($w * ($targetH / $h));

$resized = imagecreatetruecolor($targetW, $targetH);
imagealphablending($resized, false);
imagesavealpha($resized, true);

// Use high quality resampling
imagecopyresampled($resized, $im, 0, 0, 0, 0, $targetW, $targetH, $w, $h);

imagepng($resized, 'public/assets/images/PIRWANI PNG FILE.png', 9);
imagepng($resized, 'public/assets/images/pirwani_logo_sharp.png', 9);
echo "Created crisp resized logo: {$targetW}x{$targetH}\n";
