<?php

namespace Tests\Unit\Rules;

use App\Rules\GenresHasCategoriesRule;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use Tests\TestCase;

class GenreHasCategoriesRuleUnitTest extends TestCase
{
  public function testCategoriesIdField()
  {
    $rule = new GenresHasCategoriesRule([1, 2, 2, 1]);
    $reflectionClass = new ReflectionClass(GenresHasCategoriesRule::class);
    $reflectionProperty = $reflectionClass->getProperty('categoriesId');
    $reflectionProperty->setAccessible(true);
    $categoriesId = $reflectionProperty->getValue($rule);
    $this->assertEqualsCanonicalizing([1, 2], $categoriesId);
  }

  public function testGenresIdValue()
  {
    $rule = $this->createRuleMock([]);
    $rule->shouldReceive('getRows')->withAnyArgs()->andReturnNull();

    $rule->passes('', [1, 1, 2, 2]);
    $reflectionClass = new ReflectionClass(GenresHasCategoriesRule::class);
    $reflectionProperty = $reflectionClass->getProperty('genresId');
    $reflectionProperty->setAccessible(true);
    $genresId = $reflectionProperty->getValue($rule);
    $this->assertEqualsCanonicalizing([1, 2], $genresId);
  }

  public function testPassesReturnsFalseWhenCategoriesOrGenresIsArrayEmpity()
  {
    $rule = $this->createRuleMock([1]);
    $this->assertFalse($rule->passes('', []));

    $rule = $this->createRuleMock([]);
    $this->assertFalse($rule->passes('', [1]));
  }

  public function tesPassesReturnsFalseWhenGetRowsIsEmpty()
  {
    $rule = $this->createRuleMock([1]);
    $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect(''));
    $this->assertFalse($rule->passes('', [1]));
  }

  public function testPassesReturnsFalseWhenHasCategoriesWithoutGenres()
  {
    $rule = $this->createRuleMock([1, 2]);

    $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect(['category_id' => 1]));

    $this->assertFalse($rule->passes('', [1]));
  }

  public function testPassesIsValid()
  {
    $rule = $this->createRuleMock([1, 2]);
    $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect([
      ['category_id' => 1],
      ['category_id' => 2],

    ]));

    $this->assertTrue($rule->passes('', [1]));
  }

  public function createRuleMock(array $categoriesId): MockInterface
  {
    return Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId])->makePartial()->shouldAllowMockingProtectedMethods();
  }
}
