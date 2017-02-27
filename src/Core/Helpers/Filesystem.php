<?php

namespace Core\Helpers;

class Filesystem
{
    /**
     * @param $dirs
     * @param int $mode
     * @throws \Exception
     */
    public function mkdir($dirs, $mode = 0777)
    {
        foreach ($this->toArrayObject($dirs) as $dir) {
            if (is_dir($dir)) {
                continue;
            }

            if (!(mkdir($dir, $mode, true))) {
                $error = error_get_last();
                if (!is_dir($dir)) {

                    if ($error) {
                        throw new \Exception('Failed to create dir:' . $dir . $error['message']);
                    }
                    throw new \Exception("Fuck");
                }
            }
        }
    }

    /**
     * @param mixed $files
     *
     * @return \Traversable $files
     */
    private function toArrayObject($files)
    {
        if (!$files instanceof \Traversable && !(is_array($files))) {
            $files = new \ArrayObject([$files]);
        }

        return $files;
   }
}