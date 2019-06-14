# Create image from text

Create image from text. Custom text, text angle, text padding, font size and font type.
## Getting Started

These instructions will get you a copy of the project up and running

### Installing

Install with Composer

```
composer require hracik/php-recognize-average-color-from-image
```
### Usage

If you want to alter average color, e.g. to use the color on web dark/light designs, you can provide custom saturation and lightness within options.
```PHP
use Hracik\RecognizeAverageColorFromImage;

//can be path to local image or URL
$path = 'https://raw.githubusercontent.com/hracik/php-recognize-average-color-from-image/master/example/example2.jpg;
//possible return options are: RETURN_STRING_HEX, RETURN_STRING_RGB, RETURN_ARRAY_RGB, RETURN_ARRAY_RGB_NORMALIZED, RETURN_ARRAY_HSL
$return = RecognizeAverageColorFromImage::RETURN_STRING_HEX;
//only accepted keys are saturation and lightness
$options = ['saturation' => 0.6, 'lightness' => 0.3];
$color = RecognizeAverageColorFromImage::getAverageColor($path, $return);
echo $color;
```

Output [#707A1E](https://www.google.com/search?q=%23707A1E). 
Same image without options return color [#838B45](https://www.google.com/search?q=%23838B45). 

Or just get counted average without options attribute.
```PHP
use Hracik\RecognizeAverageColorFromImage;

$path = 'https://raw.githubusercontent.com/hracik/php-recognize-average-color-from-image/master/example/example3.jpg;
$color = RecognizeAverageColorFromImage::getAverageColor($path, RecognizeAverageColorFromImage::RETURN_STRING_HEX);
echo $color;
```
Output [#658FAD](https://www.google.com/search?q=%23658FAD).

## Running the tests

Run
```
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests
```   
For Windows platforms
```
./vendor/bin/phpunit.bat --bootstrap vendor/autoload.php tests
```

## Built With

* [PHPUnit](https://phpunit.de/) - The PHP Testing Framework
* [PHP: ImageMagick - Manual ](https://www.php.net/manual/en/book.imagick.php)
## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/hracik/imdb-parser/tags). 

## Authors

* **Andrej Lahucky** - *Initial work* - [Hracik](https://github.com/hracik)

See also the list of [contributors](https://github.com/hracik/imdb-parser/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Acknowledgments

* PurpleBooth

