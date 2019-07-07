<?php

namespace PhpcsDiff;

use League\CLImate\CLImate;
use PhpcsDiff\Filter\Exception\FilterException;
use PhpcsDiff\Filter\Filter;
use PhpcsDiff\Filter\Rule\HasMessagesRule;
use PhpcsDiff\Filter\Rule\PhpFileRule;
use PhpcsDiff\Mapper\PhpcsViolationsMapper;

class PhpcsDiff
{
    const DEFAULT_RULESET = 'ruleset.xml';

    /**
     * @var array
     */
    protected $argv = [];

    /**
     * @var CLImate
     */
    protected $climate;

    /**
     * @var bool
     */
    protected $isVerbose = false;

    /**
     * @var int
     */
    protected $exitCode = 0;

    /**
     * @var string
     */
    protected $baseBranch;

    /**
     * @var string
     */
    protected $currentBranch = '';

    /**
     * @param array $argv
     * @param CLImate $climate
     */
    public function __construct(array $argv, CLImate $climate)
    {
        $this->argv = $argv;
        $this->climate = $climate;

        if ($this->isFlagSet('-v')) {
            $this->climate->comment('Running in verbose mode.');
            $this->isVerbose = true;
        }

        $this->ruleset = $this->getOption('--ruleset', self::DEFAULT_RULESET);

        $this->baseBranch = $this->getArgument(1, '');
        $this->currentBranch = $this->getArgument(2, '');
        $this->climate->comment(
            'Comparing branch: "' . $this->baseBranch . '" against: "' . $this->currentBranch . '"'
        );
    }

    protected function getArgument($index, $default = null)
    {
        $arguments = array_filter($this->argv, function ($val) {
            return strpos($val, '-') === false;
        });

        return isset($arguments[$index]) ? $arguments[$index] : $default;
    }

    protected function getOption($name, $default = null)
    {
        foreach ($this->argv as $arg) {
            list($flag, $val) = explode('=', $arg);

            if ($flag == $name) {
                return $val;
            }
        }

        return $default;
    }

    /**
     * @param string $flag
     * @return bool
     */
    protected function isFlagSet($flag)
    {
        return in_array($flag, array_values($this->argv));
    }

    /**
     * @param int $exitCode
     */
    protected function setExitCode($exitCode)
    {
        if (!is_int($exitCode)) {
            throw new \UnexpectedValueException('The exit code provided is not a valid integer.');
        }

        $this->exitCode = $exitCode;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @todo Automatically look at server envs for the travis base branch, if not provided?
     */
    public function run()
    {
        try {
            $filter = new Filter([new PhpFileRule()], $this->getChangedFiles());
        } catch (FilterException $exception) {
            $this->error($exception->getMessage());
            return;
        }

        $fileDiff = $filter->filter()->getFilteredData();

        if (empty($fileDiff)) {
            $this->climate->info('No difference to compare.');
            return;
        }

        if ($this->isVerbose) {
            $fileDiffCount = count($fileDiff);
            $this->climate->comment(
                'Checking ' . $fileDiffCount . ' file(s) for violations.'
            );
        }

        $phpcsOutput = $this->runPhpcs($fileDiff, $this->ruleset);

        if (is_null($phpcsOutput)) {
            $this->error('Unable to run phpcs executable.');
            return;
        }

        if ($this->isVerbose) {
            $this->climate->comment('Filtering phpcs output.');
        }

        try {
            $filter = new Filter([new HasMessagesRule()], $phpcsOutput['files']);
        } catch (FilterException $exception) {
            $this->error($exception->getMessage());
            return;
        }

        $files = $filter->filter()->getFilteredData();

        if ($this->isVerbose) {
            $this->climate->comment('Getting changed lines from git diff.');
        }

        $changedLinesPerFile = $this->getChangedLinesPerFile($files);

        if ($this->isVerbose) {
            $this->climate->comment('Comparing phpcs output with changes lines from git diff.');
        }

        $violations = (new PhpcsViolationsMapper(
            $changedLinesPerFile,
            getcwd()
        ))->map($files);

        if ($this->isVerbose) {
            $this->climate->comment('Preparing report.');
        }

        if (empty($violations)) {
            $this->climate->info('No violations to report.');
            return;
        }

        $this->outputViolations($violations);
    }

    /**
     * Run phpcs on a list of files passed into the method
     *
     * @param array $files
     * @param string $ruleset
     * @return mixed
     */
    protected function runPhpcs(array $files = [], $ruleset = 'ruleset.xml')
    {
        $exec = 'vendor/bin/phpcs';

        if (is_file(__DIR__ . '/../../../bin/phpcs')) {
            $exec = realpath(__DIR__ . '/../../../bin/phpcs');
        } elseif (is_file(__DIR__ . '/../bin/phpcs')) {
            $exec = realpath(__DIR__ . '/../bin/phpcs');
        }

        return json_decode(
            shell_exec($exec . ' --report=json --standard=' . $ruleset . ' ' . implode(' ', $files)),
            true
        );
    }

    /**
     * @param array $output
     */
    protected function outputViolations(array $output)
    {
        $this->climate->flank(strtoupper('Start of phpcs check'), '#', 10)->br();
        $this->climate->out(implode(PHP_EOL, $output));
        $this->climate->flank(strtoupper('End of phpcs check'), '#', 11)->br();

        $this->error('Violations have been reported.');
    }

    /**
     * Returns a list of files which are within the diff based on the current branch
     *
     * @return array
     */
    protected function getChangedFiles()
    {
        // Get a list of changed files (not including deleted files)
        $output = shell_exec(
            'git diff ' . $this->baseBranch . ' ' . $this->currentBranch . ' --name-only --diff-filter=d'
        );

        // Convert files into an array
        $output = explode(PHP_EOL, $output);

        // Remove any empty values
        return array_filter($output);
    }

    /**
     * Extract the changed lines for each file from the git diff output
     *
     * @param array $files
     * @return array
     */
    protected function getChangedLinesPerFile(array $files)
    {
        $extract = [];
        $pattern = '@@ -[0-9]+(?:,[0-9]+)? \+([0-9]+)(?:,([0-9]+))? @@';

        foreach ($files as $file => $data) {
            $command = 'git diff -U0 ' . $this->baseBranch . ' ' . $this->currentBranch . ' ' . $file .
                ' | grep -P ' . escapeshellarg($pattern);
            $lineDiff = shell_exec($command);
            $lines = array_filter(explode(PHP_EOL, $lineDiff));
            $linesChanged = [];

            foreach ($lines as $line) {
                preg_match('/' . $pattern . '/', $line, $matches);

                $start = $end = (int)$matches[1];

                // Multiple lines were changed, so we need to calculate the end line
                if (isset($matches[2])) {
                    $length = (int)$matches[2];
                    $end = $start + $length - 1;
                }

                foreach (range($start, $end) as $l) {
                    $linesChanged[$l] = null;
                }
            }

            $extract[$file] = array_keys($linesChanged);
        }

        return $extract;
    }

    /**
     * @param string $message
     * @param int $exitCode
     */
    protected function error($message, $exitCode = 1)
    {
        $this->climate->error($message);
        $this->setExitCode($exitCode);
    }
}
