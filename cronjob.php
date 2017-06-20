<?php
ini_set('max_execution_time', 0);

include_once 'lib/FoldersTreeBuilder.php';
include_once 'lib/ImagesRenamer.php';
include_once 'lib/VideoCreator.php';
include_once 'lib/FolderEntity.php';

$rootSnapshotsFolder = 'camera';
$foldersTreeBuilder = new FoldersTreeBuilder($rootSnapshotsFolder);
$imageRenamer = new ImagesRenamer();
$videoCreator = new VideoCreator(__DIR__);

$today = date('Y-m-d', time());

$pictureCount = $foldersTreeBuilder->getCameraTree();

if(count($pictureCount) > 0) {
    foreach ($pictureCount as $a => $area) {
        /* @var $areaEntity \FolderEntity */
        foreach ($area as $areaEntity) {
            $areaDate = $areaEntity->getDateOnly();
            $directory = $areaEntity->getPath();

            if (strtotime($areaDate) < strtotime($today)) {


                $files = $imageRenamer->setDirectory($directory)->renameFiles()->getFiles();

                if ($files) {
                    if ($videoCreator->createVideo($directory)) {
                        $imageRenamer->removeFiles();
                    }
                }
//
//            if( count($imageRenamer->getFiles()) == 0 && !file_exists($directory.DIRECTORY_SEPARATOR."video.mp4")) {
//                unlink($directory);
//            }
            }
        }
    }
} else {
    echo "There is nothing to convert...";
}
exit();