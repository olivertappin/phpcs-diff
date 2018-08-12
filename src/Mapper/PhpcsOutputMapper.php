<?php

namespace PhpcsDiff\Mapper;

class PhpcsOutputMapper implements MapperInterface
{
    /**
     * @var array
     */
    protected $changedLinesPerFile;

    /**
     * @var string
     */
    protected $currentDirectory;

    /**
     * @param array $changedLinesPerFile
     * @param string $currentDirectory
     */
    public function __construct(array $changedLinesPerFile, $currentDirectory)
    {
        $this->changedLinesPerFile = $changedLinesPerFile;
        $this->currentDirectory = $currentDirectory;
    }

    /**
     * @param array $data
     * @return array
     */
    public function map(array $data)
    {
        $mappedData = [];

        foreach ($data as $file => $report) {
            if (!isset($this->changedLinesPerFile[$file]) || !is_array($this->changedLinesPerFile[$file])) {
                continue;
            }

            $changedLinesFromDiff = $this->changedLinesPerFile[$file];

            $output = [];
            foreach ($report['messages'] as $message) {
                if (!in_array($message['line'], $changedLinesFromDiff, true)) {
                    continue;
                }
                $output[] = ' - Line ' . $message['line'] . ' (' . $message['type'] . ') ' . $message['message'];
            }

            if (empty($output)) {
                continue;
            }

            $mappedData[] = str_replace($this->currentDirectory . '/', '', $file) . PHP_EOL .
                implode(PHP_EOL, $output) . PHP_EOL;
        }

        return $mappedData;
    }
}
