<?php
/**
 * Created by IntelliJ IDEA.
 * User: JC
 * Date: 2018-05-12
 * Time: 17:40
 */

namespace eidng8\Tests\Country;

use eidng8\Country\Country;
use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{

    public function testConstruct()
    {
        $this->assertInstanceOf(Country::class, new Country('HKG'));
    }


    /**
     * @expectedException OutOfBoundsException
     */
    public function testConstructInvalidAlpha2CodeThrowsException()
    {
        $this->assertInstanceOf(Country::class, new Country('yz'));
    }


    /**
     * @expectedException OutOfBoundsException
     */
    public function testConstructInvalidAlpha3CodeThrowsException()
    {
        $this->assertInstanceOf(Country::class, new Country('abc'));
    }


    /**
     * @expectedException DomainException
     */
    public function testConstructInvalidNumericCodeThrowsException()
    {
        $this->assertInstanceOf(Country::class, new Country(9876));
    }


    public function testName()
    {
        $sut = new Country('HKG');
        $this->assertSame('Hong Kong', $sut->name());
        $this->assertSame('中国香港特别行政区', $sut->name('zh'));
    }


    public function testNumeric()
    {
        $sut = new Country('HKG');
        $this->assertSame(344, $sut->numeric());
    }


    public function testToString()
    {
        $sut = new Country('HK');
        $this->assertSame('HKG', (string)$sut);
    }


    public function testAlpha2()
    {
        $sut = new Country('HKG');
        $this->assertSame('HK', $sut->alpha2());
    }


    public function testAlpha3()
    {
        $sut = new Country('HK');
        $this->assertSame('HKG', $sut->alpha3());
    }


    public function testCurrencies()
    {
        $sut = new Country('HK');
        $this->assertEquals(['HKD'], $sut->currencies());
    }


    public function testAreaCode()
    {
        $sut = new Country('HK');
        $this->assertEquals(852, $sut->areaCode());
    }


    public function testFromArray()
    {
        $sut = Country::fromArray(['some' => 'data', 'country' => 'hkg']);
        $this->assertInstanceOf(Country::class, $sut);
        $this->assertEquals('HKG', $sut->alpha3());
    }


    public function testFromArrayNoDataReturnsNull()
    {
        $sut = Country::fromArray(['some' => 'data', 'without' => 'country']);
        $this->assertNull($sut);
    }


    public function testEach()
    {
        $data = ['a' => 'hkg', 'b' => 'usa'];
        $sut = Country::each($data);
        $this->assertCount(count($data), $sut);
        foreach ($sut as $key => $country) {
            $this->assertInstanceOf(Country::class, $country);
            $this->assertEquals(strtoupper($data[$key]), $country->alpha3());
        }
    }


    public function testSetCountry()
    {
        $one = new Country('hk');
        $two = new Country('cn');
        $this->assertSame('HKG', $one->setCountry(null)->alpha3());
        $this->assertSame('CHN', $one->setCountry($two)->alpha3());
        $this->assertSame('USA', $one->setCountry('us')->alpha3());
        $this->assertSame('CHN', $one->setCountry(156)->alpha3());
    }


    public function testEquals()
    {
        $one = new Country('hk');
        $two = new Country('hkg');
        $this->assertNotSame($one, $two);
        $this->assertTrue($one->equals($two));

        $this->assertTrue($one->equals(344));
    }
}
