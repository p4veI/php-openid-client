<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\SelfSignedTLSClientAuth;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class SelfSignedTLSClientAuthTest extends TestCase
{
    public function testGetSupportedMethod(): void
    {
        $auth = new SelfSignedTLSClientAuth();
        static::assertSame('self_signed_tls_client_auth', $auth->getSupportedMethod());
    }

    public function testCreateRequest(): void
    {
        $auth = new SelfSignedTLSClientAuth();

        $stream = $this->prophesize(StreamInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client->getMetadata()->willReturn($metadata->reveal());
        $metadata->getClientId()->willReturn('foo');
        $metadata->getClientSecret()->shouldNotBeCalled();

        $stream->write('foo=bar&client_id=foo')->shouldBeCalled();

        $request->getBody()->willReturn($stream->reveal());

        $result = $auth->createRequest(
            $request->reveal(),
            $client->reveal(),
            ['foo' => 'bar']
        );

        static::assertSame($request->reveal(), $result);
    }
}
