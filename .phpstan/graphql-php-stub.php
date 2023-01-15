<?php

namespace GraphQL\Experimental\Executor;

/**
 * @internal
 */
interface Runtime
{
    /**
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull $type
     */
    public function evaluate(\GraphQL\Language\AST\ValueNode $valueNode, \GraphQL\Type\Definition\InputType $type);
    public function addError($error);
}
namespace GraphQL\Executor;

interface ExecutorImplementation
{
    /**
     * Returns promise of {@link ExecutionResult}. Promise should always resolve, never reject.
     */
    public function doExecute() : \GraphQL\Executor\Promise\Promise;
}
namespace GraphQL\Experimental\Executor;

class CoroutineExecutor implements \GraphQL\Experimental\Executor\Runtime, \GraphQL\Executor\ExecutorImplementation
{
    /** @var object */
    private static $undefined;
    /** @var Schema */
    private $schema;
    /** @var callable */
    private $fieldResolver;
    /** @var PromiseAdapter */
    private $promiseAdapter;
    /** @var mixed|null */
    private $rootValue;
    /** @var mixed|null */
    private $contextValue;
    /** @var mixed|null */
    private $rawVariableValues;
    /** @var mixed|null */
    private $variableValues;
    /** @var DocumentNode */
    private $documentNode;
    /** @var string|null */
    private $operationName;
    /** @var Collector|null */
    private $collector;
    /** @var array<Error> */
    private $errors;
    /** @var SplQueue */
    private $queue;
    /** @var SplQueue */
    private $schedule;
    /** @var stdClass|null */
    private $rootResult;
    /** @var int|null */
    private $pending;
    /** @var callable */
    private $doResolve;
    public function __construct(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentNode, $rootValue, $contextValue, $rawVariableValues, ?string $operationName, callable $fieldResolver)
    {
    }
    public static function create(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentNode, $rootValue, $contextValue, $variableValues, ?string $operationName, callable $fieldResolver)
    {
    }
    private static function resultToArray($value, $emptyObjectAsStdClass = true)
    {
    }
    public function doExecute() : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @param object|null $value
     * @param Error[]     $errors
     */
    private function finishExecute($value, array $errors) : \GraphQL\Executor\ExecutionResult
    {
    }
    /**
     * @internal
     *
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull $type
     */
    public function evaluate(\GraphQL\Language\AST\ValueNode $valueNode, \GraphQL\Type\Definition\InputType $type)
    {
    }
    /**
     * @internal
     */
    public function addError($error)
    {
    }
    private function run()
    {
    }
    private function done()
    {
    }
    private function spawn(\GraphQL\Experimental\Executor\CoroutineContext $ctx)
    {
    }
    private function findFieldDefinition(\GraphQL\Experimental\Executor\CoroutineContext $ctx)
    {
    }
    /**
     * @param mixed    $value
     * @param string[] $path
     * @param mixed    $returnValue
     */
    private function completeValueFast(\GraphQL\Experimental\Executor\CoroutineContext $ctx, \GraphQL\Type\Definition\Type $type, $value, array $path, &$returnValue) : bool
    {
    }
    /**
     * @param mixed         $value
     * @param string[]      $path
     * @param string[]|null $nullFence
     *
     * @return mixed
     */
    private function completeValue(\GraphQL\Experimental\Executor\CoroutineContext $ctx, \GraphQL\Type\Definition\Type $type, $value, array $path, ?array $nullFence)
    {
    }
    private function mergeSelectionSets(\GraphQL\Experimental\Executor\CoroutineContext $ctx)
    {
    }
    /**
     * @param InterfaceType|UnionType $abstractType
     *
     * @return Generator|ObjectType|Type|null
     */
    private function resolveTypeSlow(\GraphQL\Experimental\Executor\CoroutineContext $ctx, $value, \GraphQL\Type\Definition\AbstractType $abstractType)
    {
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isPromise($value)
    {
    }
}
/**
 * @internal
 */
class CoroutineContext
{
    /** @var CoroutineContextShared */
    public $shared;
    /** @var ObjectType */
    public $type;
    /** @var mixed */
    public $value;
    /** @var object */
    public $result;
    /** @var string[] */
    public $path;
    /** @var ResolveInfo */
    public $resolveInfo;
    /** @var string[]|null */
    public $nullFence;
    /**
     * @param mixed         $value
     * @param object        $result
     * @param string[]      $path
     * @param string[]|null $nullFence
     */
    public function __construct(\GraphQL\Experimental\Executor\CoroutineContextShared $shared, \GraphQL\Type\Definition\ObjectType $type, $value, $result, array $path, ?array $nullFence = null)
    {
    }
}
/**
 * @internal
 */
class Collector
{
    /** @var Schema */
    private $schema;
    /** @var Runtime */
    private $runtime;
    /** @var OperationDefinitionNode|null */
    public $operation = null;
    /** @var FragmentDefinitionNode[] */
    public $fragments = [];
    /** @var ObjectType|null */
    public $rootType;
    /** @var FieldNode[][] */
    private $fields;
    /** @var array<string, bool> */
    private $visitedFragments;
    public function __construct(\GraphQL\Type\Schema $schema, \GraphQL\Experimental\Executor\Runtime $runtime)
    {
    }
    public function initialize(\GraphQL\Language\AST\DocumentNode $documentNode, ?string $operationName = null)
    {
    }
    /**
     * @return Generator
     */
    public function collectFields(\GraphQL\Type\Definition\ObjectType $runtimeType, ?\GraphQL\Language\AST\SelectionSetNode $selectionSet)
    {
    }
    private function doCollectFields(\GraphQL\Type\Definition\ObjectType $runtimeType, ?\GraphQL\Language\AST\SelectionSetNode $selectionSet)
    {
    }
}
/**
 * @internal
 */
class Strand
{
    /** @var Generator */
    public $current;
    /** @var Generator[] */
    public $stack;
    /** @var int */
    public $depth;
    /** @var bool|null */
    public $success;
    /** @var mixed */
    public $value;
    public function __construct(\Generator $coroutine)
    {
    }
}
/**
 * @internal
 */
class CoroutineContextShared
{
    /** @var FieldNode[] */
    public $fieldNodes;
    /** @var string */
    public $fieldName;
    /** @var string */
    public $resultName;
    /** @var ValueNode[]|null */
    public $argumentValueMap;
    /** @var SelectionSetNode|null */
    public $mergedSelectionSet;
    /** @var ObjectType|null */
    public $typeGuard1;
    /** @var callable|null */
    public $resolveIfType1;
    /** @var mixed */
    public $argumentsIfType1;
    /** @var ResolveInfo|null */
    public $resolveInfoIfType1;
    /** @var ObjectType|null */
    public $typeGuard2;
    /** @var CoroutineContext[]|null */
    public $childContextsIfType2;
    /**
     * @param FieldNode[]  $fieldNodes
     * @param mixed[]|null $argumentValueMap
     */
    public function __construct(array $fieldNodes, string $fieldName, string $resultName, ?array $argumentValueMap)
    {
    }
}
namespace GraphQL\Validator;

/**
 * Implements the "Validation" section of the spec.
 *
 * Validation runs synchronously, returning an array of encountered errors, or
 * an empty array if no errors were encountered and the document is valid.
 *
 * A list of specific validation rules may be provided. If not provided, the
 * default list of rules defined by the GraphQL specification will be used.
 *
 * Each validation rule is an instance of GraphQL\Validator\Rules\ValidationRule
 * which returns a visitor (see the [GraphQL\Language\Visitor API](reference.md#graphqllanguagevisitor)).
 *
 * Visitor methods are expected to return an instance of [GraphQL\Error\Error](reference.md#graphqlerrorerror),
 * or array of such instances when invalid.
 *
 * Optionally a custom TypeInfo instance may be provided. If not provided, one
 * will be created from the provided schema.
 */
class DocumentValidator
{
    /** @var ValidationRule[] */
    private static $rules = [];
    /** @var ValidationRule[]|null */
    private static $defaultRules;
    /** @var QuerySecurityRule[]|null */
    private static $securityRules;
    /** @var ValidationRule[]|null */
    private static $sdlRules;
    /** @var bool */
    private static $initRules = false;
    /**
     * Primary method for query validation. See class description for details.
     *
     * @param ValidationRule[]|null $rules
     *
     * @return Error[]
     *
     * @api
     */
    public static function validate(\GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $ast, ?array $rules = null, ?\GraphQL\Utils\TypeInfo $typeInfo = null)
    {
    }
    /**
     * Returns all global validation rules.
     *
     * @return ValidationRule[]
     *
     * @api
     */
    public static function allRules()
    {
    }
    public static function defaultRules()
    {
    }
    /**
     * @return QuerySecurityRule[]
     */
    public static function securityRules()
    {
    }
    public static function sdlRules()
    {
    }
    /**
     * This uses a specialized visitor which runs multiple visitors in parallel,
     * while maintaining the visitor skip and break API.
     *
     * @param ValidationRule[] $rules
     *
     * @return Error[]
     */
    public static function visitUsingRules(\GraphQL\Type\Schema $schema, \GraphQL\Utils\TypeInfo $typeInfo, \GraphQL\Language\AST\DocumentNode $documentNode, array $rules)
    {
    }
    /**
     * Returns global validation rule by name. Standard rules are named by class name, so
     * example usage for such rules:
     *
     * $rule = DocumentValidator::getRule(GraphQL\Validator\Rules\QueryComplexity::class);
     *
     * @param string $name
     *
     * @return ValidationRule
     *
     * @api
     */
    public static function getRule($name)
    {
    }
    /**
     * Add rule to list of global validation rules
     *
     * @api
     */
    public static function addRule(\GraphQL\Validator\Rules\ValidationRule $rule)
    {
    }
    public static function isError($value)
    {
    }
    public static function append(&$arr, $items)
    {
    }
    /**
     * Utility which determines if a value literal node is valid for an input type.
     *
     * Deprecated. Rely on validation for documents co
     * ntaining literal values.
     *
     * @deprecated
     *
     * @return Error[]
     */
    public static function isValidLiteralValue(\GraphQL\Type\Definition\Type $type, $valueNode)
    {
    }
    /**
     * @param ValidationRule[]|null $rules
     *
     * @return Error[]
     *
     * @throws Exception
     */
    public static function validateSDL(\GraphQL\Language\AST\DocumentNode $documentAST, ?\GraphQL\Type\Schema $schemaToExtend = null, ?array $rules = null)
    {
    }
    public static function assertValidSDL(\GraphQL\Language\AST\DocumentNode $documentAST)
    {
    }
    public static function assertValidSDLExtension(\GraphQL\Language\AST\DocumentNode $documentAST, \GraphQL\Type\Schema $schema)
    {
    }
    /**
     * @param Error[] $errors
     */
    private static function combineErrorMessages(array $errors) : string
    {
    }
}
abstract class ASTValidationContext
{
    /** @var DocumentNode */
    protected $ast;
    /** @var Error[] */
    protected $errors;
    /** @var Schema */
    protected $schema;
    public function __construct(\GraphQL\Language\AST\DocumentNode $ast, ?\GraphQL\Type\Schema $schema = null)
    {
    }
    public function reportError(\GraphQL\Error\Error $error)
    {
    }
    /**
     * @return Error[]
     */
    public function getErrors()
    {
    }
    /**
     * @return DocumentNode
     */
    public function getDocument()
    {
    }
    public function getSchema() : ?\GraphQL\Type\Schema
    {
    }
}
/**
 * An instance of this class is passed as the "this" context to all validators,
 * allowing access to commonly useful contextual information from within a
 * validation rule.
 */
class ValidationContext extends \GraphQL\Validator\ASTValidationContext
{
    /** @var TypeInfo */
    private $typeInfo;
    /** @var FragmentDefinitionNode[] */
    private $fragments;
    /** @var SplObjectStorage */
    private $fragmentSpreads;
    /** @var SplObjectStorage */
    private $recursivelyReferencedFragments;
    /** @var SplObjectStorage */
    private $variableUsages;
    /** @var SplObjectStorage */
    private $recursiveVariableUsages;
    public function __construct(\GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $ast, \GraphQL\Utils\TypeInfo $typeInfo)
    {
    }
    /**
     * @return mixed[][] List of ['node' => VariableNode, 'type' => ?InputObjectType]
     */
    public function getRecursiveVariableUsages(\GraphQL\Language\AST\OperationDefinitionNode $operation)
    {
    }
    /**
     * @return mixed[][] List of ['node' => VariableNode, 'type' => ?InputObjectType]
     */
    private function getVariableUsages(\GraphQL\Language\AST\HasSelectionSet $node)
    {
    }
    /**
     * @return FragmentDefinitionNode[]
     */
    public function getRecursivelyReferencedFragments(\GraphQL\Language\AST\OperationDefinitionNode $operation)
    {
    }
    /**
     * @param OperationDefinitionNode|FragmentDefinitionNode $node
     *
     * @return FragmentSpreadNode[]
     */
    public function getFragmentSpreads(\GraphQL\Language\AST\HasSelectionSet $node) : array
    {
    }
    /**
     * @param string $name
     *
     * @return FragmentDefinitionNode|null
     */
    public function getFragment($name)
    {
    }
    public function getType() : ?\GraphQL\Type\Definition\OutputType
    {
    }
    /**
     * @return (CompositeType & Type) | null
     */
    public function getParentType() : ?\GraphQL\Type\Definition\CompositeType
    {
    }
    /**
     * @return (Type & InputType) | null
     */
    public function getInputType() : ?\GraphQL\Type\Definition\InputType
    {
    }
    /**
     * @return (Type&InputType)|null
     */
    public function getParentInputType() : ?\GraphQL\Type\Definition\InputType
    {
    }
    /**
     * @return FieldDefinition
     */
    public function getFieldDef()
    {
    }
    public function getDirective()
    {
    }
    public function getArgument()
    {
    }
}
namespace GraphQL\Validator\Rules;

abstract class ValidationRule
{
    /** @var string */
    protected $name;
    public function getName()
    {
    }
    public function __invoke(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * Returns structure suitable for GraphQL\Language\Visitor
     *
     * @see \GraphQL\Language\Visitor
     *
     * @return mixed[]
     */
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * Returns structure suitable for GraphQL\Language\Visitor
     *
     * @see \GraphQL\Language\Visitor
     *
     * @return mixed[]
     */
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
}
class ScalarLeafs extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function noSubselectionAllowedMessage($field, $type)
    {
    }
    public static function requiredSubselectionMessage($field, $type)
    {
    }
}
class UniqueInputFieldNames extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var array<string, NameNode> */
    public $knownNames;
    /** @var array<array<string, NameNode>> */
    public $knownNameStack;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
    public function getASTVisitor(\GraphQL\Validator\ASTValidationContext $context)
    {
    }
    public static function duplicateInputFieldMessage($fieldName)
    {
    }
}
class SingleFieldSubscription extends \GraphQL\Validator\Rules\ValidationRule
{
    /**
     * @return array<string, callable>
     */
    public function getVisitor(\GraphQL\Validator\ValidationContext $context) : array
    {
    }
    public static function multipleFieldsInOperation(?string $operationName) : string
    {
    }
}
class UniqueArgumentNames extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownArgNames;
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public function getASTVisitor(\GraphQL\Validator\ASTValidationContext $context)
    {
    }
    public static function duplicateArgMessage($argName)
    {
    }
}
abstract class QuerySecurityRule extends \GraphQL\Validator\Rules\ValidationRule
{
    public const DISABLED = 0;
    /** @var FragmentDefinitionNode[] */
    private $fragments = [];
    /**
     * check if equal to 0 no check is done. Must be greater or equal to 0.
     *
     * @param string $name
     * @param int    $value
     */
    protected function checkIfGreaterOrEqualToZero($name, $value)
    {
    }
    protected function getFragment(\GraphQL\Language\AST\FragmentSpreadNode $fragmentSpread)
    {
    }
    /**
     * @return FragmentDefinitionNode[]
     */
    protected function getFragments()
    {
    }
    /**
     * @param callable[] $validators
     *
     * @return callable[]
     */
    protected function invokeIfNeeded(\GraphQL\Validator\ValidationContext $context, array $validators)
    {
    }
    protected abstract function isEnabled();
    protected function gatherFragmentDefinition(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * Given a selectionSet, adds all of the fields in that selection to
     * the passed in map of fields, and returns it at the end.
     *
     * Note: This is not the same as execution's collectFields because at static
     * time we do not know what object type will be used, so we unconditionally
     * spread in all fragments.
     *
     * @see \GraphQL\Validator\Rules\OverlappingFieldsCanBeMerged
     *
     * @param Type|null $parentType
     *
     * @return ArrayObject
     */
    protected function collectFieldASTsAndDefs(\GraphQL\Validator\ValidationContext $context, $parentType, \GraphQL\Language\AST\SelectionSetNode $selectionSet, ?\ArrayObject $visitedFragmentNames = null, ?\ArrayObject $astAndDefs = null)
    {
    }
    protected function getFieldName(\GraphQL\Language\AST\FieldNode $node)
    {
    }
}
class QueryDepth extends \GraphQL\Validator\Rules\QuerySecurityRule
{
    /** @var int */
    private $maxQueryDepth;
    public function __construct($maxQueryDepth)
    {
    }
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    private function fieldDepth($node, $depth = 0, $maxDepth = 0)
    {
    }
    private function nodeDepth(\GraphQL\Language\AST\Node $node, $depth = 0, $maxDepth = 0)
    {
    }
    public function getMaxQueryDepth()
    {
    }
    /**
     * Set max query depth. If equal to 0 no check is done. Must be greater or equal to 0.
     */
    public function setMaxQueryDepth($maxQueryDepth)
    {
    }
    public static function maxQueryDepthErrorMessage($max, $count)
    {
    }
    protected function isEnabled()
    {
    }
}
class ProvidedRequiredArguments extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function missingFieldArgMessage($fieldName, $argName, $type)
    {
    }
}
class OverlappingFieldsCanBeMerged extends \GraphQL\Validator\Rules\ValidationRule
{
    /**
     * A memoization for when two fragments are compared "between" each other for
     * conflicts. Two fragments may be compared many times, so memoizing this can
     * dramatically improve the performance of this validator.
     *
     * @var PairSet
     */
    private $comparedFragmentPairs;
    /**
     * A cache for the "field map" and list of fragment names found in any given
     * selection set. Selection sets may be asked for this information multiple
     * times, so this improves the performance of this validator.
     *
     * @var SplObjectStorage
     */
    private $cachedFieldsAndFragmentNames;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * Find all conflicts found "within" a selection set, including those found
     * via spreading in fragments. Called when visiting each SelectionSet in the
     * GraphQL Document.
     *
     * @param CompositeType $parentType
     *
     * @return mixed[]
     */
    private function findConflictsWithinSelectionSet(\GraphQL\Validator\ValidationContext $context, $parentType, \GraphQL\Language\AST\SelectionSetNode $selectionSet)
    {
    }
    /**
     * Given a selection set, return the collection of fields (a mapping of response
     * name to field ASTs and definitions) as well as a list of fragment names
     * referenced via fragment spreads.
     *
     * @param CompositeType $parentType
     *
     * @return mixed[]|SplObjectStorage
     */
    private function getFieldsAndFragmentNames(\GraphQL\Validator\ValidationContext $context, $parentType, \GraphQL\Language\AST\SelectionSetNode $selectionSet)
    {
    }
    /**
     * Algorithm:
     *
     * Conflicts occur when two fields exist in a query which will produce the same
     * response name, but represent differing values, thus creating a conflict.
     * The algorithm below finds all conflicts via making a series of comparisons
     * between fields. In order to compare as few fields as possible, this makes
     * a series of comparisons "within" sets of fields and "between" sets of fields.
     *
     * Given any selection set, a collection produces both a set of fields by
     * also including all inline fragments, as well as a list of fragments
     * referenced by fragment spreads.
     *
     * A) Each selection set represented in the document first compares "within" its
     * collected set of fields, finding any conflicts between every pair of
     * overlapping fields.
     * Note: This is the *only time* that a the fields "within" a set are compared
     * to each other. After this only fields "between" sets are compared.
     *
     * B) Also, if any fragment is referenced in a selection set, then a
     * comparison is made "between" the original set of fields and the
     * referenced fragment.
     *
     * C) Also, if multiple fragments are referenced, then comparisons
     * are made "between" each referenced fragment.
     *
     * D) When comparing "between" a set of fields and a referenced fragment, first
     * a comparison is made between each field in the original set of fields and
     * each field in the the referenced set of fields.
     *
     * E) Also, if any fragment is referenced in the referenced selection set,
     * then a comparison is made "between" the original set of fields and the
     * referenced fragment (recursively referring to step D).
     *
     * F) When comparing "between" two fragments, first a comparison is made between
     * each field in the first referenced set of fields and each field in the the
     * second referenced set of fields.
     *
     * G) Also, any fragments referenced by the first must be compared to the
     * second, and any fragments referenced by the second must be compared to the
     * first (recursively referring to step F).
     *
     * H) When comparing two fields, if both have selection sets, then a comparison
     * is made "between" both selection sets, first comparing the set of fields in
     * the first selection set with the set of fields in the second.
     *
     * I) Also, if any fragment is referenced in either selection set, then a
     * comparison is made "between" the other set of fields and the
     * referenced fragment.
     *
     * J) Also, if two fragments are referenced in both selection sets, then a
     * comparison is made "between" the two fragments.
     */
    /**
     * Given a reference to a fragment, return the represented collection of fields
     * as well as a list of nested fragment names referenced via fragment spreads.
     *
     * @param CompositeType $parentType
     * @param mixed[][][]   $astAndDefs
     * @param bool[]        $fragmentNames
     */
    private function internalCollectFieldsAndFragmentNames(\GraphQL\Validator\ValidationContext $context, $parentType, \GraphQL\Language\AST\SelectionSetNode $selectionSet, array &$astAndDefs, array &$fragmentNames)
    {
    }
    /**
     * Collect all Conflicts "within" one collection of fields.
     *
     * @param mixed[][] $conflicts
     * @param mixed[][] $fieldMap
     */
    private function collectConflictsWithin(\GraphQL\Validator\ValidationContext $context, array &$conflicts, array $fieldMap)
    {
    }
    /**
     * Determines if there is a conflict between two particular fields, including
     * comparing their sub-fields.
     *
     * @param bool    $parentFieldsAreMutuallyExclusive
     * @param string  $responseName
     * @param mixed[] $field1
     * @param mixed[] $field2
     *
     * @return mixed[]|null
     */
    private function findConflict(\GraphQL\Validator\ValidationContext $context, $parentFieldsAreMutuallyExclusive, $responseName, array $field1, array $field2)
    {
    }
    /**
     * @param ArgumentNode[] $arguments1
     * @param ArgumentNode[] $arguments2
     *
     * @return bool
     */
    private function sameArguments($arguments1, $arguments2)
    {
    }
    /**
     * @return bool
     */
    private function sameValue(\GraphQL\Language\AST\Node $value1, \GraphQL\Language\AST\Node $value2)
    {
    }
    /**
     * Two types conflict if both types could not apply to a value simultaneously.
     * Composite types are ignored as their individual field types will be compared
     * later recursively. However List and Non-Null types must match.
     */
    private function doTypesConflict(\GraphQL\Type\Definition\Type $type1, \GraphQL\Type\Definition\Type $type2) : bool
    {
    }
    /**
     * Find all conflicts found between two selection sets, including those found
     * via spreading in fragments. Called when determining if conflicts exist
     * between the sub-fields of two overlapping fields.
     *
     * @param bool          $areMutuallyExclusive
     * @param CompositeType $parentType1
     * @param CompositeType $parentType2
     *
     * @return mixed[][]
     */
    private function findConflictsBetweenSubSelectionSets(\GraphQL\Validator\ValidationContext $context, $areMutuallyExclusive, $parentType1, \GraphQL\Language\AST\SelectionSetNode $selectionSet1, $parentType2, \GraphQL\Language\AST\SelectionSetNode $selectionSet2)
    {
    }
    /**
     * Collect all Conflicts between two collections of fields. This is similar to,
     * but different from the `collectConflictsWithin` function above. This check
     * assumes that `collectConflictsWithin` has already been called on each
     * provided collection of fields. This is true because this validator traverses
     * each individual selection set.
     *
     * @param mixed[][] $conflicts
     * @param bool      $parentFieldsAreMutuallyExclusive
     * @param mixed[]   $fieldMap1
     * @param mixed[]   $fieldMap2
     */
    private function collectConflictsBetween(\GraphQL\Validator\ValidationContext $context, array &$conflicts, $parentFieldsAreMutuallyExclusive, array $fieldMap1, array $fieldMap2)
    {
    }
    /**
     * Collect all conflicts found between a set of fields and a fragment reference
     * including via spreading in any nested fragments.
     *
     * @param mixed[][] $conflicts
     * @param bool[]    $comparedFragments
     * @param bool      $areMutuallyExclusive
     * @param mixed[][] $fieldMap
     * @param string    $fragmentName
     */
    private function collectConflictsBetweenFieldsAndFragment(\GraphQL\Validator\ValidationContext $context, array &$conflicts, array &$comparedFragments, $areMutuallyExclusive, array $fieldMap, $fragmentName)
    {
    }
    /**
     * Given a reference to a fragment, return the represented collection of fields
     * as well as a list of nested fragment names referenced via fragment spreads.
     *
     * @return mixed[]|SplObjectStorage
     */
    private function getReferencedFieldsAndFragmentNames(\GraphQL\Validator\ValidationContext $context, \GraphQL\Language\AST\FragmentDefinitionNode $fragment)
    {
    }
    /**
     * Collect all conflicts found between two fragments, including via spreading in
     * any nested fragments.
     *
     * @param mixed[][] $conflicts
     * @param bool      $areMutuallyExclusive
     * @param string    $fragmentName1
     * @param string    $fragmentName2
     */
    private function collectConflictsBetweenFragments(\GraphQL\Validator\ValidationContext $context, array &$conflicts, $areMutuallyExclusive, $fragmentName1, $fragmentName2)
    {
    }
    /**
     * Given a series of Conflicts which occurred between two sub-fields, generate
     * a single Conflict.
     *
     * @param mixed[][] $conflicts
     * @param string    $responseName
     *
     * @return mixed[]|null
     */
    private function subfieldConflicts(array $conflicts, $responseName, \GraphQL\Language\AST\FieldNode $ast1, \GraphQL\Language\AST\FieldNode $ast2)
    {
    }
    /**
     * @param string $responseName
     * @param string $reason
     */
    public static function fieldsConflictMessage($responseName, $reason)
    {
    }
    public static function reasonMessage($reason)
    {
    }
}
/**
 * Known type names
 *
 * A GraphQL document is only valid if referenced types (specifically
 * variable definitions and fragment conditions) are defined by the type schema.
 */
class KnownTypeNames extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * @param string   $type
     * @param string[] $suggestedTypes
     */
    public static function unknownTypeMessage($type, array $suggestedTypes)
    {
    }
}
class KnownFragmentNames extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * @param string $fragName
     */
    public static function unknownFragmentMessage($fragName)
    {
    }
}
/**
 * A GraphQL operation is only valid if all variables encountered, both directly
 * and via fragment spreads, are defined by that operation.
 */
class NoUndefinedVariables extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function undefinedVarMessage($varName, $opName = null)
    {
    }
}
class UniqueFragmentNames extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownFragmentNames;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function duplicateFragmentNameMessage($fragName)
    {
    }
}
class PossibleFragmentSpreads extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    private function doTypesOverlap(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\CompositeType $fragType, \GraphQL\Type\Definition\CompositeType $parentType)
    {
    }
    public static function typeIncompatibleAnonSpreadMessage($parentType, $fragType)
    {
    }
    private function getFragmentType(\GraphQL\Validator\ValidationContext $context, $name)
    {
    }
    public static function typeIncompatibleSpreadMessage($fragName, $parentType, $fragType)
    {
    }
}
class FieldsOnCorrectType extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * Go through all of the implementations of type, as well as the interfaces
     * that they implement. If any of those types include the provided field,
     * suggest them, sorted by how often the type is referenced, starting
     * with Interfaces.
     *
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     *
     * @return string[]
     */
    private function getSuggestedTypeNames(\GraphQL\Type\Schema $schema, $type, $fieldName)
    {
    }
    /**
     * For the field name provided, determine if there are any similar field names
     * that may be the result of a typo.
     *
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     *
     * @return array|string[]
     */
    private function getSuggestedFieldNames(\GraphQL\Type\Schema $schema, $type, $fieldName)
    {
    }
    /**
     * @param string   $fieldName
     * @param string   $type
     * @param string[] $suggestedTypeNames
     * @param string[] $suggestedFieldNames
     *
     * @return string
     */
    public static function undefinedFieldMessage($fieldName, $type, array $suggestedTypeNames, array $suggestedFieldNames)
    {
    }
}
class CustomValidationRule extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var callable */
    private $visitorFn;
    public function __construct($name, callable $visitorFn)
    {
    }
    /**
     * @return Error[]
     */
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
}
/**
 * Lone Schema definition
 *
 * A GraphQL document is only valid if it contains only one schema definition.
 */
class LoneSchemaDefinition extends \GraphQL\Validator\Rules\ValidationRule
{
    public static function schemaDefinitionNotAloneMessage()
    {
    }
    public static function canNotDefineSchemaWithinExtensionMessage()
    {
    }
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
}
/**
 * Value literals of correct type
 *
 * A GraphQL document is only valid if all value literals are of the type
 * expected at their position.
 */
class ValuesOfCorrectType extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function badValueMessage($typeName, $valueName, $message = null)
    {
    }
    /**
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $node
     */
    private function isValidScalar(\GraphQL\Validator\ValidationContext $context, \GraphQL\Language\AST\ValueNode $node, $fieldName)
    {
    }
    /**
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $node
     */
    private function enumTypeSuggestion($type, \GraphQL\Language\AST\ValueNode $node)
    {
    }
    public static function badArgumentValueMessage($typeName, $valueName, $fieldName, $argName, $message = null)
    {
    }
    public static function requiredFieldMessage($typeName, $fieldName, $fieldTypeName)
    {
    }
    public static function unknownFieldMessage($typeName, $fieldName, $message = null)
    {
    }
    private static function getBadValueMessage($typeName, $valueName, $message = null, $context = null, $fieldName = null)
    {
    }
}
class QueryComplexity extends \GraphQL\Validator\Rules\QuerySecurityRule
{
    /** @var int */
    private $maxQueryComplexity;
    /** @var mixed[]|null */
    private $rawVariableValues = [];
    /** @var ArrayObject */
    private $variableDefs;
    /** @var ArrayObject */
    private $fieldNodeAndDefs;
    /** @var ValidationContext */
    private $context;
    /** @var int */
    private $complexity;
    public function __construct($maxQueryComplexity)
    {
    }
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    private function fieldComplexity($node, $complexity = 0)
    {
    }
    private function nodeComplexity(\GraphQL\Language\AST\Node $node, $complexity = 0)
    {
    }
    private function astFieldInfo(\GraphQL\Language\AST\FieldNode $field)
    {
    }
    private function directiveExcludesField(\GraphQL\Language\AST\FieldNode $node)
    {
    }
    public function getRawVariableValues()
    {
    }
    /**
     * @param mixed[]|null $rawVariableValues
     */
    public function setRawVariableValues(?array $rawVariableValues = null)
    {
    }
    private function buildFieldArguments(\GraphQL\Language\AST\FieldNode $node)
    {
    }
    public function getQueryComplexity()
    {
    }
    public function getMaxQueryComplexity()
    {
    }
    /**
     * Set max query complexity. If equal to 0 no check is done. Must be greater or equal to 0.
     */
    public function setMaxQueryComplexity($maxQueryComplexity)
    {
    }
    public static function maxQueryComplexityErrorMessage($max, $count)
    {
    }
    protected function isEnabled()
    {
    }
}
class DisableIntrospection extends \GraphQL\Validator\Rules\QuerySecurityRule
{
    public const ENABLED = 1;
    /** @var bool */
    private $isEnabled;
    public function __construct($enabled = self::ENABLED)
    {
    }
    public function setEnabled($enabled)
    {
    }
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function introspectionDisabledMessage()
    {
    }
    protected function isEnabled()
    {
    }
}
class UniqueVariableNames extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownVariableNames;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function duplicateVariableMessage($variableName)
    {
    }
}
/**
 * Known argument names on directives
 *
 * A GraphQL directive is only valid if all supplied arguments are defined by
 * that field.
 */
