<?php
declare(strict_types=1);

namespace Konafets\Typo3Debugbar\Proxy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
 * RequestHandlerProxy
 *
 * @since   2.0.0
 * @package Typo3Debugbar
 */
final class RequestHandlerProxy implements RequestHandlerInterface
{
    /**
     * @var \Psr\Http\Server\RequestHandlerInterface
     */
    protected $tip;

    /**
     * @var string
     */
    protected $middleware;

    /**
     * Construct new RequestHandlerProxy instance.
     *
     * @param \Psr\Http\Server\RequestHandlerInterface $tip
     * @param string                                   $middleware
     */
    public function __construct(RequestHandlerInterface $tip, string $middleware) {
        $this->tip = $tip;
        $this->middleware = $middleware;
    }

    /**
     * getNext
     *
     * @return
     */
    public function getNext() {
        return $this->tip;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->tip->handle($request);

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(
//[
//    $this->middleware
//],' | RequestHandlerProxy:'.__LINE__ .' in '. __FILE__, 20 );

        return $response;
    }
}
