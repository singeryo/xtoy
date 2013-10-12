<?php

namespace XtoY;

use XtoY\Reader\ReaderInterface;
use XtoY\Mapper\MapperInterface;
use XtoY\Writer\WriterInterface;

class XtoY {

    const MODE_SEQUENTIAL = 'sequential';
    const MODE_FULL = 'full';

    protected $mode,
            $reader,
            $mapper,
            $writer;

    public function __construct() {
        $this->setMode(self::MODE_SEQUENTIAL);
        ;
    }

    public function setReader(ReaderInterface $reader) {
        $this->reader = $reader;
    }

    public function setMapper(MapperInterface $mapper) {

        $this->mapper = $mapper;
    }

    public function setWriter(WriterInterface $writer) {
        $this->writer = $writer;
    }

    public function setMode($mode) {

        $this->mode = $mode;
    }

    public function getMode() {

        return $this->mode;
    }

    public function run() {

        if ($this->mode == self::MODE_SEQUENTIAL) {
            $this->reader->open();
            $this->writer->open();
            $this->reader->preprocessing();
            $this->writer->preprocessing();
            while (false != ($data = $this->reader->fetch())) {

                $data = $this->mapper->convert($data);
                $this->writer->write($data);
            }
            $this->writer->postprocessing();
            $this->reader->close();
            $this->writer->close();
        } else {
            $this->reader->open();
            $this->reader->preprocessing();
            $datas = $this->reader->fetchAll();
            $this->reader->close();
            $datas = $this->mapper->batchConvert($datas);
            $this->writer->open();
            $this->writer->preprocessing();
            $this->writer->writeAll($datas);
            $this->writer->postprocessing();
            $this->writer->close();
        }
    }

}