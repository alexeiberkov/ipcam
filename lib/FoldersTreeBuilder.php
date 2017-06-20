<?php
class FoldersTreeBuilder
{
    private $rootFolder;

    private $cameraTree = array();
    private $allFolders = array();
    private $cameraIPs = array();
    private $areas = array();

    public function __construct($rootFolder)
    {
        $this->rootFolder = $rootFolder;
        $this->execute();
    }

    public function execute()
    {
        //Step 1. Get all folders
        $this->generateAllFolders();

        //Step 2. Get all camera IPs
        $this->generateCameraIP();

        //Step 3. Generate areas only
        $this->generateAreasOnly();

        //Step 4. Get all folders by dates and put to IPs
        $this->generateFoldersByDatesPerIP();
    }

    private function generateAllFolders()
    {
        $this->allFolders = $this->expandDirectories($this->rootFolder);
    }

    private function generateCameraIP()
    {
        $cameraIPs = array_flip(array_filter($this->getAllFolders(), function ($dir) {
            return substr_count($dir, DIRECTORY_SEPARATOR) == 2;
        }));

        array_walk($cameraIPs, function (&$value) {
            $value = array();
        });
        $this->cameraIPs = $cameraIPs;
    }

    private function generateAreasOnly()
    {
        $areas = array();
        foreach ($this->getCameraIPs() as $areaFolder => $a) {
            list($rootFolder, $place, $area) = explode(DIRECTORY_SEPARATOR, $areaFolder);
            if (!array_key_exists($place, $areas)) {
                $areas[$place] = array();
            }

            $areas[$place][] = $area;
        }

        $this->areas = $areas;
    }

    private function generateFoldersByDatesPerIP()
    {

        $directories = $this->getAllFolders();
        $cameraIPs = $this->cameraIPs;

        $cameraByDates = array_filter($directories, function ($dir) {
            return strpos($dir, DIRECTORY_SEPARATOR . "pic") !== false;
        });

        foreach ($cameraByDates as $exactDateData) {
            foreach ($cameraIPs as $ip => &$cameraIp) {
                if (strpos($exactDateData, $ip) === 0) {
                    $beans = DIRECTORY_SEPARATOR . "01" . DIRECTORY_SEPARATOR . "pic";

                    $dateFolder = substr($exactDateData, strlen($ip) + 1);
                    $dateOnly = substr($dateFolder, 0, strpos($dateFolder, $beans));

                    $cameraIp[$dateOnly] = new FolderEntity($dateOnly, $dateFolder, $ip);
                }
            }
        }

        $areasCameraIPs = array();
        foreach ($cameraIPs as $area => $dates) {
            $keyWithoutBean = substr($area, strrpos($area, DIRECTORY_SEPARATOR) + 1);
            $areasCameraIPs[$keyWithoutBean] = $dates;
        }

        $this->cameraTree = $areasCameraIPs;
    }

    public function getCameraTree()
    {
        return $this->cameraTree;
    }

    public function getAreas()
    {
        return $this->areas;
    }

    public function getAllFolders()
    {
        return $this->allFolders;
    }

    public function getCameraIPs()
    {
        return $this->cameraIPs;
    }

    static function expandDirectories($baseDir)
    {
        $directories = array();
        foreach (scandir($baseDir) as $file) {
            if ($file == '.' || $file == '..') continue;
            $dir = $baseDir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($dir)) {
                $directories [] = $dir;
                $directories = array_merge($directories, self::expandDirectories($dir));
            }
        }
        return $directories;
    }
}