class KnownArgumentNamesOnDirectives extends \GraphQL\Validator\Rules\ValidationRule
{
    /**
     * @param string[] $suggestedArgs
     */
    public static function unknownDirectiveArgMessage($argName, $directiveName, array $suggestedArgs)
    {
    }
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public function getASTVisitor(\GraphQL\Validator\ASTValidationContext $context)
    {
    }
}
/**
 * Known argument names
 *
 * A GraphQL field is only valid if all supplied arguments are defined by
 * that field.
 */
class KnownArgumentNames extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * @param string[] $suggestedArgs
     */
    public static function unknownArgMessage($argName, $fieldName, $typeName, array $suggestedArgs)
    {
    }
}
class VariablesAreInputTypes extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function nonInputTypeOnVarMessage($variableName, $typeName)
    {
    }
}
/**
 * Unique directive names per location
 *
 * A GraphQL document is only valid if all non-repeatable directives at
 * a given location are uniquely named.
 */
class UniqueDirectivesPerLocation extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
    public function getASTVisitor(\GraphQL\Validator\ASTValidationContext $context)
    {
    }
    public static function duplicateDirectiveMessage($directiveName)
    {
    }
}
class FragmentsOnCompositeTypes extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function inlineFragmentOnNonCompositeErrorMessage($type)
    {
    }
    public static function fragmentOnNonCompositeErrorMessage($fragName, $type)
    {
    }
}
/**
 * Provided required arguments on directives
 *
 * A directive is only valid if all required (non-null without a
 * default value) field arguments have been provided.
 */
class ProvidedRequiredArgumentsOnDirectives extends \GraphQL\Validator\Rules\ValidationRule
{
    public static function missingDirectiveArgMessage(string $directiveName, string $argName, string $type)
    {
    }
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public function getASTVisitor(\GraphQL\Validator\ASTValidationContext $context)
    {
    }
}
class KnownDirectives extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public function getSDLVisitor(\GraphQL\Validator\SDLValidationContext $context)
    {
    }
    public function getASTVisitor(\GraphQL\Validator\ASTValidationContext $context)
    {
    }
    public static function unknownDirectiveMessage($directiveName)
    {
    }
    /**
     * @param Node[]|NodeList[] $ancestors The type is actually (Node|NodeList)[] but this PSR-5 syntax is so far not supported by most of the tools
     *
     * @return string
     */
    private function getDirectiveLocationForASTPath(array $ancestors)
    {
    }
    public static function misplacedDirectiveMessage($directiveName, $location)
    {
    }
}
class VariablesInAllowedPosition extends \GraphQL\Validator\Rules\ValidationRule
{
    /**
     * A map from variable names to their definition nodes.
     *
     * @var VariableDefinitionNode[]
     */
    public $varDefMap;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * A var type is allowed if it is the same or more strict than the expected
     * type. It can be more strict if the variable type is non-null when the
     * expected type is nullable. If both are list types, the variable item type can
     * be more strict than the expected item type.
     */
    public static function badVarPosMessage($varName, $varType, $expectedType)
    {
    }
    /**
     * Returns true if the variable is allowed in the location it was found,
     * which includes considering if default values exist for either the variable
     * or the location at which it is located.
     *
     * @param ValueNode|null $varDefaultValue
     * @param mixed          $locationDefaultValue
     */
    private function allowedVariableUsage(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\Type $varType, $varDefaultValue, \GraphQL\Type\Definition\Type $locationType, $locationDefaultValue) : bool
    {
    }
}
class NoUnusedFragments extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var OperationDefinitionNode[] */
    public $operationDefs;
    /** @var FragmentDefinitionNode[] */
    public $fragmentDefs;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function unusedFragMessage($fragName)
    {
    }
}
/**
 * Lone anonymous operation
 *
 * A GraphQL document is only valid if when it contains an anonymous operation
 * (the query short-hand) that it contains only that one operation definition.
 */
class LoneAnonymousOperation extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function anonOperationNotAloneMessage()
    {
    }
}
class NoFragmentCycles extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var bool[] */
    public $visitedFrags;
    /** @var FragmentSpreadNode[] */
    public $spreadPath;
    /** @var (int|null)[] */
    public $spreadPathIndexByName;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    private function detectCycleRecursive(\GraphQL\Language\AST\FragmentDefinitionNode $fragment, \GraphQL\Validator\ValidationContext $context)
    {
    }
    /**
     * @param string[] $spreadNames
     */
    public static function cycleErrorMessage($fragName, array $spreadNames = [])
    {
    }
}
/**
 * Executable definitions
 *
 * A GraphQL document is only valid for execution if all definitions are either
 * operation or fragment definitions.
 */
class ExecutableDefinitions extends \GraphQL\Validator\Rules\ValidationRule
{
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function nonExecutableDefinitionMessage($defName)
    {
    }
}
class UniqueOperationNames extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var NameNode[] */
    public $knownOperationNames;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function duplicateOperationNameMessage($operationName)
    {
    }
}
class NoUnusedVariables extends \GraphQL\Validator\Rules\ValidationRule
{
    /** @var VariableDefinitionNode[] */
    public $variableDefs;
    public function getVisitor(\GraphQL\Validator\ValidationContext $context)
    {
    }
    public static function unusedVariableMessage($varName, $opName = null)
    {
    }
}
namespace GraphQL\Validator;

class SDLValidationContext extends \GraphQL\Validator\ASTValidationContext
{
}
namespace GraphQL\Executor\Promise\Adapter;

/**
 * Simplistic (yet full-featured) implementation of Promises A+ spec for regular PHP `sync` mode
 * (using queue to defer promises execution)
 *
 * Note:
 * Library users are not supposed to use SyncPromise class in their resolvers.
 * Instead they should use GraphQL\Deferred which enforces $executor callback in the constructor.
 *
 * Root SyncPromise without explicit $executor will never resolve (actually throw while trying).
 * The whole point of Deferred is to ensure it never happens and that any resolver creates
 * at least one $executor to start the promise chain.
 */
class SyncPromise
{
    const PENDING = 'pending';
    const FULFILLED = 'fulfilled';
    const REJECTED = 'rejected';
    /** @var SplQueue */
    public static $queue;
    /** @var string */
    public $state = self::PENDING;
    /** @var mixed */
    public $result;
    /**
     * Promises created in `then` method of this promise and awaiting for resolution of this promise
     *
     * @var mixed[][]
     */
    private $waiting = [];
    public static function runQueue() : void
    {
    }
    /**
     * @param callable() : mixed $executor
     */
    public function __construct(?callable $executor = null)
    {
    }
    public function resolve($value) : self
    {
    }
    public function reject($reason) : self
    {
    }
    private function enqueueWaitingPromises() : void
    {
    }
    public static function getQueue() : \SplQueue
    {
    }
    /**
     * @param callable(mixed) : mixed     $onFulfilled
     * @param callable(Throwable) : mixed $onRejected
     */
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null) : self
    {
    }
    /**
     * @param callable(Throwable) : mixed $onRejected
     */
    public function catch(callable $onRejected) : self
    {
    }
}
namespace GraphQL;

class Deferred extends \GraphQL\Executor\Promise\Adapter\SyncPromise
{
    /**
     * @param callable() : mixed $executor
     */
    public static function create(callable $executor) : self
    {
    }
    /**
     * @param callable() : mixed $executor
     */
    public function __construct(callable $executor)
    {
    }
}
namespace GraphQL\Server;

/**
 * Structure representing parsed HTTP parameters for GraphQL operation
 */
class OperationParams
{
    /**
     * Id of the query (when using persistent queries).
     *
     * Valid aliases (case-insensitive):
     * - id
     * - queryId
     * - documentId
     *
     * @api
     * @var string
     */
    public $queryId;
    /**
     * @api
     * @var string
     */
    public $query;
    /**
     * @api
     * @var string
     */
    public $operation;
    /**
     * @api
     * @var mixed[]|null
     */
    public $variables;
    /**
     * @api
     * @var mixed[]|null
     */
    public $extensions;
    /** @var mixed[] */
    private $originalInput;
    /** @var bool */
    private $readOnly;
    /**
     * Creates an instance from given array
     *
     * @param mixed[] $params
     *
     * @api
     */
    public static function create(array $params, bool $readonly = false) : \GraphQL\Server\OperationParams
    {
    }
    /**
     * @param string $key
     *
     * @return mixed
     *
     * @api
     */
    public function getOriginalInput($key)
    {
    }
    /**
     * Indicates that operation is executed in read-only context
     * (e.g. via HTTP GET request)
     *
     * @return bool
     *
     * @api
     */
    public function isReadOnly()
    {
    }
}
namespace GraphQL\Error;

/**
 * This interface is used for [default error formatting](error-handling.md).
 *
 * Only errors implementing this interface (and returning true from `isClientSafe()`)
 * will be formatted with original error message.
 *
 * All other errors will be formatted with generic "Internal server error".
 */
interface ClientAware
{
    /**
     * Returns true when exception message is safe to be displayed to a client.
     *
     * @return bool
     *
     * @api
     */
    public function isClientSafe();
    /**
     * Returns string describing a category of the error.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     *
     * @return string
     *
     * @api
     */
    public function getCategory();
}
namespace GraphQL\Server;

class RequestError extends \Exception implements \GraphQL\Error\ClientAware
{
    /**
     * Returns true when exception message is safe to be displayed to client
     *
     * @return bool
     */
    public function isClientSafe()
    {
    }
    /**
     * Returns string describing error category. E.g. "validation" for your own validation errors.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     *
     * @return string
     */
    public function getCategory()
    {
    }
}
/**
 * GraphQL server compatible with both: [express-graphql](https://github.com/graphql/express-graphql)
 * and [Apollo Server](https://github.com/apollographql/graphql-server).
 * Usage Example:
 *
 *     $server = new StandardServer([
 *       'schema' => $mySchema
 *     ]);
 *     $server->handleRequest();
 *
 * Or using [ServerConfig](reference.md#graphqlserverserverconfig) instance:
 *
 *     $config = GraphQL\Server\ServerConfig::create()
 *         ->setSchema($mySchema)
 *         ->setContext($myContext);
 *
 *     $server = new GraphQL\Server\StandardServer($config);
 *     $server->handleRequest();
 *
 * See [dedicated section in docs](executing-queries.md#using-server) for details.
 */
class StandardServer
{
    /** @var ServerConfig */
    private $config;
    /** @var Helper */
    private $helper;
    /**
     * Converts and exception to error and sends spec-compliant HTTP 500 error.
     * Useful when an exception is thrown somewhere outside of server execution context
     * (e.g. during schema instantiation).
     *
     * @param Throwable $error
     * @param int       $debug
     * @param bool      $exitWhenDone
     *
     * @api
     */
    public static function send500Error($error, $debug = \GraphQL\Error\DebugFlag::NONE, $exitWhenDone = false)
    {
    }
    /**
     * Creates new instance of a standard GraphQL HTTP server
     *
     * @param ServerConfig|mixed[] $config
     *
     * @api
     */
    public function __construct($config)
    {
    }
    /**
     * Parses HTTP request, executes and emits response (using standard PHP `header` function and `echo`)
     *
     * By default (when $parsedBody is not set) it uses PHP globals to parse a request.
     * It is possible to implement request parsing elsewhere (e.g. using framework Request instance)
     * and then pass it to the server.
     *
     * See `executeRequest()` if you prefer to emit response yourself
     * (e.g. using Response object of some framework)
     *
     * @param OperationParams|OperationParams[] $parsedBody
     * @param bool                              $exitWhenDone
     *
     * @api
     */
    public function handleRequest($parsedBody = null, $exitWhenDone = false)
    {
    }
    /**
     * Executes GraphQL operation and returns execution result
     * (or promise when promise adapter is different from SyncPromiseAdapter).
     *
     * By default (when $parsedBody is not set) it uses PHP globals to parse a request.
     * It is possible to implement request parsing elsewhere (e.g. using framework Request instance)
     * and then pass it to the server.
     *
     * PSR-7 compatible method executePsrRequest() does exactly this.
     *
     * @param OperationParams|OperationParams[] $parsedBody
     *
     * @return ExecutionResult|ExecutionResult[]|Promise
     *
     * @throws InvariantViolation
     *
     * @api
     */
    public function executeRequest($parsedBody = null)
    {
    }
    /**
     * Executes PSR-7 request and fulfills PSR-7 response.
     *
     * See `executePsrRequest()` if you prefer to create response yourself
     * (e.g. using specific JsonResponse instance of some framework).
     *
     * @return ResponseInterface|Promise
     *
     * @api
     */
    public function processPsrRequest(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response, \Psr\Http\Message\StreamInterface $writableBodyStream)
    {
    }
    /**
     * Executes GraphQL operation and returns execution result
     * (or promise when promise adapter is different from SyncPromiseAdapter)
     *
     * @return ExecutionResult|ExecutionResult[]|Promise
     *
     * @api
     */
    public function executePsrRequest(\Psr\Http\Message\RequestInterface $request)
    {
    }
    /**
     * Returns an instance of Server helper, which contains most of the actual logic for
     * parsing / validating / executing request (which could be re-used by other server implementations)
     *
     * @return Helper
     *
     * @api
     */
    public function getHelper()
    {
    }
}
/**
 * Contains functionality that could be re-used by various server implementations
 */
class Helper
{
    /**
     * Parses HTTP request using PHP globals and returns GraphQL OperationParams
     * contained in this request. For batched requests it returns an array of OperationParams.
     *
     * This function does not check validity of these params
     * (validation is performed separately in validateOperationParams() method).
     *
     * If $readRawBodyFn argument is not provided - will attempt to read raw request body
     * from `php://input` stream.
     *
     * Internally it normalizes input to $method, $bodyParams and $queryParams and
     * calls `parseRequestParams()` to produce actual return value.
     *
     * For PSR-7 request parsing use `parsePsrRequest()` instead.
     *
     * @return OperationParams|OperationParams[]
     *
     * @throws RequestError
     *
     * @api
     */
    public function parseHttpRequest(?callable $readRawBodyFn = null)
    {
    }
    /**
     * Parses normalized request params and returns instance of OperationParams
     * or array of OperationParams in case of batch operation.
     *
     * Returned value is a suitable input for `executeOperation` or `executeBatch` (if array)
     *
     * @param string  $method
     * @param mixed[] $bodyParams
     * @param mixed[] $queryParams
     *
     * @return OperationParams|OperationParams[]
     *
     * @throws RequestError
     *
     * @api
     */
    public function parseRequestParams($method, array $bodyParams, array $queryParams)
    {
    }
    /**
     * Checks validity of OperationParams extracted from HTTP request and returns an array of errors
     * if params are invalid (or empty array when params are valid)
     *
     * @return array<int, RequestError>
     *
     * @api
     */
    public function validateOperationParams(\GraphQL\Server\OperationParams $params)
    {
    }
    /**
     * Executes GraphQL operation with given server configuration and returns execution result
     * (or promise when promise adapter is different from SyncPromiseAdapter)
     *
     * @return ExecutionResult|Promise
     *
     * @api
     */
    public function executeOperation(\GraphQL\Server\ServerConfig $config, \GraphQL\Server\OperationParams $op)
    {
    }
    /**
     * Executes batched GraphQL operations with shared promise queue
     * (thus, effectively batching deferreds|promises of all queries at once)
     *
     * @param OperationParams[] $operations
     *
     * @return ExecutionResult|ExecutionResult[]|Promise
     *
     * @api
     */
    public function executeBatch(\GraphQL\Server\ServerConfig $config, array $operations)
    {
    }
    /**
     * @param bool $isBatch
     *
     * @return Promise
     */
    private function promiseToExecuteOperation(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \GraphQL\Server\ServerConfig $config, \GraphQL\Server\OperationParams $op, $isBatch = false)
    {
    }
    /**
     * @return mixed
     *
     * @throws RequestError
     */
    private function loadPersistedQuery(\GraphQL\Server\ServerConfig $config, \GraphQL\Server\OperationParams $operationParams)
    {
    }
    /**
     * @param string $operationType
     *
     * @return mixed[]|null
     */
    private function resolveValidationRules(\GraphQL\Server\ServerConfig $config, \GraphQL\Server\OperationParams $params, \GraphQL\Language\AST\DocumentNode $doc, $operationType)
    {
    }
    /**
     * @return mixed
     */
    private function resolveRootValue(\GraphQL\Server\ServerConfig $config, \GraphQL\Server\OperationParams $params, \GraphQL\Language\AST\DocumentNode $doc, string $operationType)
    {
    }
    /**
     * @param string $operationType
     *
     * @return mixed
     */
    private function resolveContextValue(\GraphQL\Server\ServerConfig $config, \GraphQL\Server\OperationParams $params, \GraphQL\Language\AST\DocumentNode $doc, $operationType)
    {
    }
    /**
     * Send response using standard PHP `header()` and `echo`.
     *
     * @param Promise|ExecutionResult|ExecutionResult[] $result
     * @param bool                                      $exitWhenDone
     *
     * @api
     */
    public function sendResponse($result, $exitWhenDone = false)
    {
    }
    private function doSendResponse($result, $exitWhenDone)
    {
    }
    /**
     * @param mixed[]|JsonSerializable $jsonSerializable
     * @param int                      $httpStatus
     * @param bool                     $exitWhenDone
     */
    public function emitResponse($jsonSerializable, $httpStatus, $exitWhenDone)
    {
    }
    /**
     * @return bool|string
     */
    private function readRawBody()
    {
    }
    /**
     * @param ExecutionResult|mixed[] $result
     *
     * @return int
     */
    private function resolveHttpStatus($result)
    {
    }
    /**
     * Converts PSR-7 request to OperationParams[]
     *
     * @return OperationParams[]|OperationParams
     *
     * @throws RequestError
     *
     * @api
     */
    public function parsePsrRequest(\Psr\Http\Message\RequestInterface $request)
    {
    }
    /**
     * Converts query execution result to PSR-7 response
     *
     * @param Promise|ExecutionResult|ExecutionResult[] $result
     *
     * @return Promise|ResponseInterface
     *
     * @api
     */
    public function toPsrResponse($result, \Psr\Http\Message\ResponseInterface $response, \Psr\Http\Message\StreamInterface $writableBodyStream)
    {
    }
    private function doConvertToPsrResponse($result, \Psr\Http\Message\ResponseInterface $response, \Psr\Http\Message\StreamInterface $writableBodyStream)
    {
    }
}
/**
 * Server configuration class.
 * Could be passed directly to server constructor. List of options accepted by **create** method is
 * [described in docs](executing-queries.md#server-configuration-options).
 *
 * Usage example:
 *
 *     $config = GraphQL\Server\ServerConfig::create()
 *         ->setSchema($mySchema)
 *         ->setContext($myContext);
 *
 *     $server = new GraphQL\Server\StandardServer($config);
 */
class ServerConfig
{
    /**
     * Converts an array of options to instance of ServerConfig
     * (or just returns empty config when array is not passed).
     *
     * @param mixed[] $config
     *
     * @return ServerConfig
     *
     * @api
     */
    public static function create(array $config = [])
    {
    }
    /** @var Schema|null */
    private $schema;
    /** @var mixed|callable */
    private $context;
    /** @var mixed|callable */
    private $rootValue;
    /** @var callable|null */
    private $errorFormatter;
    /** @var callable|null */
    private $errorsHandler;
    /** @var int */
    private $debugFlag = \GraphQL\Error\DebugFlag::NONE;
    /** @var bool */
    private $queryBatching = false;
    /** @var ValidationRule[]|callable|null */
    private $validationRules;
    /** @var callable|null */
    private $fieldResolver;
    /** @var PromiseAdapter|null */
    private $promiseAdapter;
    /** @var callable|null */
    private $persistentQueryLoader;
    /**
     * @return self
     *
     * @api
     */
    public function setSchema(\GraphQL\Type\Schema $schema)
    {
    }
    /**
     * @param mixed|callable $context
     *
     * @return self
     *
     * @api
     */
    public function setContext($context)
    {
    }
    /**
     * @param mixed|callable $rootValue
     *
     * @return self
     *
     * @api
     */
    public function setRootValue($rootValue)
    {
    }
    /**
     * Expects function(Throwable $e) : array
     *
     * @return self
     *
     * @api
     */
    public function setErrorFormatter(callable $errorFormatter)
    {
    }
    /**
     * Expects function(array $errors, callable $formatter) : array
     *
     * @return self
     *
     * @api
     */
    public function setErrorsHandler(callable $handler)
    {
    }
    /**
     * Set validation rules for this server.
     *
     * @param ValidationRule[]|callable|null $validationRules
     *
     * @return self
     *
     * @api
     */
    public function setValidationRules($validationRules)
    {
    }
    /**
     * @return self
     *
     * @api
     */
    public function setFieldResolver(callable $fieldResolver)
    {
    }
    /**
     * Expects function($queryId, OperationParams $params) : string|DocumentNode
     *
     * This function must return query string or valid DocumentNode.
     *
     * @return self
     *
     * @api
     */
    public function setPersistentQueryLoader(callable $persistentQueryLoader)
    {
    }
    /**
     * Set response debug flags. @see \GraphQL\Error\DebugFlag class for a list of all available flags
     *
     * @api
     */
    public function setDebugFlag(int $debugFlag = \GraphQL\Error\DebugFlag::INCLUDE_DEBUG_MESSAGE) : self
    {
    }
    /**
     * Allow batching queries (disabled by default)
     *
     * @api
     */
    public function setQueryBatching(bool $enableBatching) : self
    {
    }
    /**
     * @return self
     *
     * @api
     */
    public function setPromiseAdapter(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter)
    {
    }
    /**
     * @return mixed|callable
     */
    public function getContext()
    {
    }
    /**
     * @return mixed|callable
     */
    public function getRootValue()
    {
    }
    /**
     * @return Schema|null
     */
    public function getSchema()
    {
    }
    /**
     * @return callable|null
     */
    public function getErrorFormatter()
    {
    }
    /**
     * @return callable|null
     */
    public function getErrorsHandler()
    {
    }
    /**
     * @return PromiseAdapter|null
     */
    public function getPromiseAdapter()
    {
    }
    /**
     * @return ValidationRule[]|callable|null
     */
    public function getValidationRules()
    {
    }
    /**
     * @return callable|null
     */
    public function getFieldResolver()
    {
    }
    /**
     * @return callable|null
     */
    public function getPersistentQueryLoader()
    {
    }
    public function getDebugFlag() : int
    {
    }
    /**
     * @return bool
     */
    public function getQueryBatching()
    {
    }
}
namespace GraphQL\Utils;

class TypeInfo
{
    /** @var Schema */
    private $schema;
    /** @var array<(OutputType&Type)|null> */
    private $typeStack;
    /** @var array<(CompositeType&Type)|null> */
    private $parentTypeStack;
    /** @var array<(InputType&Type)|null> */
    private $inputTypeStack;
    /** @var array<FieldDefinition> */
    private $fieldDefStack;
    /** @var array<mixed> */
    private $defaultValueStack;
    /** @var Directive|null */
    private $directive;
    /** @var FieldArgument|null */
    private $argument;
    /** @var mixed */
    private $enumValue;
    /**
     * @param Type|null $initialType
     */
    public function __construct(\GraphQL\Type\Schema $schema, $initialType = null)
    {
    }
    /**
     * @deprecated moved to GraphQL\Utils\TypeComparators
     *
     * @codeCoverageIgnore
     */
    public static function isEqualType(\GraphQL\Type\Definition\Type $typeA, \GraphQL\Type\Definition\Type $typeB) : bool
    {
    }
    /**
     * @deprecated moved to GraphQL\Utils\TypeComparators
     *
     * @codeCoverageIgnore
     */
    public static function isTypeSubTypeOf(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\Type $maybeSubType, \GraphQL\Type\Definition\Type $superType)
    {
    }
    /**
     * @deprecated moved to GraphQL\Utils\TypeComparators
     *
     * @codeCoverageIgnore
     */
    public static function doTypesOverlap(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\CompositeType $typeA, \GraphQL\Type\Definition\CompositeType $typeB)
    {
    }
    /**
     * Given root type scans through all fields to find nested types. Returns array where keys are for type name
     * and value contains corresponding type instance.
     *
     * Example output:
     * [
     *     'String' => $instanceOfStringType,
     *     'MyType' => $instanceOfMyType,
     *     ...
     * ]
     *
     * @param Type|null   $type
     * @param Type[]|null $typeMap
     *
     * @return Type[]|null
     */
    public static function extractTypes($type, ?array $typeMap = null)
    {
    }
    /**
     * @param Type[] $typeMap
     *
     * @return Type[]
     */
    public static function extractTypesFromDirectives(\GraphQL\Type\Definition\Directive $directive, array $typeMap = [])
    {
    }
    /**
     * @return (Type&InputType)|null
     */
    public function getParentInputType() : ?\GraphQL\Type\Definition\InputType
    {
    }
    public function getArgument() : ?\GraphQL\Type\Definition\FieldArgument
    {
    }
    /**
     * @return mixed
     */
    public function getEnumValue()
    {
    }
    public function enter(\GraphQL\Language\AST\Node $node)
    {
    }
    /**
     * @return (Type & OutputType) | null
     */
    public function getType() : ?\GraphQL\Type\Definition\OutputType
    {
    }
    /**
     * @return (CompositeType & Type) | null
     */
    public function getParentType() : ?\GraphQL\Type\Definition\CompositeType
    {
    }
    /**
     * Not exactly the same as the executor's definition of getFieldDef, in this
     * statically evaluated environment we do not always have an Object type,
     * and need to handle Interface and Union types.
     */
    private static function getFieldDefinition(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\Type $parentType, \GraphQL\Language\AST\FieldNode $fieldNode) : ?\GraphQL\Type\Definition\FieldDefinition
    {
    }
    /**
     * @param NamedTypeNode|ListTypeNode|NonNullTypeNode $inputTypeNode
     *
     * @throws InvariantViolation
     */
    public static function typeFromAST(\GraphQL\Type\Schema $schema, $inputTypeNode) : ?\GraphQL\Type\Definition\Type
    {
    }
    public function getDirective() : ?\GraphQL\Type\Definition\Directive
    {
    }
    public function getFieldDef() : ?\GraphQL\Type\Definition\FieldDefinition
    {
    }
    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
    }
    /**
     * @return (Type & InputType) | null
     */
    public function getInputType() : ?\GraphQL\Type\Definition\InputType
    {
    }
    public function leave(\GraphQL\Language\AST\Node $node)
    {
    }
}
class BlockString
{
    /**
     * Produces the value of a block string from its parsed raw value, similar to
     * Coffeescript's block string, Python's docstring trim or Ruby's strip_heredoc.
     *
     * This implements the GraphQL spec's BlockStringValue() static algorithm.
     */
    public static function value($rawString)
    {
    }
    private static function leadingWhitespace($str)
    {
    }
}
/**
 * Similar to PHP array, but allows any type of data to act as key (including arrays, objects, scalars)
 *
 * Note: unfortunately when storing array as key - access and modification is O(N)
 * (yet this should rarely be the case and should be avoided when possible)
 */
class MixedStore implements \ArrayAccess
{
    /** @var EnumValueDefinition[] */
    private $standardStore;
    /** @var mixed[] */
    private $floatStore;
    /** @var SplObjectStorage */
    private $objectStore;
    /** @var callable[] */
    private $arrayKeys;
    /** @var EnumValueDefinition[] */
    private $arrayValues;
    /** @var callable[] */
    private $lastArrayKey;
    /** @var mixed */
    private $lastArrayValue;
    /** @var mixed */
    private $nullValue;
    /** @var bool */
    private $nullValueIsSet;
    /** @var mixed */
    private $trueValue;
    /** @var bool */
    private $trueValueIsSet;
    /** @var mixed */
    private $falseValue;
    /** @var bool */
    private $falseValueIsSet;
    public function __construct()
    {
    }
    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
    }
    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
    }
    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value  <p>
     *  The value to set.
     *  </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
    }
    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
    }
}
/**
 * Coerces a PHP value given a GraphQL Type.
 *
 * Returns either a value which is valid for the provided type or a list of
 * encountered coercion errors.
 */
class Value
{
    /**
     * Given a type and any value, return a runtime value coerced to match the type.
     *
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull $type
     * @param mixed[]                                                $path
     */
    public static function coerceValue($value, \GraphQL\Type\Definition\InputType $type, $blameNode = null, ?array $path = null)
    {
    }
    private static function ofErrors($errors)
    {
    }
    /**
     * @param string                   $message
     * @param Node                     $blameNode
     * @param mixed[]|null             $path
     * @param string                   $subMessage
     * @param Exception|Throwable|null $originalError
     *
     * @return Error
     */
    private static function coercionError($message, $blameNode, ?array $path = null, $subMessage = null, $originalError = null)
    {
    }
    /**
     * Build a string describing the path into the value where the error was found
     *
     * @param mixed[]|null $path
     *
     * @return string
     */
    private static function printPath(?array $path = null)
    {
    }
    /**
     * @param mixed $value
     *
     * @return (mixed|null)[]
     */
    private static function ofValue($value)
    {
    }
    /**
     * @param mixed|null $prev
     * @param mixed|null $key
     *
     * @return (mixed|null)[]
     */
    private static function atPath($prev, $key)
    {
    }
    /**
     * @param Error[]       $errors
     * @param Error|Error[] $moreErrors
     *
     * @return Error[]
     */
    private static function add($errors, $moreErrors)
    {
    }
}
class TypeComparators
{
    /**
     * Provided two types, return true if the types are equal (invariant).
     *
     * @return bool
     */
    public static function isEqualType(\GraphQL\Type\Definition\Type $typeA, \GraphQL\Type\Definition\Type $typeB)
    {
    }
    /**
     * Provided a type and a super type, return true if the first type is either
     * equal or a subset of the second super type (covariant).
     *
     * @return bool
     */
    public static function isTypeSubTypeOf(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\Type $maybeSubType, \GraphQL\Type\Definition\Type $superType)
    {
    }
    /**
     * Provided two composite types, determine if they "overlap". Two composite
     * types overlap when the Sets of possible concrete types for each intersect.
     *
     * This is often used to determine if a fragment of a given type could possibly
     * be visited in a context of another type.
     *
     * This function is commutative.
     *
     * @return bool
     */
    public static function doTypesOverlap(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\CompositeType $typeA, \GraphQL\Type\Definition\CompositeType $typeB)
    {
    }
}
/**
 * A way to keep track of pairs of things when the ordering of the pair does
 * not matter. We do this by maintaining a sort of double adjacency sets.
 */
