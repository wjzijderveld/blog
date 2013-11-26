<?php
/**
 * This file and its content is copyright of Beeldspraak Website Creators BV - (c) Beeldspraak 2012. All rights reserved.
 * Any redistribution or reproduction of part or all of the contents in any form is prohibited.
 * You may not, except with our express written permission, distribute or commercially exploit the content.
 *
 * @author      Beeldspraak <info@beeldspraak.com>
 * @copyright   Copyright 2012, Beeldspraak Website Creators BV
 * @link        http://beeldspraak.com
 *
 */

namespace Sculpin\Bundle\FeedBundle;


use Sculpin\Core\Converter\ConverterContextInterface;
use Sculpin\Core\Converter\ConverterInterface;
use Sculpin\Core\DataProvider\DataProviderManager;
use Sculpin\Core\Event\ConvertEvent;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedGenerator implements EventSubscriberInterface
{
    /** @var \Sculpin\Core\DataProvider\DataProviderManager  */
    private $dataProviderManager;

    /** @var  array string[] */
    private $postPaths;

    public function __construct(DataProviderManager $dataProviderManagaer, array $feedFormats)
    {
        $this->dataProviderManager = $dataProviderManagaer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            Sculpin::EVENT_AFTER_RUN => array('afterRun', 10),
        );
    }

    public function afterConvert(SourceSetEvent $afterRunEvent)
    {
        $posts = $this->dataProviderManager->dataProvider('posts')->provideData();
    }


}