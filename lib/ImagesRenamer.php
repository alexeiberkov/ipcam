<?php
class ImagesRenamer
{
    private $directory = '';

    private $imageGlob = "image-";
    private $imageExtension = "jpg";

    private $files = array();


    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;
        return $this;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    private function getImagesListInFolder($directory, $sort = true)
    {
        $files = glob($directory . DIRECTORY_SEPARATOR . '*.' . $this->imageExtension);

        if ($sort) {
            usort($files, function ($a, $b) {
                return filemtime($a) > filemtime($b);
            });
        }
        return $files;
    }

    public function renameFiles()
    {
        if (!$this->directory) {
            throw new \Exception('You did not set directory to search images');
        }

        $files = $this->getImagesListInFolder($this->directory);

        if ($files) {
            $this->renameFilesWithIterator($files);
        }

        $files = $this->getImagesListInFolder($this->directory);

        $this->setFiles($files);

        return $this;
    }

    private function renameFilesWithIterator($files)
    {
        $counter = 0;
        foreach ($files as $snapshot) {
            if (strpos($snapshot, $this->imageGlob) === false) {
                $pathOnly = substr($snapshot, 0, strrpos($snapshot, DIRECTORY_SEPARATOR));
                $newImageFile = $pathOnly . DIRECTORY_SEPARATOR . $this->imageGlob . $counter . "." . $this->imageExtension;

                if (rename($snapshot, $newImageFile)) {
                    $counter++;
                } else {
                    throw new \Exception('Can not rename file ' . $snapshot . ' to ' . $newImageFile);
                }
            }
        }

    }

    public function removeFiles()
    {
        $files = $this->getImagesListInFolder($this->getDirectory());

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function folderAndFilesHaveTheSameDate($path, $date)
    {
        $filesInDirectory = $this->getImagesListInFolder($path, false);

        if(count($filesInDirectory) == 0) return null;

        $filename = $filesInDirectory[0];
        $lastChanged = date('Y-m-d', filectime($filename));

        if(strtotime($lastChanged) === strtotime($date)) return true;

        return $lastChanged;
    }
}