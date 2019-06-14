<?php
namespace Hracik\RecognizeAverageColorFromImage;

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
		        $RGB = $pixel->getColor();
		        return sprintf('#%02X%02X%02X', $RGB['r'], $RGB['g'], $RGB['b']);
	        }
	        if ($returnType == self::RETURN_STRING_RGB) {
		        return $pixel->getColorAsString();
	        }
	        if ($returnType == self::RETURN_ARRAY_RGB) {
		        return $pixel->getColor();
	        }
	        if ($returnType == self::RETURN_ARRAY_RGB_NORMALIZED) {
		        return $pixel->getColor(true);
	        }
	        if ($returnType == self::RETURN_ARRAY_HSL) {
		        return $pixel->getHSL();
	        }
        } catch(Throwable $e) {
            throw new RecognizeAverageColorException($e->getMessage());
        }

		throw new RecognizeAverageColorException('Unknown return type.');
    }

	/**
	 * @param $HSL
	 * @param $options
	 * @param $returnType
	 * @return array|string|null
	 * @throws RecognizeAverageColorException
	 */
	private static function alterAverageColor($HSL, $options, $returnType)
	{
		if (!empty($options['saturation'])) {
			$HSL['saturation'] = $options['saturation'];
		}
		if (!empty($options['lightness'])) {
			$HSL['luminosity'] = $options['lightness'];
		}

		if ($returnType == self::RETURN_ARRAY_HSL) {
			return $HSL;
		}

		//get RGB from HSL, then return what requested
		$normalizedRGB = self::_color_hsl2rgb($HSL);
		if ($returnType == self::RETURN_ARRAY_RGB_NORMALIZED) {
			return $normalizedRGB;
		}

		$RGB = array_map(function($normalized) { return $normalized * 255;}, $normalizedRGB);
		if ($returnType == self::RETURN_STRING_HEX) {
			return sprintf('#%02X%02X%02X', $RGB['r'], $RGB['g'], $RGB['b']);
		}
		if ($returnType == self::RETURN_STRING_RGB) {
			return sprintf('rgb(%d,%d,%d)', $RGB['r'], $RGB['g'], $RGB['b']);
		}
		if ($returnType == self::RETURN_ARRAY_RGB) {
			return $RGB;
		}

		throw new RecognizeAverageColorException('Unknown return type.');
	}

	/**
	 * Convert a HSL triplet into RGB
	 * @param $hsl
	 * @return array
	 */
    private static function _color_hsl2rgb($hsl)
    {
        $h = $hsl['hue'];
        $s = $hsl['saturation'];
        $l = $hsl['luminosity'];
        $m2 = ($l <= 0.5) ? $l * ($s + 1) : $l + $s - $l*$s;
        $m1 = $l * 2 - $m2;
        return array(
        	'r' => self::_color_hue2rgb($m1, $m2, $h + 0.33333),
	        'g' => self::_color_hue2rgb($m1, $m2, $h),
	        'b' => self::_color_hue2rgb($m1, $m2, $h - 0.33333)
        );
    }

	/**
	 * Helper function for _color_hsl2rgb().
	 * @param $m1
	 * @param $m2
	 * @param $h
	 * @return float|int
	 */
    private static function _color_hue2rgb($m1, $m2, $h)
    {
        $h = ($h < 0) ? $h + 1 : (($h > 1) ? $h - 1 : $h);
        if ($h * 6 < 1) {
        	return $m1 + ($m2 - $m1) * $h * 6;
        }
        if ($h * 2 < 1) {
        	return $m2;
        }
        if ($h * 3 < 2) {
        	return $m1 + ($m2 - $m1) * (0.66666 - $h) * 6;
        }
        return $m1;
    }
}