class PairSet
{
    /** @var bool[][] */
    private $data;
    public function __construct()
    {
    }
    /**
     * @param string $a
     * @param string $b
     * @param bool   $areMutuallyExclusive
     *
     * @return bool
     */
    public function has($a, $b, $areMutuallyExclusive)
    {
    }
    /**
     * @param string $a
     * @param string $b
     * @param bool   $areMutuallyExclusive
     */
    public function add($a, $b, $areMutuallyExclusive)
    {
    }
    /**
     * @param string $a
     * @param string $b
     * @param bool   $areMutuallyExclusive
     */
    private function pairSetAdd($a, $b, $areMutuallyExclusive)
    {
    }
}
/**
 * Build instance of `GraphQL\Type\Schema` out of type language definition (string or parsed AST)
 * See [section in docs](type-system/type-language.md) for details.
 */
class BuildSchema
{
    /** @var DocumentNode */
    private $ast;
    /** @var array<string, TypeDefinitionNode> */
    private $nodeMap;
    /** @var callable|null */
    private $typeConfigDecorator;
    /** @var array<string, bool> */
    private $options;
    /**
     * @param array<string, bool> $options
     */
    public function __construct(\GraphQL\Language\AST\DocumentNode $ast, ?callable $typeConfigDecorator = null, array $options = [])
    {
    }
    /**
     * A helper function to build a GraphQLSchema directly from a source
     * document.
     *
     * @param DocumentNode|Source|string $source
     * @param array<string, bool>        $options
     *
     * @return Schema
     *
     * @api
     */
    public static function build($source, ?callable $typeConfigDecorator = null, array $options = [])
    {
    }
    /**
     * This takes the ast of a schema document produced by the parse function in
     * GraphQL\Language\Parser.
     *
     * If no schema definition is provided, then it will look for types named Query
     * and Mutation.
     *
     * Given that AST it constructs a GraphQL\Type\Schema. The resulting schema
     * has no resolve methods, so execution will use default resolvers.
     *
     * Accepts options as a third argument:
     *
     *    - commentDescriptions:
     *        Provide true to use preceding comments as the description.
     *        This option is provided to ease adoption and will be removed in v16.
     *
     * @param array<string, bool> $options
     *
     * @return Schema
     *
     * @throws Error
     *
     * @api
     */
    public static function buildAST(\GraphQL\Language\AST\DocumentNode $ast, ?callable $typeConfigDecorator = null, array $options = [])
    {
    }
    public function buildSchema()
    {
    }
    /**
     * @param SchemaDefinitionNode $schemaDef
     *
     * @return string[]
     *
     * @throws Error
     */
    private function getOperationTypes($schemaDef)
    {
    }
}
/**
 * A way to track interface implementations.
 *
 * Distinguishes between implementations by ObjectTypes and InterfaceTypes.
 */
class InterfaceImplementations
{
    /** @var array<int, ObjectType> */
    private $objects;
    /** @var array<int, InterfaceType> */
    private $interfaces;
    /**
     * @param array<int, ObjectType>    $objects
     * @param array<int, InterfaceType> $interfaces
     */
    public function __construct(array $objects, array $interfaces)
    {
    }
    /**
     * @return array<int, ObjectType>
     */
    public function objects() : array
    {
    }
    /**
     * @return array<int, InterfaceType>
     */
    public function interfaces() : array
    {
    }
}
class ASTDefinitionBuilder
{
    /** @var array<string, Node&TypeDefinitionNode> */
    private $typeDefinitionsMap;
    /** @var callable */
    private $typeConfigDecorator;
    /** @var array<string, bool> */
    private $options;
    /** @var callable */
    private $resolveType;
    /** @var array<string, Type> */
    private $cache;
    /**
     * code sniffer doesn't understand this syntax. Pr with a fix here: waiting on https://github.com/squizlabs/PHP_CodeSniffer/pull/2919
     * phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType
     * @param array<string, Node&TypeDefinitionNode> $typeDefinitionsMap
     * @param array<string, bool> $options
     */
    public function __construct(array $typeDefinitionsMap, array $options, callable $resolveType, ?callable $typeConfigDecorator = null)
    {
    }
    public function buildDirective(\GraphQL\Language\AST\DirectiveDefinitionNode $directiveNode) : \GraphQL\Type\Definition\Directive
    {
    }
    /**
     * Given an ast node, returns its string description.
     */
    private function getDescription(\GraphQL\Language\AST\Node $node) : ?string
    {
    }
    private function getLeadingCommentBlock(\GraphQL\Language\AST\Node $node) : ?string
    {
    }
    /**
     * @return array<string, array<string, mixed>>
     */
    private function makeInputValues(\GraphQL\Language\AST\NodeList $values) : array
    {
    }
    private function buildWrappedType(\GraphQL\Language\AST\TypeNode $typeNode) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @param string|(Node &NamedTypeNode)|(Node&TypeDefinitionNode) $ref
     */
    public function buildType($ref) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @param (Node &NamedTypeNode)|(Node&TypeDefinitionNode)|null $typeNode
     *
     * @throws Error
     */
    private function internalBuildType(string $typeName, ?\GraphQL\Language\AST\Node $typeNode = null) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @param ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode|EnumTypeDefinitionNode|ScalarTypeDefinitionNode|InputObjectTypeDefinitionNode|UnionTypeDefinitionNode $def
     *
     * @return CustomScalarType|EnumType|InputObjectType|InterfaceType|ObjectType|UnionType
     *
     * @throws Error
     */
    private function makeSchemaDef(\GraphQL\Language\AST\Node $def) : \GraphQL\Type\Definition\Type
    {
    }
    private function makeTypeDef(\GraphQL\Language\AST\ObjectTypeDefinitionNode $def) : \GraphQL\Type\Definition\ObjectType
    {
    }
    /**
     * @param ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode $def
     *
     * @return array<string, array<string, mixed>>
     */
    private function makeFieldDefMap(\GraphQL\Language\AST\Node $def) : array
    {
    }
    /**
     * @return array<string, mixed>
     */
    public function buildField(\GraphQL\Language\AST\FieldDefinitionNode $field) : array
    {
    }
    /**
     * Given a collection of directives, returns the string value for the
     * deprecation reason.
     *
     * @param EnumValueDefinitionNode|FieldDefinitionNode $node
     */
    private function getDeprecationReason(\GraphQL\Language\AST\Node $node) : ?string
    {
    }
    /**
     * @param ObjectTypeDefinitionNode|InterfaceTypeDefinitionNode $def
     *
     * @return array<int, Type>
     */
    private function makeImplementedInterfaces($def) : array
    {
    }
    private function makeInterfaceDef(\GraphQL\Language\AST\InterfaceTypeDefinitionNode $def) : \GraphQL\Type\Definition\InterfaceType
    {
    }
    private function makeEnumDef(\GraphQL\Language\AST\EnumTypeDefinitionNode $def) : \GraphQL\Type\Definition\EnumType
    {
    }
    private function makeUnionDef(\GraphQL\Language\AST\UnionTypeDefinitionNode $def) : \GraphQL\Type\Definition\UnionType
    {
    }
    private function makeScalarDef(\GraphQL\Language\AST\ScalarTypeDefinitionNode $def) : \GraphQL\Type\Definition\CustomScalarType
    {
    }
    private function makeInputObjectDef(\GraphQL\Language\AST\InputObjectTypeDefinitionNode $def) : \GraphQL\Type\Definition\InputObjectType
    {
    }
    /**
     * @param array<string, mixed> $config
     *
     * @return CustomScalarType|EnumType|InputObjectType|InterfaceType|ObjectType|UnionType
     *
     * @throws Error
     */
    private function makeSchemaDefFromConfig(\GraphQL\Language\AST\Node $def, array $config) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @return array<string, mixed>
     */
    public function buildInputField(\GraphQL\Language\AST\InputValueDefinitionNode $value) : array
    {
    }
    /**
     * @return array<string, mixed>
     */
    public function buildEnumValue(\GraphQL\Language\AST\EnumValueDefinitionNode $value) : array
    {
    }
}
class Utils
{
    public static function undefined()
    {
    }
    /**
     * Check if the value is invalid
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isInvalid($value)
    {
    }
    /**
     * @param object   $obj
     * @param mixed[]  $vars
     * @param string[] $requiredKeys
     *
     * @return object
     */
    public static function assign($obj, array $vars, array $requiredKeys = [])
    {
    }
    /**
     * @param iterable<mixed> $iterable
     *
     * @return mixed|null
     */
    public static function find($iterable, callable $predicate)
    {
    }
    /**
     * @param iterable<mixed> $iterable
     *
     * @return array<mixed>
     *
     * @throws Exception
     */
    public static function filter($iterable, callable $predicate) : array
    {
    }
    /**
     * @param iterable<mixed> $iterable
     *
     * @return array<mixed>
     *
     * @throws Exception
     */
    public static function map($iterable, callable $fn) : array
    {
    }
    /**
     * @param iterable<mixed> $iterable
     *
     * @return array<mixed>
     *
     * @throws Exception
     */
    public static function mapKeyValue($iterable, callable $fn) : array
    {
    }
    /**
     * @param iterable<mixed> $iterable
     *
     * @return array<mixed>
     *
     * @throws Exception
     */
    public static function keyMap($iterable, callable $keyFn) : array
    {
    }
    /**
     * @param iterable<mixed> $iterable
     */
    public static function each($iterable, callable $fn) : void
    {
    }
    /**
     * Splits original iterable to several arrays with keys equal to $keyFn return
     *
     * E.g. Utils::groupBy([1, 2, 3, 4, 5], function($value) {return $value % 3}) will output:
     * [
     *    1 => [1, 4],
     *    2 => [2, 5],
     *    0 => [3],
     * ]
     *
     * $keyFn is also allowed to return array of keys. Then value will be added to all arrays with given keys
     *
     * @param iterable<mixed> $iterable
     *
     * @return array<array<mixed>>
     */
    public static function groupBy($iterable, callable $keyFn) : array
    {
    }
    /**
     * @param iterable<mixed> $iterable
     *
     * @return array<mixed>
     */
    public static function keyValMap($iterable, callable $keyFn, callable $valFn) : array
    {
    }
    /**
     * @param iterable<mixed> $iterable
     */
    public static function every($iterable, callable $predicate) : bool
    {
    }
    /**
     * @param iterable<mixed> $iterable
     */
    public static function some($iterable, callable $predicate) : bool
    {
    }
    /**
     * @param bool   $test
     * @param string $message
     */
    public static function invariant($test, $message = '')
    {
    }
    /**
     * @param Type|mixed $var
     *
     * @return string
     */
    public static function getVariableType($var)
    {
    }
    /**
     * @param mixed $var
     *
     * @return string
     */
    public static function printSafeJson($var)
    {
    }
    /**
     * @param Type|mixed $var
     *
     * @return string
     */
    public static function printSafe($var)
    {
    }
    /**
     * UTF-8 compatible chr()
     *
     * @param string $ord
     * @param string $encoding
     *
     * @return string
     */
    public static function chr($ord, $encoding = 'UTF-8')
    {
    }
    /**
     * UTF-8 compatible ord()
     *
     * @param string $char
     * @param string $encoding
     *
     * @return mixed
     */
    public static function ord($char, $encoding = 'UTF-8')
    {
    }
    /**
     * Returns UTF-8 char code at given $positing of the $string
     *
     * @param string $string
     * @param int    $position
     *
     * @return mixed
     */
    public static function charCodeAt($string, $position)
    {
    }
    /**
     * @param int|null $code
     *
     * @return string
     */
    public static function printCharCode($code)
    {
    }
    /**
     * Upholds the spec rules about naming.
     *
     * @param string $name
     *
     * @throws Error
     */
    public static function assertValidName($name)
    {
    }
    /**
     * Returns an Error if a name is invalid.
     *
     * @param string    $name
     * @param Node|null $node
     *
     * @return Error|null
     */
    public static function isValidNameError($name, $node = null)
    {
    }
    /**
     * Wraps original callable with PHP error handling (using set_error_handler).
     * Resulting callable will collect all PHP errors that occur during the call in $errors array.
     *
     * @param ErrorException[] $errors
     *
     * @return callable
     */
    public static function withErrorHandling(callable $fn, array &$errors)
    {
    }
    /**
     * @param string[] $items
     *
     * @return string
     */
    public static function quotedOrList(array $items)
    {
    }
    /**
     * @param string[] $items
     *
     * @return string
     */
    public static function orList(array $items)
    {
    }
    /**
     * Given an invalid input string and a list of valid options, returns a filtered
     * list of valid options sorted based on their similarity with the input.
     *
     * Includes a custom alteration from Damerau-Levenshtein to treat case changes
     * as a single edit which helps identify mis-cased values with an edit distance
     * of 1
     *
     * @param string   $input
     * @param string[] $options
     *
     * @return string[]
     */
    public static function suggestionList($input, array $options)
    {
    }
}
/**
 * Given an instance of Schema, prints it in GraphQL type language.
 */
