<?php

namespace EventSourced\Serializer\ValueObject;

use EventSourced\Serializer\Serializer;
use EventSourced\ValueObject\AbstractComposite;

class Composite extends AbstractSerializer
{    
    private $serializer;
    
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }
    
    public function serialize(AbstractComposite $object)
    {
        $pararameters = $this->get_constructor_parameters(get_class($object));
        $value_objects = $this->get_private_property($object, 'value_objects');
        
        foreach ($pararameters as $index=>$parameter) {
            $name = $parameter->getName();
            $value = $value_objects[$index]; 
            $serialized[$name] = $this->serializer->serialize($value);
        }
		return $serialized;
    }
    
    public function deserialize($class, $serialized)
    {
        $deserialized_parameters = [];
        $parameters = $this->get_constructor_parameters($class);
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $parameter_class = $parameter->getClass()->getName();
            $deserialized_parameters[$name] = $this->serializer->deserialize(
                $parameter_class, $serialized[$name]
            );
        }
        return $this->call_constructor($class, $deserialized_parameters);
    }

}
