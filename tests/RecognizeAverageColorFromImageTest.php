<?php
namespace Hracik\RecognizeAverageColorFromImage;

use PHPUnit\Framework\TestCase;
use Throwable;

class RecognizeAverageColorFromImageTest extends TestCase
{
	/**
	 * @dataProvider getAverageColorProvider
	 * @param $path
	 * @param $returnType
	 * @param $options
	 * @param $expected
	 */
	public function testGetAverageColor($path, $returnType, $options, $expected)
	{
		try {
			$result = RecognizeAverageColorFromImage::getAverageColor($path, $returnType, $options);
		}
		catch (Throwable $e) {
			$result = false;
		}

		$this->assertSame($expected, $result);
	}


	public function getAverageColorProvider()
	{
		return [
			[__DIR__.'/../example/example1.jpg', RecognizeAverageColorFromImage::RETURN_STRING_HEX, null, '#C17D49'],
			[__DIR__.'/../example/example1.jpg', RecognizeAverageColorFromImage::RETURN_STRING_RGB, null, 'srgb(193,125,73)'],
			['unknown-path', RecognizeAverageColorFromImage::RETURN_STRING_HEX, [], false],
			[__DIR__.'/../example/example3.jpg', RecognizeAverageColorFromImage::RETURN_STRING_HEX, [], '#658FAD'],
			[__DIR__.'/../example/example3.jpg', RecognizeAverageColorFromImage::RETURN_STRING_HEX, ['lightness' => 0.2], '#233542'],
			[__DIR__.'/../example/example2.jpg', RecognizeAverageColorFromImage::RETURN_STRING_HEX, ['lightness' => 0.3, 'saturation' => 0.6], '#707A1E'],
		];
	}
}