class SchemaPrinter
{
    /**
     * @param array<string, bool> $options
     *    Available options:
     *    - commentDescriptions:
     *        Provide true to use preceding comments as the description.
     *        This option is provided to ease adoption and will be removed in v16.
     *
     * @api
     */
    public static function doPrint(\GraphQL\Type\Schema $schema, array $options = []) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printFilteredSchema(\GraphQL\Type\Schema $schema, callable $directiveFilter, callable $typeFilter, array $options) : string
    {
    }
    protected static function printSchemaDefinition(\GraphQL\Type\Schema $schema) : string
    {
    }
    /**
     * GraphQL schema define root types for each type of operation. These types are
     * the same as any other type and can be named in any manner, however there is
     * a common naming convention:
     *
     *   schema {
     *     query: Query
     *     mutation: Mutation
     *   }
     *
     * When using this naming convention, the schema description can be omitted.
     */
    protected static function isSchemaOfCommonNames(\GraphQL\Type\Schema $schema) : bool
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printDirective(\GraphQL\Type\Definition\Directive $directive, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printDescription(array $options, $def, $indentation = '', $firstInBlock = true) : string
    {
    }
    /**
     * @return string[]
     */
    protected static function descriptionLines(string $description, int $maxLen) : array
    {
    }
    /**
     * @return string[]
     */
    protected static function breakLine(string $line, int $maxLen) : array
    {
    }
    protected static function printDescriptionWithComments($lines, $indentation, $firstInBlock) : string
    {
    }
    protected static function escapeQuote($line) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printArgs(array $options, $args, $indentation = '') : string
    {
    }
    protected static function printInputValue($arg) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    public static function printType(\GraphQL\Type\Definition\Type $type, array $options = []) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printScalar(\GraphQL\Type\Definition\ScalarType $type, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printObject(\GraphQL\Type\Definition\ObjectType $type, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printFields(array $options, $type) : string
    {
    }
    protected static function printDeprecated($fieldOrEnumVal) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printInterface(\GraphQL\Type\Definition\InterfaceType $type, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printUnion(\GraphQL\Type\Definition\UnionType $type, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printEnum(\GraphQL\Type\Definition\EnumType $type, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printEnumValues($values, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     */
    protected static function printInputObject(\GraphQL\Type\Definition\InputObjectType $type, array $options) : string
    {
    }
    /**
     * @param array<string, bool> $options
     *
     * @api
     */
    public static function printIntrospectionSchema(\GraphQL\Type\Schema $schema, array $options = []) : string
    {
    }
}
/**
 * Various utilities dealing with AST
 */
class AST
{
    /**
     * Convert representation of AST as an associative array to instance of GraphQL\Language\AST\Node.
     *
     * For example:
     *
     * ```php
     * AST::fromArray([
     *     'kind' => 'ListValue',
     *     'values' => [
     *         ['kind' => 'StringValue', 'value' => 'my str'],
     *         ['kind' => 'StringValue', 'value' => 'my other str']
     *     ],
     *     'loc' => ['start' => 21, 'end' => 25]
     * ]);
     * ```
     *
     * Will produce instance of `ListValueNode` where `values` prop is a lazily-evaluated `NodeList`
     * returning instances of `StringValueNode` on access.
     *
     * This is a reverse operation for AST::toArray($node)
     *
     * @param mixed[] $node
     *
     * @api
     */
    public static function fromArray(array $node) : \GraphQL\Language\AST\Node
    {
    }
    /**
     * Convert AST node to serializable array
     *
     * @return mixed[]
     *
     * @api
     */
    public static function toArray(\GraphQL\Language\AST\Node $node) : array
    {
    }
    /**
     * Produces a GraphQL Value AST given a PHP value.
     *
     * Optionally, a GraphQL type may be provided, which will be used to
     * disambiguate between value primitives.
     *
     * | PHP Value     | GraphQL Value        |
     * | ------------- | -------------------- |
     * | Object        | Input Object         |
     * | Assoc Array   | Input Object         |
     * | Array         | List                 |
     * | Boolean       | Boolean              |
     * | String        | String / Enum Value  |
     * | Int           | Int                  |
     * | Float         | Int / Float          |
     * | Mixed         | Enum Value           |
     * | null          | NullValue            |
     *
     * @param Type|mixed|null $value
     *
     * @return ObjectValueNode|ListValueNode|BooleanValueNode|IntValueNode|FloatValueNode|EnumValueNode|StringValueNode|NullValueNode|null
     *
     * @api
     */
    public static function astFromValue($value, \GraphQL\Type\Definition\InputType $type)
    {
    }
    /**
     * Produces a PHP value given a GraphQL Value AST.
     *
     * A GraphQL type must be provided, which will be used to interpret different
     * GraphQL Value literals.
     *
     * Returns `null` when the value could not be validly coerced according to
     * the provided type.
     *
     * | GraphQL Value        | PHP Value     |
     * | -------------------- | ------------- |
     * | Input Object         | Assoc Array   |
     * | List                 | Array         |
     * | Boolean              | Boolean       |
     * | String               | String        |
     * | Int / Float          | Int / Float   |
     * | Enum Value           | Mixed         |
     * | Null Value           | null          |
     *
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null $valueNode
     * @param mixed[]|null                                                                                                                             $variables
     *
     * @return mixed[]|stdClass|null
     *
     * @throws Exception
     *
     * @api
     */
    public static function valueFromAST(?\GraphQL\Language\AST\ValueNode $valueNode, \GraphQL\Type\Definition\Type $type, ?array $variables = null)
    {
    }
    /**
     * Returns true if the provided valueNode is a variable which is not defined
     * in the set of variables.
     *
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $valueNode
     * @param mixed[]                                                                                                                             $variables
     *
     * @return bool
     */
    private static function isMissingVariable(\GraphQL\Language\AST\ValueNode $valueNode, $variables)
    {
    }
    /**
     * Produces a PHP value given a GraphQL Value AST.
     *
     * Unlike `valueFromAST()`, no type is provided. The resulting PHP value
     * will reflect the provided GraphQL value AST.
     *
     * | GraphQL Value        | PHP Value     |
     * | -------------------- | ------------- |
     * | Input Object         | Assoc Array   |
     * | List                 | Array         |
     * | Boolean              | Boolean       |
     * | String               | String        |
     * | Int / Float          | Int / Float   |
     * | Enum                 | Mixed         |
     * | Null                 | null          |
     *
     * @param Node         $valueNode
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     *
     * @api
     */
    public static function valueFromASTUntyped($valueNode, ?array $variables = null)
    {
    }
    /**
     * Returns type definition for given AST Type node
     *
     * @param NamedTypeNode|ListTypeNode|NonNullTypeNode $inputTypeNode
     *
     * @return Type|null
     *
     * @throws Exception
     *
     * @api
     */
    public static function typeFromAST(\GraphQL\Type\Schema $schema, $inputTypeNode)
    {
    }
    /**
     * @deprecated use getOperationAST instead.
     *
     * Returns operation type ("query", "mutation" or "subscription") given a document and operation name
     *
     * @param string $operationName
     *
     * @return bool|string
     *
     * @api
     */
    public static function getOperation(\GraphQL\Language\AST\DocumentNode $document, $operationName = null)
    {
    }
    /**
     * Returns the operation within a document by name.
     *
     * If a name is not provided, an operation is only returned if the document has exactly one.
     *
     * @api
     */
    public static function getOperationAST(\GraphQL\Language\AST\DocumentNode $document, ?string $operationName = null) : ?\GraphQL\Language\AST\OperationDefinitionNode
    {
    }
}
class BreakingChangesFinder
{
    public const BREAKING_CHANGE_FIELD_CHANGED_KIND = 'FIELD_CHANGED_KIND';
    public const BREAKING_CHANGE_FIELD_REMOVED = 'FIELD_REMOVED';
    public const BREAKING_CHANGE_TYPE_CHANGED_KIND = 'TYPE_CHANGED_KIND';
    public const BREAKING_CHANGE_TYPE_REMOVED = 'TYPE_REMOVED';
    public const BREAKING_CHANGE_TYPE_REMOVED_FROM_UNION = 'TYPE_REMOVED_FROM_UNION';
    public const BREAKING_CHANGE_VALUE_REMOVED_FROM_ENUM = 'VALUE_REMOVED_FROM_ENUM';
    public const BREAKING_CHANGE_ARG_REMOVED = 'ARG_REMOVED';
    public const BREAKING_CHANGE_ARG_CHANGED_KIND = 'ARG_CHANGED_KIND';
    public const BREAKING_CHANGE_REQUIRED_ARG_ADDED = 'REQUIRED_ARG_ADDED';
    public const BREAKING_CHANGE_REQUIRED_INPUT_FIELD_ADDED = 'REQUIRED_INPUT_FIELD_ADDED';
    public const BREAKING_CHANGE_IMPLEMENTED_INTERFACE_REMOVED = 'IMPLEMENTED_INTERFACE_REMOVED';
    public const BREAKING_CHANGE_DIRECTIVE_REMOVED = 'DIRECTIVE_REMOVED';
    public const BREAKING_CHANGE_DIRECTIVE_ARG_REMOVED = 'DIRECTIVE_ARG_REMOVED';
    public const BREAKING_CHANGE_DIRECTIVE_LOCATION_REMOVED = 'DIRECTIVE_LOCATION_REMOVED';
    public const BREAKING_CHANGE_REQUIRED_DIRECTIVE_ARG_ADDED = 'REQUIRED_DIRECTIVE_ARG_ADDED';
    public const DANGEROUS_CHANGE_ARG_DEFAULT_VALUE_CHANGED = 'ARG_DEFAULT_VALUE_CHANGE';
    public const DANGEROUS_CHANGE_VALUE_ADDED_TO_ENUM = 'VALUE_ADDED_TO_ENUM';
    public const DANGEROUS_CHANGE_IMPLEMENTED_INTERFACE_ADDED = 'IMPLEMENTED_INTERFACE_ADDED';
    public const DANGEROUS_CHANGE_TYPE_ADDED_TO_UNION = 'TYPE_ADDED_TO_UNION';
    public const DANGEROUS_CHANGE_OPTIONAL_INPUT_FIELD_ADDED = 'OPTIONAL_INPUT_FIELD_ADDED';
    public const DANGEROUS_CHANGE_OPTIONAL_ARG_ADDED = 'OPTIONAL_ARG_ADDED';
    /** @deprecated use BREAKING_CHANGE_IMPLEMENTED_INTERFACE_REMOVED instead, will be removed in v15.0.0. */
    public const BREAKING_CHANGE_INTERFACE_REMOVED_FROM_OBJECT = 'IMPLEMENTED_INTERFACE_REMOVED';
    /** @deprecated use DANGEROUS_CHANGE_IMPLEMENTED_INTERFACE_ADDED instead, will be removed in v15.0.0. */
    public const DANGEROUS_CHANGE_INTERFACE_ADDED_TO_OBJECT = 'IMPLEMENTED_INTERFACE_ADDED';
    /**
     * Given two schemas, returns an Array containing descriptions of all the types
     * of breaking changes covered by the other functions down below.
     *
     * @return string[][]
     */
    public static function findBreakingChanges(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to removing an entire type.
     *
     * @return string[][]
     */
    public static function findRemovedTypes(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to changing the type of a type.
     *
     * @return string[][]
     */
    public static function findTypesThatChangedKind(\GraphQL\Type\Schema $schemaA, \GraphQL\Type\Schema $schemaB) : iterable
    {
    }
    /**
     * @return string
     *
     * @throws TypeError
     */
    private static function typeKindName(\GraphQL\Type\Definition\Type $type)
    {
    }
    /**
     * @return string[][]
     */
    public static function findFieldsThatChangedTypeOnObjectOrInterfaceTypes(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * @return bool
     */
    private static function isChangeSafeForObjectOrInterfaceField(\GraphQL\Type\Definition\Type $oldType, \GraphQL\Type\Definition\Type $newType)
    {
    }
    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public static function findFieldsThatChangedTypeOnInputObjectTypes(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * @return bool
     */
    private static function isChangeSafeForInputObjectFieldOrFieldArg(\GraphQL\Type\Definition\Type $oldType, \GraphQL\Type\Definition\Type $newType)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to removing types from a union type.
     *
     * @return string[][]
     */
    public static function findTypesRemovedFromUnions(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any breaking
     * changes in the newSchema related to removing values from an enum type.
     *
     * @return string[][]
     */
    public static function findValuesRemovedFromEnums(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any
     * breaking or dangerous changes in the newSchema related to arguments
     * (such as removal or change of type of an argument, or a change in an
     * argument's default value).
     *
     * @return array<string, array<int,array<string, string>>>
     */
    public static function findArgChanges(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * @return string[][]
     */
    public static function findInterfacesRemovedFromObjectTypes(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * @return string[][]
     */
    public static function findRemovedDirectives(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    private static function getDirectiveMapForSchema(\GraphQL\Type\Schema $schema)
    {
    }
    public static function findRemovedDirectiveArgs(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    public static function findRemovedArgsForDirectives(\GraphQL\Type\Definition\Directive $oldDirective, \GraphQL\Type\Definition\Directive $newDirective)
    {
    }
    private static function getArgumentMapForDirective(\GraphQL\Type\Definition\Directive $directive)
    {
    }
    public static function findAddedNonNullDirectiveArgs(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * @return FieldArgument[]
     */
    public static function findAddedArgsForDirective(\GraphQL\Type\Definition\Directive $oldDirective, \GraphQL\Type\Definition\Directive $newDirective)
    {
    }
    /**
     * @return string[][]
     */
    public static function findRemovedDirectiveLocations(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    public static function findRemovedLocationsForDirective(\GraphQL\Type\Definition\Directive $oldDirective, \GraphQL\Type\Definition\Directive $newDirective)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of all the types
     * of potentially dangerous changes covered by the other functions down below.
     *
     * @return string[][]
     */
    public static function findDangerousChanges(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any dangerous
     * changes in the newSchema related to adding values to an enum type.
     *
     * @return string[][]
     */
    public static function findValuesAddedToEnums(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * @return string[][]
     */
    public static function findInterfacesAddedToObjectTypes(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
    /**
     * Given two schemas, returns an Array containing descriptions of any dangerous
     * changes in the newSchema related to adding types to a union type.
     *
     * @return string[][]
     */
    public static function findTypesAddedToUnions(\GraphQL\Type\Schema $oldSchema, \GraphQL\Type\Schema $newSchema)
    {
    }
}
class SchemaExtender
{
    const SCHEMA_EXTENSION = 'SchemaExtension';
    /** @var Type[] */
    protected static $extendTypeCache;
    /** @var mixed[] */
    protected static $typeExtensionsMap;
    /** @var ASTDefinitionBuilder */
    protected static $astBuilder;
    /**
     * @return TypeExtensionNode[]|null
     */
    protected static function getExtensionASTNodes(\GraphQL\Type\Definition\NamedType $type) : ?array
    {
    }
    /**
     * @throws Error
     */
    protected static function checkExtensionNode(\GraphQL\Type\Definition\Type $type, \GraphQL\Language\AST\Node $node) : void
    {
    }
    protected static function extendScalarType(\GraphQL\Type\Definition\ScalarType $type) : \GraphQL\Type\Definition\CustomScalarType
    {
    }
    protected static function extendUnionType(\GraphQL\Type\Definition\UnionType $type) : \GraphQL\Type\Definition\UnionType
    {
    }
    protected static function extendEnumType(\GraphQL\Type\Definition\EnumType $type) : \GraphQL\Type\Definition\EnumType
    {
    }
    protected static function extendInputObjectType(\GraphQL\Type\Definition\InputObjectType $type) : \GraphQL\Type\Definition\InputObjectType
    {
    }
    /**
     * @return mixed[]
     */
    protected static function extendInputFieldMap(\GraphQL\Type\Definition\InputObjectType $type) : array
    {
    }
    /**
     * @return mixed[]
     */
    protected static function extendValueMap(\GraphQL\Type\Definition\EnumType $type) : array
    {
    }
    /**
     * @return ObjectType[]
     */
    protected static function extendPossibleTypes(\GraphQL\Type\Definition\UnionType $type) : array
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     *
     * @return array<int, InterfaceType>
     */
    protected static function extendImplementedInterfaces(\GraphQL\Type\Definition\ImplementingType $type) : array
    {
    }
    protected static function extendType($typeDef)
    {
    }
    /**
     * @param FieldArgument[] $args
     *
     * @return mixed[]
     */
    protected static function extendArgs(array $args) : array
    {
    }
    /**
     * @param InterfaceType|ObjectType $type
     *
     * @return mixed[]
     *
     * @throws Error
     */
    protected static function extendFieldMap($type) : array
    {
    }
    protected static function extendObjectType(\GraphQL\Type\Definition\ObjectType $type) : \GraphQL\Type\Definition\ObjectType
    {
    }
    protected static function extendInterfaceType(\GraphQL\Type\Definition\InterfaceType $type) : \GraphQL\Type\Definition\InterfaceType
    {
    }
    protected static function isSpecifiedScalarType(\GraphQL\Type\Definition\Type $type) : bool
    {
    }
    protected static function extendNamedType(\GraphQL\Type\Definition\Type $type)
    {
    }
    /**
     * @return mixed|null
     */
    protected static function extendMaybeNamedType(?\GraphQL\Type\Definition\NamedType $type = null)
    {
    }
    /**
     * @param DirectiveDefinitionNode[] $directiveDefinitions
     *
     * @return Directive[]
     */
    protected static function getMergedDirectives(\GraphQL\Type\Schema $schema, array $directiveDefinitions) : array
    {
    }
    protected static function extendDirective(\GraphQL\Type\Definition\Directive $directive) : \GraphQL\Type\Definition\Directive
    {
    }
    /**
     * @param array<string, bool> $options
     */
    public static function extend(\GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentAST, array $options = [], ?callable $typeConfigDecorator = null) : \GraphQL\Type\Schema
    {
    }
}
class BuildClientSchema
{
    /** @var array<string, mixed[]> */
    private $introspection;
    /** @var array<string, bool> */
    private $options;
    /** @var array<string, NamedType&Type> */
    private $typeMap;
    /**
     * @param array<string, mixed[]> $introspectionQuery
     * @param array<string, bool>    $options
     */
    public function __construct(array $introspectionQuery, array $options = [])
    {
    }
    /**
     * Build a schema for use by client tools.
     *
     * Given the result of a client running the introspection query, creates and
     * returns a \GraphQL\Type\Schema instance which can be then used with all graphql-php
     * tools, but cannot be used to execute a query, as introspection does not
     * represent the "resolver", "parse" or "serialize" functions or any other
     * server-internal mechanisms.
     *
     * This function expects a complete introspection result. Don't forget to check
     * the "errors" field of a server response before calling this function.
     *
     * Accepts options as a third argument:
     *
     *    - assumeValid:
     *          When building a schema from a GraphQL service's introspection result, it
     *          might be safe to assume the schema is valid. Set to true to assume the
     *          produced schema is valid.
     *
     *          Default: false
     *
     * @param array<string, mixed[]> $introspectionQuery
     * @param array<string, bool>    $options
     *
     * @api
     */
    public static function build(array $introspectionQuery, array $options = []) : \GraphQL\Type\Schema
    {
    }
    public function buildSchema() : \GraphQL\Type\Schema
    {
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getType(array $typeRef) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @return NamedType&Type
     */
    private function getNamedType(string $typeName) : \GraphQL\Type\Definition\NamedType
    {
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getInputType(array $typeRef) : \GraphQL\Type\Definition\InputType
    {
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getOutputType(array $typeRef) : \GraphQL\Type\Definition\OutputType
    {
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    private function getObjectType(array $typeRef) : \GraphQL\Type\Definition\ObjectType
    {
    }
    /**
     * @param array<string, mixed> $typeRef
     */
    public function getInterfaceType(array $typeRef) : \GraphQL\Type\Definition\InterfaceType
    {
    }
    /**
     * @param array<string, mixed> $type
     */
    private function buildType(array $type) : \GraphQL\Type\Definition\NamedType
    {
    }
    /**
     * @param array<string, string> $scalar
     */
    private function buildScalarDef(array $scalar) : \GraphQL\Type\Definition\ScalarType
    {
    }
    /**
     * @param array<string, mixed> $implementingIntrospection
     *
     * @return array<int, InterfaceType>
     */
    private function buildImplementationsList(array $implementingIntrospection) : array
    {
    }
    /**
     * @param array<string, mixed> $object
     */
    private function buildObjectDef(array $object) : \GraphQL\Type\Definition\ObjectType
    {
    }
    /**
     * @param array<string, mixed> $interface
     */
    private function buildInterfaceDef(array $interface) : \GraphQL\Type\Definition\InterfaceType
    {
    }
    /**
     * @param array<string, string|array<string>> $union
     */
    private function buildUnionDef(array $union) : \GraphQL\Type\Definition\UnionType
    {
    }
    /**
     * @param array<string, string|array<string, string>> $enum
     */
    private function buildEnumDef(array $enum) : \GraphQL\Type\Definition\EnumType
    {
    }
    /**
     * @param array<string, mixed> $inputObject
     */
    private function buildInputObjectDef(array $inputObject) : \GraphQL\Type\Definition\InputObjectType
    {
    }
    /**
     * @param array<string, mixed> $typeIntrospection
     */
    private function buildFieldDefMap(array $typeIntrospection)
    {
    }
    /**
     * @param array<int, array<string, mixed>> $inputValueIntrospections
     *
     * @return array<string, array<string, mixed>>
     */
    private function buildInputValueDefMap(array $inputValueIntrospections) : array
    {
    }
    /**
     * @param array<string, mixed> $inputValueIntrospection
     *
     * @return array<string, mixed>
     */
    public function buildInputValue(array $inputValueIntrospection) : array
    {
    }
    /**
     * @param array<string, mixed> $directive
     */
    public function buildDirective(array $directive) : \GraphQL\Type\Definition\Directive
    {
    }
}
namespace GraphQL\Language;

/**
 * Utility for efficient AST traversal and modification.
 *
 * `visit()` will walk through an AST using a depth first traversal, calling
 * the visitor's enter function at each node in the traversal, and calling the
 * leave function after visiting that node and all of it's child nodes.
 *
 * By returning different values from the enter and leave functions, the
 * behavior of the visitor can be altered, including skipping over a sub-tree of
 * the AST (by returning false), editing the AST by returning a value or null
 * to remove the value, or to stop the whole traversal by returning BREAK.
 *
 * When using `visit()` to edit an AST, the original AST will not be modified, and
 * a new version of the AST with the changes applied will be returned from the
 * visit function.
 *
 *     $editedAST = Visitor::visit($ast, [
 *       'enter' => function ($node, $key, $parent, $path, $ancestors) {
 *         // return
 *         //   null: no action
 *         //   Visitor::skipNode(): skip visiting this node
 *         //   Visitor::stop(): stop visiting altogether
 *         //   Visitor::removeNode(): delete this node
 *         //   any value: replace this node with the returned value
 *       },
 *       'leave' => function ($node, $key, $parent, $path, $ancestors) {
 *         // return
 *         //   null: no action
 *         //   Visitor::stop(): stop visiting altogether
 *         //   Visitor::removeNode(): delete this node
 *         //   any value: replace this node with the returned value
 *       }
 *     ]);
 *
 * Alternatively to providing enter() and leave() functions, a visitor can
 * instead provide functions named the same as the [kinds of AST nodes](reference.md#graphqllanguageastnodekind),
 * or enter/leave visitors at a named key, leading to four permutations of
 * visitor API:
 *
 * 1) Named visitors triggered when entering a node a specific kind.
 *
 *     Visitor::visit($ast, [
 *       'Kind' => function ($node) {
 *         // enter the "Kind" node
 *       }
 *     ]);
 *
 * 2) Named visitors that trigger upon entering and leaving a node of
 *    a specific kind.
 *
 *     Visitor::visit($ast, [
 *       'Kind' => [
 *         'enter' => function ($node) {
 *           // enter the "Kind" node
 *         }
 *         'leave' => function ($node) {
 *           // leave the "Kind" node
 *         }
 *       ]
 *     ]);
 *
 * 3) Generic visitors that trigger upon entering and leaving any node.
 *
 *     Visitor::visit($ast, [
 *       'enter' => function ($node) {
 *         // enter any node
 *       },
 *       'leave' => function ($node) {
 *         // leave any node
 *       }
 *     ]);
 *
 * 4) Parallel visitors for entering and leaving nodes of a specific kind.
 *
 *     Visitor::visit($ast, [
 *       'enter' => [
 *         'Kind' => function($node) {
 *           // enter the "Kind" node
 *         }
 *       },
 *       'leave' => [
 *         'Kind' => function ($node) {
 *           // leave the "Kind" node
 *         }
 *       ]
 *     ]);
 */
class Visitor
{
    /** @var string[][] */
    public static $visitorKeys = [\GraphQL\Language\AST\NodeKind::NAME => [], \GraphQL\Language\AST\NodeKind::DOCUMENT => ['definitions'], \GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION => ['name', 'variableDefinitions', 'directives', 'selectionSet'], \GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION => ['variable', 'type', 'defaultValue', 'directives'], \GraphQL\Language\AST\NodeKind::VARIABLE => ['name'], \GraphQL\Language\AST\NodeKind::SELECTION_SET => ['selections'], \GraphQL\Language\AST\NodeKind::FIELD => ['alias', 'name', 'arguments', 'directives', 'selectionSet'], \GraphQL\Language\AST\NodeKind::ARGUMENT => ['name', 'value'], \GraphQL\Language\AST\NodeKind::FRAGMENT_SPREAD => ['name', 'directives'], \GraphQL\Language\AST\NodeKind::INLINE_FRAGMENT => ['typeCondition', 'directives', 'selectionSet'], \GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION => [
        'name',
        // Note: fragment variable definitions are experimental and may be changed
        // or removed in the future.
        'variableDefinitions',
        'typeCondition',
        'directives',
        'selectionSet',
    ], \GraphQL\Language\AST\NodeKind::INT => [], \GraphQL\Language\AST\NodeKind::FLOAT => [], \GraphQL\Language\AST\NodeKind::STRING => [], \GraphQL\Language\AST\NodeKind::BOOLEAN => [], \GraphQL\Language\AST\NodeKind::NULL => [], \GraphQL\Language\AST\NodeKind::ENUM => [], \GraphQL\Language\AST\NodeKind::LST => ['values'], \GraphQL\Language\AST\NodeKind::OBJECT => ['fields'], \GraphQL\Language\AST\NodeKind::OBJECT_FIELD => ['name', 'value'], \GraphQL\Language\AST\NodeKind::DIRECTIVE => ['name', 'arguments'], \GraphQL\Language\AST\NodeKind::NAMED_TYPE => ['name'], \GraphQL\Language\AST\NodeKind::LIST_TYPE => ['type'], \GraphQL\Language\AST\NodeKind::NON_NULL_TYPE => ['type'], \GraphQL\Language\AST\NodeKind::SCHEMA_DEFINITION => ['directives', 'operationTypes'], \GraphQL\Language\AST\NodeKind::OPERATION_TYPE_DEFINITION => ['type'], \GraphQL\Language\AST\NodeKind::SCALAR_TYPE_DEFINITION => ['description', 'name', 'directives'], \GraphQL\Language\AST\NodeKind::OBJECT_TYPE_DEFINITION => ['description', 'name', 'interfaces', 'directives', 'fields'], \GraphQL\Language\AST\NodeKind::FIELD_DEFINITION => ['description', 'name', 'arguments', 'type', 'directives'], \GraphQL\Language\AST\NodeKind::INPUT_VALUE_DEFINITION => ['description', 'name', 'type', 'defaultValue', 'directives'], \GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_DEFINITION => ['description', 'name', 'interfaces', 'directives', 'fields'], \GraphQL\Language\AST\NodeKind::UNION_TYPE_DEFINITION => ['description', 'name', 'directives', 'types'], \GraphQL\Language\AST\NodeKind::ENUM_TYPE_DEFINITION => ['description', 'name', 'directives', 'values'], \GraphQL\Language\AST\NodeKind::ENUM_VALUE_DEFINITION => ['description', 'name', 'directives'], \GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_DEFINITION => ['description', 'name', 'directives', 'fields'], \GraphQL\Language\AST\NodeKind::SCALAR_TYPE_EXTENSION => ['name', 'directives'], \GraphQL\Language\AST\NodeKind::OBJECT_TYPE_EXTENSION => ['name', 'interfaces', 'directives', 'fields'], \GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_EXTENSION => ['name', 'interfaces', 'directives', 'fields'], \GraphQL\Language\AST\NodeKind::UNION_TYPE_EXTENSION => ['name', 'directives', 'types'], \GraphQL\Language\AST\NodeKind::ENUM_TYPE_EXTENSION => ['name', 'directives', 'values'], \GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_EXTENSION => ['name', 'directives', 'fields'], \GraphQL\Language\AST\NodeKind::DIRECTIVE_DEFINITION => ['description', 'name', 'arguments', 'locations'], \GraphQL\Language\AST\NodeKind::SCHEMA_EXTENSION => ['directives', 'operationTypes']];
    /**
     * Visit the AST (see class description for details)
     *
     * @param Node|ArrayObject|stdClass $root
     * @param callable[]                $visitor
     * @param mixed[]|null              $keyMap
     *
     * @return Node|mixed
     *
     * @throws Exception
     *
     * @api
     */
    public static function visit($root, $visitor, $keyMap = null)
    {
    }
    /**
     * Returns marker for visitor break
     *
     * @return VisitorOperation
     *
     * @api
     */
    public static function stop()
    {
    }
    /**
     * Returns marker for skipping current node
     *
     * @return VisitorOperation
     *
     * @api
     */
    public static function skipNode()
    {
    }
    /**
     * Returns marker for removing a node
     *
     * @return VisitorOperation
     *
     * @api
     */
    public static function removeNode()
    {
    }
    /**
     * @param callable[][] $visitors
     *
     * @return array<string, callable>
     */
    public static function visitInParallel($visitors)
    {
    }
    /**
     * Creates a new visitor instance which maintains a provided TypeInfo instance
     * along with visiting visitor.
     */
    public static function visitWithTypeInfo(\GraphQL\Utils\TypeInfo $typeInfo, $visitor)
    {
    }
    /**
     * @param callable[]|null $visitor
     * @param string          $kind
     * @param bool            $isLeaving
     */
    public static function getVisitFn($visitor, $kind, $isLeaving) : ?callable
    {
    }
}
class SourceLocation implements \JsonSerializable
{
    /** @var int */
    public $line;
    /** @var int */
    public $column;
    /**
     * @param int $line
     * @param int $col
     */
    public function __construct($line, $col)
    {
    }
    /**
     * @return int[]
     */
    public function toArray()
    {
    }
    /**
     * @return int[]
     */
    public function toSerializableArray()
    {
    }
    /**
     * @return int[]
     */
    public function jsonSerialize()
    {
    }
}
/**
 * Represents a range of characters represented by a lexical token
 * within a Source.
 */
class Token
{
    // Each kind of token.
    public const SOF = '<SOF>';
    public const EOF = '<EOF>';
    public const BANG = '!';
    public const DOLLAR = '$';
    public const AMP = '&';
    public const PAREN_L = '(';
    public const PAREN_R = ')';
    public const SPREAD = '...';
    public const COLON = ':';
    public const EQUALS = '=';
    public const AT = '@';
    public const BRACKET_L = '[';
    public const BRACKET_R = ']';
    public const BRACE_L = '{';
    public const PIPE = '|';
    public const BRACE_R = '}';
    public const NAME = 'Name';
    public const INT = 'Int';
    public const FLOAT = 'Float';
    public const STRING = 'String';
    public const BLOCK_STRING = 'BlockString';
    public const COMMENT = 'Comment';
    /**
     * The kind of Token (see one of constants above).
     *
     * @var string
     */
    public $kind;
    /**
     * The character offset at which this Node begins.
     *
     * @var int
     */
    public $start;
    /**
     * The character offset at which this Node ends.
     *
     * @var int
     */
    public $end;
    /**
     * The 1-indexed line number on which this Token appears.
     *
     * @var int
     */
    public $line;
    /**
     * The 1-indexed column number at which this Token begins.
     *
     * @var int
     */
    public $column;
    /** @var string|null */
    public $value;
    /**
     * Tokens exist as nodes in a double-linked-list amongst all tokens
     * including ignored tokens. <SOF> is always the first node and <EOF>
     * the last.
     *
     * @var Token
     */
    public $prev;
    /** @var Token|null */
    public $next;
    /**
     * @param mixed $value
     */
    public function __construct(string $kind, int $start, int $end, int $line, int $column, ?\GraphQL\Language\Token $previous = null, $value = null)
    {
    }
    public function getDescription() : string
    {
    }
    /**
     * @return (string|int|null)[]
     */
    public function toArray() : array
    {
    }
}
/**
 * Parses string containing GraphQL query or [type definition](type-system/type-language.md) to Abstract Syntax Tree.
 *
 * Those magic functions allow partial parsing:
 *
 * @method static NameNode name(Source|string $source, bool[] $options = [])
 * @method static DocumentNode document(Source|string $source, bool[] $options = [])
 * @method static ExecutableDefinitionNode|TypeSystemDefinitionNode definition(Source|string $source, bool[] $options = [])
 * @method static ExecutableDefinitionNode executableDefinition(Source|string $source, bool[] $options = [])
 * @method static OperationDefinitionNode operationDefinition(Source|string $source, bool[] $options = [])
 * @method static string operationType(Source|string $source, bool[] $options = [])
 * @method static NodeList<VariableDefinitionNode> variableDefinitions(Source|string $source, bool[] $options = [])
 * @method static VariableDefinitionNode variableDefinition(Source|string $source, bool[] $options = [])
 * @method static VariableNode variable(Source|string $source, bool[] $options = [])
 * @method static SelectionSetNode selectionSet(Source|string $source, bool[] $options = [])
 * @method static mixed selection(Source|string $source, bool[] $options = [])
 * @method static FieldNode field(Source|string $source, bool[] $options = [])
 * @method static NodeList<ArgumentNode> arguments(Source|string $source, bool[] $options = [])
 * @method static NodeList<ArgumentNode> constArguments(Source|string $source, bool[] $options = [])
 * @method static ArgumentNode argument(Source|string $source, bool[] $options = [])
 * @method static ArgumentNode constArgument(Source|string $source, bool[] $options = [])
 * @method static FragmentSpreadNode|InlineFragmentNode fragment(Source|string $source, bool[] $options = [])
 * @method static FragmentDefinitionNode fragmentDefinition(Source|string $source, bool[] $options = [])
 * @method static NameNode fragmentName(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|NullValueNode|ObjectValueNode|StringValueNode|VariableNode valueLiteral(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|NullValueNode|ObjectValueNode|StringValueNode constValueLiteral(Source|string $source, bool[] $options = [])
 * @method static StringValueNode stringLiteral(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|StringValueNode constValue(Source|string $source, bool[] $options = [])
 * @method static BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|ObjectValueNode|StringValueNode|VariableNode variableValue(Source|string $source, bool[] $options = [])
 * @method static ListValueNode array(Source|string $source, bool[] $options = [])
 * @method static ListValueNode constArray(Source|string $source, bool[] $options = [])
 * @method static ObjectValueNode object(Source|string $source, bool[] $options = [])
 * @method static ObjectValueNode constObject(Source|string $source, bool[] $options = [])
 * @method static ObjectFieldNode objectField(Source|string $source, bool[] $options = [])
 * @method static ObjectFieldNode constObjectField(Source|string $source, bool[] $options = [])
 * @method static NodeList<DirectiveNode> directives(Source|string $source, bool[] $options = [])
 * @method static NodeList<DirectiveNode> constDirectives(Source|string $source, bool[] $options = [])
 * @method static DirectiveNode directive(Source|string $source, bool[] $options = [])
 * @method static DirectiveNode constDirective(Source|string $source, bool[] $options = [])
 * @method static ListTypeNode|NamedTypeNode|NonNullTypeNode typeReference(Source|string $source, bool[] $options = [])
 * @method static NamedTypeNode namedType(Source|string $source, bool[] $options = [])
 * @method static TypeSystemDefinitionNode typeSystemDefinition(Source|string $source, bool[] $options = [])
 * @method static StringValueNode|null description(Source|string $source, bool[] $options = [])
 * @method static SchemaDefinitionNode schemaDefinition(Source|string $source, bool[] $options = [])
 * @method static OperationTypeDefinitionNode operationTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static ScalarTypeDefinitionNode scalarTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static ObjectTypeDefinitionNode objectTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<NamedTypeNode> implementsInterfaces(Source|string $source, bool[] $options = [])
 * @method static NodeList<FieldDefinitionNode> fieldsDefinition(Source|string $source, bool[] $options = [])
 * @method static FieldDefinitionNode fieldDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<InputValueDefinitionNode> argumentsDefinition(Source|string $source, bool[] $options = [])
 * @method static InputValueDefinitionNode inputValueDefinition(Source|string $source, bool[] $options = [])
 * @method static InterfaceTypeDefinitionNode interfaceTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static UnionTypeDefinitionNode unionTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<NamedTypeNode> unionMemberTypes(Source|string $source, bool[] $options = [])
 * @method static EnumTypeDefinitionNode enumTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<EnumValueDefinitionNode> enumValuesDefinition(Source|string $source, bool[] $options = [])
 * @method static EnumValueDefinitionNode enumValueDefinition(Source|string $source, bool[] $options = [])
 * @method static InputObjectTypeDefinitionNode inputObjectTypeDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<InputValueDefinitionNode> inputFieldsDefinition(Source|string $source, bool[] $options = [])
 * @method static TypeExtensionNode typeExtension(Source|string $source, bool[] $options = [])
 * @method static SchemaTypeExtensionNode schemaTypeExtension(Source|string $source, bool[] $options = [])
 * @method static ScalarTypeExtensionNode scalarTypeExtension(Source|string $source, bool[] $options = [])
 * @method static ObjectTypeExtensionNode objectTypeExtension(Source|string $source, bool[] $options = [])
 * @method static InterfaceTypeExtensionNode interfaceTypeExtension(Source|string $source, bool[] $options = [])
 * @method static UnionTypeExtensionNode unionTypeExtension(Source|string $source, bool[] $options = [])
 * @method static EnumTypeExtensionNode enumTypeExtension(Source|string $source, bool[] $options = [])
 * @method static InputObjectTypeExtensionNode inputObjectTypeExtension(Source|string $source, bool[] $options = [])
 * @method static DirectiveDefinitionNode directiveDefinition(Source|string $source, bool[] $options = [])
 * @method static NodeList<NameNode> directiveLocations(Source|string $source, bool[] $options = [])
 * @method static NameNode directiveLocation(Source|string $source, bool[] $options = [])
 */
class Parser
{
    /**
     * Given a GraphQL source, parses it into a `GraphQL\Language\AST\DocumentNode`.
     * Throws `GraphQL\Error\SyntaxError` if a syntax error is encountered.
     *
     * Available options:
     *
     * noLocation: boolean,
     *   (By default, the parser creates AST nodes that know the location
     *   in the source that they correspond to. This configuration flag
     *   disables that behavior for performance or testing.)
     *
     * allowLegacySDLEmptyFields: boolean
     *   If enabled, the parser will parse empty fields sets in the Schema
     *   Definition Language. Otherwise, the parser will follow the current
     *   specification.
     *
     *   This option is provided to ease adoption of the final SDL specification
     *   and will be removed in a future major release.
     *
     * allowLegacySDLImplementsInterfaces: boolean
     *   If enabled, the parser will parse implemented interfaces with no `&`
     *   character between each interface. Otherwise, the parser will follow the
     *   current specification.
     *
     *   This option is provided to ease adoption of the final SDL specification
     *   and will be removed in a future major release.
     *
     * experimentalFragmentVariables: boolean,
     *   (If enabled, the parser will understand and parse variable definitions
     *   contained in a fragment definition. They'll be represented in the
     *   `variableDefinitions` field of the FragmentDefinitionNode.
     *
     *   The syntax is identical to normal, query-defined variables. For example:
     *
     *     fragment A($var: Boolean = false) on T  {
     *       ...
     *     }
     *
     *   Note: this feature is experimental and may change or be removed in the
     *   future.)
     *
     * @param Source|string $source
     * @param bool[]        $options
     *
     * @return DocumentNode
     *
     * @throws SyntaxError
     *
     * @api
     */
    public static function parse($source, array $options = [])
    {
    }
    /**
     * Given a string containing a GraphQL value (ex. `[42]`), parse the AST for
     * that value.
     * Throws `GraphQL\Error\SyntaxError` if a syntax error is encountered.
     *
     * This is useful within tools that operate upon GraphQL Values directly and
     * in isolation of complete GraphQL documents.
     *
     * Consider providing the results to the utility function: `GraphQL\Utils\AST::valueFromAST()`.
     *
     * @param Source|string $source
     * @param bool[]        $options
     *
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|ObjectValueNode|StringValueNode|VariableNode
     *
     * @api
     */
    public static function parseValue($source, array $options = [])
    {
    }
    /**
     * Given a string containing a GraphQL Type (ex. `[Int!]`), parse the AST for
     * that type.
     * Throws `GraphQL\Error\SyntaxError` if a syntax error is encountered.
     *
     * This is useful within tools that operate upon GraphQL Types directly and
     * in isolation of complete GraphQL documents.
     *
     * Consider providing the results to the utility function: `GraphQL\Utils\AST::typeFromAST()`.
     *
     * @param Source|string $source
     * @param bool[]        $options
     *
     * @return ListTypeNode|NamedTypeNode|NonNullTypeNode
     *
     * @api
     */
    public static function parseType($source, array $options = [])
    {
    }
    /**
     * Parse partial source by delegating calls to the internal parseX methods.
     *
     * @param bool[] $arguments
     *
     * @throws SyntaxError
     */
    public static function __callStatic(string $name, array $arguments)
    {
    }
    /** @var Lexer */
    private $lexer;
    /**
     * @param Source|string $source
     * @param bool[]        $options
     */
    public function __construct($source, array $options = [])
    {
    }
    /**
     * Returns a location object, used to identify the place in
     * the source that created a given parsed object.
     */
    private function loc(\GraphQL\Language\Token $startToken) : ?\GraphQL\Language\AST\Location
    {
    }
    /**
     * Determines if the next token is of a given kind
     */
    private function peek(string $kind) : bool
    {
    }
    /**
     * If the next token is of the given kind, return true after advancing
     * the parser. Otherwise, do not change the parser state and return false.
     */
    private function skip(string $kind) : bool
    {
    }
    /**
     * If the next token is of the given kind, return that token after advancing
     * the parser. Otherwise, do not change the parser state and return false.
     *
     * @throws SyntaxError
     */
    private function expect(string $kind) : \GraphQL\Language\Token
    {
    }
    /**
     * If the next token is a keyword with the given value, advance the lexer.
     * Otherwise, throw an error.
     *
     * @throws SyntaxError
     */
    private function expectKeyword(string $value) : void
    {
    }
    /**
     * If the next token is a given keyword, return "true" after advancing
     * the lexer. Otherwise, do not change the parser state and return "false".
     */
    private function expectOptionalKeyword(string $value) : bool
    {
    }
    private function unexpected(?\GraphQL\Language\Token $atToken = null) : \GraphQL\Error\SyntaxError
    {
    }
    /**
     * Returns a possibly empty list of parse nodes, determined by
     * the parseFn. This list begins with a lex token of openKind
     * and ends with a lex token of closeKind. Advances the parser
     * to the next lex token after the closing token.
     *
     * @throws SyntaxError
     */
    private function any(string $openKind, callable $parseFn, string $closeKind) : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * Returns a non-empty list of parse nodes, determined by
     * the parseFn. This list begins with a lex token of openKind
     * and ends with a lex token of closeKind. Advances the parser
     * to the next lex token after the closing token.
     *
     * @throws SyntaxError
     */
    private function many(string $openKind, callable $parseFn, string $closeKind) : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * Converts a name lex token into a name parse node.
     *
     * @throws SyntaxError
     */
    private function parseName() : \GraphQL\Language\AST\NameNode
    {
    }
    /**
     * Implements the parsing rules in the Document section.
     *
     * @throws SyntaxError
     */
    private function parseDocument() : \GraphQL\Language\AST\DocumentNode
    {
    }
    /**
     * @return ExecutableDefinitionNode|TypeSystemDefinitionNode
     *
     * @throws SyntaxError
     */
    private function parseDefinition() : \GraphQL\Language\AST\DefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseExecutableDefinition() : \GraphQL\Language\AST\ExecutableDefinitionNode
    {
    }
    // Implements the parsing rules in the Operations section.
    /**
     * @throws SyntaxError
     */
    private function parseOperationDefinition() : \GraphQL\Language\AST\OperationDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseOperationType() : string
    {
    }
    private function parseVariableDefinitions() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseVariableDefinition() : \GraphQL\Language\AST\VariableDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseVariable() : \GraphQL\Language\AST\VariableNode
    {
    }
    private function parseSelectionSet() : \GraphQL\Language\AST\SelectionSetNode
    {
    }
    /**
     *  Selection :
     *   - Field
     *   - FragmentSpread
     *   - InlineFragment
     */
    private function parseSelection() : \GraphQL\Language\AST\SelectionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseField() : \GraphQL\Language\AST\FieldNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseArguments(bool $isConst) : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseArgument() : \GraphQL\Language\AST\ArgumentNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseConstArgument() : \GraphQL\Language\AST\ArgumentNode
    {
    }
    // Implements the parsing rules in the Fragments section.
    /**
     * @return FragmentSpreadNode|InlineFragmentNode
     *
     * @throws SyntaxError
     */
    private function parseFragment() : \GraphQL\Language\AST\SelectionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseFragmentDefinition() : \GraphQL\Language\AST\FragmentDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseFragmentName() : \GraphQL\Language\AST\NameNode
    {
    }
    // Implements the parsing rules in the Values section.
    /**
     * Value[Const] :
     *   - [~Const] Variable
     *   - IntValue
     *   - FloatValue
     *   - StringValue
     *   - BooleanValue
     *   - NullValue
     *   - EnumValue
     *   - ListValue[?Const]
     *   - ObjectValue[?Const]
     *
     * BooleanValue : one of `true` `false`
     *
     * NullValue : `null`
     *
     * EnumValue : Name but not `true`, `false` or `null`
     *
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|StringValueNode|VariableNode|ListValueNode|ObjectValueNode|NullValueNode
     *
     * @throws SyntaxError
     */
    private function parseValueLiteral(bool $isConst) : \GraphQL\Language\AST\ValueNode
    {
    }
    private function parseStringLiteral() : \GraphQL\Language\AST\StringValueNode
    {
    }
    /**
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|StringValueNode|VariableNode
     *
     * @throws SyntaxError
     */
    private function parseConstValue() : \GraphQL\Language\AST\ValueNode
    {
    }
    /**
     * @return BooleanValueNode|EnumValueNode|FloatValueNode|IntValueNode|ListValueNode|ObjectValueNode|StringValueNode|VariableNode
     */
    private function parseVariableValue() : \GraphQL\Language\AST\ValueNode
    {
    }
    private function parseArray(bool $isConst) : \GraphQL\Language\AST\ListValueNode
    {
    }
    private function parseObject(bool $isConst) : \GraphQL\Language\AST\ObjectValueNode
    {
    }
    private function parseObjectField(bool $isConst) : \GraphQL\Language\AST\ObjectFieldNode
    {
    }
    // Implements the parsing rules in the Directives section.
    /**
     * @throws SyntaxError
     */
    private function parseDirectives(bool $isConst) : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseDirective(bool $isConst) : \GraphQL\Language\AST\DirectiveNode
    {
    }
    // Implements the parsing rules in the Types section.
    /**
     * Handles the Type: TypeName, ListType, and NonNullType parsing rules.
     *
     * @return ListTypeNode|NamedTypeNode|NonNullTypeNode
     *
     * @throws SyntaxError
     */
    private function parseTypeReference() : \GraphQL\Language\AST\TypeNode
    {
    }
    private function parseNamedType() : \GraphQL\Language\AST\NamedTypeNode
    {
    }
    // Implements the parsing rules in the Type Definition section.
    /**
     * TypeSystemDefinition :
     *   - SchemaDefinition
     *   - TypeDefinition
     *   - TypeExtension
     *   - DirectiveDefinition
     *
     * TypeDefinition :
     *   - ScalarTypeDefinition
     *   - ObjectTypeDefinition
     *   - InterfaceTypeDefinition
     *   - UnionTypeDefinition
     *   - EnumTypeDefinition
     *   - InputObjectTypeDefinition
     *
     * @throws SyntaxError
     */
    private function parseTypeSystemDefinition() : \GraphQL\Language\AST\TypeSystemDefinitionNode
    {
    }
    private function peekDescription() : bool
    {
    }
    private function parseDescription() : ?\GraphQL\Language\AST\StringValueNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseSchemaDefinition() : \GraphQL\Language\AST\SchemaDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseOperationTypeDefinition() : \GraphQL\Language\AST\OperationTypeDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseScalarTypeDefinition() : \GraphQL\Language\AST\ScalarTypeDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseObjectTypeDefinition() : \GraphQL\Language\AST\ObjectTypeDefinitionNode
    {
    }
    /**
     * ImplementsInterfaces :
     *   - implements `&`? NamedType
     *   - ImplementsInterfaces & NamedType
     */
    private function parseImplementsInterfaces() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseFieldsDefinition() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseFieldDefinition() : \GraphQL\Language\AST\FieldDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseArgumentsDefinition() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputValueDefinition() : \GraphQL\Language\AST\InputValueDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseInterfaceTypeDefinition() : \GraphQL\Language\AST\InterfaceTypeDefinitionNode
    {
    }
    /**
     * UnionTypeDefinition :
     *   - Description? union Name Directives[Const]? UnionMemberTypes?
     *
     * @throws SyntaxError
     */
    private function parseUnionTypeDefinition() : \GraphQL\Language\AST\UnionTypeDefinitionNode
    {
    }
    /**
     * UnionMemberTypes :
     *   - = `|`? NamedType
     *   - UnionMemberTypes | NamedType
     */
    private function parseUnionMemberTypes() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumTypeDefinition() : \GraphQL\Language\AST\EnumTypeDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumValuesDefinition() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumValueDefinition() : \GraphQL\Language\AST\EnumValueDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputObjectTypeDefinition() : \GraphQL\Language\AST\InputObjectTypeDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputFieldsDefinition() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * TypeExtension :
     *   - ScalarTypeExtension
     *   - ObjectTypeExtension
     *   - InterfaceTypeExtension
     *   - UnionTypeExtension
     *   - EnumTypeExtension
     *   - InputObjectTypeDefinition
     *
     * @throws SyntaxError
     */
    private function parseTypeExtension() : \GraphQL\Language\AST\TypeExtensionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseSchemaTypeExtension() : \GraphQL\Language\AST\SchemaTypeExtensionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseScalarTypeExtension() : \GraphQL\Language\AST\ScalarTypeExtensionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseObjectTypeExtension() : \GraphQL\Language\AST\ObjectTypeExtensionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseInterfaceTypeExtension() : \GraphQL\Language\AST\InterfaceTypeExtensionNode
    {
    }
    /**
     * UnionTypeExtension :
     *   - extend union Name Directives[Const]? UnionMemberTypes
     *   - extend union Name Directives[Const]
     *
     * @throws SyntaxError
     */
    private function parseUnionTypeExtension() : \GraphQL\Language\AST\UnionTypeExtensionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseEnumTypeExtension() : \GraphQL\Language\AST\EnumTypeExtensionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseInputObjectTypeExtension() : \GraphQL\Language\AST\InputObjectTypeExtensionNode
    {
    }
    /**
     * DirectiveDefinition :
     *   - Description? directive @ Name ArgumentsDefinition? `repeatable`? on DirectiveLocations
     *
     * @throws SyntaxError
     */
    private function parseDirectiveDefinition() : \GraphQL\Language\AST\DirectiveDefinitionNode
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseDirectiveLocations() : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @throws SyntaxError
     */
    private function parseDirectiveLocation() : \GraphQL\Language\AST\NameNode
    {
    }
}
class VisitorOperation
{
    /** @var bool */
    public $doBreak;
    /** @var bool */
    public $doContinue;
    /** @var bool */
    public $removeNode;
}
/**
 * A Lexer is a stateful stream generator in that every time
 * it is advanced, it returns the next token in the Source. Assuming the
 * source lexes, the final Token emitted by the lexer will be of kind
 * EOF, after which the lexer will repeatedly return the same EOF token
 * whenever called.
 *
 * Algorithm is O(N) both on memory and time
 */
class Lexer
{
    private const TOKEN_BANG = 33;
    private const TOKEN_HASH = 35;
    private const TOKEN_DOLLAR = 36;
    private const TOKEN_AMP = 38;
    private const TOKEN_PAREN_L = 40;
    private const TOKEN_PAREN_R = 41;
    private const TOKEN_DOT = 46;
    private const TOKEN_COLON = 58;
    private const TOKEN_EQUALS = 61;
    private const TOKEN_AT = 64;
    private const TOKEN_BRACKET_L = 91;
    private const TOKEN_BRACKET_R = 93;
    private const TOKEN_BRACE_L = 123;
    private const TOKEN_PIPE = 124;
    private const TOKEN_BRACE_R = 125;
    /** @var Source */
    public $source;
    /** @var bool[] */
    public $options;
    /**
     * The previously focused non-ignored token.
     *
     * @var Token
     */
    public $lastToken;
    /**
     * The currently focused non-ignored token.
     *
     * @var Token
     */
    public $token;
    /**
     * The (1-indexed) line containing the current token.
     *
     * @var int
     */
    public $line;
    /**
     * The character offset at which the current line begins.
     *
     * @var int
     */
    public $lineStart;
    /**
     * Current cursor position for UTF8 encoding of the source
     *
     * @var int
     */
    private $position;
    /**
     * Current cursor position for ASCII representation of the source
     *
     * @var int
     */
    private $byteStreamPosition;
    /**
     * @param bool[] $options
     */
    public function __construct(\GraphQL\Language\Source $source, array $options = [])
    {
    }
    /**
     * @return Token
     */
    public function advance()
    {
    }
    public function lookahead()
    {
    }
    /**
     * @return Token
     *
     * @throws SyntaxError
     */
    private function readToken(\GraphQL\Language\Token $prev)
    {
    }
    private function unexpectedCharacterMessage($code)
    {
    }
    /**
     * Reads an alphanumeric + underscore name from the source.
     *
     * [_A-Za-z][_0-9A-Za-z]*
     *
     * @param int $line
     * @param int $col
     *
     * @return Token
     */
    private function readName($line, $col, \GraphQL\Language\Token $prev)
    {
    }
    /**
     * Reads a number token from the source file, either a float
     * or an int depending on whether a decimal point appears.
     *
     * Int:   -?(0|[1-9][0-9]*)
     * Float: -?(0|[1-9][0-9]*)(\.[0-9]+)?((E|e)(+|-)?[0-9]+)?
     *
     * @param int $line
     * @param int $col
     *
     * @return Token
     *
     * @throws SyntaxError
     */
    private function readNumber($line, $col, \GraphQL\Language\Token $prev)
    {
    }
    /**
     * Returns string with all digits + changes current string cursor position to point to the first char after digits
     */
    private function readDigits()
    {
    }
    /**
     * @param int $line
     * @param int $col
     *
     * @return Token
     *
     * @throws SyntaxError
     */
    private function readString($line, $col, \GraphQL\Language\Token $prev)
    {
    }
    /**
     * Reads a block string token from the source file.
     *
     * """("?"?(\\"""|\\(?!=""")|[^"\\]))*"""
     */
    private function readBlockString($line, $col, \GraphQL\Language\Token $prev)
    {
    }
    private function assertValidStringCharacterCode($code, $position)
    {
    }
    private function assertValidBlockStringCharacterCode($code, $position)
    {
    }
    /**
     * Reads from body starting at startPosition until it finds a non-whitespace
     * or commented character, then places cursor to the position of that character.
     */
    private function positionAfterWhitespace()
    {
    }
    /**
     * Reads a comment token from the source file.
     *
     * #[\u0009\u0020-\uFFFF]*
     *
     * @param int $line
     * @param int $col
     *
     * @return Token
     */
    private function readComment($line, $col, \GraphQL\Language\Token $prev)
    {
    }
    /**
     * Reads next UTF8Character from the byte stream, starting from $byteStreamPosition.
     *
     * @param bool $advance
     * @param int  $byteStreamPosition
     *
     * @return (string|int)[]
     */
    private function readChar($advance = false, $byteStreamPosition = null)
    {
    }
    /**
     * Reads next $numberOfChars UTF8 characters from the byte stream, starting from $byteStreamPosition.
     *
     * @param int  $charCount
     * @param bool $advance
     * @param null $byteStreamPosition
     *
     * @return (string|int)[]
     */
    private function readChars($charCount, $advance = false, $byteStreamPosition = null)
    {
    }
    /**
     * Moves internal string cursor position
     *
     * @param int $positionOffset
     * @param int $byteStreamOffset
     *
     * @return self
     */
    private function moveStringCursor($positionOffset, $byteStreamOffset)
    {
    }
}
namespace GraphQL\Language\AST;

/**
export type ValueNode = VariableNode
| NullValueNode
| IntValueNode
| FloatValueNode
| StringValueNode
| BooleanValueNode
| EnumValueNode
| ListValueNode
| ObjectValueNode
*/
interface ValueNode
{
}
/**
 * type Node = NameNode
 * | DocumentNode
 * | OperationDefinitionNode
 * | VariableDefinitionNode
 * | VariableNode
 * | SelectionSetNode
 * | FieldNode
 * | ArgumentNode
 * | FragmentSpreadNode
 * | InlineFragmentNode
 * | FragmentDefinitionNode
 * | IntValueNode
 * | FloatValueNode
 * | StringValueNode
 * | BooleanValueNode
 * | EnumValueNode
 * | ListValueNode
 * | ObjectValueNode
 * | ObjectFieldNode
 * | DirectiveNode
 * | ListTypeNode
 * | NonNullTypeNode
 */
abstract class Node
{
    /** @var Location|null */
    public $loc;
    /** @var string */
    public $kind;
    /**
     * @param (NameNode|NodeList|SelectionSetNode|Location|string|int|bool|float|null)[] $vars
     */
    public function __construct(array $vars)
    {
    }
    /**
     * @return self
     */
    public function cloneDeep()
    {
    }
    /**
     * @param string|NodeList|Location|Node|(Node|NodeList|Location)[] $value
     *
     * @return string|NodeList|Location|Node
     */
    private function cloneValue($value)
    {
    }
    public function __toString() : string
    {
    }
    /**
     * @return mixed[]
     */
    public function toArray(bool $recursive = false) : array
    {
    }
    /**
     * @return mixed[]
     */
    private function recursiveToArray(\GraphQL\Language\AST\Node $node)
    {
    }
}
class ListValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::LST;
    /** @var NodeList<ValueNode&Node> */
    public $values;
}
/**
 * export type DefinitionNode =
 *   | ExecutableDefinitionNode
 *   | TypeSystemDefinitionNode;
 */
interface DefinitionNode
{
}
/**
 * export type TypeSystemDefinitionNode =
 * | SchemaDefinitionNode
 * | TypeDefinitionNode
 * | TypeExtensionNode
 * | DirectiveDefinitionNode
 *
 * @property NameNode $name
 */
interface TypeSystemDefinitionNode extends \GraphQL\Language\AST\DefinitionNode
{
}
/**
 * export type TypeExtensionNode =
 * | ScalarTypeExtensionNode
 * | ObjectTypeExtensionNode
 * | InterfaceTypeExtensionNode
 * | UnionTypeExtensionNode
 * | EnumTypeExtensionNode
 * | InputObjectTypeExtensionNode;
 */
interface TypeExtensionNode extends \GraphQL\Language\AST\TypeSystemDefinitionNode
{
}
class UnionTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::UNION_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<NamedTypeNode> */
    public $types;
}
class ObjectTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::OBJECT_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<NamedTypeNode> */
    public $interfaces;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<FieldDefinitionNode> */
    public $fields;
}
/**
 * @template T of Node
 * @phpstan-implements ArrayAccess<int|string, T>
 * @phpstan-implements IteratorAggregate<T>
 */
class NodeList implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var Node[]
     * @phpstan-var array<T>
     */
    private $nodes;
    /**
     * @param Node[] $nodes
     *
     * @phpstan-param array<T> $nodes
     * @phpstan-return self<T>
     */
    public static function create(array $nodes) : self
    {
    }
    /**
     * @param Node[] $nodes
     *
     * @phpstan-param array<T> $nodes
     */
    public function __construct(array $nodes)
    {
    }
    /**
     * @param int|string $offset
     */
    public function offsetExists($offset) : bool
    {
    }
    /**
     * TODO enable strict typing by changing how the Visitor deals with NodeList.
     * Ideally, this function should always return a Node instance.
     * However, the Visitor currently allows mutation of the NodeList
     * and puts arbitrary values in the NodeList, such as strings.
     * We will have to switch to using an array or a less strict
     * type instead so we can enable strict typing in this class.
     *
     * @param int|string $offset
     *
     * @phpstan-return T
     */
    public function offsetGet($offset)
    {
    }
    /**
     * @param int|string|null $offset
     * @param Node|mixed[]    $value
     *
     * @phpstan-param T|mixed[] $value
     */
    public function offsetSet($offset, $value) : void
    {
    }
    /**
     * @param int|string $offset
     */
    public function offsetUnset($offset) : void
    {
    }
    /**
     * @param mixed $replacement
     *
     * @phpstan-return NodeList<T>
     */
    public function splice(int $offset, int $length, $replacement = null) : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @param NodeList|Node[] $list
     *
     * @phpstan-param NodeList<T>|array<T> $list
     * @phpstan-return NodeList<T>
     */
    public function merge($list) : \GraphQL\Language\AST\NodeList
    {
    }
    public function getIterator() : \Traversable
    {
    }
    public function count() : int
    {
    }
}
class EnumValueDefinitionNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::ENUM_VALUE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var StringValueNode|null */
    public $description;
}
class ArgumentNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::ARGUMENT;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode */
    public $value;
    /** @var NameNode */
    public $name;
}
class DocumentNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::DOCUMENT;
    /** @var NodeList<DefinitionNode&Node> */
    public $definitions;
}
class SelectionSetNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::SELECTION_SET;
    /** @var NodeList<SelectionNode&Node> */
    public $selections;
}
class NullValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::NULL;
}
class DirectiveDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeSystemDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::DIRECTIVE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var StringValueNode|null */
    public $description;
    /** @var NodeList<InputValueDefinitionNode> */
    public $arguments;
    /** @var bool */
    public $repeatable;
    /** @var NodeList<NameNode> */
    public $locations;
}
/**
 * export type SelectionNode = FieldNode | FragmentSpreadNode | InlineFragmentNode
 */
interface SelectionNode
{
}
class FragmentSpreadNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\SelectionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::FRAGMENT_SPREAD;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
}
/**
 * export type TypeDefinitionNode = ScalarTypeDefinitionNode
 * | ObjectTypeDefinitionNode
 * | InterfaceTypeDefinitionNode
 * | UnionTypeDefinitionNode
 * | EnumTypeDefinitionNode
 * | InputObjectTypeDefinitionNode
 */
interface TypeDefinitionNode extends \GraphQL\Language\AST\TypeSystemDefinitionNode
{
}
class EnumTypeDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::ENUM_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<EnumValueDefinitionNode> */
    public $values;
    /** @var StringValueNode|null */
    public $description;
}
class InputObjectTypeDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<InputValueDefinitionNode> */
    public $fields;
    /** @var StringValueNode|null */
    public $description;
}
class FloatValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::FLOAT;
    /** @var string */
    public $value;
}
class BooleanValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::BOOLEAN;
    /** @var bool */
    public $value;
}
class ScalarTypeDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::SCALAR_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var StringValueNode|null */
    public $description;
}
/**
 * export type DefinitionNode = OperationDefinitionNode
 *                        | FragmentDefinitionNode
 *
 * @property SelectionSetNode $selectionSet
 */
interface HasSelectionSet
{
}
class IntValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INT;
    /** @var string */
    public $value;
}
/**
 * export type ExecutableDefinitionNode =
 *   | OperationDefinitionNode
 *   | FragmentDefinitionNode;
 */
interface ExecutableDefinitionNode extends \GraphQL\Language\AST\DefinitionNode
{
}
class FragmentDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ExecutableDefinitionNode, \GraphQL\Language\AST\HasSelectionSet
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::FRAGMENT_DEFINITION;
    /** @var NameNode */
    public $name;
    /**
     * Note: fragment variable definitions are experimental and may be changed
     * or removed in the future.
     *
     * @var NodeList<VariableDefinitionNode>
     */
    public $variableDefinitions;
    /** @var NamedTypeNode */
    public $typeCondition;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var SelectionSetNode */
    public $selectionSet;
}
/**
 * export type TypeNode = NamedTypeNode
 * | ListTypeNode
 * | NonNullTypeNode
 */
interface TypeNode
{
}
class NamedTypeNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::NAMED_TYPE;
    /** @var NameNode */
    public $name;
}
class ObjectFieldNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::OBJECT_FIELD;
    /** @var NameNode */
    public $name;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode */
    public $value;
}
class NodeKind
{
    // constants from language/kinds.js:
    const NAME = 'Name';
    // Document
    const DOCUMENT = 'Document';
    const OPERATION_DEFINITION = 'OperationDefinition';
    const VARIABLE_DEFINITION = 'VariableDefinition';
    const VARIABLE = 'Variable';
    const SELECTION_SET = 'SelectionSet';
    const FIELD = 'Field';
    const ARGUMENT = 'Argument';
    // Fragments
    const FRAGMENT_SPREAD = 'FragmentSpread';
    const INLINE_FRAGMENT = 'InlineFragment';
    const FRAGMENT_DEFINITION = 'FragmentDefinition';
    // Values
    const INT = 'IntValue';
    const FLOAT = 'FloatValue';
    const STRING = 'StringValue';
    const BOOLEAN = 'BooleanValue';
    const ENUM = 'EnumValue';
    const NULL = 'NullValue';
    const LST = 'ListValue';
    const OBJECT = 'ObjectValue';
    const OBJECT_FIELD = 'ObjectField';
    // Directives
    const DIRECTIVE = 'Directive';
    // Types
    const NAMED_TYPE = 'NamedType';
    const LIST_TYPE = 'ListType';
    const NON_NULL_TYPE = 'NonNullType';
    // Type System Definitions
    const SCHEMA_DEFINITION = 'SchemaDefinition';
    const OPERATION_TYPE_DEFINITION = 'OperationTypeDefinition';
    // Type Definitions
    const SCALAR_TYPE_DEFINITION = 'ScalarTypeDefinition';
    const OBJECT_TYPE_DEFINITION = 'ObjectTypeDefinition';
    const FIELD_DEFINITION = 'FieldDefinition';
    const INPUT_VALUE_DEFINITION = 'InputValueDefinition';
    const INTERFACE_TYPE_DEFINITION = 'InterfaceTypeDefinition';
    const UNION_TYPE_DEFINITION = 'UnionTypeDefinition';
    const ENUM_TYPE_DEFINITION = 'EnumTypeDefinition';
    const ENUM_VALUE_DEFINITION = 'EnumValueDefinition';
    const INPUT_OBJECT_TYPE_DEFINITION = 'InputObjectTypeDefinition';
    // Type Extensions
    const SCALAR_TYPE_EXTENSION = 'ScalarTypeExtension';
    const OBJECT_TYPE_EXTENSION = 'ObjectTypeExtension';
    const INTERFACE_TYPE_EXTENSION = 'InterfaceTypeExtension';
    const UNION_TYPE_EXTENSION = 'UnionTypeExtension';
    const ENUM_TYPE_EXTENSION = 'EnumTypeExtension';
    const INPUT_OBJECT_TYPE_EXTENSION = 'InputObjectTypeExtension';
    // Directive Definitions
    const DIRECTIVE_DEFINITION = 'DirectiveDefinition';
    // Type System Extensions
    const SCHEMA_EXTENSION = 'SchemaExtension';
    /** @var string[] */
    public static $classMap = [
        self::NAME => \GraphQL\Language\AST\NameNode::class,
        // Document
        self::DOCUMENT => \GraphQL\Language\AST\DocumentNode::class,
        self::OPERATION_DEFINITION => \GraphQL\Language\AST\OperationDefinitionNode::class,
        self::VARIABLE_DEFINITION => \GraphQL\Language\AST\VariableDefinitionNode::class,
        self::VARIABLE => \GraphQL\Language\AST\VariableNode::class,
        self::SELECTION_SET => \GraphQL\Language\AST\SelectionSetNode::class,
        self::FIELD => \GraphQL\Language\AST\FieldNode::class,
        self::ARGUMENT => \GraphQL\Language\AST\ArgumentNode::class,
        // Fragments
        self::FRAGMENT_SPREAD => \GraphQL\Language\AST\FragmentSpreadNode::class,
        self::INLINE_FRAGMENT => \GraphQL\Language\AST\InlineFragmentNode::class,
        self::FRAGMENT_DEFINITION => \GraphQL\Language\AST\FragmentDefinitionNode::class,
        // Values
        self::INT => \GraphQL\Language\AST\IntValueNode::class,
        self::FLOAT => \GraphQL\Language\AST\FloatValueNode::class,
        self::STRING => \GraphQL\Language\AST\StringValueNode::class,
        self::BOOLEAN => \GraphQL\Language\AST\BooleanValueNode::class,
        self::ENUM => \GraphQL\Language\AST\EnumValueNode::class,
        self::NULL => \GraphQL\Language\AST\NullValueNode::class,
        self::LST => \GraphQL\Language\AST\ListValueNode::class,
        self::OBJECT => \GraphQL\Language\AST\ObjectValueNode::class,
        self::OBJECT_FIELD => \GraphQL\Language\AST\ObjectFieldNode::class,
        // Directives
        self::DIRECTIVE => \GraphQL\Language\AST\DirectiveNode::class,
        // Types
        self::NAMED_TYPE => \GraphQL\Language\AST\NamedTypeNode::class,
        self::LIST_TYPE => \GraphQL\Language\AST\ListTypeNode::class,
        self::NON_NULL_TYPE => \GraphQL\Language\AST\NonNullTypeNode::class,
        // Type System Definitions
        self::SCHEMA_DEFINITION => \GraphQL\Language\AST\SchemaDefinitionNode::class,
        self::OPERATION_TYPE_DEFINITION => \GraphQL\Language\AST\OperationTypeDefinitionNode::class,
        // Type Definitions
        self::SCALAR_TYPE_DEFINITION => \GraphQL\Language\AST\ScalarTypeDefinitionNode::class,
        self::OBJECT_TYPE_DEFINITION => \GraphQL\Language\AST\ObjectTypeDefinitionNode::class,
        self::FIELD_DEFINITION => \GraphQL\Language\AST\FieldDefinitionNode::class,
        self::INPUT_VALUE_DEFINITION => \GraphQL\Language\AST\InputValueDefinitionNode::class,
        self::INTERFACE_TYPE_DEFINITION => \GraphQL\Language\AST\InterfaceTypeDefinitionNode::class,
        self::UNION_TYPE_DEFINITION => \GraphQL\Language\AST\UnionTypeDefinitionNode::class,
        self::ENUM_TYPE_DEFINITION => \GraphQL\Language\AST\EnumTypeDefinitionNode::class,
        self::ENUM_VALUE_DEFINITION => \GraphQL\Language\AST\EnumValueDefinitionNode::class,
        self::INPUT_OBJECT_TYPE_DEFINITION => \GraphQL\Language\AST\InputObjectTypeDefinitionNode::class,
        // Type Extensions
        self::SCALAR_TYPE_EXTENSION => \GraphQL\Language\AST\ScalarTypeExtensionNode::class,
        self::OBJECT_TYPE_EXTENSION => \GraphQL\Language\AST\ObjectTypeExtensionNode::class,
        self::INTERFACE_TYPE_EXTENSION => \GraphQL\Language\AST\InterfaceTypeExtensionNode::class,
        self::UNION_TYPE_EXTENSION => \GraphQL\Language\AST\UnionTypeExtensionNode::class,
        self::ENUM_TYPE_EXTENSION => \GraphQL\Language\AST\EnumTypeExtensionNode::class,
        self::INPUT_OBJECT_TYPE_EXTENSION => \GraphQL\Language\AST\InputObjectTypeExtensionNode::class,
        // Directive Definitions
        self::DIRECTIVE_DEFINITION => \GraphQL\Language\AST\DirectiveDefinitionNode::class,
    ];
}
class OperationDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ExecutableDefinitionNode, \GraphQL\Language\AST\HasSelectionSet
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::OPERATION_DEFINITION;
    /** @var NameNode|null */
    public $name;
    /** @var string (oneOf 'query', 'mutation', 'subscription')) */
    public $operation;
    /** @var NodeList<VariableDefinitionNode> */
    public $variableDefinitions;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var SelectionSetNode */
    public $selectionSet;
}
class UnionTypeDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::UNION_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<NamedTypeNode> */
    public $types;
    /** @var StringValueNode|null */
    public $description;
}
class SchemaTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::SCHEMA_EXTENSION;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<OperationTypeDefinitionNode> */
    public $operationTypes;
}
class InterfaceTypeDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<NamedTypeNode> */
    public $interfaces;
    /** @var NodeList<FieldDefinitionNode> */
    public $fields;
    /** @var StringValueNode|null */
    public $description;
}
class DirectiveNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::DIRECTIVE;
    /** @var NameNode */
    public $name;
    /** @var NodeList<ArgumentNode> */
    public $arguments;
}
class NameNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::NAME;
    /** @var string */
    public $value;
}
class StringValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::STRING;
    /** @var string */
    public $value;
    /** @var bool */
    public $block;
}
class ScalarTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::SCALAR_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
}
class NonNullTypeNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::NON_NULL_TYPE;
    /** @var NamedTypeNode|ListTypeNode */
    public $type;
}
class FieldDefinitionNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::FIELD_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<InputValueDefinitionNode> */
    public $arguments;
    /** @var NamedTypeNode|ListTypeNode|NonNullTypeNode */
    public $type;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var StringValueNode|null */
    public $description;
}
/**
 * Contains a range of UTF-8 character offsets and token references that
 * identify the region of the source from which the AST derived.
 */
