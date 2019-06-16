<?php
namespace Hracik\RecognizeAverageColorFromImage;

use Hracik\ColorConverter\ColorConverter;
use Imagick;
use ImagickPixel;
use Throwable;

class RecognizeAverageColorFromImage
{

	const RETURN_STRING_HEX = 0;
	const RETURN_STRING_RGB = 1;
	const RETURN_ARRAY_RGB = 2;
	const RETURN_ARRAY_RGB_NORMALIZED = 3;
	const RETURN_ARRAY_HSL = 3;

	/**
	 * @param string     $path
	 * @param array|null $options
	 * @param int        $returnType
	 * @return array|string|null
	 * @throws RecognizeAverageColorException
	 */
	public static function getAverageColor(string $path, int $returnType = self::RETURN_STRING_HEX, ?array $options = null)
	{
		//todo check return srgb vs. rgb
        try {
            // Read image file with Imagick
            $image = new Imagick($path);
            // Scale down to 1x1 pixel to make Imagick do the average
            $image->scaleImage(1, 1);
	        $pixels = $image->getImageHistogram();
	        /** @var ImagickPixel$pixel */
	        $pixel = reset($pixels);
	        if (null !== $options) {
	        	return self::alterAverageColor($pixel->getHSL(), $options, $returnType);
	        }

	        if ($returnType == self::RETURN_STRING_HEX) {
		        $rgb = $pixel->getColor();
		        //remove array keys
		        $rgb = array_values($rgb);
		        return ColorConverter::rgb2hex($rgb);
	        }
	        if ($returnType == self::RETURN_STRING_RGB) {
		        return $pixel->getColorAsString();
	        }
	        if ($returnType == self::RETURN_ARRAY_RGB) {
		        $rgb = $pixel->getColor();
		        //remove array keys
		        return array_values($rgb);
	        }
	        if ($returnType == self::RETURN_ARRAY_RGB_NORMALIZED) {
		        $rgbNormalized = $pixel->getColor(true);
		        //remove array keys
		        return array_values($rgbNormalized);
	        }
	        if ($returnType == self::RETURN_ARRAY_HSL) {
		        $hsl = $pixel->getHSL();
		        //remove array keys
		        return array_values($hsl);
	        }
        } catch(Throwable $e) {
            throw new RecognizeAverageColorException($e->getMessage());
        }

		throw new RecognizeAverageColorException('Unknown return type.');
    }

	/**
	 * @param $hsl
	 * @param $options
	 * @param $returnType
	 * @return array|string|null
	 * @throws RecognizeAverageColorException
	 */
	private static function alterAverageColor($hsl, $options, $returnType)
	{
		if (!empty($options['saturation'])) {
			$hsl['saturation'] = $options['saturation'];
		}
		if (!empty($options['lightness'])) {
			$hsl['luminosity'] = $options['lightness'];
		}

		$hsl = array_values($hsl);
		if ($returnType == self::RETURN_ARRAY_HSL) {
			return $hsl;
		}

		//get RGB from HSL, then return what requested
		$rgb = ColorConverter::hsl2rgb($hsl);
		if ($returnType == self::RETURN_ARRAY_RGB_NORMALIZED) {
			return array_map(function($toNormalize) { return $toNormalize / 255;}, $rgb);
		}

		if ($returnType == self::RETURN_STRING_HEX) {
			return ColorConverter::rgb2hex($rgb);
		}
		if ($returnType == self::RETURN_STRING_RGB) {
			return sprintf('rgb(%d,%d,%d)', $rgb[0], $rgb[1], $rgb[2]);
		}
		if ($returnType == self::RETURN_ARRAY_RGB) {
			return $rgb;
		}

		throw new RecognizeAverageColorException('Unknown return type.');
	}
}
