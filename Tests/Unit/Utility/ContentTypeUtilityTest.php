<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Utility;

use Cundd\PersistentObjectStore\Server\ContentType;

/**
 * Tests for the ContentType Utility
 */
class ContentTypeUtilityTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function convertContentTypeToSuffixTest()
    {
        $this->assertEquals('jpg', ContentTypeUtility::convertContentTypeToSuffix(ContentType::JPEG_IMAGE));
        // The suffix 'jpg' comes first
        // $this->assertEquals('jpeg', ContentTypeUtility::convertContentTypeToSuffix(ContentType::JPEG_IMAGE));
        $this->assertEquals('png', ContentTypeUtility::convertContentTypeToSuffix(ContentType::PNG_IMAGE));
        $this->assertEquals('gif', ContentTypeUtility::convertContentTypeToSuffix(ContentType::GIF_IMAGE));
        $this->assertEquals('json', ContentTypeUtility::convertContentTypeToSuffix(ContentType::JSON_APPLICATION));
        $this->assertEquals('js', ContentTypeUtility::convertContentTypeToSuffix(ContentType::JAVASCRIPT_APPLICATION));
        $this->assertEquals('xml', ContentTypeUtility::convertContentTypeToSuffix(ContentType::XML_TEXT));
        $this->assertEquals('html', ContentTypeUtility::convertContentTypeToSuffix(ContentType::HTML_TEXT));
        $this->assertEquals('plain', ContentTypeUtility::convertContentTypeToSuffix(ContentType::PLAIN_TEXT));
        $this->assertEquals('csv', ContentTypeUtility::convertContentTypeToSuffix(ContentType::CSV_TEXT));
        $this->assertEquals('css', ContentTypeUtility::convertContentTypeToSuffix(ContentType::CSS_TEXT));
    }

    /**
     * @test
     */
    public function convertSuffixToContentTypeTest()
    {
        $this->assertEquals(ContentType::JPEG_IMAGE, ContentTypeUtility::convertSuffixToContentType('jpg'));
        $this->assertEquals(ContentType::JPEG_IMAGE, ContentTypeUtility::convertSuffixToContentType('jpeg'));
        $this->assertEquals(ContentType::PNG_IMAGE, ContentTypeUtility::convertSuffixToContentType('png'));
        $this->assertEquals(ContentType::GIF_IMAGE, ContentTypeUtility::convertSuffixToContentType('gif'));
        $this->assertEquals(ContentType::JSON_APPLICATION, ContentTypeUtility::convertSuffixToContentType('json'));
        $this->assertEquals(ContentType::JAVASCRIPT_APPLICATION, ContentTypeUtility::convertSuffixToContentType('js'));
        $this->assertEquals(ContentType::XML_TEXT, ContentTypeUtility::convertSuffixToContentType('xml'));
        $this->assertEquals(ContentType::HTML_TEXT, ContentTypeUtility::convertSuffixToContentType('html'));
        $this->assertEquals(ContentType::PLAIN_TEXT, ContentTypeUtility::convertSuffixToContentType('plain'));
        $this->assertEquals(ContentType::CSV_TEXT, ContentTypeUtility::convertSuffixToContentType('csv'));
        $this->assertEquals(ContentType::CSS_TEXT, ContentTypeUtility::convertSuffixToContentType('css'));
    }
}
 