class Location
{
    /**
     * The character offset at which this Node begins.
     *
     * @var int
     */
    public $start;
    /**
     * The character offset at which this Node ends.
     *
     * @var int
     */
    public $end;
    /**
     * The Token at which this Node begins.
     *
     * @var Token|null
     */
    public $startToken;
    /**
     * The Token at which this Node ends.
     *
     * @var Token|null
     */
    public $endToken;
    /**
     * The Source document the AST represents.
     *
     * @var Source|null
     */
    public $source;
    /**
     * @param int $start
     * @param int $end
     *
     * @return static
     */
    public static function create($start, $end)
    {
    }
    public function __construct(?\GraphQL\Language\Token $startToken = null, ?\GraphQL\Language\Token $endToken = null, ?\GraphQL\Language\Source $source = null)
    {
    }
}
class EnumValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::ENUM;
    /** @var string */
    public $value;
}
class OperationTypeDefinitionNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::OPERATION_TYPE_DEFINITION;
    /**
     * One of 'query' | 'mutation' | 'subscription'
     *
     * @var string
     */
    public $operation;
    /** @var NamedTypeNode */
    public $type;
}
class EnumTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::ENUM_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<EnumValueDefinitionNode> */
    public $values;
}
class ObjectValueNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::OBJECT;
    /** @var NodeList<ObjectFieldNode> */
    public $fields;
}
class InterfaceTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INTERFACE_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<InterfaceTypeDefinitionNode> */
    public $interfaces;
    /** @var NodeList<FieldDefinitionNode> */
    public $fields;
}
class FieldNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\SelectionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::FIELD;
    /** @var NameNode */
    public $name;
    /** @var NameNode|null */
    public $alias;
    /** @var NodeList<ArgumentNode> */
    public $arguments;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var SelectionSetNode|null */
    public $selectionSet;
}
class InlineFragmentNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\SelectionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INLINE_FRAGMENT;
    /** @var NamedTypeNode */
    public $typeCondition;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var SelectionSetNode */
    public $selectionSet;
}
class InputValueDefinitionNode extends \GraphQL\Language\AST\Node
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INPUT_VALUE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NamedTypeNode|ListTypeNode|NonNullTypeNode */
    public $type;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null */
    public $defaultValue;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var StringValueNode|null */
    public $description;
}
class VariableDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\DefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::VARIABLE_DEFINITION;
    /** @var VariableNode */
    public $variable;
    /** @var NamedTypeNode|ListTypeNode|NonNullTypeNode */
    public $type;
    /** @var VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode|null */
    public $defaultValue;
    /** @var NodeList<DirectiveNode> */
    public $directives;
}
class InputObjectTypeExtensionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeExtensionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::INPUT_OBJECT_TYPE_EXTENSION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<InputValueDefinitionNode> */
    public $fields;
}
class ListTypeNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::LIST_TYPE;
    /** @var NamedTypeNode|ListTypeNode|NonNullTypeNode */
    public $type;
}
class VariableNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\ValueNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::VARIABLE;
    /** @var NameNode */
    public $name;
}
class ObjectTypeDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::OBJECT_TYPE_DEFINITION;
    /** @var NameNode */
    public $name;
    /** @var NodeList<NamedTypeNode> */
    public $interfaces;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<FieldDefinitionNode> */
    public $fields;
    /** @var StringValueNode|null */
    public $description;
}
class SchemaDefinitionNode extends \GraphQL\Language\AST\Node implements \GraphQL\Language\AST\TypeSystemDefinitionNode
{
    /** @var string */
    public $kind = \GraphQL\Language\AST\NodeKind::SCHEMA_DEFINITION;
    /** @var NodeList<DirectiveNode> */
    public $directives;
    /** @var NodeList<OperationTypeDefinitionNode> */
    public $operationTypes;
}
namespace GraphQL\Language;

class Source
{
    /** @var string */
    public $body;
    /** @var int */
    public $length;
    /** @var string */
    public $name;
    /** @var SourceLocation */
    public $locationOffset;
    /**
     * A representation of source input to GraphQL.
     * `name` and `locationOffset` are optional. They are useful for clients who
     * store GraphQL documents in source files; for example, if the GraphQL input
     * starts at line 40 in a file named Foo.graphql, it might be useful for name to
     * be "Foo.graphql" and location to be `{ line: 40, column: 0 }`.
     * line and column in locationOffset are 1-indexed
     *
     * @param string      $body
     * @param string|null $name
     */
    public function __construct($body, $name = null, ?\GraphQL\Language\SourceLocation $location = null)
    {
    }
    /**
     * @param int $position
     *
     * @return SourceLocation
     */
    public function getLocation($position)
    {
    }
}
/**
 * List of available directive locations
 */
