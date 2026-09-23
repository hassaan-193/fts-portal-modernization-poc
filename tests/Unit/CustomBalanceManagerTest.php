<?php

namespace Tests\Unit;

use App\Services\CustomBalanceManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Tests\TestCase;

class CustomBalanceManagerTest extends TestCase
{
    /** @var \Mockery\MockInterface|Connection */
    protected $dbMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = \Mockery::mock(Connection::class);

        config([
            'balance.accountTable' => 'accounts',
            'balance.transactionTable' => 'transactions',
            'balance.extraAccountLinkAttribute' => 'extra_link',
            'balance.dataAttribute' => 'data',
            'balance.accountBalanceAttribute' => 'current_balance',
        ]);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_constructs_and_loads_configuration_properties()
    {
        $manager = new CustomBalanceManager($this->dbMock);

        $refClass = new \ReflectionClass($manager);

        $accProp = $refClass->getProperty('accountTable');
        $accProp->setAccessible(true);
        $this->assertEquals('accounts', $accProp->getValue($manager));

        $txProp = $refClass->getProperty('transactionTable');
        $txProp->setAccessible(true);
        $this->assertEquals('transactions', $txProp->getValue($manager));

        $balProp = $refClass->getProperty('accountBalanceAttribute');
        $balProp->setAccessible(true);
        $this->assertEquals('current_balance', $balProp->getValue($manager));
    }

    /** @test */
    public function it_creates_transaction_with_explicit_references()
    {
        $manager = new CustomBalanceManager($this->dbMock);

        $schemaMock = \Mockery::mock(SchemaBuilder::class);
        $schemaMock->shouldReceive('getColumnListing')
            ->with('transactions')
            ->andReturn(['id', 'amount', 'reference_type', 'reference_id', 'data']);

        $queryBuilderMock = \Mockery::mock(Builder::class);
        $queryBuilderMock->shouldReceive('insertGetId')
            ->once()
            ->with(\Mockery::on(function ($attrs) {
                return $attrs['amount'] === 500
                    && $attrs['reference_type'] === 'App\\Models\\Invoice'
                    && $attrs['reference_id'] === 42;
            }))
            ->andReturn(999);

        $this->dbMock->shouldReceive('getSchemaBuilder')->andReturn($schemaMock);
        $this->dbMock->shouldReceive('table')->with('transactions')->andReturn($queryBuilderMock);

        $method = new \ReflectionMethod(CustomBalanceManager::class, 'createTransaction');
        $method->setAccessible(true);

        $result = $method->invoke($manager, [
            'amount' => 500,
            'reference_type' => 'App\\Models\\Invoice',
            'reference_id' => 42,
        ]);

        $this->assertEquals(999, $result);
    }

    /** @test */
    public function it_creates_transaction_when_references_are_null()
    {
        $manager = new CustomBalanceManager($this->dbMock);

        $schemaMock = \Mockery::mock(SchemaBuilder::class);
        $schemaMock->shouldReceive('getColumnListing')
            ->with('transactions')
            ->andReturn(['id', 'amount', 'reference_type', 'reference_id', 'data']);

        $queryBuilderMock = \Mockery::mock(Builder::class);
        $queryBuilderMock->shouldReceive('insertGetId')
            ->once()
            ->with(\Mockery::on(function ($attrs) {
                return $attrs['amount'] === 100
                    && $attrs['reference_type'] === null
                    && $attrs['reference_id'] === null;
            }))
            ->andReturn(1000);

        $this->dbMock->shouldReceive('getSchemaBuilder')->andReturn($schemaMock);
        $this->dbMock->shouldReceive('table')->with('transactions')->andReturn($queryBuilderMock);

        $method = new \ReflectionMethod(CustomBalanceManager::class, 'createTransaction');
        $method->setAccessible(true);

        $result = $method->invoke($manager, [
            'amount' => 100,
        ]);

        $this->assertEquals(1000, $result);
    }
}
