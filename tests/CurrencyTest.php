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

namespace eidng8\Tests\Country;

use eidng8\Country\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{

    public function testConstruct()
    {
        $this->assertInstanceOf(Currency::class, new Currency('HKD'));
    }


    /**
     * @expectedException \OutOfBoundsException
     */
    public function testConstructWithInvalidAlpha3CodeThrowsException()
    {
        $this->assertInstanceOf(Currency::class, new Currency('zyx'));
    }


    /**
     * @expectedException \DomainException
     */
    public function testConstructWithInvalidNumericCodeThrowsException()
    {
        $this->assertInstanceOf(Currency::class, new Currency(9876));
    }


    public function testDecimals()
    {
        $sut = new Currency('HKD');
        $this->assertSame(2, $sut->decimals());
    }


    public function testNumeric()
    {
        $sut = new Currency('HKD');
        $this->assertSame(344, $sut->numeric());
    }


    public function testCountryCodes()
    {
        $sut = new Currency('HKD');
        $this->assertSame(['HK'], $sut->countryCodes());
    }


    public function testCountries()
    {
        $sut = new Currency('HKD');
        $this->assertCount(1, $sut->countries());
        $this->assertSame('HKG', (string)$sut->countries()[0]);
    }


    public function testName()
    {
        $sut = new Currency('HKD');
        $this->assertSame('Hong Kong Dollar', $sut->name());
    }


    public function testAlpha3()
    {
        $sut = new Currency(344);
        $this->assertSame('HKD', $sut->alpha3());
    }


    public function testToString()
    {
        $sut = new Currency(344);
        $this->assertSame('HKD', (string)$sut);
    }


    public function testFromArray()
    {
        $sut = Currency::fromArray(['some' => 'data', 'currency' => 'hkd']);
        $this->assertInstanceOf(Currency::class, $sut);
        $this->assertEquals('HKD', $sut->alpha3());
    }


    public function testFromArrayNoDataReturnsNull()
    {
        $sut = Currency::fromArray(['some' => 'data', 'without' => 'currency']);
        $this->assertNull($sut);
    }


    public function testEach()
    {
        $data = ['a' => 'hkd', 'b' => 'usd'];
        $sut = Currency::each($data);
        $this->assertCount(count($data), $sut);
        foreach ($sut as $key => $currency) {
            $this->assertInstanceOf(Currency::class, $currency);
            $this->assertEquals(strtoupper($data[$key]), $currency->alpha3());
        }
    }


    public function testSetCurrency()
    {
        $one = new Currency(344);
        $two = new Currency(156);
        $this->assertSame('HKD', $one->setCurrency(null)->alpha3());
        $this->assertSame('CNY', $one->setCurrency($two)->alpha3());
        $this->assertSame('USD', $one->setCurrency('usd')->alpha3());
        $this->assertSame('CNY', $one->setCurrency(156)->alpha3());
    }


    public function testEquals()
    {
        $one = new Currency('hkd');
        $two = new Currency(344);
        $this->assertNotSame($one, $two);
        $this->assertTrue($one->equals($two));

        $this->assertTrue($one->equals(344));
    }
}