class DirectiveLocation
{
    // Request Definitions
    const QUERY = 'QUERY';
    const MUTATION = 'MUTATION';
    const SUBSCRIPTION = 'SUBSCRIPTION';
    const FIELD = 'FIELD';
    const FRAGMENT_DEFINITION = 'FRAGMENT_DEFINITION';
    const FRAGMENT_SPREAD = 'FRAGMENT_SPREAD';
    const INLINE_FRAGMENT = 'INLINE_FRAGMENT';
    const VARIABLE_DEFINITION = 'VARIABLE_DEFINITION';
    // Type System Definitions
    const SCHEMA = 'SCHEMA';
    const SCALAR = 'SCALAR';
    const OBJECT = 'OBJECT';
    const FIELD_DEFINITION = 'FIELD_DEFINITION';
    const ARGUMENT_DEFINITION = 'ARGUMENT_DEFINITION';
    const IFACE = 'INTERFACE';
    const UNION = 'UNION';
    const ENUM = 'ENUM';
    const ENUM_VALUE = 'ENUM_VALUE';
    const INPUT_OBJECT = 'INPUT_OBJECT';
    const INPUT_FIELD_DEFINITION = 'INPUT_FIELD_DEFINITION';
    /** @var string[] */
    private static $locations = [self::QUERY => self::QUERY, self::MUTATION => self::MUTATION, self::SUBSCRIPTION => self::SUBSCRIPTION, self::FIELD => self::FIELD, self::FRAGMENT_DEFINITION => self::FRAGMENT_DEFINITION, self::FRAGMENT_SPREAD => self::FRAGMENT_SPREAD, self::INLINE_FRAGMENT => self::INLINE_FRAGMENT, self::SCHEMA => self::SCHEMA, self::SCALAR => self::SCALAR, self::OBJECT => self::OBJECT, self::FIELD_DEFINITION => self::FIELD_DEFINITION, self::ARGUMENT_DEFINITION => self::ARGUMENT_DEFINITION, self::IFACE => self::IFACE, self::UNION => self::UNION, self::ENUM => self::ENUM, self::ENUM_VALUE => self::ENUM_VALUE, self::INPUT_OBJECT => self::INPUT_OBJECT, self::INPUT_FIELD_DEFINITION => self::INPUT_FIELD_DEFINITION];
    public static function has(string $name) : bool
    {
    }
}
/**
 * Prints AST to string. Capable of printing GraphQL queries and Type definition language.
 * Useful for pretty-printing queries or printing back AST for logging, documentation, etc.
 *
 * Usage example:
 *
 * ```php
 * $query = 'query myQuery {someField}';
 * $ast = GraphQL\Language\Parser::parse($query);
 * $printed = GraphQL\Language\Printer::doPrint($ast);
 * ```
 */
class Printer
{
    /**
     * Prints AST to string. Capable of printing GraphQL queries and Type definition language.
     *
     * @param Node $ast
     *
     * @return string
     *
     * @api
     */
    public static function doPrint($ast)
    {
    }
    protected function __construct()
    {
    }
    /**
     * Traverse an AST bottom-up, converting all nodes to strings.
     *
     * That means the AST is manipulated in such a way that it no longer
     * resembles the well-formed result of parsing.
     */
    public function printAST($ast)
    {
    }
    public function addDescription(callable $cb)
    {
    }
    /**
     * If maybeString is not null or empty, then wrap with start and end, otherwise
     * print an empty string.
     */
    public function wrap($start, $maybeString, $end = '')
    {
    }
    /**
     * Given array, print each item on its own line, wrapped in an
     * indented "{ }" block.
     */
    public function block($array)
    {
    }
    public function indent($maybeString)
    {
    }
    public function manyList($start, $list, $separator, $end)
    {
    }
    public function length($maybeArray)
    {
    }
    public function join($maybeArray, $separator = '') : string
    {
    }
    /**
     * Print a block string in the indented block form by adding a leading and
     * trailing blank line. However, if a block string starts with whitespace and is
     * a single-line, adding a leading blank line would strip that whitespace.
     */
    private function printBlockString($value, $isDescription)
    {
    }
}
namespace GraphQL;

/**
 * This is the primary facade for fulfilling GraphQL operations.
 * See [related documentation](executing-queries.md).
 */
class GraphQL
{
    /**
     * Executes graphql query.
     *
     * More sophisticated GraphQL servers, such as those which persist queries,
     * may wish to separate the validation and execution phases to a static time
     * tooling step, and a server runtime step.
     *
     * Available options:
     *
     * schema:
     *    The GraphQL type system to use when validating and executing a query.
     * source:
     *    A GraphQL language formatted string representing the requested operation.
     * rootValue:
     *    The value provided as the first argument to resolver functions on the top
     *    level type (e.g. the query object type).
     * contextValue:
     *    The context value is provided as an argument to resolver functions after
     *    field arguments. It is used to pass shared information useful at any point
     *    during executing this query, for example the currently logged in user and
     *    connections to databases or other services.
     * variableValues:
     *    A mapping of variable name to runtime value to use for all variables
     *    defined in the requestString.
     * operationName:
     *    The name of the operation to use if requestString contains multiple
     *    possible operations. Can be omitted if requestString contains only
     *    one operation.
     * fieldResolver:
     *    A resolver function to use when one is not provided by the schema.
     *    If not provided, the default field resolver is used (which looks for a
     *    value on the source value with the field's name).
     * validationRules:
     *    A set of rules for query validation step. Default value is all available rules.
     *    Empty array would allow to skip query validation (may be convenient for persisted
     *    queries which are validated before persisting and assumed valid during execution)
     *
     * @param string|DocumentNode $source
     * @param mixed               $rootValue
     * @param mixed               $contextValue
     * @param mixed[]|null        $variableValues
     * @param ValidationRule[]    $validationRules
     *
     * @api
     */
    public static function executeQuery(\GraphQL\Type\Schema $schema, $source, $rootValue = null, $contextValue = null, $variableValues = null, ?string $operationName = null, ?callable $fieldResolver = null, ?array $validationRules = null) : \GraphQL\Executor\ExecutionResult
    {
    }
    /**
     * Same as executeQuery(), but requires PromiseAdapter and always returns a Promise.
     * Useful for Async PHP platforms.
     *
     * @param string|DocumentNode   $source
     * @param mixed                 $rootValue
     * @param mixed                 $context
     * @param mixed[]|null          $variableValues
     * @param ValidationRule[]|null $validationRules
     *
     * @api
     */
    public static function promiseToExecute(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \GraphQL\Type\Schema $schema, $source, $rootValue = null, $context = null, $variableValues = null, ?string $operationName = null, ?callable $fieldResolver = null, ?array $validationRules = null) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @deprecated Use executeQuery()->toArray() instead
     *
     * @param string|DocumentNode $source
     * @param mixed               $rootValue
     * @param mixed               $contextValue
     * @param mixed[]|null        $variableValues
     *
     * @return Promise|mixed[]
     *
     * @codeCoverageIgnore
     */
    public static function execute(\GraphQL\Type\Schema $schema, $source, $rootValue = null, $contextValue = null, $variableValues = null, ?string $operationName = null)
    {
    }
    /**
     * @deprecated renamed to executeQuery()
     *
     * @param string|DocumentNode $source
     * @param mixed               $rootValue
     * @param mixed               $contextValue
     * @param mixed[]|null        $variableValues
     *
     * @return ExecutionResult|Promise
     *
     * @codeCoverageIgnore
     */
    public static function executeAndReturnResult(\GraphQL\Type\Schema $schema, $source, $rootValue = null, $contextValue = null, $variableValues = null, ?string $operationName = null)
    {
    }
    /**
     * Returns directives defined in GraphQL spec
     *
     * @return Directive[]
     *
     * @api
     */
    public static function getStandardDirectives() : array
    {
    }
    /**
     * Returns types defined in GraphQL spec
     *
     * @return Type[]
     *
     * @api
     */
    public static function getStandardTypes() : array
    {
    }
    /**
     * Replaces standard types with types from this list (matching by name)
     * Standard types not listed here remain untouched.
     *
     * @param array<string, ScalarType> $types
     *
     * @api
     */
    public static function overrideStandardTypes(array $types)
    {
    }
    /**
     * Returns standard validation rules implementing GraphQL spec
     *
     * @return ValidationRule[]
     *
     * @api
     */
    public static function getStandardValidationRules() : array
    {
    }
    /**
     * Set default resolver implementation
     *
     * @api
     */
    public static function setDefaultFieldResolver(callable $fn) : void
    {
    }
    public static function setPromiseAdapter(?\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter = null) : void
    {
    }
    /**
     * Experimental: Switch to the new executor
     */
    public static function useExperimentalExecutor()
    {
    }
    /**
     * Experimental: Switch back to the default executor
     */
    public static function useReferenceExecutor()
    {
    }
    /**
     * Returns directives defined in GraphQL spec
     *
     * @deprecated Renamed to getStandardDirectives
     *
     * @return Directive[]
     *
     * @codeCoverageIgnore
     */
    public static function getInternalDirectives() : array
    {
    }
}
namespace GraphQL\Type;

class TypeKind
{
    const SCALAR = 'SCALAR';
    const OBJECT = 'OBJECT';
    const INTERFACE = 'INTERFACE';
    const UNION = 'UNION';
    const ENUM = 'ENUM';
    const INPUT_OBJECT = 'INPUT_OBJECT';
    const LIST = 'LIST';
    const NON_NULL = 'NON_NULL';
}
class SchemaValidationContext
{
    /** @var Error[] */
    private $errors = [];
    /** @var Schema */
    private $schema;
    /** @var InputObjectCircularRefs */
    private $inputObjectCircularRefs;
    public function __construct(\GraphQL\Type\Schema $schema)
    {
    }
    /**
     * @return Error[]
     */
    public function getErrors()
    {
    }
    public function validateRootTypes() : void
    {
    }
    /**
     * @param string                                       $message
     * @param Node[]|Node|TypeNode|TypeDefinitionNode|null $nodes
     */
    public function reportError($message, $nodes = null)
    {
    }
    /**
     * @param Error $error
     */
    private function addError($error)
    {
    }
    /**
     * @param Type   $type
     * @param string $operation
     *
     * @return NamedTypeNode|ListTypeNode|NonNullTypeNode|TypeDefinitionNode
     */
    private function getOperationTypeNode($type, $operation)
    {
    }
    public function validateDirectives()
    {
    }
    public function validateDirectiveDefinitions()
    {
    }
    /**
     * @param Type|Directive|FieldDefinition|EnumValueDefinition|InputObjectField $node
     */
    private function validateName($node)
    {
    }
    /**
     * @param string $argName
     *
     * @return InputValueDefinitionNode[]
     */
    private function getAllDirectiveArgNodes(\GraphQL\Type\Definition\Directive $directive, $argName)
    {
    }
    /**
     * @param string $argName
     *
     * @return NamedTypeNode|ListTypeNode|NonNullTypeNode|null
     */
    private function getDirectiveArgTypeNode(\GraphQL\Type\Definition\Directive $directive, $argName) : ?\GraphQL\Language\AST\TypeNode
    {
    }
    public function validateTypes() : void
    {
    }
    /**
     * @param NodeList<DirectiveNode> $directives
     */
    private function validateDirectivesAtLocation($directives, string $location)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     */
    private function validateFields($type)
    {
    }
    /**
     * @param Schema|ObjectType|InterfaceType|UnionType|EnumType|InputObjectType|Directive $obj
     *
     * @return ObjectTypeDefinitionNode[]|ObjectTypeExtensionNode[]|InterfaceTypeDefinitionNode[]|InterfaceTypeExtensionNode[]
     */
    private function getAllNodes($obj)
    {
    }
    /**
     * @param Schema|ObjectType|InterfaceType|UnionType|EnumType|Directive $obj
     */
    private function getAllSubNodes($obj, callable $getter) : \GraphQL\Language\AST\NodeList
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     *
     * @return FieldDefinitionNode[]
     */
    private function getAllFieldNodes($type, $fieldName)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     *
     * @return NamedTypeNode|ListTypeNode|NonNullTypeNode|null
     */
    private function getFieldTypeNode($type, $fieldName) : ?\GraphQL\Language\AST\TypeNode
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     *
     * @return FieldDefinitionNode|null
     */
    private function getFieldNode($type, $fieldName)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     * @param string                   $argName
     *
     * @return InputValueDefinitionNode[]
     */
    private function getAllFieldArgNodes($type, $fieldName, $argName)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     * @param string                   $argName
     *
     * @return NamedTypeNode|ListTypeNode|NonNullTypeNode|null
     */
    private function getFieldArgTypeNode($type, $fieldName, $argName) : ?\GraphQL\Language\AST\TypeNode
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     * @param string                   $fieldName
     * @param string                   $argName
     *
     * @return InputValueDefinitionNode|null
     */
    private function getFieldArgNode($type, $fieldName, $argName)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     */
    private function validateInterfaces(\GraphQL\Type\Definition\ImplementingType $type) : void
    {
    }
    /**
     * @param Schema|Type $object
     *
     * @return NodeList<DirectiveNode>
     */
    private function getDirectives($object)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     */
    private function getImplementsInterfaceNode(\GraphQL\Type\Definition\ImplementingType $type, \GraphQL\Type\Definition\Type $shouldBeInterface) : ?\GraphQL\Language\AST\NamedTypeNode
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     *
     * @return array<int, NamedTypeNode>
     */
    private function getAllImplementsInterfaceNodes(\GraphQL\Type\Definition\ImplementingType $type, \GraphQL\Type\Definition\Type $shouldBeInterface) : array
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     */
    private function validateTypeImplementsInterface(\GraphQL\Type\Definition\ImplementingType $type, \GraphQL\Type\Definition\InterfaceType $iface)
    {
    }
    /**
     * @param ObjectType|InterfaceType $type
     */
    private function validateTypeImplementsAncestors(\GraphQL\Type\Definition\ImplementingType $type, \GraphQL\Type\Definition\InterfaceType $iface) : void
    {
    }
    private function validateUnionMembers(\GraphQL\Type\Definition\UnionType $union)
    {
    }
    /**
     * @param string $typeName
     *
     * @return NamedTypeNode[]
     */
    private function getUnionMemberTypeNodes(\GraphQL\Type\Definition\UnionType $union, $typeName)
    {
    }
    private function validateEnumValues(\GraphQL\Type\Definition\EnumType $enumType)
    {
    }
    /**
     * @param string $valueName
     *
     * @return EnumValueDefinitionNode[]
     */
    private function getEnumValueNodes(\GraphQL\Type\Definition\EnumType $enum, $valueName)
    {
    }
    private function validateInputFields(\GraphQL\Type\Definition\InputObjectType $inputObj)
    {
    }
}
class Introspection
{
    const SCHEMA_FIELD_NAME = '__schema';
    const TYPE_FIELD_NAME = '__type';
    const TYPE_NAME_FIELD_NAME = '__typename';
    /** @var array<string, mixed> */
    private static $map = [];
    /**
     * @param array<string, bool> $options
     *      Available options:
     *      - descriptions
     *        Whether to include descriptions in the introspection result.
     *        Default: true
     *      - directiveIsRepeatable
     *        Whether to include `isRepeatable` flag on directives.
     *        Default: false
     *
     * @return string
     *
     * @api
     */
    public static function getIntrospectionQuery(array $options = [])
    {
    }
    /**
     * @param Type $type
     *
     * @return bool
     */
    public static function isIntrospectionType($type)
    {
    }
    public static function getTypes()
    {
    }
    /**
     * Build an introspection query from a Schema
     *
     * Introspection is useful for utilities that care about type and field
     * relationships, but do not need to traverse through those relationships.
     *
     * This is the inverse of BuildClientSchema::build(). The primary use case is outside
     * of the server context, for instance when doing schema comparisons.
     *
     * @param array<string, bool> $options
     *      Available options:
     *      - descriptions
     *        Whether to include `isRepeatable` flag on directives.
     *        Default: true
     *      - directiveIsRepeatable
     *        Whether to include descriptions in the introspection result.
     *        Default: true
     *
     * @return array<string, array<mixed>>|null
     *
     * @api
     */
    public static function fromSchema(\GraphQL\Type\Schema $schema, array $options = []) : ?array
    {
    }
    public static function _schema()
    {
    }
    public static function _type()
    {
    }
    public static function _typeKind()
    {
    }
    public static function _field()
    {
    }
    public static function _inputValue()
    {
    }
    public static function _enumValue()
    {
    }
    public static function _directive()
    {
    }
    public static function _directiveLocation()
    {
    }
    public static function schemaMetaFieldDef() : \GraphQL\Type\Definition\FieldDefinition
    {
    }
    public static function typeMetaFieldDef() : \GraphQL\Type\Definition\FieldDefinition
    {
    }
    public static function typeNameMetaFieldDef() : \GraphQL\Type\Definition\FieldDefinition
    {
    }
}
namespace GraphQL\Type\Definition;

interface WrappingType
{
    public function getWrappedType(bool $recurse = false) : \GraphQL\Type\Definition\Type;
}
class EnumValueDefinition
{
    /** @var string */
    public $name;
    /** @var mixed */
    public $value;
    /** @var string|null */
    public $deprecationReason;
    /** @var string|null */
    public $description;
    /** @var EnumValueDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
    }
    /**
     * @return bool
     */
    public function isDeprecated()
    {
    }
}
/**
 * Structure containing information useful for field resolution process.
 *
 * Passed as 4th argument to every field resolver. See [docs on field resolving (data fetching)](data-fetching.md).
 */
class ResolveInfo
{
    /**
     * The definition of the field being resolved.
     *
     * @api
     * @var FieldDefinition
     */
    public $fieldDefinition;
    /**
     * The name of the field being resolved.
     *
     * @api
     * @var string
     */
    public $fieldName;
    /**
     * Expected return type of the field being resolved.
     *
     * @api
     * @var Type
     */
    public $returnType;
    /**
     * AST of all nodes referencing this field in the query.
     *
     * @api
     * @var FieldNode[]
     */
    public $fieldNodes = [];
    /**
     * Parent type of the field being resolved.
     *
     * @api
     * @var ObjectType
     */
    public $parentType;
    /**
     * Path to this field from the very root value.
     *
     * @api
     * @var string[]
     */
    public $path;
    /**
     * Instance of a schema used for execution.
     *
     * @api
     * @var Schema
     */
    public $schema;
    /**
     * AST of all fragments defined in query.
     *
     * @api
     * @var FragmentDefinitionNode[]
     */
    public $fragments = [];
    /**
     * Root value passed to query execution.
     *
     * @api
     * @var mixed
     */
    public $rootValue;
    /**
     * AST of operation definition node (query, mutation).
     *
     * @api
     * @var OperationDefinitionNode|null
     */
    public $operation;
    /**
     * Array of variables passed to query execution.
     *
     * @api
     * @var mixed[]
     */
    public $variableValues = [];
    /**
     * Lazily initialized.
     *
     * @var QueryPlan
     */
    private $queryPlan;
    /**
     * @param FieldNode[]              $fieldNodes
     * @param string[]                 $path
     * @param FragmentDefinitionNode[] $fragments
     * @param mixed|null               $rootValue
     * @param mixed[]                  $variableValues
     */
    public function __construct(\GraphQL\Type\Definition\FieldDefinition $fieldDefinition, iterable $fieldNodes, \GraphQL\Type\Definition\ObjectType $parentType, array $path, \GraphQL\Type\Schema $schema, array $fragments, $rootValue, ?\GraphQL\Language\AST\OperationDefinitionNode $operation, array $variableValues)
    {
    }
    /**
     * Helper method that returns names of all fields selected in query for
     * $this->fieldName up to $depth levels.
     *
     * Example:
     * query MyQuery{
     * {
     *   root {
     *     id,
     *     nested {
     *      nested1
     *      nested2 {
     *        nested3
     *      }
     *     }
     *   }
     * }
     *
     * Given this ResolveInfo instance is a part of "root" field resolution, and $depth === 1,
     * method will return:
     * [
     *     'id' => true,
     *     'nested' => [
     *         nested1 => true,
     *         nested2 => true
     *     ]
     * ]
     *
     * Warning: this method it is a naive implementation which does not take into account
     * conditional typed fragments. So use it with care for fields of interface and union types.
     *
     * @param int $depth How many levels to include in output
     *
     * @return array<string, mixed>
     *
     * @api
     */
    public function getFieldSelection($depth = 0)
    {
    }
    /**
     * @param mixed[] $options
     */
    public function lookAhead(array $options = []) : \GraphQL\Type\Definition\QueryPlan
    {
    }
    /**
     * @return bool[]
     */
    private function foldSelectionSet(\GraphQL\Language\AST\SelectionSetNode $selectionSet, int $descend) : array
    {
    }
}
/*
export type GraphQLNullableType =
 | GraphQLScalarType
 | GraphQLObjectType
 | GraphQLInterfaceType
 | GraphQLUnionType
 | GraphQLEnumType
 | GraphQLInputObjectType
 | GraphQLList<any>;
*/
interface NullableType
{
}
/*
export type GraphQLUnmodifiedType =
GraphQLScalarType |
GraphQLObjectType |
GraphQLInterfaceType |
GraphQLUnionType |
GraphQLEnumType |
GraphQLInputObjectType;
*/
interface UnmodifiedType
{
}
/*
GraphQLScalarType |
GraphQLObjectType |
GraphQLInterfaceType |
GraphQLUnionType |
GraphQLEnumType |
GraphQLList |
GraphQLNonNull;
*/
interface OutputType
{
}
/**
export type InputType =
 | ScalarType
 | EnumType
 | InputObjectType
 | ListOfType<InputType>
 | NonNull<
     | ScalarType
     | EnumType
     | InputObjectType
     | ListOfType<InputType>,
   >;
*/
interface InputType
{
}
/*
export type GraphQLLeafType =
GraphQLScalarType |
GraphQLEnumType;
*/
interface LeafType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function serialize($value);
    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function parseValue($value);
    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|NullValueNode $valueNode
     * @param mixed[]|null                                                               $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null);
}
/**
export type NamedType =
 | ScalarType
 | ObjectType
 | InterfaceType
 | UnionType
 | EnumType
 | InputObjectType;
*/
interface NamedType
{
}
/**
 * Registry of standard GraphQL types
 * and a base class for all other types.
 */
abstract class Type implements \JsonSerializable
{
    public const STRING = 'String';
    public const INT = 'Int';
    public const BOOLEAN = 'Boolean';
    public const FLOAT = 'Float';
    public const ID = 'ID';
    /** @var array<string, ScalarType> */
    protected static $standardTypes;
    /** @var Type[] */
    private static $builtInTypes;
    /** @var string */
    public $name;
    /** @var string|null */
    public $description;
    /** @var TypeDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /** @var TypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @api
     */
    public static function id() : \GraphQL\Type\Definition\ScalarType
    {
    }
    /**
     * @api
     */
    public static function string() : \GraphQL\Type\Definition\ScalarType
    {
    }
    /**
     * @api
     */
    public static function boolean() : \GraphQL\Type\Definition\ScalarType
    {
    }
    /**
     * @api
     */
    public static function int() : \GraphQL\Type\Definition\ScalarType
    {
    }
    /**
     * @api
     */
    public static function float() : \GraphQL\Type\Definition\ScalarType
    {
    }
    /**
     * @api
     */
    public static function listOf(\GraphQL\Type\Definition\Type $wrappedType) : \GraphQL\Type\Definition\ListOfType
    {
    }
    /**
     * @param callable|NullableType $wrappedType
     *
     * @api
     */
    public static function nonNull($wrappedType) : \GraphQL\Type\Definition\NonNull
    {
    }
    /**
     * Checks if the type is a builtin type
     */
    public static function isBuiltInType(\GraphQL\Type\Definition\Type $type) : bool
    {
    }
    /**
     * Returns all builtin in types including base scalar and
     * introspection types
     *
     * @return Type[]
     */
    public static function getAllBuiltInTypes()
    {
    }
    /**
     * Returns all builtin scalar types
     *
     * @return ScalarType[]
     */
    public static function getStandardTypes()
    {
    }
    /**
     * @deprecated Use method getStandardTypes() instead
     *
     * @return Type[]
     *
     * @codeCoverageIgnore
     */
    public static function getInternalTypes()
    {
    }
    /**
     * @param array<string, ScalarType> $types
     */
    public static function overrideStandardTypes(array $types)
    {
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isInputType($type) : bool
    {
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function getNamedType($type) : ?\GraphQL\Type\Definition\Type
    {
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isOutputType($type) : bool
    {
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isLeafType($type) : bool
    {
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isCompositeType($type) : bool
    {
    }
    /**
     * @param Type $type
     *
     * @api
     */
    public static function isAbstractType($type) : bool
    {
    }
    /**
     * @param mixed $type
     */
    public static function assertType($type) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @api
     */
    public static function getNullableType(\GraphQL\Type\Definition\Type $type) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid()
    {
    }
    /**
     * @return string
     */
    public function jsonSerialize()
    {
    }
    /**
     * @return string
     */
    public function toString()
    {
    }
    /**
     * @return string
     */
    public function __toString()
    {
    }
    /**
     * @return string|null
     */
    protected function tryInferName()
    {
    }
}
/**
 * Scalar Type Definition
 *
 * The leaf values of any request and input values to arguments are
 * Scalars (or Enums) and are defined with a name and a series of coercion
 * functions used to ensure validity.
 *
 * Example:
 *
 * class OddType extends ScalarType
 * {
 *     public $name = 'Odd',
 *     public function serialize($value)
 *     {
 *         return $value % 2 === 1 ? $value : null;
 *     }
 * }
 */
abstract class ScalarType extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\InputType, \GraphQL\Type\Definition\LeafType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\NamedType
{
    /** @var ScalarTypeDefinitionNode|null */
    public $astNode;
    /** @var ScalarTypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config = [])
    {
    }
}
class IDType extends \GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = 'ID';
    /** @var string */
    public $description = 'The `ID` scalar type represents a unique identifier, often used to
refetch an object or as key for a cache. The ID type appears in a JSON
response as a String; however, it is not intended to be human-readable.
When expected as an input type, any string (such as `"4"`) or integer
(such as `4`) input value will be accepted as an ID.';
    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws Error
     */
    public function serialize($value)
    {
    }
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function parseValue($value) : string
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return string
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
}
class EnumType extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\InputType, \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\LeafType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\NamedType
{
    /** @var EnumTypeDefinitionNode|null */
    public $astNode;
    /**
     * Lazily initialized.
     *
     * @var EnumValueDefinition[]
     */
    private $values;
    /**
     * Lazily initialized.
     *
     * Actually a MixedStore<mixed, EnumValueDefinition>, PHPStan won't let us type it that way.
     *
     * @var MixedStore
     */
    private $valueLookup;
    /** @var ArrayObject<string, EnumValueDefinition> */
    private $nameLookup;
    /** @var EnumTypeExtensionNode[] */
    public $extensionASTNodes;
    public function __construct($config)
    {
    }
    /**
     * @param string|mixed[] $name
     *
     * @return EnumValueDefinition|null
     */
    public function getValue($name)
    {
    }
    private function getNameLookup() : \ArrayObject
    {
    }
    /**
     * @return EnumValueDefinition[]
     */
    public function getValues() : array
    {
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function serialize($value)
    {
    }
    /**
     * Actually returns a MixedStore<mixed, EnumValueDefinition>, PHPStan won't let us type it that way
     */
    private function getValueLookup() : \GraphQL\Utils\MixedStore
    {
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function parseValue($value)
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return null
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid()
    {
    }
}
class CustomScalarType extends \GraphQL\Type\Definition\ScalarType
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value)
    {
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
    public function assertValid()
    {
    }
}
class ListOfType extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\WrappingType, \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\InputType
{
    /** @var callable():Type|Type */
    public $ofType;
    /**
     * @param callable():Type|Type $type
     */
    public function __construct($type)
    {
    }
    public function toString() : string
    {
    }
    public function getOfType()
    {
    }
    public function getWrappedType(bool $recurse = false) : \GraphQL\Type\Definition\Type
    {
    }
}
class QueryPlan
{
    /** @var string[][] */
    private $types = [];
    /** @var Schema */
    private $schema;
    /** @var array<string, mixed> */
    private $queryPlan = [];
    /** @var mixed[] */
    private $variableValues;
    /** @var FragmentDefinitionNode[] */
    private $fragments;
    /** @var bool */
    private $groupImplementorFields;
    /**
     * @param FieldNode[]              $fieldNodes
     * @param mixed[]                  $variableValues
     * @param FragmentDefinitionNode[] $fragments
     * @param mixed[]                  $options
     */
    public function __construct(\GraphQL\Type\Definition\ObjectType $parentType, \GraphQL\Type\Schema $schema, iterable $fieldNodes, array $variableValues, array $fragments, array $options = [])
    {
    }
    /**
     * @return mixed[]
     */
    public function queryPlan() : array
    {
    }
    /**
     * @return string[]
     */
    public function getReferencedTypes() : array
    {
    }
    public function hasType(string $type) : bool
    {
    }
    /**
     * @return string[]
     */
    public function getReferencedFields() : array
    {
    }
    public function hasField(string $field) : bool
    {
    }
    /**
     * @return string[]
     */
    public function subFields(string $typename) : array
    {
    }
    /**
     * @param FieldNode[] $fieldNodes
     */
    private function analyzeQueryPlan(\GraphQL\Type\Definition\ObjectType $parentType, iterable $fieldNodes) : void
    {
    }
    /**
     * @param InterfaceType|ObjectType $parentType
     * @param mixed[]                  $implementors
     *
     * @return mixed[]
     *
     * @throws Error
     */
    private function analyzeSelectionSet(\GraphQL\Language\AST\SelectionSetNode $selectionSet, \GraphQL\Type\Definition\Type $parentType, array &$implementors) : array
    {
    }
    /**
     * @param mixed[] $implementors
     *
     * @return mixed[]
     */
    private function analyzeSubFields(\GraphQL\Type\Definition\Type $type, \GraphQL\Language\AST\SelectionSetNode $selectionSet, array &$implementors = []) : array
    {
    }
    /**
     * @param mixed[] $fields
     * @param mixed[] $subfields
     * @param mixed[] $implementors
     *
     * @return mixed[]
     */
    private function mergeFields(\GraphQL\Type\Definition\Type $parentType, \GraphQL\Type\Definition\Type $type, array $fields, array $subfields, array &$implementors) : array
    {
    }
    /**
     * similar to array_merge_recursive this merges nested arrays, but handles non array values differently
     * while array_merge_recursive tries to merge non array values, in this implementation they will be overwritten
     *
     * @see https://stackoverflow.com/a/25712428
     *
     * @param mixed[] $array1
     * @param mixed[] $array2
     *
     * @return mixed[]
     */
    private function arrayMergeDeep(array $array1, array $array2) : array
    {
    }
}
class BooleanType extends \GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = \GraphQL\Type\Definition\Type::BOOLEAN;
    /** @var string */
    public $description = 'The `Boolean` scalar type represents `true` or `false`.';
    /**
     * Serialize the given value to a boolean.
     *
     * The GraphQL spec leaves this up to the implementations, so we just do what
     * PHP does natively to make this intuitive for developers.
     *
     * @param mixed $value
     */
    public function serialize($value) : bool
    {
    }
    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @throws Error
     */
    public function parseValue($value)
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
}
interface HasFieldsType
{
    /**
     * @throws InvariantViolation
     */
    public function getField(string $name) : \GraphQL\Type\Definition\FieldDefinition;
    public function hasField(string $name) : bool;
    public function findField(string $name) : ?\GraphQL\Type\Definition\FieldDefinition;
    /**
     * @return array<string, FieldDefinition>
     *
     * @throws InvariantViolation
     */
    public function getFields() : array;
    /**
     * @return array<int, string>
     *
     * @throws InvariantViolation
     */
    public function getFieldNames() : array;
}
/*
export type GraphQLCompositeType =
GraphQLObjectType |
GraphQLInterfaceType |
GraphQLUnionType;
*/
interface CompositeType
{
}
/**
export type GraphQLImplementingType =
GraphQLObjectType |
GraphQLInterfaceType;
*/
interface ImplementingType
{
    public function implementsInterface(\GraphQL\Type\Definition\InterfaceType $interfaceType) : bool;
    /**
     * @return array<int, InterfaceType>
     */
    public function getInterfaces() : array;
}
abstract class TypeWithFields extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\HasFieldsType
{
    /**
     * Lazily initialized.
     *
     * @var array<string, FieldDefinition>
     */
    private $fields;
    private function initializeFields() : void
    {
    }
    public function getField(string $name) : \GraphQL\Type\Definition\FieldDefinition
    {
    }
    public function findField(string $name) : ?\GraphQL\Type\Definition\FieldDefinition
    {
    }
    public function hasField(string $name) : bool
    {
    }
    /** @inheritDoc */
    public function getFields() : array
    {
    }
    /** @inheritDoc */
    public function getFieldNames() : array
    {
    }
}
/**
 * Object Type Definition
 *
 * Almost all of the GraphQL types you define will be object types. Object types
 * have a name, but most importantly describe their fields.
 *
 * Example:
 *
 *     $AddressType = new ObjectType([
 *       'name' => 'Address',
 *       'fields' => [
 *         'street' => [ 'type' => GraphQL\Type\Definition\Type::string() ],
 *         'number' => [ 'type' => GraphQL\Type\Definition\Type::int() ],
 *         'formatted' => [
 *           'type' => GraphQL\Type\Definition\Type::string(),
 *           'resolve' => function($obj) {
 *             return $obj->number . ' ' . $obj->street;
 *           }
 *         ]
 *       ]
 *     ]);
 *
 * When two types need to refer to each other, or a type needs to refer to
 * itself in a field, you can use a function expression (aka a closure or a
 * thunk) to supply the fields lazily.
 *
 * Example:
 *
 *     $PersonType = null;
 *     $PersonType = new ObjectType([
 *       'name' => 'Person',
 *       'fields' => function() use (&$PersonType) {
 *          return [
 *              'name' => ['type' => GraphQL\Type\Definition\Type::string() ],
 *              'bestFriend' => [ 'type' => $PersonType ],
 *          ];
 *        }
 *     ]);
 */
class ObjectType extends \GraphQL\Type\Definition\TypeWithFields implements \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\CompositeType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\NamedType, \GraphQL\Type\Definition\ImplementingType
{
    /** @var ObjectTypeDefinitionNode|null */
    public $astNode;
    /** @var ObjectTypeExtensionNode[] */
    public $extensionASTNodes;
    /** @var ?callable */
    public $resolveFieldFn;
    /**
     * Lazily initialized.
     *
     * @var array<int, InterfaceType>
     */
    private $interfaces;
    /**
     * Lazily initialized.
     *
     * @var array<string, InterfaceType>
     */
    private $interfaceMap;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
    }
    /**
     * @param mixed $type
     *
     * @return $this
     *
     * @throws InvariantViolation
     */
    public static function assertObjectType($type) : self
    {
    }
    public function implementsInterface(\GraphQL\Type\Definition\InterfaceType $interfaceType) : bool
    {
    }
    /**
     * @return array<int, InterfaceType>
     */
    public function getInterfaces() : array
    {
    }
    /**
     * @param mixed $value
     * @param mixed $context
     *
     * @return bool|Deferred|null
     */
    public function isTypeOf($value, $context, \GraphQL\Type\Definition\ResolveInfo $info)
    {
    }
    /**
     * Validates type config and throws if one of type options is invalid.
     * Note: this method is shallow, it won't validate object fields and their arguments.
     *
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
    }
}
class IntType extends \GraphQL\Type\Definition\ScalarType
{
    // As per the GraphQL Spec, Integers are only treated as valid when a valid
    // 32-bit signed integer, providing the broadest support across platforms.
    //
    // n.b. JavaScript's integers are safe between -(2^53 - 1) and 2^53 - 1 because
    // they are internally represented as IEEE 754 doubles.
    private const MAX_INT = 2147483647;
    private const MIN_INT = -2147483648;
    /** @var string */
    public $name = \GraphQL\Type\Definition\Type::INT;
    /** @var string */
    public $description = 'The `Int` scalar type represents non-fractional signed whole numeric
values. Int can represent values between -(2^31) and 2^31 - 1. ';
    /**
     * @param mixed $value
     *
     * @return int|null
     *
     * @throws Error
     */
    public function serialize($value)
    {
    }
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function parseValue($value) : int
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return int
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
}
/**
export type AbstractType =
InterfaceType |
UnionType;
*/
interface AbstractType
{
    /**
     * Resolves concrete ObjectType for given object value
     *
     * @param object  $objectValue
     * @param mixed[] $context
     *
     * @return mixed
     */
    public function resolveType($objectValue, $context, \GraphQL\Type\Definition\ResolveInfo $info);
}
class UnionType extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\AbstractType, \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\CompositeType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\NamedType
{
    /** @var UnionTypeDefinitionNode */
    public $astNode;
    /**
     * Lazily initialized.
     *
     * @var ObjectType[]
     */
    private $types;
    /**
     * Lazily initialized.
     *
     * @var array<string, bool>
     */
    private $possibleTypeNames;
    /** @var UnionTypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
    }
    public function isPossibleType(\GraphQL\Type\Definition\Type $type) : bool
    {
    }
    /**
     * @return ObjectType[]
     *
     * @throws InvariantViolation
     */
    public function getTypes() : array
    {
    }
    /**
     * Resolves concrete ObjectType for given object value
     *
     * @param object $objectValue
     * @param mixed  $context
     *
     * @return callable|null
     */
    public function resolveType($objectValue, $context, \GraphQL\Type\Definition\ResolveInfo $info)
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
    }
}
class InterfaceType extends \GraphQL\Type\Definition\TypeWithFields implements \GraphQL\Type\Definition\AbstractType, \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\CompositeType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\NamedType, \GraphQL\Type\Definition\ImplementingType
{
    /** @var InterfaceTypeDefinitionNode|null */
    public $astNode;
    /** @var array<int, InterfaceTypeExtensionNode> */
    public $extensionASTNodes;
    /**
     * Lazily initialized.
     *
     * @var array<int, InterfaceType>
     */
    private $interfaces;
    /**
     * Lazily initialized.
     *
     * @var array<string, InterfaceType>
     */
    private $interfaceMap;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
    }
    /**
     * @param mixed $type
     *
     * @return $this
     *
     * @throws InvariantViolation
     */
    public static function assertInterfaceType($type) : self
    {
    }
    public function implementsInterface(\GraphQL\Type\Definition\InterfaceType $interfaceType) : bool
    {
    }
    /**
     * @return array<int, InterfaceType>
     */
    public function getInterfaces() : array
    {
    }
    /**
     * Resolves concrete ObjectType for given object value
     *
     * @param object $objectValue
     * @param mixed  $context
     *
     * @return Type|null
     */
    public function resolveType($objectValue, $context, \GraphQL\Type\Definition\ResolveInfo $info)
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
    }
}
class FloatType extends \GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = \GraphQL\Type\Definition\Type::FLOAT;
    /** @var string */
    public $description = 'The `Float` scalar type represents signed double-precision fractional
values as specified by
[IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point). ';
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function serialize($value) : float
    {
    }
    /**
     * @param mixed $value
     *
     * @throws Error
     */
    public function parseValue($value) : float
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return float
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
}
class InputObjectField
{
    /** @var string */
    public $name;
    /** @var mixed|null */
    public $defaultValue;
    /** @var string|null */
    public $description;
    /** @var Type&InputType */
    private $type;
    /** @var InputValueDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /**
     * @param mixed[] $opts
     */
    public function __construct(array $opts)
    {
    }
    public function __isset(string $name) : bool
    {
    }
    public function __get(string $name)
    {
    }
    public function __set(string $name, $value)
    {
    }
    /**
     * @return Type&InputType
     */
    public function getType() : \GraphQL\Type\Definition\Type
    {
    }
    public function defaultValueExists() : bool
    {
    }
    public function isRequired() : bool
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid(\GraphQL\Type\Definition\Type $parentType)
    {
    }
}
/**
 * @todo Move complexity-related code to it's own place
 */
