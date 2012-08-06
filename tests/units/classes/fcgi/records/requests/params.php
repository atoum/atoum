<?php

namespace mageekguy\atoum\tests\units\fcgi\records\requests;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi\records\requests\params as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class params extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\fcgi\records\request');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::type)->isEqualTo(4)
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass())
			->then
				->string($record->getType())->isEqualTo(testedClass::type)
				->array($record->getValues())->isEmpty()
		;
	}

	public function test__set()
	{
		$this
			->if($record = new testedClass())
			->and($record->AUTH_TYPE = $authType = uniqid())
			->then
				->string($record->AUTH_TYPE)->isEqualTo($authType)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType
					)
				)
			->if($record->AUTH_TYPE = $authType = uniqid())
			->then
				->string($record->auth_type)->isEqualTo($authType)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType
					)
				)
			->if($record->CONTENT_LENGTH = $contentLength = uniqid())
			->then
				->string($record->CONTENT_LENGTH)->isEqualTo($contentLength)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength
					)
				)
			->if($record->content_length = $contentLength = uniqid())
			->then
				->string($record->content_length)->isEqualTo($contentLength)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength
					)
				)
			->if($record->CONTENT_TYPE = $contentType = uniqid())
			->then
				->string($record->CONTENT_TYPE)->isEqualTo($contentType)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType
					)
				)
			->if($record->content_type = $contentType = uniqid())
			->then
				->string($record->content_length)->isEqualTo($contentLength)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType
					)
				)
			->if($record->GATEWAY_INTERFACE = $gatewayInterface = uniqid())
			->then
				->string($record->GATEWAY_INTERFACE)->isEqualTo($gatewayInterface)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface
					)
				)
			->if($record->gateway_interface = $gatewayInterface = uniqid())
			->then
				->string($record->gateway_interface)->isEqualTo($gatewayInterface)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface
					)
				)
			->if($record->PATH_INFO = $pathInfo = uniqid())
			->then
				->string($record->PATH_INFO)->isEqualTo($pathInfo)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo
					)
				)
			->if($record->path_info = $pathInfo = uniqid())
			->then
				->string($record->path_info)->isEqualTo($pathInfo)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo
					)
				)
			->if($record->PATH_TRANSLATED = $pathTranslated = uniqid())
			->then
				->string($record->PATH_TRANSLATED)->isEqualTo($pathTranslated)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated
					)
				)
			->if($record->path_translated = $pathTranslated = uniqid())
			->then
				->string($record->path_translated)->isEqualTo($pathTranslated)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated
					)
				)
			->if($record->QUERY_STRING = $queryString = uniqid())
			->then
				->string($record->QUERY_STRING)->isEqualTo($queryString)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString
					)
				)
			->if($record->query_string = $queryString = uniqid())
			->then
				->string($record->query_string)->isEqualTo($queryString)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString
					)
				)
			->if($record->REMOTE_ADDR = $remoteAddr = uniqid())
			->then
				->string($record->REMOTE_ADDR)->isEqualTo($remoteAddr)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr
					)
				)
			->if($record->remote_addr = $remoteAddr = uniqid())
			->then
				->string($record->remote_addr)->isEqualTo($remoteAddr)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr
					)
				)
			->if($record->REMOTE_HOST = $remoteHost = uniqid())
			->then
				->string($record->REMOTE_HOST)->isEqualTo($remoteHost)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost
					)
				)
			->if($record->remote_host = $remoteHost = uniqid())
			->then
				->string($record->remote_host)->isEqualTo($remoteHost)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost
					)
				)
			->if($record->REMOTE_IDENT = $remoteIdent = uniqid())
			->then
				->string($record->REMOTE_IDENT)->isEqualTo($remoteIdent)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent
					)
				)
			->if($record->remote_ident = $remoteIdent = uniqid())
			->then
				->string($record->remote_ident)->isEqualTo($remoteIdent)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent
					)
				)
			->if($record->REMOTE_USER = $remoteUser = uniqid())
			->then
				->string($record->REMOTE_USER)->isEqualTo($remoteUser)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser
					)
				)
			->if($record->remote_user = $remoteUser = uniqid())
			->then
				->string($record->remote_user)->isEqualTo($remoteUser)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser
					)
				)
			->if($record->REQUEST_METHOD = $requestMethod = uniqid())
			->then
				->string($record->REQUEST_METHOD)->isEqualTo($requestMethod)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod
					)
				)
			->if($record->request_method = $requestMethod = uniqid())
			->then
				->string($record->request_method)->isEqualTo($requestMethod)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod
					)
				)
			->if($record->SCRIPT_NAME = $scriptName = uniqid())
			->then
				->string($record->SCRIPT_NAME)->isEqualTo($scriptName)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName
					)
				)
			->if($record->script_name = $scriptName = uniqid())
			->then
				->string($record->script_name)->isEqualTo($scriptName)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName
					)
				)
			->if($record->SCRIPT_FILENAME = $scriptFileName = uniqid())
			->then
				->string($record->SCRIPT_FILENAME)->isEqualTo($scriptFileName)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName
					)
				)
			->if($record->script_filename = $scriptFileName = uniqid())
			->then
				->string($record->script_filename)->isEqualTo($scriptFileName)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName
					)
				)
			->if($record->SERVER_NAME = $serverName = uniqid())
			->then
				->string($record->SERVER_NAME)->isEqualTo($serverName)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName
					)
				)
			->if($record->server_name = $serverName = uniqid())
			->then
				->string($record->server_name)->isEqualTo($serverName)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName
					)
				)
			->if($record->SERVER_PORT = $serverPort = uniqid())
			->then
				->string($record->SERVER_PORT)->isEqualTo($serverPort)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName,
						'SERVER_PORT' => $serverPort
					)
				)
			->if($record->server_port = $serverPort = uniqid())
			->then
				->string($record->server_port)->isEqualTo($serverPort)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName,
						'SERVER_PORT' => $serverPort
					)
				)
			->if($record->SERVER_PROTOCOL = $serverProtocol = uniqid())
			->then
				->string($record->SERVER_PROTOCOL)->isEqualTo($serverProtocol)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName,
						'SERVER_PORT' => $serverPort,
						'SERVER_PROTOCOL' => $serverProtocol
					)
				)
			->if($record->server_protocol = $serverProtocol = uniqid())
			->then
				->string($record->server_protocol)->isEqualTo($serverProtocol)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName,
						'SERVER_PORT' => $serverPort,
						'SERVER_PROTOCOL' => $serverProtocol
					)
				)
			->if($record->SERVER_SOFTWARE = $serverSoftware = uniqid())
			->then
				->string($record->SERVER_SOFTWARE)->isEqualTo($serverSoftware)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName,
						'SERVER_PORT' => $serverPort,
						'SERVER_PROTOCOL' => $serverProtocol,
						'SERVER_SOFTWARE' => $serverSoftware
					)
				)
			->if($record->server_software = $serverSoftware = uniqid())
			->then
				->string($record->server_software)->isEqualTo($serverSoftware)
				->array($record->getValues())->isEqualTo(array(
						'AUTH_TYPE' => $authType,
						'CONTENT_LENGTH' => $contentLength,
						'CONTENT_TYPE' => $contentType,
						'GATEWAY_INTERFACE' => $gatewayInterface,
						'PATH_INFO' => $pathInfo,
						'PATH_TRANSLATED' => $pathTranslated,
						'QUERY_STRING' => $queryString,
						'REMOTE_ADDR' => $remoteAddr,
						'REMOTE_HOST' => $remoteHost,
						'REMOTE_IDENT' => $remoteIdent,
						'REMOTE_USER' => $remoteUser,
						'REQUEST_METHOD' => $requestMethod,
						'SCRIPT_NAME' => $scriptName,
						'SCRIPT_FILENAME' => $scriptFileName,
						'SERVER_NAME' => $serverName,
						'SERVER_PORT' => $serverPort,
						'SERVER_PROTOCOL' => $serverProtocol,
						'SERVER_SOFTWARE' => $serverSoftware
					)
				)
			->exception(function() use ($record, & $value) { $record->{$value = uniqid()} = uniqid(); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Value \'' . $value . '\' is unknown')
		;
	}

	public function test__get()
	{
		$this
			->if($record = new testedClass())
			->then
				->variable($record->AUTH_TYPE)->isNull()
			->if($record->AUTH_TYPE = $authType = uniqid())
			->then
				->string($record->AUTH_TYPE)->isEqualTo($authType)
			->exception(function() use ($record, & $value) { $record->{$value = uniqid()}; })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Value \'' . $value . '\' is unknown')
		;
	}

	public function test__isset()
	{
		$this
			->if($record = new testedClass())
			->then
				->boolean(isset($record->AUTH_TYPE))->isFalse()
			->if($record->AUTH_TYPE = $authType = uniqid())
			->then
				->boolean(isset($record->AUTH_TYPE))->isTrue()
			->exception(function() use ($record, & $value) { isset($record->{$value = uniqid()}); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Value \'' . $value . '\' is unknown')
		;
	}

	public function test__unset()
	{
		$this
			->if($record = new testedClass())
			->when(function() use ($record) { unset($record->AUTH_TYPE); })
			->then
				->boolean(isset($record->AUTH_TYPE))->isFalse()
			->if($record->AUTH_TYPE = $authType = uniqid())
			->when(function() use ($record) { unset($record->AUTH_TYPE); })
			->then
				->boolean(isset($record->AUTH_TYPE))->isFalse()
			->exception(function() use ($record, & $value) { unset($record->{$value = uniqid()}); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Value \'' . $value . '\' is unknown')
		;
	}

	public function testGetContentData()
	{
		$this
			->if($record = new testedClass())
			->then
				->string($record->getContentData())->isEmpty()
			->if($record->AUTH_TYPE = $authType = uniqid())
			->then
				->string($record->getContentData())->isEqualTo("\t\rAUTH_TYPE" . $authType)
			->if($record->CONTENT_LENGTH = $contentLength = uniqid())
			->then
				->string($record->getContentData())->isEqualTo("\t\rAUTH_TYPE" . $authType . "\016\rCONTENT_LENGTH" . $contentLength)
		;
	}
}
