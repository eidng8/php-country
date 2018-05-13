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

use Alcohol\ISO4217;

/**
 * A utility class that holds currency information
 */
class Currency
{

    /**
     * ISO-4217 code
     *
     * @var array
     */
    protected $currency;

    /**
     * Countries that use this currency
     *
     * @var Country[]
     */
    protected $countries = [];


    /**
     * Create an instance from the given array.
     *
     * @param array  $array
     * @param string $key
     *
     * @return null|static
     * @throws \DomainException Thrown if the given alpha-3 code is invalid
     * @throws \OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public static function fromArray($array, $key = 'currency')
    {
        if (isset($array[$key])) {
            return new static($array[$key]);
        }

        return null;
    }


    /**
     * Create currency instances for each element in the given array.
     *
     * @param array $array
     *
     * @return Country[]
     * @throws \DomainException Thrown if the given alpha-3 code is invalid
     * @throws \OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public static function each(array $array)
    {
        $countries = [];
        foreach ($array as $key => $item) {
            $countries[$key] =
                is_array($item) ? static::fromArray($item) : new static($item);
        }

        return $countries;
    }


    /**
     * Create a currency instance
     *
     * @param int|string|static $code
     * @throws \DomainException Thrown if the given alpha-3 code is invalid
     * @throws \OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public function __construct($code = 'HKD')
    {
        $this->setCurrency($code);
    }


    /**
     * Return the ISO-4217 alpha-3 currency code
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
     * @param int|string|static $currency
     * @return bool
     * @throws \DomainException Thrown if the given alpha-3 code is invalid
     * @throws \OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public function equals($currency)
    {
        if ($currency instanceof static) {
            return $this->alpha3() == $currency->alpha3();
        }

        return $this->alpha3() == (new static($currency))->alpha3();
    }


    /**
     * ISO-4217 alpha-3 代码
     *
     * @return string
     */
    public function alpha3()
    {
        return $this->currency['alpha3'];
    }


    /**
     * List of countries that use this currency.
     *
     * @return string[] ISO-3166 alpha-2 country code
     */
    public function countryCodes()
    {
        return $this->currency['country'];
    }


    /**
     * List of countries that use this currency.
     *
     * @return array array of {@see Country} instances
     */
    public function countries()
    {
        if (empty($this->countryCodes())) {
            // @codeCoverageIgnoreStart
            // just in case
            return [];
            // @codeCoverageIgnoreEnd
        }

        if ($this->countries && $this->countries[0] instanceof Country) {
            return $this->countries;
        }

        $countries = [];
        foreach ($this->countryCodes() as $code) {
            $countries[] = new Country($code);
        }

        return $this->countries = $countries;
    }


    /**
     * Decimals used by the currency
     *
     * @return int
     */
    public function decimals()
    {
        return $this->currency['exp'];
    }


    /**
     * English name
     *
     * @return string
     */
    public function name()
    {
        return $this->currency['name'];
    }


    /**
     * ISO-4217 numeric code
     *
     * @return int
     */
    public function numeric()
    {
        return $this->currency['numeric'];
    }


    /**
     * Set the underlying ISO-4217 data
     *
     * @param int|string|static $code
     * @return static
     * @throws \DomainException Thrown if the given alpha-3 code is invalid
     * @throws \OutOfBoundsException Thrown if the given numeric code is invalid
     */
    public function setCurrency($code)
    {
        if (empty($code)) {
            return $this;
        }

        if ($code instanceof static) {
            $this->currency = array_merge($code->currency);
            $this->countries = [];

            return $this;
        }

        $iso = new ISO4217();
        if (is_numeric($code)) {
            $this->currency = $iso->getByNumeric($code);
        } else {
            $this->currency = $iso->getByAlpha3($code);
        }

        $this->currency['numeric'] = (int)$this->currency['numeric'];
        $this->currency['country'] = (array)$this->currency['country'];

        return $this;
    }
}
