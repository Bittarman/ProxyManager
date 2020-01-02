<?php

declare(strict_types=1);

namespace ProxyManagerTest\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use Laminas\Code\Generator\PropertyGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicGet;
use ProxyManagerTestAsset\ClassWithMagicMethods;
use ProxyManagerTestAsset\EmptyClass;
use ReflectionClass;

/**
 * Tests for {@see \ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicGet}
 *
 * @group Coverage
 */
final class MagicGetTest extends TestCase
{
    /**
     * @covers \ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicGet::__construct
     */
    public function testBodyStructure() : void
    {
        $reflection         = new ReflectionClass(EmptyClass::class);
        $prefixInterceptors = $this->createMock(PropertyGenerator::class);
        $suffixInterceptors = $this->createMock(PropertyGenerator::class);

        $prefixInterceptors->method('getName')->willReturn('pre');
        $suffixInterceptors->method('getName')->willReturn('post');

        $magicGet = new MagicGet(
            $reflection,
            $prefixInterceptors,
            $suffixInterceptors
        );

        self::assertSame('__get', $magicGet->getName());
        self::assertCount(1, $magicGet->getParameters());
        self::assertStringMatchesFormat('%a$returnValue = & $accessor();%a', $magicGet->getBody());
    }

    /**
     * @covers \ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\MagicGet::__construct
     */
    public function testBodyStructureWithInheritedMethod() : void
    {
        $reflection         = new ReflectionClass(ClassWithMagicMethods::class);
        $prefixInterceptors = $this->createMock(PropertyGenerator::class);
        $suffixInterceptors = $this->createMock(PropertyGenerator::class);

        $prefixInterceptors->method('getName')->willReturn('pre');
        $suffixInterceptors->method('getName')->willReturn('post');

        $magicGet = new MagicGet(
            $reflection,
            $prefixInterceptors,
            $suffixInterceptors
        );

        self::assertSame('__get', $magicGet->getName());
        self::assertCount(1, $magicGet->getParameters());
        self::assertStringMatchesFormat('%a$returnValue = & parent::__get($name);%a', $magicGet->getBody());
    }
}
