<?

#phpx cli.php privateAccess 1
class privateAccessController extends cli
{
    static function main()
    {
        $privateVariablesAndMethods = ['methods'=>[],'vars'=>[]];
        $private = new exclusive(['a' => 1]);
        $reflect = new ReflectionClass('exclusive');
#ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_ABSTRACT | ReflectionMethod::IS_FINAL
        $methods = $reflect->getMethods(ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            $method->setAccessible(true);
            $privateVariablesAndMethods['methods'][$method->getName()] = $method->invoke($private);#new exclusive
        }

        $props = $reflect->getProperties(ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $v = $prop->getValue($private);
            $prop->setValue($private, $v . '_2');#alter private prop
            $privateVariablesAndMethods[$prop->getName()] = $prop->getValue($private);
        }
        print_r($privateVariablesAndMethods);
    }
}

return; ?>
$method = new ReflectionMethod('exclusive','method1');
$method->setAccessible(true);
$privateVariablesAndMethods=$variables=[];
$privateVariablesAndMethods['method1']=$method->invoke(new exclusive);

$method = new ReflectionMethod('exclusive','method2');
$method->setAccessible(true);
$privateVariablesAndMethods['method2']=$method->invoke(new exclusive);
