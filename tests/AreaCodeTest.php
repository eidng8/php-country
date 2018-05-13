<?php
/**
 * Created by IntelliJ IDEA.
 * User: JC
 * Date: 2018-05-12
 * Time: 12:14
 */

namespace eidng8\Tests\Country;

use eidng8\Country\AreaCode;
use eidng8\Country\Country;
use PHPUnit\Framework\TestCase;

class AreaCodeTest extends TestCase
{

    public function testByCode()
    {
        $this->assertEquals(['CA', 'PR', 'US'], AreaCode::byCode(1));
        $this->assertEquals(['HK'], AreaCode::byCode(852));
        // call again to hit the cache, for coverage
        $this->assertEquals(['CA', 'PR', 'US'], AreaCode::byCode(1));
    }


    public function testByCountry()
    {
        $this->assertSame(852, AreaCode::byCountry('HK'));
        $this->assertSame(852, AreaCode::byCountry(new Country('HKG')));
    }
}