class FieldDefinition
{
    public const DEFAULT_COMPLEXITY_FN = 'GraphQL\\Type\\Definition\\FieldDefinition::defaultComplexity';
    /** @var string */
    public $name;
    /** @var FieldArgument[] */
    public $args;
    /**
     * Callback for resolving field value given parent value.
     * Mutually exclusive with `map`
     *
     * @var callable|null
     */
    public $resolveFn;
    /**
     * Callback for mapping list of parent values to list of field values.
     * Mutually exclusive with `resolve`
     *
     * @var callable|null
     */
    public $mapFn;
    /** @var string|null */
    public $description;
    /** @var string|null */
    public $deprecationReason;
    /** @var FieldDefinitionNode|null */
    public $astNode;
    /**
     * Original field definition config
     *
     * @var mixed[]
     */
    public $config;
    /** @var OutputType&Type */
    private $type;
    /** @var callable|string */
    private $complexityFn;
    /**
     * @param mixed[] $config
     */
    protected function __construct(array $config)
    {
    }
    /**
     * @param (callable():mixed[])|mixed[] $fields
     *
     * @return array<string, self>
     */
    public static function defineFieldMap(\GraphQL\Type\Definition\Type $type, $fields) : array
    {
    }
    /**
     * @param mixed[] $field
     *
     * @return FieldDefinition
     */
    public static function create($field)
    {
    }
    /**
     * @param int $childrenComplexity
     *
     * @return mixed
     */
    public static function defaultComplexity($childrenComplexity)
    {
    }
    /**
     * @param string $name
     *
     * @return FieldArgument|null
     */
    public function getArg($name)
    {
    }
    public function getName() : string
    {
    }
    public function getType() : \GraphQL\Type\Definition\Type
    {
    }
    public function __isset(string $name) : bool
    {
    }
    public function __get(string $name)
    {
    }
    public function __set(string $name, $value)
    {
    }
    /**
     * @return bool
     */
    public function isDeprecated()
    {
    }
    /**
     * @return callable|callable
     */
    public function getComplexityFn()
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function assertValid(\GraphQL\Type\Definition\Type $parentType)
    {
    }
}
class InputObjectType extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\InputType, \GraphQL\Type\Definition\NullableType, \GraphQL\Type\Definition\NamedType
{
    /** @var InputObjectTypeDefinitionNode|null */
    public $astNode;
    /**
     * Lazily initialized.
     *
     * @var InputObjectField[]
     */
    private $fields;
    /** @var InputObjectTypeExtensionNode[] */
    public $extensionASTNodes;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
    }
    /**
     * @throws InvariantViolation
     */
    public function getField(string $name) : \GraphQL\Type\Definition\InputObjectField
    {
    }
    /**
     * @return InputObjectField[]
     */
    public function getFields() : array
    {
    }
    protected function initializeFields() : void
    {
    }
    /**
     * Validates type config and throws if one of type options is invalid.
     * Note: this method is shallow, it won't validate object fields and their arguments.
     *
     * @throws InvariantViolation
     */
    public function assertValid() : void
    {
    }
}
class Directive
{
    public const DEFAULT_DEPRECATION_REASON = 'No longer supported';
    public const INCLUDE_NAME = 'include';
    public const IF_ARGUMENT_NAME = 'if';
    public const SKIP_NAME = 'skip';
    public const DEPRECATED_NAME = 'deprecated';
    public const REASON_ARGUMENT_NAME = 'reason';
    /** @var Directive[]|null */
    public static $internalDirectives;
    // Schema Definitions
    /** @var string */
    public $name;
    /** @var string|null */
    public $description;
    /** @var FieldArgument[] */
    public $args = [];
    /** @var bool */
    public $isRepeatable;
    /** @var string[] */
    public $locations;
    /** @var DirectiveDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
    }
    /**
     * @return Directive
     */
    public static function includeDirective()
    {
    }
    /**
     * @return Directive[]
     */
    public static function getInternalDirectives() : array
    {
    }
    /**
     * @return Directive
     */
    public static function skipDirective()
    {
    }
    /**
     * @return Directive
     */
    public static function deprecatedDirective()
    {
    }
    /**
     * @return bool
     */
    public static function isSpecifiedDirective(\GraphQL\Type\Definition\Directive $directive)
    {
    }
}
class FieldArgument
{
    /** @var string */
    public $name;
    /** @var mixed */
    public $defaultValue;
    /** @var string|null */
    public $description;
    /** @var InputValueDefinitionNode|null */
    public $astNode;
    /** @var mixed[] */
    public $config;
    /** @var Type&InputType */
    private $type;
    /** @param mixed[] $def */
    public function __construct(array $def)
    {
    }
    /**
     * @param mixed[] $config
     *
     * @return FieldArgument[]
     */
    public static function createMap(array $config) : array
    {
    }
    public function getType() : \GraphQL\Type\Definition\Type
    {
    }
    public function defaultValueExists() : bool
    {
    }
    public function isRequired() : bool
    {
    }
    public function assertValid(\GraphQL\Type\Definition\FieldDefinition $parentField, \GraphQL\Type\Definition\Type $parentType)
    {
    }
}
class UnresolvedFieldDefinition
{
    /** @var Type $type */
    private $type;
    /** @var string $name */
    private $name;
    /** @var callable(): (FieldDefinition|array<string, mixed>|Type) $resolver */
    private $resolver;
    /**
     * @param callable(): (FieldDefinition|array<string, mixed>|Type) $resolver
     */
    public function __construct(\GraphQL\Type\Definition\Type $type, string $name, callable $resolver)
    {
    }
    public function getName() : string
    {
    }
    public function resolve() : \GraphQL\Type\Definition\FieldDefinition
    {
    }
}
class NonNull extends \GraphQL\Type\Definition\Type implements \GraphQL\Type\Definition\WrappingType, \GraphQL\Type\Definition\OutputType, \GraphQL\Type\Definition\InputType
{
    /** @var callable():(NullableType&Type)|(NullableType&Type) */
    private $ofType;
    /**
     * code sniffer doesn't understand this syntax. Pr with a fix here: waiting on https://github.com/squizlabs/PHP_CodeSniffer/pull/2919
     * phpcs:disable Squiz.Commenting.FunctionComment.SpacingAfterParamType
     * @param callable():(NullableType&Type)|(NullableType&Type) $type
     */
    public function __construct($type)
    {
    }
    public function toString() : string
    {
    }
    public function getOfType()
    {
    }
    /**
     * @return (NullableType&Type)
     */
    public function getWrappedType(bool $recurse = false) : \GraphQL\Type\Definition\Type
    {
    }
}
class StringType extends \GraphQL\Type\Definition\ScalarType
{
    /** @var string */
    public $name = \GraphQL\Type\Definition\Type::STRING;
    /** @var string */
    public $description = 'The `String` scalar type represents textual data, represented as UTF-8
character sequences. The String type is most often used by GraphQL to
represent free-form human-readable text.';
    /**
     * @param mixed $value
     *
     * @return mixed|string
     *
     * @throws Error
     */
    public function serialize($value)
    {
    }
    /**
     * @param mixed $value
     *
     * @return string
     *
     * @throws Error
     */
    public function parseValue($value)
    {
    }
    /**
     * @param mixed[]|null $variables
     *
     * @return string
     *
     * @throws Exception
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
    }
}
namespace GraphQL\Type;

/**
 * Schema Definition (see [related docs](type-system/schema.md))
 *
 * A Schema is created by supplying the root types of each type of operation:
 * query, mutation (optional) and subscription (optional). A schema definition is
 * then supplied to the validator and executor. Usage Example:
 *
 *     $schema = new GraphQL\Type\Schema([
 *       'query' => $MyAppQueryRootType,
 *       'mutation' => $MyAppMutationRootType,
 *     ]);
 *
 * Or using Schema Config instance:
 *
 *     $config = GraphQL\Type\SchemaConfig::create()
 *         ->setQuery($MyAppQueryRootType)
 *         ->setMutation($MyAppMutationRootType);
 *
 *     $schema = new GraphQL\Type\Schema($config);
 */
class Schema
{
    /** @var SchemaConfig */
    private $config;
    /**
     * Contains currently resolved schema types
     *
     * @var Type[]
     */
    private $resolvedTypes = [];
    /**
     * Lazily initialised.
     *
     * @var array<string, InterfaceImplementations>
     */
    private $implementationsMap;
    /**
     * True when $resolvedTypes contain all possible schema types
     *
     * @var bool
     */
    private $fullyLoaded = false;
    /** @var Error[] */
    private $validationErrors;
    /** @var SchemaTypeExtensionNode[] */
    public $extensionASTNodes = [];
    /**
     * @param mixed[]|SchemaConfig $config
     *
     * @api
     */
    public function __construct($config)
    {
    }
    /**
     * @return Generator
     */
    private function resolveAdditionalTypes()
    {
    }
    /**
     * Returns array of all types in this schema. Keys of this array represent type names, values are instances
     * of corresponding type definitions
     *
     * This operation requires full schema scan. Do not use in production environment.
     *
     * @return array<string, Type>
     *
     * @api
     */
    public function getTypeMap() : array
    {
    }
    /**
     * @return Type[]
     */
    private function collectAllTypes()
    {
    }
    /**
     * Returns a list of directives supported by this schema
     *
     * @return Directive[]
     *
     * @api
     */
    public function getDirectives()
    {
    }
    /**
     * @param string $operation
     *
     * @return ObjectType|null
     */
    public function getOperationType($operation)
    {
    }
    /**
     * Returns schema query type
     *
     * @return ObjectType
     *
     * @api
     */
    public function getQueryType() : ?\GraphQL\Type\Definition\Type
    {
    }
    /**
     * Returns schema mutation type
     *
     * @return ObjectType|null
     *
     * @api
     */
    public function getMutationType() : ?\GraphQL\Type\Definition\Type
    {
    }
    /**
     * Returns schema subscription
     *
     * @return ObjectType|null
     *
     * @api
     */
    public function getSubscriptionType() : ?\GraphQL\Type\Definition\Type
    {
    }
    /**
     * @return SchemaConfig
     *
     * @api
     */
    public function getConfig()
    {
    }
    /**
     * Returns type by its name
     *
     * @api
     */
    public function getType(string $name) : ?\GraphQL\Type\Definition\Type
    {
    }
    public function hasType(string $name) : bool
    {
    }
    private function loadType(string $typeName) : ?\GraphQL\Type\Definition\Type
    {
    }
    protected function throwNotAType($type, string $typeName)
    {
    }
    private function defaultTypeLoader(string $typeName) : ?\GraphQL\Type\Definition\Type
    {
    }
    /**
     * @param Type|callable():Type $type
     */
    public static function resolveType($type) : \GraphQL\Type\Definition\Type
    {
    }
    /**
     * Returns all possible concrete types for given abstract type
     * (implementations for interfaces and members of union type for unions)
     *
     * This operation requires full schema scan. Do not use in production environment.
     *
     * @param InterfaceType|UnionType $abstractType
     *
     * @return array<Type&ObjectType>
     *
     * @api
     */
    public function getPossibleTypes(\GraphQL\Type\Definition\Type $abstractType) : array
    {
    }
    /**
     * Returns all types that implement a given interface type.
     *
     * This operations requires full schema scan. Do not use in production environment.
     *
     * @api
     */
    public function getImplementations(\GraphQL\Type\Definition\InterfaceType $abstractType) : \GraphQL\Utils\InterfaceImplementations
    {
    }
    /**
     * @return array<string, InterfaceImplementations>
     */
    private function collectImplementations() : array
    {
    }
    /**
     * @deprecated as of 14.4.0 use isSubType instead, will be removed in 15.0.0.
     *
     * Returns true if object type is concrete type of given abstract type
     * (implementation for interfaces and members of union type for unions)
     *
     * @api
     * @codeCoverageIgnore
     */
    public function isPossibleType(\GraphQL\Type\Definition\AbstractType $abstractType, \GraphQL\Type\Definition\ObjectType $possibleType) : bool
    {
    }
    /**
     * Returns true if the given type is a sub type of the given abstract type.
     *
     * @param UnionType|InterfaceType  $abstractType
     * @param ObjectType|InterfaceType $maybeSubType
     *
     * @api
     */
    public function isSubType(\GraphQL\Type\Definition\AbstractType $abstractType, \GraphQL\Type\Definition\ImplementingType $maybeSubType) : bool
    {
    }
    /**
     * Returns instance of directive by name
     *
     * @api
     */
    public function getDirective(string $name) : ?\GraphQL\Type\Definition\Directive
    {
    }
    public function getAstNode() : ?\GraphQL\Language\AST\SchemaDefinitionNode
    {
    }
    /**
     * Validates schema.
     *
     * This operation requires full schema scan. Do not use in production environment.
     *
     * @throws InvariantViolation
     *
     * @api
     */
    public function assertValid()
    {
    }
    /**
     * Validates schema.
     *
     * This operation requires full schema scan. Do not use in production environment.
     *
     * @return InvariantViolation[]|Error[]
     *
     * @api
     */
    public function validate()
    {
    }
}
/**
 * Schema configuration class.
 * Could be passed directly to schema constructor. List of options accepted by **create** method is
 * [described in docs](type-system/schema.md#configuration-options).
 *
 * Usage example:
 *
 *     $config = SchemaConfig::create()
 *         ->setQuery($myQueryType)
 *         ->setTypeLoader($myTypeLoader);
 *
 *     $schema = new Schema($config);
 */
class SchemaConfig
{
    /** @var ObjectType|null */
    public $query;
    /** @var ObjectType|null */
    public $mutation;
    /** @var ObjectType|null */
    public $subscription;
    /** @var Type[]|callable */
    public $types = [];
    /** @var Directive[]|null */
    public $directives;
    /** @var callable|null */
    public $typeLoader;
    /** @var SchemaDefinitionNode|null */
    public $astNode;
    /** @var bool */
    public $assumeValid = false;
    /** @var SchemaTypeExtensionNode[] */
    public $extensionASTNodes = [];
    /**
     * Converts an array of options to instance of SchemaConfig
     * (or just returns empty config when array is not passed).
     *
     * @param mixed[] $options
     *
     * @return SchemaConfig
     *
     * @api
     */
    public static function create(array $options = [])
    {
    }
    /**
     * @return SchemaDefinitionNode|null
     */
    public function getAstNode()
    {
    }
    /**
     * @return SchemaConfig
     */
    public function setAstNode(\GraphQL\Language\AST\SchemaDefinitionNode $astNode)
    {
    }
    /**
     * @return ObjectType|null
     *
     * @api
     */
    public function getQuery()
    {
    }
    /**
     * @param ObjectType|null $query
     *
     * @return SchemaConfig
     *
     * @api
     */
    public function setQuery($query)
    {
    }
    /**
     * @return ObjectType|null
     *
     * @api
     */
    public function getMutation()
    {
    }
    /**
     * @param ObjectType|null $mutation
     *
     * @return SchemaConfig
     *
     * @api
     */
    public function setMutation($mutation)
    {
    }
    /**
     * @return ObjectType|null
     *
     * @api
     */
    public function getSubscription()
    {
    }
    /**
     * @param ObjectType|null $subscription
     *
     * @return SchemaConfig
     *
     * @api
     */
    public function setSubscription($subscription)
    {
    }
    /**
     * @return Type[]|callable
     *
     * @api
     */
    public function getTypes()
    {
    }
    /**
     * @param Type[]|callable $types
     *
     * @return SchemaConfig
     *
     * @api
     */
    public function setTypes($types)
    {
    }
    /**
     * @return Directive[]|null
     *
     * @api
     */
    public function getDirectives()
    {
    }
    /**
     * @param Directive[] $directives
     *
     * @return SchemaConfig
     *
     * @api
     */
    public function setDirectives(array $directives)
    {
    }
    /**
     * @return callable|null
     *
     * @api
     */
    public function getTypeLoader()
    {
    }
    /**
     * @return SchemaConfig
     *
     * @api
     */
    public function setTypeLoader(callable $typeLoader)
    {
    }
    /**
     * @return bool
     */
    public function getAssumeValid()
    {
    }
    /**
     * @param bool $assumeValid
     *
     * @return SchemaConfig
     */
    public function setAssumeValid($assumeValid)
    {
    }
    /**
     * @return SchemaTypeExtensionNode[]
     */
    public function getExtensionASTNodes()
    {
    }
    /**
     * @param SchemaTypeExtensionNode[] $extensionASTNodes
     */
    public function setExtensionASTNodes(array $extensionASTNodes)
    {
    }
}
namespace GraphQL\Type\Validation;

class InputObjectCircularRefs
{
    /** @var SchemaValidationContext */
    private $schemaValidationContext;
    /**
     * Tracks already visited types to maintain O(N) and to ensure that cycles
     * are not redundantly reported.
     *
     * @var array<string, bool>
     */
    private $visitedTypes = [];
    /** @var InputObjectField[] */
    private $fieldPath = [];
    /**
     * Position in the type path.
     *
     * [string $typeName => int $index]
     *
     * @var int[]
     */
    private $fieldPathIndexByTypeName = [];
    public function __construct(\GraphQL\Type\SchemaValidationContext $schemaValidationContext)
    {
    }
    /**
     * This does a straight-forward DFS to find cycles.
     * It does not terminate when a cycle was found but continues to explore
     * the graph to find all possible cycles.
     */
    public function validate(\GraphQL\Type\Definition\InputObjectType $inputObj) : void
    {
    }
}
namespace GraphQL\Executor;

class Values
{
    /**
     * Prepares an object map of variables of the correct type based on the provided
     * variable definitions and arbitrary input. If the input cannot be coerced
     * to match the variable definitions, a Error will be thrown.
     *
     * @param VariableDefinitionNode[] $varDefNodes
     * @param mixed[]                  $inputs
     *
     * @return mixed[]
     */
    public static function getVariableValues(\GraphQL\Type\Schema $schema, $varDefNodes, array $inputs)
    {
    }
    /**
     * Prepares an object map of argument values given a directive definition
     * and a AST node which may contain directives. Optionally also accepts a map
     * of variable values.
     *
     * If the directive does not exist on the node, returns undefined.
     *
     * @param FragmentSpreadNode|FieldNode|InlineFragmentNode|EnumValueDefinitionNode|FieldDefinitionNode $node
     * @param mixed[]|null                                                                                $variableValues
     *
     * @return mixed[]|null
     */
    public static function getDirectiveValues(\GraphQL\Type\Definition\Directive $directiveDef, $node, $variableValues = null)
    {
    }
    /**
     * Prepares an object map of argument values given a list of argument
     * definitions and list of argument AST nodes.
     *
     * @param FieldDefinition|Directive $def
     * @param FieldNode|DirectiveNode   $node
     * @param mixed[]                   $variableValues
     *
     * @return mixed[]
     *
     * @throws Error
     */
    public static function getArgumentValues($def, $node, $variableValues = null)
    {
    }
    /**
     * @param FieldDefinition|Directive $fieldDefinition
     * @param ArgumentNode[]            $argumentValueMap
     * @param mixed[]                   $variableValues
     * @param Node|null                 $referenceNode
     *
     * @return mixed[]
     *
     * @throws Error
     */
    public static function getArgumentValuesForMap($fieldDefinition, $argumentValueMap, $variableValues = null, $referenceNode = null)
    {
    }
    /**
     * @deprecated as of 8.0 (Moved to \GraphQL\Utils\AST::valueFromAST)
     *
     * @param VariableNode|NullValueNode|IntValueNode|FloatValueNode|StringValueNode|BooleanValueNode|EnumValueNode|ListValueNode|ObjectValueNode $valueNode
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull                                                                              $type
     * @param mixed[]|null                                                                                                                        $variables
     *
     * @return mixed[]|stdClass|null
     *
     * @codeCoverageIgnore
     */
    public static function valueFromAST(\GraphQL\Language\AST\ValueNode $valueNode, \GraphQL\Type\Definition\InputType $type, ?array $variables = null)
    {
    }
    /**
     * @deprecated as of 0.12 (Use coerceValue() directly for richer information)
     *
     * @param mixed[]                                                $value
     * @param ScalarType|EnumType|InputObjectType|ListOfType|NonNull $type
     *
     * @return string[]
     *
     * @codeCoverageIgnore
     */
    public static function isValidPHPValue($value, \GraphQL\Type\Definition\InputType $type)
    {
    }
}
/**
 * Returned after [query execution](executing-queries.md).
 * Represents both - result of successful execution and of a failed one
 * (with errors collected in `errors` prop)
 *
 * Could be converted to [spec-compliant](https://facebook.github.io/graphql/#sec-Response-Format)
 * serializable array using `toArray()`
 */
class ExecutionResult implements \JsonSerializable
{
    /**
     * Data collected from resolvers during query execution
     *
     * @api
     * @var mixed[]
     */
    public $data;
    /**
     * Errors registered during query execution.
     *
     * If an error was caused by exception thrown in resolver, $error->getPrevious() would
     * contain original exception.
     *
     * @api
     * @var Error[]
     */
    public $errors;
    /**
     * User-defined serializable array of extensions included in serialized result.
     * Conforms to
     *
     * @api
     * @var mixed[]
     */
    public $extensions;
    /** @var callable */
    private $errorFormatter;
    /** @var callable */
    private $errorsHandler;
    /**
     * @param mixed[] $data
     * @param Error[] $errors
     * @param mixed[] $extensions
     */
    public function __construct($data = null, array $errors = [], array $extensions = [])
    {
    }
    /**
     * Define custom error formatting (must conform to http://facebook.github.io/graphql/#sec-Errors)
     *
     * Expected signature is: function (GraphQL\Error\Error $error): array
     *
     * Default formatter is "GraphQL\Error\FormattedError::createFromException"
     *
     * Expected returned value must be an array:
     * array(
     *    'message' => 'errorMessage',
     *    // ... other keys
     * );
     *
     * @return self
     *
     * @api
     */
    public function setErrorFormatter(callable $errorFormatter)
    {
    }
    /**
     * Define custom logic for error handling (filtering, logging, etc).
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * Default handler is:
     * function (array $errors, callable $formatter) {
     *     return array_map($formatter, $errors);
     * }
     *
     * @return self
     *
     * @api
     */
    public function setErrorsHandler(callable $handler)
    {
    }
    /**
     * @return mixed[]
     */
    public function jsonSerialize()
    {
    }
    /**
     * Converts GraphQL query result to spec-compliant serializable array using provided
     * errors handler and formatter.
     *
     * If debug argument is passed, output of error formatter is enriched which debugging information
     * ("debugMessage", "trace" keys depending on flags).
     *
     * $debug argument must sum of flags from @see \GraphQL\Error\DebugFlag
     *
     * @return mixed[]
     *
     * @api
     */
    public function toArray(int $debug = \GraphQL\Error\DebugFlag::NONE) : array
    {
    }
}
class ReferenceExecutor implements \GraphQL\Executor\ExecutorImplementation
{
    /** @var object */
    protected static $UNDEFINED;
    /** @var ExecutionContext */
    protected $exeContext;
    /** @var SplObjectStorage */
    protected $subFieldCache;
    protected function __construct(\GraphQL\Executor\ExecutionContext $context)
    {
    }
    /**
     * @param mixed                    $rootValue
     * @param mixed                    $contextValue
     * @param array<mixed>|Traversable $variableValues
     */
    public static function create(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentNode, $rootValue, $contextValue, $variableValues, ?string $operationName, callable $fieldResolver) : \GraphQL\Executor\ExecutorImplementation
    {
    }
    /**
     * Constructs an ExecutionContext object from the arguments passed to
     * execute, which we will pass throughout the other execution methods.
     *
     * @param mixed                    $rootValue
     * @param mixed                    $contextValue
     * @param array<mixed>|Traversable $rawVariableValues
     *
     * @return ExecutionContext|array<Error>
     */
    protected static function buildExecutionContext(\GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentNode, $rootValue, $contextValue, $rawVariableValues, ?string $operationName = null, ?callable $fieldResolver = null, ?\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter = null)
    {
    }
    public function doExecute() : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @param mixed|Promise|null $data
     *
     * @return ExecutionResult|Promise
     */
    protected function buildResponse($data)
    {
    }
    /**
     * Implements the "Evaluating operations" section of the spec.
     *
     * @param mixed $rootValue
     *
     * @return array<mixed>|Promise|stdClass|null
     */
    protected function executeOperation(\GraphQL\Language\AST\OperationDefinitionNode $operation, $rootValue)
    {
    }
    /**
     * Extracts the root type of the operation from the schema.
     *
     * @throws Error
     */
    protected function getOperationRootType(\GraphQL\Type\Schema $schema, \GraphQL\Language\AST\OperationDefinitionNode $operation) : \GraphQL\Type\Definition\ObjectType
    {
    }
    /**
     * Given a selectionSet, adds all of the fields in that selection to
     * the passed in map of fields, and returns it at the end.
     *
     * CollectFields requires the "runtime type" of an object. For a field which
     * returns an Interface or Union type, the "runtime type" will be the actual
     * Object type returned by that field.
     */
    protected function collectFields(\GraphQL\Type\Definition\ObjectType $runtimeType, \GraphQL\Language\AST\SelectionSetNode $selectionSet, \ArrayObject $fields, \ArrayObject $visitedFragmentNames) : \ArrayObject
    {
    }
    /**
     * Determines if a field should be included based on the @include and @skip
     * directives, where @skip has higher precedence than @include.
     *
     * @param FragmentSpreadNode|FieldNode|InlineFragmentNode $node
     */
    protected function shouldIncludeNode(\GraphQL\Language\AST\SelectionNode $node) : bool
    {
    }
    /**
     * Implements the logic to compute the key of a given fields entry
     */
    protected static function getFieldEntryKey(\GraphQL\Language\AST\FieldNode $node) : string
    {
    }
    /**
     * Determines if a fragment is applicable to the given type.
     *
     * @param FragmentDefinitionNode|InlineFragmentNode $fragment
     */
    protected function doesFragmentConditionMatch(\GraphQL\Language\AST\Node $fragment, \GraphQL\Type\Definition\ObjectType $type) : bool
    {
    }
    /**
     * Implements the "Evaluating selection sets" section of the spec
     * for "write" mode.
     *
     * @param mixed             $rootValue
     * @param array<string|int> $path
     *
     * @return array<mixed>|Promise|stdClass
     */
    protected function executeFieldsSerially(\GraphQL\Type\Definition\ObjectType $parentType, $rootValue, array $path, \ArrayObject $fields)
    {
    }
    /**
     * Resolves the field on the given root value.
     *
     * In particular, this figures out the value that the field returns
     * by calling its resolve function, then calls completeValue to complete promises,
     * serialize scalars, or execute the sub-selection-set for objects.
     *
     * @param mixed             $rootValue
     * @param array<string|int> $path
     *
     * @return array<mixed>|Throwable|mixed|null
     */
    protected function resolveField(\GraphQL\Type\Definition\ObjectType $parentType, $rootValue, \ArrayObject $fieldNodes, array $path)
    {
    }
    /**
     * This method looks up the field on the given type definition.
     *
     * It has special casing for the two introspection fields, __schema
     * and __typename. __typename is special because it can always be
     * queried as a field, even in situations where no other fields
     * are allowed, like on a Union. __schema could get automatically
     * added to the query type, but that would require mutating type
     * definitions, which would cause issues.
     */
    protected function getFieldDef(\GraphQL\Type\Schema $schema, \GraphQL\Type\Definition\ObjectType $parentType, string $fieldName) : ?\GraphQL\Type\Definition\FieldDefinition
    {
    }
    /**
     * Isolates the "ReturnOrAbrupt" behavior to not de-opt the `resolveField` function.
     * Returns the result of resolveFn or the abrupt-return Error object.
     *
     * @param mixed $rootValue
     *
     * @return Throwable|Promise|mixed
     */
    protected function resolveFieldValueOrError(\GraphQL\Type\Definition\FieldDefinition $fieldDef, \GraphQL\Language\AST\FieldNode $fieldNode, callable $resolveFn, $rootValue, \GraphQL\Type\Definition\ResolveInfo $info)
    {
    }
    /**
     * This is a small wrapper around completeValue which detects and logs errors
     * in the execution context.
     *
     * @param array<string|int> $path
     * @param mixed             $result
     *
     * @return array<mixed>|Promise|stdClass|null
     */
    protected function completeValueCatchingError(\GraphQL\Type\Definition\Type $returnType, \ArrayObject $fieldNodes, \GraphQL\Type\Definition\ResolveInfo $info, array $path, $result)
    {
    }
    /**
     * @param mixed             $rawError
     * @param array<string|int> $path
     *
     * @throws Error
     */
    protected function handleFieldError($rawError, \ArrayObject $fieldNodes, array $path, \GraphQL\Type\Definition\Type $returnType) : void
    {
    }
    /**
     * Implements the instructions for completeValue as defined in the
     * "Field entries" section of the spec.
     *
     * If the field type is Non-Null, then this recursively completes the value
     * for the inner type. It throws a field error if that completion returns null,
     * as per the "Nullability" section of the spec.
     *
     * If the field type is a List, then this recursively completes the value
     * for the inner type on each item in the list.
     *
     * If the field type is a Scalar or Enum, ensures the completed value is a legal
     * value of the type by calling the `serialize` method of GraphQL type
     * definition.
     *
     * If the field is an abstract type, determine the runtime type of the value
     * and then complete based on that type
     *
     * Otherwise, the field type expects a sub-selection set, and will complete the
     * value by evaluating all sub-selections.
     *
     * @param array<string|int> $path
     * @param mixed             $result
     *
     * @return array<mixed>|mixed|Promise|null
     *
     * @throws Error
     * @throws Throwable
     */
    protected function completeValue(\GraphQL\Type\Definition\Type $returnType, \ArrayObject $fieldNodes, \GraphQL\Type\Definition\ResolveInfo $info, array $path, &$result)
    {
    }
    /**
     * @param mixed $value
     */
    protected function isPromise($value) : bool
    {
    }
    /**
     * Only returns the value if it acts like a Promise, i.e. has a "then" function,
     * otherwise returns null.
     *
     * @param mixed $value
     */
    protected function getPromise($value) : ?\GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * Similar to array_reduce(), however the reducing callback may return
     * a Promise, in which case reduction will continue after each promise resolves.
     *
     * If the callback does not return a Promise, then this function will also not
     * return a Promise.
     *
     * @param array<mixed>       $values
     * @param Promise|mixed|null $initialValue
     *
     * @return Promise|mixed|null
     */
    protected function promiseReduce(array $values, callable $callback, $initialValue)
    {
    }
    /**
     * Complete a list value by completing each item in the list with the inner type.
     *
     * @param array<string|int>        $path
     * @param array<mixed>|Traversable $results
     *
     * @return array<mixed>|Promise|stdClass
     *
     * @throws Exception
     */
    protected function completeListValue(\GraphQL\Type\Definition\ListOfType $returnType, \ArrayObject $fieldNodes, \GraphQL\Type\Definition\ResolveInfo $info, array $path, &$results)
    {
    }
    /**
     * Complete a Scalar or Enum by serializing to a valid value, throwing if serialization is not possible.
     *
     * @param mixed $result
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function completeLeafValue(\GraphQL\Type\Definition\LeafType $returnType, &$result)
    {
    }
    /**
     * Complete a value of an abstract type by determining the runtime object type
     * of that value, then complete the value for that type.
     *
     * @param array<string|int> $path
     * @param array<mixed>      $result
     *
     * @return array<mixed>|Promise|stdClass
     *
     * @throws Error
     */
    protected function completeAbstractValue(\GraphQL\Type\Definition\AbstractType $returnType, \ArrayObject $fieldNodes, \GraphQL\Type\Definition\ResolveInfo $info, array $path, &$result)
    {
    }
    /**
     * If a resolveType function is not given, then a default resolve behavior is
     * used which attempts two strategies:
     *
     * First, See if the provided value has a `__typename` field defined, if so, use
     * that value as name of the resolved type.
     *
     * Otherwise, test each possible type for the abstract type by calling
     * isTypeOf for the object being coerced, returning the first type that matches.
     *
     * @param mixed|null              $value
     * @param mixed|null              $contextValue
     * @param InterfaceType|UnionType $abstractType
     *
     * @return Promise|Type|string|null
     */
    protected function defaultTypeResolver($value, $contextValue, \GraphQL\Type\Definition\ResolveInfo $info, \GraphQL\Type\Definition\AbstractType $abstractType)
    {
    }
    /**
     * Complete an Object value by executing all sub-selections.
     *
     * @param array<string|int> $path
     * @param mixed             $result
     *
     * @return array<mixed>|Promise|stdClass
     *
     * @throws Error
     */
    protected function completeObjectValue(\GraphQL\Type\Definition\ObjectType $returnType, \ArrayObject $fieldNodes, \GraphQL\Type\Definition\ResolveInfo $info, array $path, &$result)
    {
    }
    /**
     * @param array<mixed> $result
     *
     * @return Error
     */
    protected function invalidReturnTypeError(\GraphQL\Type\Definition\ObjectType $returnType, $result, \ArrayObject $fieldNodes)
    {
    }
    /**
     * @param array<string|int> $path
     * @param mixed             $result
     *
     * @return array<mixed>|Promise|stdClass
     *
     * @throws Error
     */
    protected function collectAndExecuteSubfields(\GraphQL\Type\Definition\ObjectType $returnType, \ArrayObject $fieldNodes, array $path, &$result)
    {
    }
    /**
     * A memoized collection of relevant subfields with regard to the return
     * type. Memoizing ensures the subfields are not repeatedly calculated, which
     * saves overhead when resolving lists of values.
     */
    protected function collectSubFields(\GraphQL\Type\Definition\ObjectType $returnType, \ArrayObject $fieldNodes) : \ArrayObject
    {
    }
    /**
     * Implements the "Evaluating selection sets" section of the spec
     * for "read" mode.
     *
     * @param mixed             $rootValue
     * @param array<string|int> $path
     *
     * @return Promise|stdClass|array<mixed>
     */
    protected function executeFields(\GraphQL\Type\Definition\ObjectType $parentType, $rootValue, array $path, \ArrayObject $fields)
    {
    }
    /**
     * Differentiate empty objects from empty lists.
     *
     * @see https://github.com/webonyx/graphql-php/issues/59
     *
     * @param array<mixed>|mixed $results
     *
     * @return array<mixed>|stdClass|mixed
     */
    protected static function fixResultsIfEmptyArray($results)
    {
    }
    /**
     * Transform an associative array with Promises to a Promise which resolves to an
     * associative array where all Promises were resolved.
     *
     * @param array<string, Promise|mixed> $assoc
     */
    protected function promiseForAssocArray(array $assoc) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @param string|ObjectType|null  $runtimeTypeOrName
     * @param InterfaceType|UnionType $returnType
     * @param mixed                   $result
     */
    protected function ensureValidRuntimeType($runtimeTypeOrName, \GraphQL\Type\Definition\AbstractType $returnType, \GraphQL\Type\Definition\ResolveInfo $info, &$result) : \GraphQL\Type\Definition\ObjectType
    {
    }
}
/**
 * Data that must be available at all points during query execution.
 *
 * Namely, schema of the type system that is currently executing,
 * and the fragments defined in the query document.
 *
 * @internal
 */
class ExecutionContext
{
    /** @var Schema */
    public $schema;
    /** @var FragmentDefinitionNode[] */
    public $fragments;
    /** @var mixed */
    public $rootValue;
    /** @var mixed */
    public $contextValue;
    /** @var OperationDefinitionNode */
    public $operation;
    /** @var mixed[] */
    public $variableValues;
    /** @var callable */
    public $fieldResolver;
    /** @var Error[] */
    public $errors;
    /** @var PromiseAdapter */
    public $promiseAdapter;
    public function __construct($schema, $fragments, $rootValue, $contextValue, $operation, $variableValues, $errors, $fieldResolver, $promiseAdapter)
    {
    }
    public function addError(\GraphQL\Error\Error $error)
    {
    }
}
namespace GraphQL\Executor\Promise;

/**
 * Convenience wrapper for promises represented by Promise Adapter
 */
class Promise
{
    /** @var SyncPromise|ReactPromise */
    public $adoptedPromise;
    /** @var PromiseAdapter */
    private $adapter;
    /**
     * @param mixed $adoptedPromise
     */
    public function __construct($adoptedPromise, \GraphQL\Executor\Promise\PromiseAdapter $adapter)
    {
    }
    /**
     * @return Promise
     */
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null)
    {
    }
}
/**
 * Provides a means for integration of async PHP platforms ([related docs](data-fetching.md#async-php))
 */
