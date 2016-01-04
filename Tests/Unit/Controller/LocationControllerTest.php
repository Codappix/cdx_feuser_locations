<?php
namespace WebVision\WvFeuserLocations\Tests\Unit\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class LocationControllerTest extends UnitTestCase
{
    /**
     * @var WebVision\WvFeuserLocations\Domain\Repository\LocationRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\WebVision\WvFeuserLocations\Domain\Repository\LocationRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository->expects($this->once())
            ->method('findAll')
            ->will(static::returnValue(['someKey' => 'someValue']));
    }

    /**
     * @test
     */
    public function indexActionWillProvideAllLocations()
    {
        $controller = $this->getMockBuilder('\WebVision\WvFeuserLocations\Controller\LocationController')
            ->setMethods(['assignVariablesToView'])
            ->getMock();
        $controller->injectRepository($this->repository);

        // Check whether method is called with test data.
        $controller->expects($this->once())
            ->method('assignVariablesToView')
            ->with(['locations' => ['someKey' => 'someValue']]);

        $controller->indexAction();
    }

    /**
     * @test
     */
    public function indexActionWillTriggerSignalWithAllLocations()
    {
        $signalSlotDispatcher = $this->getMockBuilder('\TYPO3\CMS\Extbase\SignalSlot\Dispatcher')
            ->setMethods(['dispatch'])
            ->getMock();
        $view = $this->getMockBuilder('\TYPO3\CMS\Fluid\View\TemplateView')
            ->disableOriginalConstructor()
            ->setMethods(['assignMultiple'])
            ->getMock();
        $controller = $this->getMockBuilder('\WebVision\WvFeuserLocations\Controller\LocationController')
            ->setMockClassName('ControllerMock')
            ->setMethods(null)
            ->getMock();
        $controller->injectRepository($this->repository);
        $controller->injectSignalSlotDispatcher($signalSlotDispatcher);
        $controller->setView($view);

        $templateVariables = [
            'extendedVariables' => [],
            'locations' => ['someKey' => 'someValue'],
        ];

        // Check whether method is called with test data.
        $signalSlotDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                'ControllerMock',
                'assignVariables.indexAction',
                $templateVariables
            )
            ->will(static::returnValue($templateVariables));

        // Check whether method is called with test data.
        $view->expects($this->once())
            ->method('assignMultiple')
            ->with($templateVariables);

        $controller->indexAction();
    }
}
