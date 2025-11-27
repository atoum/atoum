<?php

namespace atoum\atoum\mock;

use atoum\atoum\adapter;
use atoum\atoum\exceptions;

class stream
{
    public const defaultProtocol = 'atoum';
    public const protocolSeparator = '://';

    public $context;

    protected ?stream\controller $streamController = null;

    protected static ?adapter $adapter = null;
    protected static array $streams = [];
    protected static array $protocols = [];

    public function __call(string $method, array $arguments): mixed
    {
        return call_user_func_array([$this->setControllerForMethod($method, $arguments)->streamController, $method], $arguments);
    }

    public static function getAdapter(): adapter
    {
        return (static::$adapter = static::$adapter ?: new adapter());
    }

    public static function setAdapter(adapter $adapter): void
    {
        static::$adapter = $adapter;
    }

    public static function get(?string $name = null): stream\controller
    {
        $name = static::setDirectorySeparator($name ?: uniqid());

        $adapter = static::getAdapter();

        if (($protocol = static::getProtocol($name)) === null) {
            $protocol = static::defaultProtocol;
            $name = $protocol . static::protocolSeparator . $name;
        }

        if (in_array($protocol, $adapter->stream_get_wrappers()) === false && $adapter->stream_wrapper_register($protocol, get_called_class(), 0) === false) {
            throw new exceptions\runtime('Unable to register ' . $protocol . ' stream');
        }

        $stream = static::findControllerForStream($name);

        if ($stream === null) {
            static::$streams[] = $stream = static::getController($name);
        }

        return $stream;
    }

    public static function getSubStream(stream\controller $controller, ?string $stream = null): stream\controller
    {
        return static::get($controller . DIRECTORY_SEPARATOR . static::setDirectorySeparator($stream ?: uniqid()));
    }

    public static function getProtocol(string $stream): ?string
    {
        $scheme = null;

        $schemeSeparator = strpos($stream, static::protocolSeparator);

        if ($schemeSeparator !== false) {
            $scheme = substr($stream, 0, $schemeSeparator);
        }

        return $scheme;
    }

    public static function setDirectorySeparator($stream, $directorySeparator = DIRECTORY_SEPARATOR)
    {
        $path = str_replace(($directorySeparator == '/' ? '\\' : '/'), $directorySeparator, preg_replace('#^[^:]+://#', '', $stream));

        return substr($stream, 0, strlen($stream) - strlen($path)) . $path;
    }

    protected function setControllerForMethod(string $method, array $arguments): static
    {
        switch (strtolower($method)) {
            case 'dir_opendir':
            case 'mkdir':
            case 'rename':
            case 'rmdir':
            case 'stream_metadata':
            case 'stream_open':
            case 'unlink':
            case 'url_stat':
            case 'stat':
                $this->streamController = static::getControllerFromArguments($method, $arguments)->duplicate();
                break;
        }

        return $this;
    }

    protected static function getController(string $stream): stream\controller
    {
        return new stream\controller($stream);
    }

    protected static function getControllerFromArguments($method, array $arguments)
    {
        if (isset($arguments[0]) === false) {
            throw new exceptions\logic('Argument 0 is undefined for function ' . $method . '()');
        }

        $stream = static::findControllerForStream(static::setDirectorySeparator($arguments[0]));

        if ($stream === null) {
            $stream = static::get($arguments[0]);
        }

        return $stream;
    }

    protected static function findControllerForStream($path)
    {
        foreach (static::$streams as $stream) {
            if ($stream->getPath() === $path) {
                return $stream;
            }
        }

        return null;
    }
}