interface PromiseAdapter
{
    /**
     * Return true if the value is a promise or a deferred of the underlying platform
     *
     * @param mixed $value
     *
     * @return bool
     *
     * @api
     */
    public function isThenable($value);
    /**
     * Converts thenable of the underlying platform into GraphQL\Executor\Promise\Promise instance
     *
     * @param object $thenable
     *
     * @return Promise
     *
     * @api
     */
    public function convertThenable($thenable);
    /**
     * Accepts our Promise wrapper, extracts adopted promise out of it and executes actual `then` logic described
     * in Promises/A+ specs. Then returns new wrapped instance of GraphQL\Executor\Promise\Promise.
     *
     * @return Promise
     *
     * @api
     */
    public function then(\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null);
    /**
     * Creates a Promise
     *
     * Expected resolver signature:
     *     function(callable $resolve, callable $reject)
     *
     * @return Promise
     *
     * @api
     */
    public function create(callable $resolver);
    /**
     * Creates a fulfilled Promise for a value if the value is not a promise.
     *
     * @param mixed $value
     *
     * @return Promise
     *
     * @api
     */
    public function createFulfilled($value = null);
    /**
     * Creates a rejected promise for a reason if the reason is not a promise. If
     * the provided reason is a promise, then it is returned as-is.
     *
     * @param Throwable $reason
     *
     * @return Promise
     *
     * @api
     */
    public function createRejected($reason);
    /**
     * Given an array of promises (or values), returns a promise that is fulfilled when all the
     * items in the array are fulfilled.
     *
     * @param Promise[]|mixed[] $promisesOrValues Promises or values.
     *
     * @return Promise
     *
     * @api
     */
    public function all(array $promisesOrValues);
}
namespace GraphQL\Executor\Promise\Adapter;

class ReactPromiseAdapter implements \GraphQL\Executor\Promise\PromiseAdapter
{
    /**
     * @inheritdoc
     */
    public function isThenable($value)
    {
    }
    /**
     * @inheritdoc
     */
    public function convertThenable($thenable)
    {
    }
    /**
     * @inheritdoc
     */
    public function then(\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null)
    {
    }
    /**
     * @inheritdoc
     */
    public function create(callable $resolver)
    {
    }
    /**
     * @inheritdoc
     */
    public function createFulfilled($value = null)
    {
    }
    /**
     * @inheritdoc
     */
    public function createRejected($reason)
    {
    }
    /**
     * @inheritdoc
     */
    public function all(array $promisesOrValues)
    {
    }
}
/**
 * Allows changing order of field resolution even in sync environments
 * (by leveraging queue of deferreds and promises)
 */
class SyncPromiseAdapter implements \GraphQL\Executor\Promise\PromiseAdapter
{
    /**
     * @inheritdoc
     */
    public function isThenable($value)
    {
    }
    /**
     * @inheritdoc
     */
    public function convertThenable($thenable)
    {
    }
    /**
     * @inheritdoc
     */
    public function then(\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null)
    {
    }
    /**
     * @inheritdoc
     */
    public function create(callable $resolver)
    {
    }
    /**
     * @inheritdoc
     */
    public function createFulfilled($value = null)
    {
    }
    /**
     * @inheritdoc
     */
    public function createRejected($reason)
    {
    }
    /**
     * @inheritdoc
     */
    public function all(array $promisesOrValues)
    {
    }
    /**
     * Synchronously wait when promise completes
     *
     * @return ExecutionResult
     */
    public function wait(\GraphQL\Executor\Promise\Promise $promise)
    {
    }
    /**
     * Execute just before starting to run promise completion
     */
    protected function beforeWait(\GraphQL\Executor\Promise\Promise $promise)
    {
    }
    /**
     * Execute while running promise completion
     */
    protected function onWait(\GraphQL\Executor\Promise\Promise $promise)
    {
    }
}
class AmpPromiseAdapter implements \GraphQL\Executor\Promise\PromiseAdapter
{
    /**
     * @inheritdoc
     */
    public function isThenable($value) : bool
    {
    }
    /**
     * @inheritdoc
     */
    public function convertThenable($thenable) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @inheritdoc
     */
    public function then(\GraphQL\Executor\Promise\Promise $promise, ?callable $onFulfilled = null, ?callable $onRejected = null) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @inheritdoc
     */
    public function create(callable $resolver) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @inheritdoc
     */
    public function createFulfilled($value = null) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @inheritdoc
     */
    public function createRejected($reason) : \GraphQL\Executor\Promise\Promise
    {
    }
    /**
     * @inheritdoc
     */
    public function all(array $promisesOrValues) : \GraphQL\Executor\Promise\Promise
    {
    }
    private static function resolveWithCallable(\Amp\Deferred $deferred, callable $callback, $argument) : void
    {
    }
}
namespace GraphQL\Executor;

/**
 * Implements the "Evaluating requests" section of the GraphQL specification.
 */
class Executor
{
    /** @var callable */
    private static $defaultFieldResolver = [self::class, 'defaultFieldResolver'];
    /** @var PromiseAdapter */
    private static $defaultPromiseAdapter;
    /** @var callable */
    private static $implementationFactory = [\GraphQL\Executor\ReferenceExecutor::class, 'create'];
    public static function getDefaultFieldResolver() : callable
    {
    }
    /**
     * Set a custom default resolve function.
     */
    public static function setDefaultFieldResolver(callable $fieldResolver)
    {
    }
    public static function getPromiseAdapter() : \GraphQL\Executor\Promise\PromiseAdapter
    {
    }
    /**
     * Set a custom default promise adapter.
     */
    public static function setPromiseAdapter(?\GraphQL\Executor\Promise\PromiseAdapter $defaultPromiseAdapter = null)
    {
    }
    public static function getImplementationFactory() : callable
    {
    }
    /**
     * Set a custom executor implementation factory.
     */
    public static function setImplementationFactory(callable $implementationFactory)
    {
    }
    /**
     * Executes DocumentNode against given $schema.
     *
     * Always returns ExecutionResult and never throws.
     * All errors which occur during operation execution are collected in `$result->errors`.
     *
     * @param mixed|null                    $rootValue
     * @param mixed|null                    $contextValue
     * @param array<mixed>|ArrayAccess|null $variableValues
     * @param string|null                   $operationName
     *
     * @return ExecutionResult|Promise
     *
     * @api
     */
    public static function execute(\GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentNode, $rootValue = null, $contextValue = null, $variableValues = null, $operationName = null, ?callable $fieldResolver = null)
    {
    }
    /**
     * Same as execute(), but requires promise adapter and returns a promise which is always
     * fulfilled with an instance of ExecutionResult and never rejected.
     *
     * Useful for async PHP platforms.
     *
     * @param mixed|null        $rootValue
     * @param mixed|null        $contextValue
     * @param array<mixed>|null $variableValues
     * @param string|null       $operationName
     *
     * @return Promise
     *
     * @api
     */
    public static function promiseToExecute(\GraphQL\Executor\Promise\PromiseAdapter $promiseAdapter, \GraphQL\Type\Schema $schema, \GraphQL\Language\AST\DocumentNode $documentNode, $rootValue = null, $contextValue = null, $variableValues = null, $operationName = null, ?callable $fieldResolver = null)
    {
    }
    /**
     * If a resolve function is not given, then a default resolve behavior is used
     * which takes the property of the root value of the same name as the field
     * and returns it as the result, or if it's a function, returns the result
     * of calling that function while passing along args and context.
     *
     * @param mixed                $objectValue
     * @param array<string, mixed> $args
     * @param mixed|null           $contextValue
     *
     * @return mixed|null
     */
    public static function defaultFieldResolver($objectValue, $args, $contextValue, \GraphQL\Type\Definition\ResolveInfo $info)
    {
    }
}
namespace GraphQL\Error;

/**
 * Encapsulates warnings produced by the library.
 *
 * Warnings can be suppressed (individually or all) if required.
 * Also it is possible to override warning handler (which is **trigger_error()** by default)
 */
final class Warning
{
    public const WARNING_ASSIGN = 2;
    public const WARNING_CONFIG = 4;
    public const WARNING_FULL_SCHEMA_SCAN = 8;
    public const WARNING_CONFIG_DEPRECATION = 16;
    public const WARNING_NOT_A_TYPE = 32;
    public const ALL = 63;
    /** @var int */
    private static $enableWarnings = self::ALL;
    /** @var mixed[] */
    private static $warned = [];
    /** @var callable|null */
    private static $warningHandler;
    /**
     * Sets warning handler which can intercept all system warnings.
     * When not set, trigger_error() is used to notify about warnings.
     *
     * @api
     */
    public static function setWarningHandler(?callable $warningHandler = null) : void
    {
    }
    /**
     * Suppress warning by id (has no effect when custom warning handler is set)
     *
     * Usage example:
     * Warning::suppress(Warning::WARNING_NOT_A_TYPE)
     *
     * When passing true - suppresses all warnings.
     *
     * @param bool|int $suppress
     *
     * @api
     */
    public static function suppress($suppress = true) : void
    {
    }
    /**
     * Re-enable previously suppressed warning by id
     *
     * Usage example:
     * Warning::suppress(Warning::WARNING_NOT_A_TYPE)
     *
     * When passing true - re-enables all warnings.
     *
     * @param bool|int $enable
     *
     * @api
     */
    public static function enable($enable = true) : void
    {
    }
    public static function warnOnce(string $errorMessage, int $warningId, ?int $messageLevel = null) : void
    {
    }
    public static function warn(string $errorMessage, int $warningId, ?int $messageLevel = null) : void
    {
    }
}
/**
 * Collection of flags for [error debugging](error-handling.md#debugging-tools).
 */
final class DebugFlag
{
    public const NONE = 0;
    public const INCLUDE_DEBUG_MESSAGE = 1;
    public const INCLUDE_TRACE = 2;
    public const RETHROW_INTERNAL_EXCEPTIONS = 4;
    public const RETHROW_UNSAFE_EXCEPTIONS = 8;
}
/**
 * Error caused by actions of GraphQL clients. Can be safely displayed to a client...
 */
class UserError extends \RuntimeException implements \GraphQL\Error\ClientAware
{
    /**
     * @return bool
     */
    public function isClientSafe()
    {
    }
    /**
     * @return string
     */
    public function getCategory()
    {
    }
}
/**
 * Note:
 * This exception should not inherit base Error exception as it is raised when there is an error somewhere in
 * user-land code
 */
class InvariantViolation extends \LogicException
{
    public static function shouldNotHappen() : self
    {
    }
}
/**
 * This class is used for [default error formatting](error-handling.md).
 * It converts PHP exceptions to [spec-compliant errors](https://facebook.github.io/graphql/#sec-Errors)
 * and provides tools for error debugging.
 */
class FormattedError
{
    /** @var string */
    private static $internalErrorMessage = 'Internal server error';
    /**
     * Set default error message for internal errors formatted using createFormattedError().
     * This value can be overridden by passing 3rd argument to `createFormattedError()`.
     *
     * @param string $msg
     *
     * @api
     */
    public static function setInternalErrorMessage($msg)
    {
    }
    /**
     * Prints a GraphQLError to a string, representing useful location information
     * about the error's position in the source.
     *
     * @return string
     */
    public static function printError(\GraphQL\Error\Error $error)
    {
    }
    /**
     * Render a helpful description of the location of the error in the GraphQL
     * Source document.
     *
     * @return string
     */
    private static function highlightSourceAtLocation(\GraphQL\Language\Source $source, \GraphQL\Language\SourceLocation $location)
    {
    }
    /**
     * @return int
     */
    private static function getColumnOffset(\GraphQL\Language\Source $source, \GraphQL\Language\SourceLocation $location)
    {
    }
    /**
     * @param int $len
     *
     * @return string
     */
    private static function whitespace($len)
    {
    }
    /**
     * @param int $len
     *
     * @return string
     */
    private static function lpad($len, $str)
    {
    }
    /**
     * Standard GraphQL error formatter. Converts any exception to array
     * conforming to GraphQL spec.
     *
     * This method only exposes exception message when exception implements ClientAware interface
     * (or when debug flags are passed).
     *
     * For a list of available debug flags @see \GraphQL\Error\DebugFlag constants.
     *
     * @param string $internalErrorMessage
     *
     * @return mixed[]
     *
     * @throws Throwable
     *
     * @api
     */
    public static function createFromException(\Throwable $exception, int $debug = \GraphQL\Error\DebugFlag::NONE, $internalErrorMessage = null) : array
    {
    }
    /**
     * Decorates spec-compliant $formattedError with debug entries according to $debug flags
     * (@see \GraphQL\Error\DebugFlag for available flags)
     *
     * @param mixed[] $formattedError
     *
     * @return mixed[]
     *
     * @throws Throwable
     */
    public static function addDebugEntries(array $formattedError, \Throwable $e, int $debugFlag) : array
    {
    }
    /**
     * Prepares final error formatter taking in account $debug flags.
     * If initial formatter is not set, FormattedError::createFromException is used
     */
    public static function prepareFormatter(?callable $formatter, int $debug) : callable
    {
    }
    /**
     * Returns error trace as serializable array
     *
     * @param Throwable $error
     *
     * @return mixed[]
     *
     * @api
     */
    public static function toSafeTrace($error)
    {
    }
    /**
     * @param mixed $var
     *
     * @return string
     */
    public static function printVar($var)
    {
    }
    /**
     * @deprecated as of v0.8.0
     *
     * @param string           $error
     * @param SourceLocation[] $locations
     *
     * @return mixed[]
     */
    public static function create($error, array $locations = [])
    {
    }
    /**
     * @deprecated as of v0.10.0, use general purpose method createFromException() instead
     *
     * @return mixed[]
     *
     * @codeCoverageIgnore
     */
    public static function createFromPHPError(\ErrorException $e)
    {
    }
}
/**
 * Describes an Error found during the parse, validate, or
 * execute phases of performing a GraphQL operation. In addition to a message
 * and stack trace, it also includes information about the locations in a
 * GraphQL document and/or execution result that correspond to the Error.
 *
 * When the error was caused by an exception thrown in resolver, original exception
 * is available via `getPrevious()`.
 *
 * Also read related docs on [error handling](error-handling.md)
 *
 * Class extends standard PHP `\Exception`, so all standard methods of base `\Exception` class
 * are available in addition to those listed below.
 */
class Error extends \Exception implements \JsonSerializable, \GraphQL\Error\ClientAware
{
    const CATEGORY_GRAPHQL = 'graphql';
    const CATEGORY_INTERNAL = 'internal';
    /**
     * Lazily initialized.
     *
     * @var SourceLocation[]
     */
    private $locations;
    /**
     * An array describing the JSON-path into the execution response which
     * corresponds to this error. Only included for errors during execution.
     *
     * @var mixed[]|null
     */
    public $path;
    /**
     * An array of GraphQL AST Nodes corresponding to this error.
     *
     * @var Node[]|null
     */
    public $nodes;
    /**
     * The source GraphQL document for the first location of this error.
     *
     * Note that if this Error represents more than one node, the source may not
     * represent nodes after the first node.
     *
     * @var Source|null
     */
    private $source;
    /** @var int[] */
    private $positions;
    /** @var bool */
    private $isClientSafe;
    /** @var string */
    protected $category;
    /** @var mixed[]|null */
    protected $extensions;
    /**
     * @param string                       $message
     * @param Node|Node[]|Traversable|null $nodes
     * @param mixed[]                      $positions
     * @param mixed[]|null                 $path
     * @param Throwable                    $previous
     * @param mixed[]                      $extensions
     */
    public function __construct($message = '', $nodes = null, ?\GraphQL\Language\Source $source = null, array $positions = [], $path = null, $previous = null, array $extensions = [])
    {
    }
    /**
     * Given an arbitrary Error, presumably thrown while attempting to execute a
     * GraphQL operation, produce a new GraphQLError aware of the location in the
     * document responsible for the original Error.
     *
     * @param mixed        $error
     * @param Node[]|null  $nodes
     * @param mixed[]|null $path
     *
     * @return Error
     */
    public static function createLocatedError($error, $nodes = null, $path = null)
    {
    }
    /**
     * @return mixed[]
     */
    public static function formatError(\GraphQL\Error\Error $error)
    {
    }
    /**
     * @inheritdoc
     */
    public function isClientSafe()
    {
    }
    /**
     * @inheritdoc
     */
    public function getCategory()
    {
    }
    public function getSource() : ?\GraphQL\Language\Source
    {
    }
    /**
     * @return int[]
     */
    public function getPositions() : array
    {
    }
    /**
     * An array of locations within the source GraphQL document which correspond to this error.
     *
     * Each entry has information about `line` and `column` within source GraphQL document:
     * $location->line;
     * $location->column;
     *
     * Errors during validation often contain multiple locations, for example to
     * point out to field mentioned in multiple fragments. Errors during execution include a
     * single location, the field which produced the error.
     *
     * @return SourceLocation[]
     *
     * @api
     */
    public function getLocations() : array
    {
    }
    /**
     * @return Node[]|null
     */
    public function getNodes()
    {
    }
    /**
     * Returns an array describing the path from the root value to the field which produced this error.
     * Only included for execution errors.
     *
     * @return mixed[]|null
     *
     * @api
     */
    public function getPath()
    {
    }
    /**
     * @return mixed[]
     */
    public function getExtensions()
    {
    }
    /**
     * Returns array representation of error suitable for serialization
     *
     * @deprecated Use FormattedError::createFromException() instead
     *
     * @return mixed[]
     *
     * @codeCoverageIgnore
     */
    public function toSerializableArray()
    {
    }
    /**
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
    }
    /**
     * @return string
     */
    public function __toString()
    {
    }
}
class SyntaxError extends \GraphQL\Error\Error
{
    /**
     * @param int    $position
     * @param string $description
     */
    public function __construct(\GraphQL\Language\Source $source, $position, $description)
    {
    }
}
namespace GraphQL\Exception;

final class InvalidArgument extends \InvalidArgumentException
{
    /**
     * @param mixed $argument
     */
    public static function fromExpectedTypeAndArgument(string $expectedType, $argument) : self
    {
    }
}