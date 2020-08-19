<?php
namespace Kibo\Phast\ValueObjects;

class Query implements \IteratorAggregate {
    private $tuples = [];

    /**
     * @param array $assoc
     * @return Query
     */
    public static function fromAssoc($assoc) {
        $result = new static();
        foreach ($assoc as $k => $v) {
            $result->add($k, $v);
        }
        return $result;
    }

    /**
     * @param string $string
     * @return Query
     */
    public static function fromString($string) {
        $result = new static();
        foreach (explode('&', $string) as $piece) {
            if ($piece === '') {
                continue;
            }
            $parts = array_map('urldecode', explode('=', $piece, 2));
            $result->add($parts[0], isset($parts[1]) ? $parts[1] : '');
        }
        return $result;
    }

    public function add($key, $value) {
        $this->tuples[] = [(string) $key, (string) $value];
    }

    public function get($key, $default = null) {
        foreach ($this->tuples as $tuple) {
            if ($tuple[0] === (string) $key) {
                return $tuple[1];
            }
        }
        return $default;
    }

    public function delete($key) {
        $this->tuples = array_filter($this->tuples, function ($tuple) use ($key) {
            return $tuple[0] !== (string) $key;
        });
    }

    public function set($key, $value) {
        $this->delete($key);
        $this->add($key, $value);
    }

    public function has($key) {
        foreach ($this->tuples as $tuple) {
            if ($tuple[0] === (string) $key) {
                return true;
            }
        }
        return false;
    }

    public function update(Query $source) {
        foreach ($source as $key => $value) {
            $this->delete($key);
        }
        foreach ($source as $key => $value) {
            $this->add($key, $value);
        }
    }

    public function toAssoc() {
        $assoc = [];
        foreach ($this->tuples as $tuple) {
            if (!array_key_exists($tuple[0], $assoc)) {
                $assoc[$tuple[0]] = $tuple[1];
            }
        }
        return $assoc;
    }

    public function getIterator() {
        foreach ($this->tuples as $tuple) {
            yield $tuple[0] => $tuple[1];
        }
    }

    public function pop($key) {
        $value = $this->get($key);
        $this->delete($key);
        return $value;
    }

    public function getAll($key) {
        $result = [];
        foreach ($this->tuples as $tuple) {
            if ($tuple[0] === (string) $key) {
                $result[] = $tuple[1];
            }
        }
        return $result;
    }
}
