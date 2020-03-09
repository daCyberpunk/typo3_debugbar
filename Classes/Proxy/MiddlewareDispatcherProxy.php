<?php
declare(strict_types=1);

namespace Konafets\Typo3Debugbar\Proxy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\MiddlewareDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***
 * This file is part of the "Typo3Debugbar" Extension for TYPO3 CMS.
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2020-present Falk Röder <mail@falk-roeder.de>
 ***/

/**
 * MiddlewareDispatcherProxy
 *
 * @since   2.0.0
 * @package Typo3Debugbar
 */
final class MiddlewareDispatcherProxy extends MiddlewareDispatcher
{

    /**
     * @var array
     */
    protected $added;

    /**
     * @var array
     */
    protected $middlewares;

    /**
     * Construct new MiddlewareDispatcherProxy instance.
     *
     * @param \Psr\Http\Server\RequestHandlerInterface $kernel
     * @param array                                    $middlewares
     */
    public function __construct(
        RequestHandlerInterface $kernel,
        array $middlewares = []
    ) {
        parent::__construct($kernel, $middlewares);
    }


    /**
     * Invoke the middleware stack
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->tip->handle($request);
    }

    /**
     * Add a new middleware to the stack
     *
     * Middlewares are organized as a stack. That means middlewares
     * that have been added before will be executed after the newly
     * added one (last in, first out).
     *
     * @param MiddlewareInterface $middleware
     */
    public function add(MiddlewareInterface $middleware){
        $this->tip = new RequestHandlerProxy($this->tip, get_class($middleware));
        $this->added[] = $this->tip;
        parent::add($middleware);
    }

    /**
     * Add a new middleware by class name
     *
     * Middlewares are organized as a stack. That means middlewares
     * that have been added before will be executed after the newly
     * added one (last in, first out).
     *
     * @param string $middleware
     */
    public function lazy(string $middleware)
    {
        $this->tip = new RequestHandlerProxy($this->tip, $middleware);
        $this->added[] = $this->tip;
        parent::lazy($middleware);
    }


}
