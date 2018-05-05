<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\Tests\Core\Slot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\PublishVersionSignal;
use Novactive\eZSmartCacheClearBundle\Core\Rule\Children;
use Novactive\eZSmartCacheClearBundle\Core\Rule\Parents;
use Novactive\eZSmartCacheClearBundle\Core\Rule\Siblings;
use Novactive\eZSmartCacheClearBundle\Core\Rule\Subtree;
use Novactive\eZSmartCacheClearBundle\Core\Slot\Publish;
use eZ\Publish\SPI\Persistence\Handler;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Handler as ContentHandler;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandler;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Location;
use PHPUnit\Framework\TestCase;

class PublishTest extends TestCase
{
    private $rules = [];

    public function setUp()
    {
        $parentsRuleMock = $this->getMockBuilder(Parents::class)->setMethods(['clearCache'])->disableOriginalConstructor()->getMock();
        $childrenRuleMock = $this->getMockBuilder(Children::class)->setMethods(['clearCache'])->disableOriginalConstructor()->getMock();
        $siblingsRuleMock = $this->getMockBuilder(Siblings::class)->setMethods(['clearCache'])->disableOriginalConstructor()->getMock();
        $subtreeRuleMock = $this->getMockBuilder(Subtree::class)->setMethods(['clearCache'])->disableOriginalConstructor()->getMock();
        $this->rules = [
            'parents' => $parentsRuleMock,
            'children' => $childrenRuleMock,
            'siblings' => $siblingsRuleMock,
            'subtree' => $subtreeRuleMock
        ];
    }

    /**
     * @dataProvider providerReceive
     */
    public function testReceive($config)
    {
        $signal = $this->getMockBuilder(PublishVersionSignal::class)->getMock();
        $signal->contentId = 1;
        $signal->versionNo = 1;
        $contentMock = $this->getMockBuilder(Content::class)->getMock();
        $contentMock->versionInfo = new \stdClass();
        $contentMock->versionInfo->contentInfo = new \stdClass();
        $contentMock->versionInfo->contentInfo->contentTypeId = 1;

        $contentTypeMock = $this->getMockBuilder(Type::class)->getMock();
        $contentTypeMock->identifier = 'my_content_type';

        $locationMock = $this->getMockBuilder(Location::class)->getMock();

        $contentHandler = $this->getMockBuilder(ContentHandler::class)
                               ->getMock();
        $contentHandler->expects($this->any())->method('load')->will($this->returnValue($contentMock));

        $contentTypeHandler = $this->getMockBuilder(ContentTypeHandler::class)
                                   ->getMock();
        $contentTypeHandler->expects($this->any())->method('load')->will($this->returnValue($contentTypeMock));

        $locationHandler = $this->getMockBuilder(LocationHandler::class)
                                ->getMock();
        $locationHandler->expects($this->any())->method('loadLocationsByContent')->will($this->returnValue([$locationMock]));

        $persistenceHandler = $this->getMockBuilder(Handler::class)
                                   ->getMock();
        $persistenceHandler->expects($this->any())->method('contentHandler')->will($this->returnValue($contentHandler));
        $persistenceHandler->expects($this->any())->method('contentTypeHandler')->will($this->returnValue($contentTypeHandler));
        $persistenceHandler->expects($this->any())->method('locationHandler')->will($this->returnValue($locationHandler));

        $slot = new Publish($persistenceHandler, $config);
        foreach ($config as $contentTypeConfig) {
            if ($contentTypeConfig['content_type'] == $contentTypeMock->identifier) {
                foreach ($this->rules as $ruleName => $rule) {
                    if (isset($contentTypeConfig['rules'][$ruleName]) &&
                        isset($contentTypeConfig['rules'][$ruleName]['enabled']) &&
                        $contentTypeConfig['rules'][$ruleName]['enabled'] === true
                    ) {
                        $this->rules[$ruleName]->expects($this->once())->method('clearCache');
                    } else {
                        $this->rules[$ruleName]->expects($this->never())->method('clearCache');
                    }
                }
            } else {
                foreach ($this->rules as $ruleName => $rule) {
                    $this->rules[$ruleName]->expects($this->never())->method('clearCache');
                }
            }
        }
        foreach ($this->rules as $ruleName => $rule) {
            $slot->addCacheClearRule($rule, $ruleName);
        }

        $slot->receive($signal);
    }

    public function providerReceive()
    {
        return [
            [[['content_type' => 'my_content_type', 'rules' => ['parents' => ['enabled' => true]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['parents' => ['enabled' => false]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['children' => ['enabled' => true]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['children' => ['enabled' => false]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['siblings' => ['enabled' => true]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['siblings' => ['enabled' => false]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['subtree' => ['enabled' => true]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['subtree' => ['enabled' => false]]]]],
            [[['content_type' => 'my_content_type', 'rules' => ['parents' => ['enabled' => true], 'children' => ['enabled' => true], 'siblings' => ['enabled' => true], 'subtree' => ['enabled' => true]]]]],
            [[['content_type' => 'my_content_type_2', 'rules' => ['parents' => ['enabled' => true], 'children' => ['enabled' => true], 'siblings' => ['enabled' => true], 'subtree' => ['enabled' => true]]]]],
        ];
    }
}
