<?php

namespace eidng8\Country;

use Locale;

/**
 * General language tag processing。See {@see \Locale} for reference.
 *
 * *Note* that this class does NOT perform any validation on language tags.
 * Any error in language tag may cause undetermined outcome, WITHOUT exception.
 */
class Language
{

    /**
     * regular expression used to parse language tags.
     */
    const REGEX = '/^[a-z]{2,3}(?:-[a-z]{2,})*$/i';

    /**
     * Spoken dialects' primary languages map
     *
     * @var array
     */
    protected static $SPOKEN = [
        'cdo' => 'zh',
        'cjy' => 'zh',
        'cmn' => 'zh',
        'cpx' => 'zh',
        'czh' => 'zh',
        'czo' => 'zh',
        'gan' => 'zh',
        'hak' => 'zh',
        'hsn' => 'zh',
        'mnp' => 'zh',
        'nan' => 'zh',
        'wuu' => 'zh',
        'yue' => 'zh',
        'och' => 'zh',
        'ltc' => 'zh',
        'lzh' => 'zh',
    ];

    /**
     * ISO-639, ISO-15924, and ISO-3166 subtags separated by underscore ('_')
     *
     * @var string
     */
    protected $code;

    /**
     * Subtags returned by {@see \Locale::parseLocale()}. With all variants in
     * the `variants` key, instead of `variantX` keys.
     *
     * @var array
     */
    protected $subtags = [];


    /**
     * See {@see \Locale::composeLocale()}
     *
     * @param string          $primary
     * @param string          $script
     * @param string          $region
     * @param string|string[] $variants
     * @param string|string[] $privates
     * @return static
     */
    public static function compose(
        $primary,
        $script = null,
        $region = null,
        $variants = null,
        $privates = null
    ) {
        $subtags = ['language' => $primary];

        if ($script) {
            $subtags['script'] = $script;
        }
        if ($region) {
            $subtags['region'] = $region;
        }
        if ($variants) {
            foreach ((array)$variants as $idx => $variant) {
                $subtags["variant$idx"] = $variant;
            }
        }
        if ($privates) {
            foreach ((array)$privates as $idx => $private) {
                $subtags["private$idx"] = $private;
            }
        }

        return new static(Locale::composeLocale($subtags));
    }


    /**
     * See {@see \Locale::getDefault()}
     *
     * @return static
     */
    public static function systemDefault()
    {
        return new Language(Locale::getDefault());
    }


    /**
     * See {@see \Locale::acceptFromHttp()}
     *
     * Be careful to the new found bug, told at the bottom of the official
     * page. The note states that `zh-tw` and `zh-cn` can't be properly dealt
     * with. There is a few checks in the unit test for this bug. To work
     * around this bug, parse the header yourself, and `new` an instance.
     *
     * @param string $header
     * @return static
     */
    public static function fromHttp($header = null)
    {
        return new static(
            Locale::acceptFromHttp($header ?: $_SERVER['HTTP_ACCEPT_LANGUAGE'])
        );
    }


    /**
     * Returns the primary language of the given language tag.
     *
     * @param static|string $lang
     *
     * @return string
     */
    public static function spokenPrimary($lang)
    {
        if (null === $lang) {
            return null;
        }

        if (!($lang instanceof static)) {
            $lang = new static($lang);
        }

        $primary = $lang->primary();
        if (isset(static::$SPOKEN[$primary])) {
            return static::$SPOKEN[$primary];
        }

        return $primary;
    }


    /**
     * Language constructor. See {@see set()}
     * Also see [Locale](http://php.net/manual/en/class.locale.php)
     *
     * @param string|static $code   ISO-639, ISO-15924, and ISO-3166 hyphenated
     *                              codes
     */
    public function __construct($code)
    {
        $this->set($code);
    }


    /**
     * Returns ISO-639, ISO-15924, and ISO-3166 hyphenated codes
     *
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }


    /**
     * Returns language code
     *
     * @return string
     */
    public function code()
    {
        return $this->code;
    }


