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

use eidng8\Country\Language;

class LanguageTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $this->assertInstanceOf(Language::class, new Language('zh'));
    }


    public function testToString()
    {
        $sut = new Language('zh-cn-hans-yue');
        $this->assertSame('zh_CN_HANS_YUE', (string)$sut);
    }


    public function testCode()
    {
        $sut = new Language('zh');
        $this->assertSame('zh', $sut->code());
    }


    public function testSet()
    {
        $sut = new Language('zh');
        $en = new Language('en');
        $this->assertSame('en', $sut->set('en')->code());
        $this->assertSame('en', $sut->set($en)->code());
    }


    public function testPrimary()
    {
        $sut = new Language('zh-cn');
        $this->assertSame('zh', $sut->primary());
    }


    public function testDisplayLanguage()
    {
        $sut = new Language('zh-cn');
        $this->assertSame('中文', $sut->displayLanguage());
        $this->assertSame('Chinese', $sut->displayLanguage('en'));
    }


    public function testRegion()
    {
        $sut = new Language('zh-cn');
        $this->assertSame('CN', $sut->region());
        $this->assertNull($sut->set('en')->region());
    }


    public function testDisplayRegion()
    {
        $sut = new Language('zh-cn');
        $this->assertSame('中国', $sut->displayRegion());
        $this->assertSame('China', $sut->displayRegion('en'));
    }


    public function testScript()
    {
        $sut = new Language('zh-hans');
        $this->assertSame('Hans', $sut->script());
    }


    public function testVariants()
    {
        $sut = new Language('zh-hans-cn-yue-goh');
        $this->assertEquals(['YUE', 'GOH'], $sut->variants());
        $this->assertEquals([], $sut->set('zh')->variants());
    }


    public function testKeywords()
    {
        $this->assertEquals(
            ['currency' => 'EUR', 'collation' => 'PHONE'],
            (new Language('de_DE@currency=EUR;collation=PHONE'))->keywords()
        );
    }


    public function testDisplayScript()
    {
        $sut = new Language('zh-hans');
        $this->assertSame('简体中文', $sut->displayScript());
        $this->assertSame('Simplified Han', $sut->displayScript('en'));
    }


    public function testFromHttp()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'zh,en-gb;q=0.8,en;q=0.7';
        $sut = Language::fromHttp();
        $this->assertSame('zh', $sut->code());

        $sut = Language::fromHttp('en-gb');
        $this->assertSame('en_GB', $sut->code());

        // The following tests are left here because of a new found bug.
        // http://php.net/manual/en/locale.acceptfromhttp.php
        // Theses assertions will fail when the bug were fixed.
        $this->assertNotSame('zh_TW', Language::fromHttp('zh-tw')->code());
        $this->assertNotSame('zh_CN', Language::fromHttp('zh-cn')->code());
        $this->assertNotSame('zh_CN', Language::fromHttp('zh_cn')->code());
        $this->assertNotSame('zh_TW', Language::fromHttp('zh_tw')->code());
    }


    public function testCompose()
    {
        $this->assertSame(
            'zh_TW',
            Language::compose('zh', null, 'tw')->code()
        );
        $this->assertSame(
            'zh_Hans_TW_YUE_X_PVT',
            Language::compose('zh', 'hans', 'tw', 'yue', 'pvt')->code()
        );
    }


    public function testSystemDefault()
    {
        $ini = ini_get('intl.default_locale');
        ini_set('intl.default_locale', 'de-de');
        $this->assertSame('de_DE', Language::systemDefault()->code());
        ini_set('intl.default_locale', $ini);
    }


    public function testSpokenPrimary()
    {
        $sut = new Language('yue');
        $this->assertSame('zh', Language::spokenPrimary($sut));
        $this->assertSame('abc', Language::spokenPrimary('abc'));
        $this->assertSame('zh', Language::spokenPrimary('yue'));
        $this->assertNull(Language::spokenPrimary(null));
    }


    public function testEquals()
    {
        $zh = new Language('zh');
        $hans = new Language('zh-hans');
        $hant = new Language('zh-hant');
        $spoken = new Language('yue');
        $this->assertTrue($zh->equals($hans));
        $this->assertTrue($hans->equals('zh-cn-hans'));
        $this->assertTrue($zh->equals($spoken, true));
        $this->assertFalse($hant->equals($hans));
    }


    public function testMatches()
    {
        $zh = new Language('zh-cn');
        $hans = new Language('zh-hans');
        $this->assertFalse($zh->matches($hans));
        $this->assertTrue($hans->matches('zh'));
    }

    public function testConcatNames()
    {
        $zh = new Language('zh');
        $this->assertSame('张三', $zh->concatNames('三', '张', '李四'));

        $en = new Language('en');
        $this->assertSame(
            'John Camus Vas Dough',
            $en->concatNames(' John', 'Dough ', ' Camus ', 'Vas ')
        );
    }
}
