<?php
    class process{
        private $handler;

        public function __construct($handler){
            $this->handler = $handler;
        }
        
        public function pVideo($targetPath, $extension, $v_id){
            global $ffmpeg;

            $query = $this->handler->prepare('SELECT * FROM processingqueue WHERE v_id = :v_id AND pr_busy = 0');
            $query->execute([
                ':v_id' => $v_id
            ]);

            $fetch = $query->fetch(PDO::FETCH_ASSOC);

            $queryU = $this->handler->prepare('UPDATE processingqueue SET pr_busy = 1 WHERE v_id = :v_id');
            $queryU->execute([
                ':v_id' => $fetch['v_id']
            ]);

            $video = $ffmpeg->open($targetPath . '1.' . $extension);
            $format = new \FFMpeg\Format\Video\X264();
            $format->on('progress', function ($video, $format, $percentage) {
                $queryU = $this->handler->prepare('UPDATE processingqueue SET pr_current = :current WHERE v_id = :v_id');
                $queryU->execute([
                    ':current'  => $percentage,
                    ':v_id'     => $fetch['v_id']
                ]);
            });
            $format->setVideoCodec('libx264');
            $format->setAudioCodec('aac');
            $format->setAdditionalParameters(['-preset', 'fast', '-crf', '24']);
            $video->save($format, $targetPath . '.mp4');
            unlink($targetPath . '1.' . $extension);

            $queryD = $this->handler->prepare('DELETE FROM processingqueue WHERE v_id = :v_id');
            $queryD->execute([
                ':v_id'     => $fetch['v_id']
            ]);

            $this->handler->query('ALTER TABLE processingqueue AUTO_INCREMENT = 1');
        }
    }
?>