    /**
     * Set language code
     *
     * @param string|static $code ISO-639, ISO-15924, and ISO-3166 hyphenated
     *                            codes
     *
     * @return static
     */
    public function set($code)
    {
        $this->code = Locale::canonicalize((string)$code);
        $this->subtags = Locale::parseLocale($this->code);
        $this->subtags['keywords'] = Locale::getKeywords($this->code);

        // re-organize variants
        $idx = 0;
        $key = "variant$idx";
        $this->subtags['variants'] = [];
        while (isset($this->subtags[$key])) {
            $this->subtags['variants'][] = $this->subtags[$key];
            unset($this->subtags[$key]);
            $idx++;
            $key = "variant$idx";
        }

        return $this;
    }


    /**
     * See {@see \Locale::getDisplayLanguage()}
     *
     * @param null|string $locale
     *
     * @return string
     */
    public function displayLanguage($locale = null)
    {
        return Locale::getDisplayLanguage($this->code, $locale ?: $this->code);
    }


    /**
     * See {@see \Locale::getDisplayRegion()}
     *
     * @param null|string $locale
     *
     * @return string
     */
    public function displayRegion($locale = null)
    {
        return Locale::getDisplayRegion($this->code, $locale ?: $this->code);
    }


    /**
     * See {@see \Locale::getDisplayScript()}
     *
     * @param null|string $locale
     *
     * @return string
     */
    public function displayScript($locale = null)
    {
        return Locale::getDisplayScript($this->code, $locale ?: $this->code);
    }


    /**
     * See {@see \Locale::getPrimaryLanguage()}
     *
     * @return string
     */
    public function primary()
    {
        return $this->subtags['language'];
    }


    /**
     * See {@see \Locale::getRegion()}
     *
     * @return string
     */
    public function region()
    {
        if (isset($this->subtags['region'])) {
            return $this->subtags['region'];
        }

        return null;
    }


    /**
     * See {@see \Locale::getScript()}
     *
     * @return string
     */
    public function script()
    {
        if (isset($this->subtags['script'])) {
            return $this->subtags['script'];
        }

        return null;
    }


    /**
     * See {@see \Locale::getKeywords()}
     *
     * @return string[]
     */
    public function keywords()
    {
        return $this->subtags['keywords'];
    }


    /**
     * See {@see \Locale::getAllVariants()}
     *
     * @return string[]
     */
    public function variants()
    {
        return $this->subtags['variants'];
    }


    /**
     * See {@see \Locale::filterMatches()}
     *
     * @param string|static $locale
     * @param bool          $canonicalize
     * @return bool
     */
    public function matches($locale)
    {
        return \Locale::filterMatches($this->code, (string)$locale, true);
    }


    /**
     * Check if the given language is same as this instance.
     * Comparison is mainly done with primary language. And if script is
     * provided in both instance, they are also checked.
     *
     * @param static|string $lang   language to be checked
     * @param bool          $spoken pass `true` to check spoken language
     *
     * @return bool
     */
    public function equals($lang, $spoken = false)
    {
        if (!($lang instanceof static)) {
            $lang = new static($lang);
        }

        // also check written script if exists
        $script = true;
        if ($lang->script() && $this->script()) {
            $script = $lang->script() == $this->script();
        }

        if ($spoken) {
            $primary =
                static::spokenPrimary($lang) == static::spokenPrimary($this);
        } else {
            $primary = $lang->primary() == $this->primary();
        }

        return $primary && $script;
    }


    /**
     * Concatenate names according to linguistic rules.
     *
     * @param string firstName
     * @param string lastName
     * @param string[] ...middleNames
     *
     * @return string
     */
    public function concatNames($firstName, $lastName, ...$middleNames)
    {
        // checks the language of the instance and concat accordingly
        $primary = static::spokenPrimary($this);
        if ('zh' == $primary) {
            return trim($lastName) . trim($firstName);
        }

        // by default we use Latin rules
        return implode(
            ' ',
            array_filter(
                array_map(
                    'trim',
                    array_merge([$firstName], $middleNames, [$lastName])
                )
            )
        );
    }
}//end class
