<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * TestSetting
 *
 * @ORM\Table(name="test_settings")
 * @ORM\Entity
 */
class TestSetting extends BaseEntity
{
    protected $id;

    protected $key;

    protected $value;

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}

