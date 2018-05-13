<?php
/**
 * Locale utilities
 *
 * @author    Jackey Cheung <cheung.jackey@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/eidng8/php-country
 *
 */

/**
 * Locale utilities
 *
 * @author    Jackey Cheung <cheung.jackey@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/eidng8/php-country
 *
 */

namespace eidng8\Country;

use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\OutOfBoundsException;
use League\ISO3166\ISO3166;
use Locale;

/**
 * A utility class that holds country information.
 */
class Country
{

    /**
     * ISO-3166 array
     *
     * @var string[]
     */
    protected $country;


    /**
     * Create an instance from the given array.
     *
     * @param array      $array Data set that contains country code
     * @param string|int $key   Key or index of the country element
     *
     * @return null|static
     * @throws DomainException Thrown if the given alpha-2 or alpha-3 code is
     *                         invalid
     * @throws OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public static function fromArray($array, $key = 'country')
    {
        if (isset($array[$key])) {
            return new static($array[$key]);
        }

        return null;
    }


    /**
     * Create country instances for each element in the given array.
     *
     * @param array $array
     *
     * @return Country[]
     * @throws DomainException Thrown if the given alpha-2 or alpha-3 code is
     *                         invalid
     * @throws OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public static function each($array)
    {
        $countries = [];
        foreach ($array as $key => $item) {
            $countries[$key] =
                is_array($item) ? static::fromArray($item) : new static($item);
        }

        return $countries;
    }


    /**
     * Country constructor. See {@see setCountry()}.
     *
     * @param static|string|int $code ISO-3166 country code
     * @throws DomainException Thrown if the given alpha-2 or alpha-3 code is
     *                                invalid
     * @throws OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public function __construct($code)
    {
        $this->set($code);
    }


    /**
     * Return the ISO-3166 alpha-3 code.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->alpha3();
    }


    /**
     * Determines if the given country equals to the given one.
     * The equality is perform by checking their alpha-3 codes.
     *
     * @param int|string|static $country
     * @return bool
     * @throws DomainException Thrown if the given alpha-2 or alpha-3 code is
     *                         invalid
     * @throws OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public function equals($country)
    {
        if ($country instanceof static) {
            return $this->alpha3() == $country->alpha3();
        }

        return $this->alpha3() == (new static($country))->alpha3();
    }


    /**
     * English name
     *
     * @param string $locale
     * @return string
     */
    public function name($locale = null)
    {
        if (!$locale) {
            return $this->country['name'];
        }

        return Locale::getDisplayRegion("en-{$this->alpha2()}", $locale);
    }


    /**
     * ISO-3166 alpha-2 code
     *
     * @return string
     */
    public function alpha2()
    {
        return $this->country['alpha2'];
    }


    /**
     * ISO-3166 alpha-3 code
     *
     * @return string
     */
    public function alpha3()
    {
        return $this->country['alpha3'];
    }


    /**
     * ISO-3166 numeric code
     *
     * @return int
     */
    public function numeric()
    {
        return $this->country['numeric'];
    }


    /**
     * Official currency
     *
     * @return string[]
     */
    public function currencies()
    {
        return $this->country['currency'];
    }


    /**
     * Telephone area code
     *
     * @return int|null
     */
    public function areaCode()
    {
        return AreaCode::byCountry($this);
    }


    /**
     * Set the underlying ISO-3166 data.
     * If a `Country` instance is passed, the given instance's data will be
     * copied to this instance. There will be no shared reference between them.
     *
     * @param int|string|static $code
     *
     * @return static
     * @throws DomainException Thrown if the given alpha-2 or alpha-3 code is
     *                         invalid
     * @throws OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public function set($code)
    {
        if (empty($code)) {
            return $this;
        }

        if ($code instanceof static) {
            $this->country = array_merge($code->country);

            return $this;
        }

        $iso = new ISO3166();
        if (is_numeric($code)) {
            $this->country = $iso->numeric((string)$code);
        } elseif (2 === strlen($code)) {
            $this->country = $iso->alpha2($code);
        } else {
            $this->country = $iso->alpha3($code);
        }

        $this->country['numeric'] = (int)$this->country['numeric'];

        return $this;
    }
}
