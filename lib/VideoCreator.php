<?php
class VideoCreator
{
    private $logOutput;
    private $workingDir;
    private $output = "video.mp4";

    public function __construct($workingDir, $logOutput = true)
    {
        $this->workingDir = $workingDir;
        $this->logOutput = $logOutput;
    }

    public function createVideo($directory)
    {
        chdir($directory);

        $command = "ffmpeg -framerate 6 -y -i image-%d.jpg -c:v libx264 -vf \"fps=15,format=yuv420p\" -threads 1 " . $this->getOutput();


        echo "\n\n------------------------------\n\n";
        echo "cd ".$directory." & ";
        echo $command."\n\n";
        echo "\n\n------------------------------\n\n";

        if($this->logOutput) {
            echo "Starting ffmpeg...\n\n";
            echo shell_exec($command);
            echo "Done.\n";
        } else {
            shell_exec($command);
        }

        $result = file_exists($this->getOutput());

        chdir($this->workingDir);

        return $result;
    }

    public function getOutput()
    {
        return $this->output;
    }
}