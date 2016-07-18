<?php

namespace Cmp\Queue\Domain;


abstract class AbstractWriter
{

    private $writableDomainObjects = [];

    /**
     * @param WritableDomainObject $writableDomainObject
     *
     * @return mixed
     */
    abstract protected function writeOne(WritableDomainObject $writableDomainObject);

    /**
     * @param WritableDomainObject[] $writableDomainObjects
     *
     * @return mixed
     */
    abstract protected function writeSome(array $writableDomainObjects);

    /**
     * @param WritableDomainObject $writableDomainObject
     */
    public function add(WritableDomainObject $writableDomainObject)
    {
        array_push($this->writableDomainObjects, $writableDomainObject);
    }

    /**
     * @throws \Cmp\Queue\Domain\ConnectionException
     */
    public function write()
    {
        $numOfDomainEvents = count($this->writableDomainObjects);

        if ($numOfDomainEvents === 1) {
            $this->writeOne($this->writableDomainObjects[0]);
        } else if($numOfDomainEvents > 1) {
            $this->writeSome($this->writableDomainObjects);
        }
    }

}